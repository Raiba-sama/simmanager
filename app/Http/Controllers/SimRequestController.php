<?php

namespace App\Http\Controllers;

use App\Mail\RequestRejectedNotification;
use App\Mail\RequestValidatedNotification;
use App\Mail\NewRequestNotification;
use App\Models\Sim;
use App\Models\SimRequest;
use App\Models\User;
use App\Models\Plan;
use App\Models\SimHistory;
use App\Exports\SimRequestsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
class SimRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = SimRequest::with(['user', 'sim', 'validator', 'plan', 'admin', 'favoritedBy'])
            ->orderBy('created_at', 'desc');

        // Visibilité : User voit seulement ses demandes, Validator/Admin voient tout
        if (!$user->isValidator()) {
            $query->where('user_id', $user->id);
        }

        // Filtre favoris
        if ($request->has('favorites') && $request->favorites === '1') {
            $query->whereHas('favoritedBy', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        $requests = $query->paginate(15);
        
        // Charger les favoris de l'utilisateur pour chaque demande
        $favoriteIds = $user->favorites()->pluck('sim_request_id')->toArray();
        foreach ($requests as $request) {
            $request->is_favorite = in_array($request->id, $favoriteIds);
            // Calculer les jours en attente pour l'affichage
            if ($request->status === 'en_attente') {
                $request->days_pending = now()->diffInDays($request->created_at);
            } else {
                $request->days_pending = 0;
            }
        }

        return view('sim-requests.index', compact('requests'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        
        $query = SimRequest::with(['user', 'sim', 'validator', 'plan', 'admin'])
            ->orderBy('created_at', 'desc');

        // Visibilité : User voit seulement ses demandes, Validator/Admin voient tout
        if (!$user->isValidator()) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        // Export des éléments sélectionnés si des IDs sont fournis
        if ($request->has('ids') && is_array($request->ids)) {
            $query->whereIn('id', $request->ids);
        }

        $requests = $query->get();
        
        $format = $request->get('format', 'excel');
        $filename = 'demandes_sim_' . date('Y-m-d_His') . '.' . ($format === 'pdf' ? 'pdf' : 'xlsx');

        if ($format === 'pdf') {
            return $this->exportPdf($requests);
        }

        try {
            return Excel::download(new SimRequestsExport($requests), $filename);
        } catch (\Exception $e) {
            \Log::error('Export Excel error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    private function exportPdf($requests)
    {
        $pdf = Pdf::loadView('sim-requests.export-pdf', compact('requests'));
        return $pdf->download('demandes_sim_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Formulaire de création - différencie user (récupération) et validator (autres types)
     */
    public function create()
    {
        $user = auth()->user();
        $sims = Sim::libre()->get();
        $plans = Plan::active()->get();
        $users = User::where('active', true)->get();
        
        // Récupérer la liste des fonctions disponibles
        $fonctions = $this->getFonctionsList();

        // User peut seulement créer une demande de récupération
        if (!$user->isValidator()) {
            // Récupérer la SIM actuelle de l'user s'il en a une
            $currentSim = Sim::where('assigned_to', $user->id)
                ->whereIn('status', ['attribue', 'suspendu'])
                ->first();
            
            return view('sim-requests.create-recuperation', compact('sims', 'currentSim'));
        }

        // Validator peut créer tous les types sauf récupération
        return view('sim-requests.create-validator', compact('sims', 'plans', 'users', 'fonctions'));
    }
    
    /**
     * Récupère la liste des fonctions disponibles
     */
    private function getFonctionsList(): array
    {
        // Liste des fonctions officielles
        $fonctions = [
            'DIRECTEUR GENERAL',
            'DIRECTEUR GENERAL ADJOINT',
            'DIRECTEUR SYSTÈME D\'INFORMATION',
            'DIRECTEUR AUDIT INTERNE',
            'CODIR',
            'DIRECTEUR DE RESEAUX D\'AGENCE',
            'RESPONSABLES',
            'CHARGES',
            'CHARGE DE MISSION AUPRES DCOM',
            'ASSISTANTS',
            'CHEF D\'AGENCE',
            'SUPERVISEUR CLIENTELE',
            'JURISTE',
            'CHARGE DE PRÊT',
            'CHEF DE CAISSE',
            'CONSEILLER CLIENTELE',
            'CHARGE BACK OFFICE',
            'AGENT POLYVALENT',
            'SECRETAIRE JURISTE',
            'AUDITEUR INTERNE SANS DATA',
            'AUDITEUR INTERNE AVEC DATA',
            'CONTROLEUR OPERATIONNEL SANS DATA',
            'CONTROLEUR OPERATIONNEL AVEC DATA',
            'CHEF DE PROJET',
            'BRAND MANAGER',
            'GESTIONNAIRE BACK OFFICE',
            'GESTIONNAIRE RELATION CLIENT',
            'COURSIER',
            'TRESORIER PRINCIPAL',
            'AGENT DE SECURITE',
            'ARCHIVISTE',
            'CHEF COMPTABLE',
            'ANALYSTE RISQUE',
            'ANIMATEUR RESEAU',
            'TECHNICIEN MAINTENANCE',
            'ACHETEUR SENIOR',
            'BUSINESS ANALYST',
            'CONSULTANT FINANCE RURAL',
            'CONSULTANT METIER OPERATIONNEL',
            'CONSULTANT EN SURETE ET SECURITE',
            'PUCE DATA DES CC',
        ];
        
        // Récupérer les fonctions existantes dans la base de données (pour compléter)
        $fonctionsFromDb = User::whereNotNull('fonction')
            ->where('fonction', '!=', '')
            ->distinct()
            ->pluck('fonction')
            ->filter()
            ->map(function ($fonction) {
                return strtoupper(trim($fonction));
            })
            ->reject(function ($fonction) use ($fonctions) {
                return in_array($fonction, $fonctions);
            })
            ->values()
            ->toArray();
        
        // Fusionner et supprimer les doublons
        $allFonctions = array_unique(array_merge($fonctions, $fonctionsFromDb));
        sort($allFonctions);
        
        // Créer un tableau associatif pour le select
        return array_combine($allFonctions, $allFonctions);
    }

    /**
     * Stockage de la demande - logique différente selon le type
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $requestType = $request->input('request_type');

        // Validation selon le type de demande
        if ($requestType === 'recuperation') {
            // Seul un user peut créer une récupération
            if ($user->isValidator()) {
                return back()->with('error', 'Seuls les utilisateurs peuvent créer une demande de récupération.');
            }
            
            $validated = $this->validateRecuperation($request);
            $simRequest = $this->createRecuperationRequest($validated, $user);
            
        } else {
            // Seul un validator peut créer les autres types
            if (!$user->isValidator()) {
                return back()->with('error', 'Seuls les validateurs peuvent créer ce type de demande.');
            }

            switch ($requestType) {
                case 'creation':
                    $validated = $this->validateCreation($request);
                    $simRequest = $this->createCreationRequest($validated, $user);
                    break;
                case 'suspension':
                case 'desactivation':
                    $validated = $this->validateSuspensionDesactivation($request);
                    $simRequest = $this->createSuspensionDesactivationRequest($validated, $user, $requestType);
                    break;
                case 'ajustement':
                    $validated = $this->validateAjustement($request);
                    $simRequest = $this->createAjustementRequest($validated, $user);
                    break;
                default:
                    return back()->with('error', 'Type de demande invalide.');
            }
        }

        if ($simRequest) {
            // Envoyer email aux validateurs seulement pour récupération
            if ($simRequest->isRecuperation()) {
                $validators = User::whereIn('role', ['admin', 'validator'])->where('active', true)->get();
                foreach ($validators as $validator) {
                    Mail::to($validator->email)->queue(new NewRequestNotification($simRequest));
                    // Envoyer notification in-app
                    $validator->notify(new \App\Notifications\RequestCreated($simRequest));
                }
            }

            return redirect()->route('sim-requests.show', $simRequest)
                ->with('success', 'Demande créée avec succès.');
        }

        return back()->withInput()->with('error', 'Erreur lors de la création de la demande.');
    }

    public function show(SimRequest $simRequest)
    {
        $user = auth()->user();
        
        // Vérifier la visibilité
        // Un validateur peut voir toutes les demandes
        // Un simple utilisateur peut voir ses propres demandes (user_id)
        // Un validateur qui a créé une demande peut la voir (created_by)
        if (!$user->isValidator() && $simRequest->user_id !== $user->id && $simRequest->created_by !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette demande.');
        }

        // Charger les relations nécessaires, y compris les historiques
        $simRequest->load(['user', 'sim', 'validator', 'creator', 'admin', 'plan', 'histories.user', 'favoritedBy']);
        $isFavorite = $user->favorites()->where('sim_request_id', $simRequest->id)->exists();
        return view('sim-requests.show', compact('simRequest', 'isFavorite'));
    }

    /**
     * Afficher le formulaire d'édition d'une demande
     */
    public function edit(SimRequest $simRequest)
    {
        $user = auth()->user();
        
        // Vérifier les permissions
        // Un utilisateur peut modifier ses propres demandes seulement si elles sont en attente
        // Un validateur peut modifier les demandes qu'il a créées ou toutes les demandes en attente
        $canEdit = false;
        
        if ($user->isValidator()) {
            // Validateur peut modifier s'il a créé la demande OU si la demande est en attente
            $canEdit = ($simRequest->created_by === $user->id) || ($simRequest->status === 'en_attente');
        } else {
            // Utilisateur peut modifier seulement ses propres demandes en attente
            $canEdit = ($simRequest->user_id === $user->id) && ($simRequest->status === 'en_attente');
        }
        
        if (!$canEdit) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }
        
        // Vérifier que la demande n'a pas été envoyée au webhook
        if ($simRequest->status === 'demande_envoyee') {
            return back()->with('error', 'Les demandes déjà envoyées au webhook ne peuvent plus être modifiées.');
        }
        
        // Charger les données nécessaires
        $sims = Sim::libre()->get();
        $plans = Plan::active()->get();
        $users = User::where('active', true)->get();
        $fonctions = $this->getFonctionsList();
        
        // Déterminer quelle vue utiliser selon le type de demande
        if ($simRequest->request_type === 'recuperation') {
            $currentSim = Sim::where('assigned_to', $user->id)
                ->whereIn('status', ['attribue', 'suspendu'])
                ->first();
            return view('sim-requests.edit-recuperation', compact('simRequest', 'sims', 'currentSim'));
        } else {
            return view('sim-requests.edit-validator', compact('simRequest', 'sims', 'plans', 'users', 'fonctions'));
        }
    }

    /**
     * Mettre à jour une demande
     */
    public function update(Request $request, SimRequest $simRequest)
    {
        $user = auth()->user();
        
        // Vérifier les permissions (même logique que edit)
        $canEdit = false;
        
        if ($user->isValidator()) {
            $canEdit = ($simRequest->created_by === $user->id) || ($simRequest->status === 'en_attente');
        } else {
            $canEdit = ($simRequest->user_id === $user->id) && ($simRequest->status === 'en_attente');
        }
        
        if (!$canEdit) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }
        
        // Vérifier que la demande n'a pas été envoyée au webhook
        if ($simRequest->status === 'demande_envoyee') {
            return back()->with('error', 'Les demandes déjà envoyées au webhook ne peuvent plus être modifiées.');
        }
        
        $requestType = $simRequest->request_type;
        
        DB::beginTransaction();
        try {
            // Sauvegarder les anciennes valeurs pour l'historique
            $oldData = $simRequest->toArray();
            
            // Validation et mise à jour selon le type
            if ($requestType === 'recuperation') {
                $validated = $this->validateRecuperation($request);
                $simRequest->update([
                    'sim_id' => $validated['sim_id'] ?? null,
                    'requested_iccid' => $validated['requested_iccid'] ?? null,
                    'motif' => $validated['motif'],
                    'updated_by' => $user->id,
                ]);
            } else {
                switch ($requestType) {
                    case 'creation':
                        $validated = $this->validateCreation($request);
                        $simRequest->update([
                            'beneficiary_name' => $validated['beneficiary_name'],
                            'beneficiary_first_name' => $validated['beneficiary_first_name'] ?? null,
                            'beneficiary_fonction' => $validated['beneficiary_fonction'] ?? null,
                            'beneficiary_matricule' => $validated['beneficiary_matricule'] ?? null,
                            'plan_id' => $validated['plan_id'],
                            'sim_id' => $validated['sim_id'] ?? null,
                            'requested_iccid' => $validated['requested_iccid'] ?? null,
                            'motif' => $validated['motif'],
                            'updated_by' => $user->id,
                        ]);
                        break;
                    case 'suspension':
                    case 'desactivation':
                        $validated = $this->validateSuspensionDesactivation($request);
                        $simRequest->update([
                            'phone_number' => $validated['phone_number'],
                            'motif' => $validated['motif'],
                            'updated_by' => $user->id,
                        ]);
                        break;
                    case 'ajustement':
                        $validated = $this->validateAjustement($request);
                        $simRequest->update([
                            'phone_number' => $validated['phone_number'],
                            'plan_id' => $validated['plan_id'],
                            'updated_by' => $user->id,
                        ]);
                        break;
                }
            }
            
            // Créer un historique pour la modification
            $this->createRequestHistory($simRequest, 'updated', [
                'old_data' => $oldData,
                'new_data' => $simRequest->fresh()->toArray(),
                'updated_by' => $user->id,
            ]);
            
            logActivity('update_request', "Demande modifiée: {$simRequest->request_number}", 'sim_requests', $simRequest->id);
            
            DB::commit();
            return redirect()->route('sim-requests.show', $simRequest)
                ->with('success', 'Demande modifiée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    /**
     * Validation d'une demande de récupération (seule demande qui nécessite validation)
     */
    public function approve(Request $request, SimRequest $simRequest)
    {
        if (!$simRequest->isRecuperation()) {
            return back()->with('error', 'Seules les demandes de récupération peuvent être validées.');
        }

        // Vérifier que le validateur ne valide pas sa propre demande
        // Un validateur ne peut valider que les demandes des simples utilisateurs
        if ($simRequest->created_by === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas valider votre propre demande.');
        }

        $this->authorize('validateRequest', $simRequest);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $simRequest->update([
                'status' => 'validee',
                'validator_id' => auth()->id(),
                'validated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            // Récupérer la SIM associée si disponible
            $sim = null;
            if ($simRequest->sim_id) {
                $sim = Sim::find($simRequest->sim_id);
            } elseif ($simRequest->phone_number) {
                // Chercher la SIM par numéro de téléphone
                $sim = Sim::where('phone_number', $simRequest->phone_number)->first();
            }
            
            // Assigner la SIM si disponible et libre
            if ($sim && $sim->isLibre()) {
                $sim->update([
                    'status' => 'attribue',
                    'assigned_to' => $simRequest->user_id,
                    'assigned_to_matricule' => $simRequest->user->matricule,
                    'assigned_at' => now(),
                ]);

                $sim->histories()->create([
                    'action' => 'assigned',
                    'user_id' => auth()->id(),
                    'user_matricule' => auth()->user()->matricule,
                    'request_id' => $simRequest->id,
                    'new_data' => $sim->toArray(),
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
            
            // Envoyer les informations de validation au webhook externe
            // (même si la SIM n'est pas trouvée, on envoie quand même la demande)
            $response = $this->sendRequestToWebhook($simRequest, $sim);
            
            // Si l'envoi au webhook est réussi, mettre à jour le statut à "demande envoyée"
            if ($response && $response->successful()) {
                $simRequest->update([
                    'status' => 'demande_envoyee',
                    // admin_id reste null car c'est le validateur qui valide, pas l'admin
                    'admin_processed_at' => now(),
                ]);
            }

            // Créer un historique pour la validation
            $this->createRequestHistory($simRequest, 'validated', [
                'old_status' => 'en_attente',
                'new_status' => 'validee',
                'validator_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            logActivity('approve_request', "Demande approuvée: {$simRequest->request_number}", 'sim_requests', $simRequest->id);

            Mail::to($simRequest->user->email)->queue(new RequestValidatedNotification($simRequest));
            
            // Envoyer notification in-app au demandeur
            $simRequest->user->notify(new \App\Notifications\RequestValidated($simRequest));

            DB::commit();
            return redirect()->route('sim-requests.show', $simRequest)
                ->with('success', 'Demande approuvée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SimRequest $simRequest)
    {
        if (!$simRequest->isRecuperation()) {
            return back()->with('error', 'Seules les demandes de récupération peuvent être rejetées.');
        }

        // Vérifier que le validateur ne rejette pas sa propre demande
        // Un validateur ne peut valider/rejeter que les demandes des simples utilisateurs
        if ($simRequest->created_by === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas rejeter votre propre demande.');
        }

        $this->authorize('rejectRequest', $simRequest);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $simRequest->update([
                'status' => 'rejetee',
                'validator_id' => auth()->id(),
                'validated_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'updated_by' => auth()->id(),
            ]);

            // Créer un historique pour le rejet
            $this->createRequestHistory($simRequest, 'rejected', [
                'old_status' => 'en_attente',
                'new_status' => 'rejetee',
                'validator_id' => auth()->id(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            logActivity('reject_request', "Demande rejetée: {$simRequest->request_number}", 'sim_requests', $simRequest->id, ['reason' => $validated['rejection_reason']]);

            Mail::to($simRequest->user->email)->queue(new RequestRejectedNotification($simRequest));
            
            // Envoyer notification in-app au demandeur
            $simRequest->user->notify(new \App\Notifications\RequestRejected($simRequest));

            DB::commit();
            return redirect()->route('sim-requests.show', $simRequest)
                ->with('success', 'Demande rejetée.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    /**
     * Raccourci pour mettre à jour rapidement le statut opérateur
     */
    public function quickUpdateStatus(Request $request, SimRequest $simRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent effectuer cette action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,refused,accepted',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $simRequest->status;
            $simRequest->update([
                'status' => $validated['status'],
                'admin_id' => auth()->id(),
                'admin_processed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            $statusLabels = [
                'pending' => 'Pending (En attente)',
                'accepted' => 'Accepted (Accepté)',
                'refused' => 'Refused (Refusé)',
            ];

            // Créer un historique pour le changement de statut
            if ($oldStatus !== $validated['status']) {
                $this->createRequestHistory($simRequest, 'status_updated', [
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'admin_id' => auth()->id(),
                ]);
            }

            logActivity('quick_status_update', "Statut opérateur mis à jour rapidement: {$simRequest->request_number} -> {$validated['status']}", 'sim_requests', $simRequest->id);
            
            // Envoyer notification in-app au demandeur si le statut a changé
            if ($oldStatus !== $validated['status']) {
                $simRequest->user->notify(new \App\Notifications\RequestStatusChanged($simRequest, $oldStatus, $validated['status']));
            }

            DB::commit();
            return redirect()->route('sim-requests.show', $simRequest)
                ->with('success', 'Statut opérateur mis à jour: ' . $statusLabels[$validated['status']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Actions admin : commenter et changer le statut (pending/refused/accepted)
     */
    public function adminAction(Request $request, SimRequest $simRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent effectuer cette action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,refused,accepted',
            'admin_comment' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $simRequest->update([
                'status' => $validated['status'],
                'admin_comment' => $validated['admin_comment'] ?? null,
                'admin_id' => auth()->id(),
                'admin_processed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            // Créer un historique pour l'action admin
            $oldStatus = $simRequest->getOriginal('status');
            if ($oldStatus !== $validated['status']) {
                $this->createRequestHistory($simRequest, 'status_updated', [
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'admin_id' => auth()->id(),
                    'admin_comment' => $validated['admin_comment'] ?? null,
                ]);
            }

            logActivity('admin_action_request', "Action admin sur demande: {$simRequest->request_number} - {$validated['status']}", 'sim_requests', $simRequest->id);

            DB::commit();
            return redirect()->route('sim-requests.show', $simRequest)
                ->with('success', 'Statut mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Annulation d'une demande par le demandeur
     */
    public function cancel(SimRequest $simRequest)
    {
        $user = auth()->user();

        // Vérifier que c'est bien le demandeur qui annule
        if ($simRequest->user_id !== $user->id && $simRequest->created_by !== $user->id) {
            abort(403, 'Vous ne pouvez annuler que vos propres demandes.');
        }

        // Vérifier que la demande peut être annulée (seulement si en attente)
        if (!$simRequest->isEnAttente() && !$simRequest->isPending()) {
            return back()->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        DB::beginTransaction();
        try {
            $requestNumber = $simRequest->request_number;
            
            // Créer un historique avant la suppression
            $this->createRequestHistory($simRequest, 'cancelled', [
                'old_status' => $simRequest->status,
                'cancelled_by' => auth()->id(),
            ]);

            logActivity('cancel_request', "Demande annulée: {$requestNumber}", 'sim_requests', $simRequest->id);

            // Supprimer la demande
            $simRequest->delete();

            DB::commit();
            return redirect()->route('sim-requests.index')
                ->with('success', 'Demande annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'une demande par le validateur
     */
    public function destroy(SimRequest $simRequest)
    {
        $user = auth()->user();

        // Vérifier que c'est un validateur
        if (!$user->isValidator()) {
            abort(403, 'Seuls les validateurs peuvent supprimer une demande.');
        }

        DB::beginTransaction();
        try {
            $requestNumber = $simRequest->request_number;
            
            logActivity('delete_request', "Demande supprimée par validateur: {$requestNumber}", 'sim_requests', $simRequest->id);

            // Supprimer la demande
            $simRequest->delete();

            DB::commit();
            return redirect()->route('sim-requests.index')
                ->with('success', 'Demande supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // ========== Méthodes privées de validation ==========

    private function validateRecuperation(Request $request)
    {
        return $request->validate([
            'sim_id' => 'nullable|exists:sims,id',
            'requested_iccid' => 'nullable|string|max:255',
            'motif' => 'required|string|max:500',
        ]);
    }

    private function validateCreation(Request $request)
    {
        return $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'beneficiary_name' => 'required|string|max:255',
            'beneficiary_first_name' => 'nullable|string|max:255',
            'beneficiary_fonction' => 'nullable|string|max:255',
            'beneficiary_matricule' => 'nullable|string|max:255',
            'sim_id' => 'nullable|exists:sims,id',
            'requested_iccid' => 'nullable|string|max:255',
            'motif' => 'required|string|max:500',
        ]);
    }

    private function validateSuspensionDesactivation(Request $request)
    {
        return $request->validate([
            'phone_number' => 'required|string|max:255',
            'motif' => 'required|string|max:500',
        ]);
    }

    private function validateAjustement(Request $request)
    {
        return $request->validate([
            'phone_number' => 'required|string|max:255',
            'plan_id' => 'required|exists:plans,id',
        ]);
    }

    // ========== Méthodes privées de création ==========

    private function createRecuperationRequest(array $validated, User $user)
    {
        DB::beginTransaction();
        try {
            // Récupérer automatiquement le numéro de l'user s'il a une SIM
            $currentSim = Sim::where('assigned_to', $user->id)
                ->whereIn('status', ['attribue', 'suspendu'])
                ->first();

            $simRequest = SimRequest::create([
                'request_number' => SimRequest::generateRequestNumber(),
                'user_id' => $user->id,
                'sim_id' => $validated['sim_id'] ?? ($currentSim ? $currentSim->id : null),
                'requested_iccid' => $validated['requested_iccid'] ?? null,
                'phone_number' => $currentSim ? $currentSim->phone_number : null,
                'request_type' => 'recuperation',
                'motif' => $validated['motif'],
                'status' => 'en_attente',
                'created_by' => $user->id,
            ]);

            // Créer un historique pour la création
            $this->createRequestHistory($simRequest, 'created', [
                'status' => 'en_attente',
                'created_by' => $user->id,
            ]);

            logActivity('create_request', "Nouvelle demande de récupération: {$simRequest->request_number}", 'sim_requests', $simRequest->id, $validated);

            DB::commit();
            return $simRequest;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    private function createCreationRequest(array $validated, User $user)
    {
        DB::beginTransaction();
        try {
            $plan = Plan::find($validated['plan_id']);

            $simRequest = SimRequest::create([
                'request_number' => SimRequest::generateRequestNumber(),
                'user_id' => $user->id, // Le validator qui crée
                'sim_id' => $validated['sim_id'] ?? null,
                'requested_iccid' => $validated['requested_iccid'] ?? null,
                'request_type' => 'creation',
                'plan_id' => $validated['plan_id'],
                'limite_credit' => $plan->limite_credit,
                'limite_data' => $plan->limite_data,
                'beneficiary_name' => $validated['beneficiary_name'],
                'beneficiary_first_name' => $validated['beneficiary_first_name'] ?? null,
                'beneficiary_fonction' => $validated['beneficiary_fonction'] ?? null,
                'beneficiary_matricule' => $validated['beneficiary_matricule'] ?? null,
                'motif' => $validated['motif'],
                'status' => 'pending', // Directement en attente chez l'opérateur
                'created_by' => $user->id,
            ]);

            // Créer un historique pour la création
            $this->createRequestHistory($simRequest, 'created', [
                'status' => 'pending',
                'created_by' => $user->id,
                'request_type' => 'creation',
            ]);

            logActivity('create_request', "Nouvelle demande de création: {$simRequest->request_number}", 'sim_requests', $simRequest->id, $validated);

            DB::commit();
            return $simRequest;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    private function createSuspensionDesactivationRequest(array $validated, User $user, string $type)
    {
        DB::beginTransaction();
        try {
            // Trouver la SIM par numéro de téléphone
            $sim = Sim::where('phone_number', $validated['phone_number'])->first();

            $simRequest = SimRequest::create([
                'request_number' => SimRequest::generateRequestNumber(),
                'user_id' => $sim ? $sim->assigned_to : $user->id,
                'sim_id' => $sim ? $sim->id : null,
                'phone_number' => $validated['phone_number'],
                'request_type' => $type,
                'motif' => $validated['motif'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            // Créer un historique pour la création
            $this->createRequestHistory($simRequest, 'created', [
                'status' => 'pending',
                'created_by' => $user->id,
                'request_type' => $type,
            ]);

            logActivity('create_request', "Nouvelle demande de {$type}: {$simRequest->request_number}", 'sim_requests', $simRequest->id, $validated);

            DB::commit();
            return $simRequest;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    private function createAjustementRequest(array $validated, User $user)
    {
        DB::beginTransaction();
        try {
            $sim = Sim::where('phone_number', $validated['phone_number'])->first();
            $plan = Plan::find($validated['plan_id']);

            $simRequest = SimRequest::create([
                'request_number' => SimRequest::generateRequestNumber(),
                'user_id' => $sim ? $sim->assigned_to : $user->id,
                'sim_id' => $sim ? $sim->id : null,
                'phone_number' => $validated['phone_number'],
                'request_type' => 'ajustement',
                'plan_id' => $validated['plan_id'],
                'limite_credit' => $plan->limite_credit,
                'limite_data' => $plan->limite_data,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            // Créer un historique pour la création
            $this->createRequestHistory($simRequest, 'created', [
                'status' => 'pending',
                'created_by' => $user->id,
                'request_type' => 'ajustement',
            ]);

            logActivity('create_request', "Nouvelle demande d'ajustement: {$simRequest->request_number}", 'sim_requests', $simRequest->id, $validated);

            DB::commit();
            return $simRequest;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * Envoie les informations de la requête validée au webhook externe
     * 
     * @param SimRequest $simRequest La demande validée
     * @param Sim|null $sim La SIM associée (optionnel)
     * @return \Illuminate\Http\Client\Response|null La réponse HTTP ou null en cas d'erreur
     */
    private function sendRequestToWebhook(SimRequest $simRequest, ?Sim $sim = null)
    {
        try {
            $username = env('N8N_USERNAME');
            $password = env('N8N_PASSWORD');

            if (!$username || !$password) {
                Log::warning('Webhook credentials not configured');
                return null;
            }

            // Charger les relations nécessaires
            $simRequest->load(['creator', 'user', 'validator', 'sim', 'plan']);
            
            // Récupérer les informations du demandeur
            // Pour une demande de récupération, user_id = created_by (l'utilisateur qui fait la demande)
            // Pour les autres demandes, user_id est le bénéficiaire et created_by est le validator
            // On utilise user_id car c'est toujours l'utilisateur pour qui la demande est faite
            $requester = $simRequest->user;
            $createdByMatricule = $requester->matricule ?? '';
            $createdByName = trim(($requester->name ?? '') . ' ' . ($requester->first_name ?? ''));
            $createdByEmail = $requester->email ?? '';
            $createdByNumeroFlotte = $requester->numero_flotte ?? '';
            
            // Déterminer le numéro de téléphone à utiliser
            // Pour les demandes de suspension, désactivation, ajustement : utiliser le numéro de la ligne concernée
            // Sinon : utiliser le numéro personnel du demandeur
            $phoneNumberToUse = '';
            if (in_array($simRequest->request_type, ['suspension', 'desactivation', 'ajustement'])) {
                // Utiliser le numéro de la ligne concernée par la demande
                $phoneNumberToUse = $simRequest->phone_number ?? '';
                if (empty($phoneNumberToUse) && $sim) {
                    $phoneNumberToUse = $sim->phone_number ?? '';
                }
            } else {
                // Pour les autres types (récupération, création), utiliser le numéro personnel du demandeur
                $phoneNumberToUse = $requester->phone ?? '';
            }

            // Construire les paramètres de base de la requête
            $params = [
                'request_type' => $simRequest->request_type,
                'request_number' => $simRequest->request_number,
                'request_date' => $simRequest->created_at?->toDateTimeString() ?? '',
                'request_by_matricule' => $createdByMatricule,
                'request_by_name' => $createdByName,
                'request_by_email' => $createdByEmail,
                'request_by_phone' => $phoneNumberToUse,
                'request_by_numero_flotte' => $createdByNumeroFlotte,
            ];

            // Ajouter les informations de la SIM si disponible
            if ($sim) {
                $sim->load(['assignedUser']);
                $assignedUser = $sim->assignedUser;
                
                $params['sim_id'] = $sim->id;
                $params['sim_number'] = $sim->phone_number ?? '';
                $params['sim_iccid'] = $sim->iccid ?? '';
                $params['sim_status'] = $sim->status ?? '';
                $params['sim_assigned_to'] = $sim->assigned_to ?? '';
                $params['sim_assigned_to_matricule'] = $sim->assigned_to_matricule ?? ($assignedUser->matricule ?? '');
                $params['sim_assigned_at'] = $sim->assigned_at?->toDateTimeString() ?? '';
                
                // Récupérer les informations de la personne qui a assigné la SIM
                // Si la SIM a été assignée via une demande, récupérer le validator
                if ($simRequest->validator) {
                    $params['sim_assigned_by'] = $simRequest->validator->id ?? '';
                    $params['sim_assigned_by_matricule'] = $simRequest->validator->matricule ?? '';
                    $params['sim_assigned_by_name'] = trim(($simRequest->validator->name ?? '') . ' ' . ($simRequest->validator->first_name ?? ''));
                } else {
                    $params['sim_assigned_by'] = '';
                    $params['sim_assigned_by_matricule'] = '';
                    $params['sim_assigned_by_name'] = '';
                }
            } else {
                // Si pas de SIM, on peut quand même envoyer les infos de la requête
                $params['sim_id'] = $simRequest->sim_id ?? '';
            }

            // Ajouter le numéro de ligne de la demande (important pour suspension, désactivation, ajustement)
            if ($simRequest->phone_number) {
                $params['phone_number'] = $simRequest->phone_number;
            } elseif ($sim && $sim->phone_number) {
                // Si pas de phone_number dans la demande mais qu'on a une SIM, utiliser celui de la SIM
                $params['phone_number'] = $sim->phone_number;
            }

            // Ajouter des paramètres spécifiques selon le type de requête
            switch ($simRequest->request_type) {
                case 'creation':
                case 'ajustement':
                    if ($simRequest->plan) {
                        $params['plan_id'] = $simRequest->plan_id;
                        // Utiliser les valeurs du plan directement (valeurs numériques brutes sans formatage)
                        // Convertir en nombre entier si c'est un entier, sinon garder les décimales
                        $limiteCredit = (float) $simRequest->plan->limite_credit;
                        $limiteData = (float) $simRequest->plan->limite_data;
                        $params['limite_credit'] = $limiteCredit == (int) $limiteCredit ? (int) $limiteCredit : $limiteCredit;
                        $params['limite_data'] = $limiteData == (int) $limiteData ? (int) $limiteData : $limiteData;
                    } elseif ($simRequest->limite_credit || $simRequest->limite_data) {
                        // Si pas de plan mais des limites dans la demande, utiliser celles-ci
                        if ($simRequest->limite_credit) {
                            $limiteCredit = (float) $simRequest->limite_credit;
                            $params['limite_credit'] = $limiteCredit == (int) $limiteCredit ? (int) $limiteCredit : $limiteCredit;
                        } else {
                            $params['limite_credit'] = '';
                        }
                        if ($simRequest->limite_data) {
                            $limiteData = (float) $simRequest->limite_data;
                            $params['limite_data'] = $limiteData == (int) $limiteData ? (int) $limiteData : $limiteData;
                        } else {
                            $params['limite_data'] = '';
                        }
                    }
                    if ($simRequest->request_type === 'creation') {
                        $params['beneficiary_name'] = $simRequest->beneficiary_name ?? '';
                        $params['beneficiary_first_name'] = $simRequest->beneficiary_first_name ?? '';
                        $params['beneficiary_matricule'] = $simRequest->beneficiary_matricule ?? '';
                        $params['beneficiary_fonction'] = $simRequest->beneficiary_fonction ?? '';
                    }
                    break;
                case 'suspension':
                case 'desactivation':
                case 'recuperation':
                    $params['motif'] = $simRequest->motif ?? '';
                    break;
            }

            // Construire l'URL avec les paramètres
            $baseUrl = 'https://acepmg.it4life.org/webhook/get_infos';
            $url = $baseUrl . '?' . http_build_query($params);

            // Log de l'URL complète
            Log::info('Webhook URL', [
                'url' => $url,
                'request_number' => $simRequest->request_number,
                'request_type' => $simRequest->request_type
            ]);

            Log::info('Sending request to webhook', [
                'request_number' => $simRequest->request_number,
                'request_type' => $simRequest->request_type,
                'url' => $url
            ]);

            // Envoyer la requête HTTP
            $response = Http::withBasicAuth($username, $password)
                ->withoutVerifying()
                ->timeout(30)
                ->get($url);

            if ($response->successful()) {
                $responseData = $response->json();
                $responseBody = $response->body();
                
                // Initialiser responseData comme tableau vide si null
                if ($responseData === null) {
                    $responseData = [];
                }
                
                // Si la réponse est un tableau indexé (ex: [0 => [...]], prendre le premier élément
                if (is_array($responseData) && isset($responseData[0]) && is_array($responseData[0])) {
                    $responseData = $responseData[0];
                }
                
                Log::info('Webhook request successful', [
                    'request_number' => $simRequest->request_number,
                    'response_json' => $responseData,
                    'response_body' => $responseBody,
                    'response_body_length' => strlen($responseBody),
                    'response_status' => $response->status()
                ]);

                // Si la réponse n'est pas un JSON valide, essayer de parser le body
                if (empty($responseData) && !empty($responseBody)) {
                    $parsedData = json_decode($responseBody, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Si c'est un tableau indexé, prendre le premier élément
                        if (is_array($parsedData) && isset($parsedData[0]) && is_array($parsedData[0])) {
                            $responseData = $parsedData[0];
                        } else {
                            $responseData = $parsedData;
                        }
                    } else {
                        Log::warning('Webhook response is not valid JSON', [
                            'request_number' => $simRequest->request_number,
                            'body' => $responseBody
                        ]);
                    }
                }

                // Vérifier si la réponse est vide ou ne contient pas de données utiles
                $hasValidData = !empty($responseData) && (
                    isset($responseData['message_subject']) || 
                    isset($responseData['subject']) || 
                    isset($responseData['message_corps']) || 
                    isset($responseData['body']) || 
                    isset($responseData['message'])
                );

                // Sauvegarder le retour du webhook dans mail_sent si le statut est OK
                // Vérifier le statut de différentes manières possibles
                $status = null;
                if (!empty($responseData)) {
                    $status = strtolower(trim($responseData['status'] ?? $responseData['Status'] ?? ''));
                }
                
                // Vérifier si on doit stocker : le statut doit être OK ET la réponse doit contenir des données
                $shouldStore = false;
                if ($status === 'ok' || ($response->status() === 200 && $hasValidData)) {
                    // Si le statut n'est pas explicitement "ok" mais qu'on a des données valides, on peut continuer
                    if ($status !== 'ok' && $hasValidData) {
                        $responseData['status'] = 'ok';
                        $status = 'ok';
                    }
                    $shouldStore = ($status === 'ok' && $hasValidData);
                }
                
                if ($shouldStore && !empty($responseData)) {
                    try {
                        Log::info('Attempting to store mail from webhook', [
                            'request_number' => $simRequest->request_number,
                            'response_data' => $responseData,
                            'http_status' => $response->status()
                        ]);
                        
                        $mailSentController = new \App\Http\Controllers\MailSentController();
                        
                        // Ne prendre que message_subject et message_corps du webhook
                        // Les autres données viennent de la demande
                        $mailData = [
                            // Données du webhook (seulement message_subject et message_corps)
                            'message_subject' => $responseData['message_subject'] ?? $responseData['subject'] ?? null,
                            'message_corps' => $responseData['message_corps'] ?? $responseData['body'] ?? $responseData['message'] ?? null,
                            'thread_id' => $responseData['threadId'] ?? $responseData['thread_id'] ?? null,
                            'label_ids' => $responseData['labelIds'] ?? $responseData['label_ids'] ?? null,
                            // Données de la demande (pas du webhook)
                            'request_id' => $simRequest->id,
                            'request_number' => $simRequest->request_number,
                            'request_type' => $simRequest->request_type,
                            'request_matricule' => $simRequest->user->matricule ?? null,
                            'request_name' => trim(($simRequest->user->name ?? '') . ' ' . ($simRequest->user->first_name ?? '')),
                            'sim_iccid' => $sim ? $sim->iccid : ($simRequest->requested_iccid ?? null),
                            'sim_assign_to' => $sim ? $sim->assigned_to : null,
                            'status' => 'ok', // Forcer le statut à OK pour la sauvegarde
                        ];
                        
                        // Construire un message_subject par défaut si absent du webhook
                        if (empty($mailData['message_subject'])) {
                            $typeLabels = [
                                'recuperation' => 'Récupération',
                                'creation' => 'Création',
                                'suspension' => 'Suspension',
                                'desactivation' => 'Désactivation',
                                'ajustement' => 'Ajustement',
                            ];
                            $typeLabel = $typeLabels[$simRequest->request_type] ?? ucfirst($simRequest->request_type);
                            $mailData['message_subject'] = "Demande {$typeLabel} - {$simRequest->request_number}";
                        }
                        
                        // Vérifier que message_corps n'est pas vide (contenu essentiel du mail)
                        if (empty($mailData['message_corps'])) {
                            Log::warning('Webhook response missing message_corps, mail not stored', [
                                'request_number' => $simRequest->request_number,
                                'response_data' => $responseData,
                                'mail_data' => $mailData
                            ]);
                            throw new \Exception('Le webhook n\'a pas retourné de contenu de message (message_corps). Impossible de stocker le mail sans contenu.');
                        }
                        
                        $storeRequest = new \Illuminate\Http\Request($mailData);
                        $storeResponse = $mailSentController->store($storeRequest);
                        
                        // Vérifier si la réponse indique un succès
                        if (is_object($storeResponse) && method_exists($storeResponse, 'getData')) {
                            $storeData = $storeResponse->getData(true);
                            if (isset($storeData['success']) && $storeData['success']) {
                                Log::info('Mail stored successfully', [
                                    'request_number' => $simRequest->request_number,
                                    'mail_id' => $storeData['mail_id'] ?? 'unknown'
                                ]);
                            } else {
                                Log::warning('Mail store returned false', [
                                    'request_number' => $simRequest->request_number,
                                    'store_response' => $storeData
                                ]);
                            }
                        } else {
                            Log::info('Mail stored (response format unknown)', [
                                'request_number' => $simRequest->request_number
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error storing mail from webhook response', [
                            'request_number' => $simRequest->request_number,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'response_data' => $responseData
                        ]);
                    }
                } else {
                    // Déterminer la raison pour laquelle le mail n'est pas stocké
                    $reason = 'unknown';
                    if (empty($responseData)) {
                        $reason = 'empty_response';
                    } elseif (!$hasValidData) {
                        $reason = 'no_valid_data';
                    } elseif ($status !== 'ok') {
                        $reason = 'status_not_ok';
                    }
                    
                    Log::warning('Webhook response not suitable for storage, mail not stored', [
                        'request_number' => $simRequest->request_number,
                        'response_data' => $responseData,
                        'response_body' => $responseBody,
                        'status' => $status ?? 'not set',
                        'http_status' => $response->status(),
                        'should_store' => $shouldStore,
                        'has_valid_data' => $hasValidData ?? false,
                        'reason' => $reason
                    ]);
                }
            } else {
                Log::error('Webhook request failed', [
                    'request_number' => $simRequest->request_number,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Error sending request to webhook', [
                'request_number' => $simRequest->request_number ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Soumet la demande au webhook externe
     * Seul l'admin peut envoyer une demande au webhook
     */
    public function submitToWebhook(SimRequest $simRequest)
    {
        $user = auth()->user();

        // Vérifier que c'est un admin
        if (!$user->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent soumettre une demande au webhook.');
        }

        // Pour les demandes de récupération, permettre l'envoi manuel seulement si elles sont validées
        // (elles sont normalement envoyées automatiquement après validation, mais l'admin peut les réenvoyer si nécessaire)
        if ($simRequest->isRecuperation() && !$simRequest->isValidee()) {
            return back()->with('error', 'Les demandes de récupération doivent être validées avant d\'être envoyées au webhook.');
        }

        // Vérifier que la demande est dans un statut valide pour être soumise
        if ($simRequest->isRejetee()) {
            return back()->with('error', 'Les demandes rejetées ne peuvent pas être soumises au webhook.');
        }

        DB::beginTransaction();
        try {
            // Récupérer la SIM associée si disponible
            $sim = null;
            if ($simRequest->sim_id) {
                $sim = Sim::find($simRequest->sim_id);
            } elseif ($simRequest->phone_number) {
                // Chercher la SIM par numéro de téléphone
                $sim = Sim::where('phone_number', $simRequest->phone_number)->first();
            }

            // Envoyer la demande au webhook
            $response = $this->sendRequestToWebhook($simRequest, $sim);

            if ($response && $response->successful()) {
                // Mettre à jour le statut de la demande à "demande envoyée"
                $oldStatus = $simRequest->status;
                $simRequest->update([
                    'status' => 'demande_envoyee',
                    'admin_id' => auth()->id(),
                    'admin_processed_at' => now(),
                    'updated_by' => auth()->id(),
                ]);

                // Créer un historique pour l'envoi au webhook
                $this->createRequestHistory($simRequest, 'submitted_to_webhook', [
                    'old_status' => $oldStatus,
                    'new_status' => 'demande_envoyee',
                    'admin_id' => auth()->id(),
                ]);

                logActivity('submit_to_webhook', "Demande soumise au webhook: {$simRequest->request_number}", 'sim_requests', $simRequest->id);

                // Envoyer un email de notification (similaire à la récupération)
                // Note: Vous pouvez créer un nouveau mail RequestSubmittedNotification si nécessaire
                // Pour l'instant, on utilise RequestValidatedNotification
                if ($simRequest->user && $simRequest->user->email) {
                    Mail::to($simRequest->user->email)->queue(new RequestValidatedNotification($simRequest));
                }

                DB::commit();
                return redirect()->route('sim-requests.show', $simRequest)
                    ->with('success', 'Demande soumise au webhook avec succès.');
            } else {
                DB::rollBack();
                return back()->with('error', 'Erreur lors de l\'envoi au webhook. Veuillez réessayer.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting request to webhook', [
                'request_number' => $simRequest->request_number ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de la soumission: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le bordereau de transmission
     */
    public function generateBordereau(SimRequest $simRequest)
    {
        // Vérifier que c'est un admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent accéder au bordereau de transmission.');
        }

        // Vérifier que la demande est acceptée
        if ($simRequest->status !== 'accepted') {
            return back()->with('error', 'Le bordereau de transmission ne peut être généré que pour les demandes acceptées.');
        }

        // Charger les relations nécessaires
        $simRequest->load(['user', 'sim', 'validator', 'plan', 'admin', 'creator']);

        // Afficher directement la vue HTML
        return view('sim-requests.bordereau', compact('simRequest'));
    }

    /**
     * Actions en masse
     */
    public function bulkApprove(Request $request)
    {
        if (!auth()->user()->isValidator()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sim_requests,id',
        ]);

        $count = 0;
        DB::beginTransaction();
        try {
            $requests = SimRequest::whereIn('id', $validated['ids'])
                ->where('status', 'en_attente')
                ->where('request_type', 'recuperation')
                ->get();

            foreach ($requests as $simRequest) {
                // Vérifier que le validateur ne valide pas sa propre demande
                if ($simRequest->created_by === auth()->id()) {
                    continue;
                }

                $sim = null;
                if ($simRequest->sim_id) {
                    $sim = Sim::find($simRequest->sim_id);
                } elseif ($simRequest->phone_number) {
                    $sim = Sim::where('phone_number', $simRequest->phone_number)->first();
                }

                $simRequest->update([
                    'status' => 'validee',
                    'validator_id' => auth()->id(),
                    'validated_at' => now(),
                    'updated_by' => auth()->id(),
                ]);

                if ($sim && $sim->isLibre()) {
                    $sim->update([
                        'status' => 'attribue',
                        'assigned_to' => $simRequest->user_id,
                        'assigned_to_matricule' => $simRequest->user->matricule,
                        'assigned_at' => now(),
                    ]);
                }

                $this->sendRequestToWebhook($simRequest, $sim);
                $simRequest->user->notify(new \App\Notifications\RequestValidated($simRequest));
                $count++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "{$count} demande(s) validée(s) avec succès."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkReject(Request $request)
    {
        if (!auth()->user()->isValidator()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sim_requests,id',
            'rejection_reason' => 'required|string|max:500',
        ]);

        $count = 0;
        DB::beginTransaction();
        try {
            $requests = SimRequest::whereIn('id', $validated['ids'])
                ->where('status', 'en_attente')
                ->where('request_type', 'recuperation')
                ->get();

            foreach ($requests as $simRequest) {
                if ($simRequest->created_by === auth()->id()) {
                    continue;
                }

                $simRequest->update([
                    'status' => 'rejetee',
                    'validator_id' => auth()->id(),
                    'validated_at' => now(),
                    'rejection_reason' => $validated['rejection_reason'],
                    'updated_by' => auth()->id(),
                ]);

                $simRequest->user->notify(new \App\Notifications\RequestRejected($simRequest));
                $count++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "{$count} demande(s) rejetée(s) avec succès."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sim_requests,id',
            'status' => 'required|in:pending,accepted,refused',
        ]);

        $count = 0;
        DB::beginTransaction();
        try {
            $requests = SimRequest::whereIn('id', $validated['ids'])->get();

            foreach ($requests as $simRequest) {
                $oldStatus = $simRequest->status;
                $simRequest->update([
                    'status' => $validated['status'],
                    'admin_id' => auth()->id(),
                    'admin_processed_at' => now(),
                    'updated_by' => auth()->id(),
                ]);

                if ($oldStatus !== $validated['status']) {
                    $simRequest->user->notify(new \App\Notifications\RequestStatusChanged($simRequest, $oldStatus, $validated['status']));
                }
                $count++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Statut de {$count} demande(s) mis à jour avec succès."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleFavorite(Request $request, SimRequest $simRequest)
    {
        $user = auth()->user();
        
        if ($user->favorites()->where('sim_request_id', $simRequest->id)->exists()) {
            $user->favorites()->detach($simRequest->id);
            $message = 'Demande retirée des favoris';
            $isFavorite = false;
        } else {
            $user->favorites()->attach($simRequest->id);
            $message = 'Demande ajoutée aux favoris';
            $isFavorite = true;
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => $isFavorite
            ]);
        }
        
        return back()->with('success', $message);
    }

    public function bulkDelete(Request $request)
    {
        if (!auth()->user()->canValidateRequests()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sim_requests,id',
        ]);

        $count = SimRequest::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} demande(s) supprimée(s) avec succès."
        ]);
    }

    /**
     * Helper method pour créer un historique de demande
     */
    private function createRequestHistory(SimRequest $simRequest, string $action, array $data = []): void
    {
        $oldData = $simRequest->getOriginal() ?: $simRequest->toArray();
        $newData = array_merge($simRequest->toArray(), $data);

        // Créer l'historique directement avec sim_id nullable
        SimHistory::create([
            'sim_id' => $simRequest->sim_id,
            'action' => $action,
            'user_id' => auth()->id(),
            'user_matricule' => auth()->user()->matricule,
            'request_id' => $simRequest->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'notes' => $data['notes'] ?? $data['rejection_reason'] ?? $data['admin_comment'] ?? null,
        ]);
    }
}
