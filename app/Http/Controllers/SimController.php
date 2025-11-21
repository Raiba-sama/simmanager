<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\User;
use App\Exports\SimsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Sim::with('assignedUser')->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('iccid')) {
            $query->where('iccid', 'like', '%' . $request->iccid . '%');
        }

        $sims = $query->paginate(20);
        return view('sims.index', compact('sims'));
    }

    public function export(Request $request)
    {
        $query = Sim::with('assignedUser')->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('iccid')) {
            $query->where('iccid', 'like', '%' . $request->iccid . '%');
        }

        // Export des éléments sélectionnés si des IDs sont fournis
        if ($request->has('ids') && is_array($request->ids)) {
            $query->whereIn('id', $request->ids);
        }

        $sims = $query->get();
        
        $format = $request->get('format', 'excel');
        $filename = 'sims_' . date('Y-m-d_His') . '.' . ($format === 'pdf' ? 'pdf' : 'xlsx');

        if ($format === 'pdf') {
            return $this->exportPdf($sims);
        }

        try {
            return Excel::download(new SimsExport($sims), $filename);
        } catch (\Exception $e) {
            \Log::error('Export Excel error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    private function exportPdf($sims)
    {
        $pdf = Pdf::loadView('sims.export-pdf', compact('sims'));
        return $pdf->download('sims_' . date('Y-m-d_His') . '.pdf');
    }

    public function show(Sim $sim)
    {
        $sim->load(['assignedUser', 'requests', 'histories.user']);
        return view('sims.show', compact('sim'));
    }

    /**
     * Mettre à jour le statut d'une SIM
     */
    public function updateStatus(Request $request, Sim $sim)
    {
        $validated = $request->validate([
            'status' => 'required|in:libre,attribue,suspendu,defectueuse',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = $sim->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return back()->with('error', 'Le statut est déjà défini sur ' . $newStatus);
        }

        DB::beginTransaction();
        try {
            $oldData = $sim->toArray();
            
            // Si on passe à "libre", désassigner la SIM
            if ($newStatus === 'libre') {
                $sim->update([
                    'status' => $newStatus,
                    'assigned_to' => null,
                    'assigned_to_matricule' => null,
                    'assigned_at' => null,
                ]);
            } else {
                $sim->update([
                    'status' => $newStatus,
                ]);
            }

            // Enregistrer dans l'historique
            $sim->histories()->create([
                'action' => 'status_changed',
                'user_id' => auth()->id(),
                'user_matricule' => auth()->user()->matricule,
                'old_data' => array_merge($oldData, ['old_status' => $oldStatus]),
                'new_data' => array_merge($sim->toArray(), ['new_status' => $newStatus, 'reason' => $validated['reason'] ?? null]),
            ]);

            DB::commit();

            $statusLabels = [
                'libre' => 'libre',
                'attribue' => 'attribuée',
                'suspendu' => 'suspendue',
                'defectueuse' => 'défectueuse',
            ];

            return back()->with('success', "Le statut de la SIM a été changé de '{$statusLabels[$oldStatus]}' à '{$statusLabels[$newStatus]}'.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour du statut: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'iccid' => 'required|string|unique:sims,iccid',
            'phone_number' => 'nullable|string',
            'operator' => 'nullable|string',
            'plan_type' => 'nullable|string',
            'monthly_cost' => 'nullable|numeric|min:0',
        ]);

        $sim = Sim::create($validated);
        logModelAction($sim, 'create');

        return redirect()->route('sims.show', $sim)
            ->with('success', 'SIM créée avec succès.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'nullable|file|mimes:csv,txt|max:2048',
            'iccid_list' => 'nullable|string|max:10000',
        ]);

        $imported = 0;
        $errors = [];
        $iccidList = [];

        // Si un fichier CSV est fourni
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $index => $line) {
                if ($index === 0) continue; // Skip header si présent
                
                $data = str_getcsv($line);
                if (empty($data[0])) continue;

                $iccid = trim($data[0]);
                if (!empty($iccid)) {
                    $iccidList[] = [
                        'iccid' => $iccid,
                        'phone_number' => $data[1] ?? null,
                        'operator' => $data[2] ?? null,
                        'plan_type' => $data[3] ?? null,
                        'monthly_cost' => isset($data[4]) ? (float) $data[4] : null,
                    ];
                }
            }
        } 
        // Si une liste d'ICCID est fournie (texte)
        elseif ($request->has('iccid_list') && !empty($request->iccid_list)) {
            $lines = preg_split('/[\r\n,;]+/', $request->iccid_list);
            
            foreach ($lines as $line) {
                $iccid = trim($line);
                if (!empty($iccid)) {
                    $iccidList[] = [
                        'iccid' => $iccid,
                        'phone_number' => null,
                        'operator' => null,
                        'plan_type' => null,
                        'monthly_cost' => null,
                    ];
                }
            }
        } else {
            return back()->with('error', 'Veuillez fournir un fichier CSV ou une liste d\'ICCID.');
        }

        if (empty($iccidList)) {
            return back()->with('error', 'Aucun ICCID valide trouvé.');
        }

        DB::beginTransaction();
        try {
            foreach ($iccidList as $index => $data) {
                $iccid = trim($data['iccid']);
                
                // Vérifier que l'ICCID n'est pas vide
                if (empty($iccid)) {
                    continue;
                }
                
                // Vérifier si l'ICCID existe déjà dans la base de données
                if (Sim::where('iccid', $iccid)->exists()) {
                    $errors[] = "La SIM avec l'ICCID {$iccid} existe déjà";
                    continue;
                }

                try {
                    // Créer la SIM avec statut "libre" par défaut
                    $sim = Sim::create([
                        'iccid' => $iccid,
                        'phone_number' => $data['phone_number'],
                        'operator' => $data['operator'],
                        'plan_type' => $data['plan_type'],
                        'monthly_cost' => $data['monthly_cost'],
                        'status' => 'libre',
                    ]);

                    // Créer un historique pour l'import
                    $sim->histories()->create([
                        'action' => 'created',
                        'user_id' => auth()->id(),
                        'user_matricule' => auth()->user()->matricule,
                        'old_data' => null,
                        'new_data' => $sim->toArray(),
                        'notes' => 'Import en masse',
                    ]);

                    $imported++;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Gérer l'erreur de contrainte unique au niveau de la base de données
                    if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                        $errors[] = "La SIM avec l'ICCID {$iccid} existe déjà (contrainte base de données)";
                    } else {
                        $errors[] = "Erreur lors de la création de la SIM {$iccid}: " . $e->getMessage();
                    }
                    continue;
                }
            }

            logActivity('import_sims', "Import: {$imported} SIMs importées", 'sims', null, ['imported' => $imported, 'errors' => count($errors)]);

            DB::commit();
            
            if (!empty($errors)) {
                $errorCount = count($errors);
                $errorMessages = [];
                $errorMessages[] = $errorCount . " SIM(s) déjà existante(s) ou en erreur:";
                $displayErrors = array_slice($errors, 0, 10); // Afficher jusqu'à 10 erreurs
                foreach ($displayErrors as $error) {
                    $errorMessages[] = "• " . $error;
                }
                if ($errorCount > 10) {
                    $errorMessages[] = "... et " . ($errorCount - 10) . " autre(s) erreur(s)";
                }
                
                // Si des erreurs mais aussi des succès, utiliser un warning
                if ($imported > 0) {
                    $message = "{$imported} SIM(s) importée(s) avec succès.\n\n" . implode("\n", $errorMessages);
                    return back()->with('warning', $message);
                } else {
                    // Si aucune importation réussie, utiliser error
                    $message = "Aucune SIM importée.\n\n" . implode("\n", $errorMessages);
                    return back()->with('error', $message);
                }
            }
            
            return back()->with('success', "{$imported} SIM(s) importée(s) avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    public function assign(Request $request, Sim $sim)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if (!$sim->isLibre()) {
            return back()->with('error', 'Cette SIM n\'est pas libre.');
        }

        $user = User::findOrFail($validated['user_id']);

        DB::beginTransaction();
        try {
            $oldData = $sim->toArray();
            $sim->update([
                'status' => 'attribue',
                'assigned_to' => $user->id,
                'assigned_to_matricule' => $user->matricule,
                'assigned_at' => now(),
            ]);

            $sim->histories()->create([
                'action' => 'assigned',
                'user_id' => auth()->id(),
                'user_matricule' => auth()->user()->matricule,
                'old_data' => $oldData,
                'new_data' => $sim->toArray(),
            ]);

            logModelAction($sim, 'assign');
            
            // Envoyer notification in-app à l'utilisateur
            $user->notify(new \App\Notifications\SimAssigned($sim));
            
            DB::commit();

            return back()->with('success', 'SIM attribuée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sims,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $count = 0;
        DB::beginTransaction();
        try {
            $user = User::findOrFail($validated['user_id']);
            $sims = Sim::whereIn('id', $validated['ids'])->where('status', 'libre')->get();

            foreach ($sims as $sim) {
                $sim->update([
                    'status' => 'attribue',
                    'assigned_to' => $user->id,
                    'assigned_to_matricule' => $user->matricule,
                    'assigned_at' => now(),
                ]);

                $sim->histories()->create([
                    'action' => 'assigned',
                    'user_id' => auth()->id(),
                    'user_matricule' => auth()->user()->matricule,
                    'new_data' => $sim->toArray(),
                ]);

                $user->notify(new \App\Notifications\SimAssigned($sim));
                $count++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "{$count} SIM(s) attribuée(s) avec succès à {$user->full_name}."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkUnassign(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sims,id',
        ]);

        $count = 0;
        DB::beginTransaction();
        try {
            $sims = Sim::whereIn('id', $validated['ids'])->where('status', 'attribue')->get();

            foreach ($sims as $sim) {
                $oldData = $sim->toArray();
                $sim->update([
                    'status' => 'libre',
                    'assigned_to' => null,
                    'assigned_to_matricule' => null,
                    'assigned_at' => null,
                ]);

                $sim->histories()->create([
                    'action' => 'unassigned',
                    'user_id' => auth()->id(),
                    'user_matricule' => auth()->user()->matricule,
                    'old_data' => $oldData,
                    'new_data' => $sim->toArray(),
                ]);
                $count++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "{$count} SIM(s) libérée(s) avec succès."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function unassign(Sim $sim)
    {
        if (!$sim->isAttribue()) {
            return back()->with('error', 'Cette SIM n\'est pas attribuée.');
        }

        DB::beginTransaction();
        try {
            $oldData = $sim->toArray();
            $sim->update([
                'status' => 'libre',
                'assigned_to' => null,
                'assigned_to_matricule' => null,
                'assigned_at' => null,
            ]);

            $sim->histories()->create([
                'action' => 'unassigned',
                'user_id' => auth()->id(),
                'user_matricule' => auth()->user()->matricule,
                'old_data' => $oldData,
                'new_data' => $sim->toArray(),
            ]);

            logModelAction($sim, 'unassign');
            DB::commit();

            return back()->with('success', 'SIM libérée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}

