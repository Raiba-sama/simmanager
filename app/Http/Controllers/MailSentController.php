<?php

namespace App\Http\Controllers;

use App\Models\MailSent;
use App\Models\SimRequest;
use App\Models\SimHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailSentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des mails envoyés
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = MailSent::with('simRequest')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        if ($request->filled('request_number')) {
            $query->where('request_number', 'like', "%{$request->request_number}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('request_matricule', 'like', "%{$search}%")
                  ->orWhere('request_name', 'like', "%{$search}%")
                  ->orWhere('sim_iccid', 'like', "%{$search}%")
                  ->orWhere('message_subject', 'like', "%{$search}%");
            });
        }

        $mails = $query->paginate(20)->withQueryString();

        // Statistiques
        $stats = [
            'total' => MailSent::count(),
            'this_month' => MailSent::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'this_week' => MailSent::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('mail-sent.index', compact('mails', 'stats'));
    }

    /**
     * Sauvegarder le retour du webhook get_infos
     * Appelé depuis SimRequestController après l'envoi au webhook
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('MailSentController::store called', [
                'data_keys' => array_keys($data),
                'status' => $data['status'] ?? 'not set',
                'request_number' => $data['request_number'] ?? 'not set'
            ]);
            
            // Vérifier que le statut est OK (avec trim pour enlever les espaces)
            $status = isset($data['status']) ? strtolower(trim($data['status'])) : null;
            if (!$status || $status !== 'ok') {
                Log::warning('Mail not stored: status is not OK', [
                    'status_received' => $data['status'] ?? 'not set',
                    'status_normalized' => $status,
                    'data' => $data
                ]);
                return response()->json(['success' => false, 'message' => 'Status is not OK'], 400);
            }

            // Extraire les données du webhook
            // On ne sauvegarde que message_subject et message_corps depuis le webhook
            // Les autres champs viennent de la demande (request_id, request_number, etc.)
            $mailData = [
                'thread_id' => $data['threadId'] ?? $data['thread_id'] ?? null,
                'label_ids' => $data['labelIds'] ?? $data['label_ids'] ?? null,
                'message_subject' => $data['message_subject'] ?? $data['subject'] ?? null,
                'message_corps' => $data['message_corps'] ?? $data['body'] ?? $data['message'] ?? null,
                // Les champs suivants viennent de la demande, pas du webhook
                'request_type' => $data['request_type'] ?? null,
                'request_number' => $data['request_number'] ?? null,
                'request_matricule' => $data['request_matricule'] ?? null,
                'request_name' => $data['request_name'] ?? null,
                'sim_iccid' => $data['sim_iccid'] ?? null,
                'sim_assign_to' => $data['sim_assign_to'] ?? null,
                'request_id' => $data['request_id'] ?? null,
                'operator_response' => null, // Sera mis à jour plus tard
                'status_after_sent' => null, // Sera mis à jour plus tard
            ];

            // Si request_id n'est pas fourni mais request_number l'est, chercher la demande
            if (!$mailData['request_id'] && $mailData['request_number']) {
                $simRequest = SimRequest::where('request_number', $mailData['request_number'])->first();
                if ($simRequest) {
                    $mailData['request_id'] = $simRequest->id;
                    Log::info('Found request by request_number', [
                        'request_number' => $mailData['request_number'],
                        'request_id' => $simRequest->id
                    ]);
                } else {
                    Log::warning('Request not found by request_number', [
                        'request_number' => $mailData['request_number']
                    ]);
                }
            }

            Log::info('Attempting to create MailSent', [
                'mail_data' => $mailData
            ]);

            $mailSent = MailSent::create($mailData);

            Log::info('Mail stored successfully', [
                'mail_id' => $mailSent->id,
                'request_number' => $mailData['request_number'],
                'request_id' => $mailData['request_id']
            ]);

            return response()->json(['success' => true, 'mail_id' => $mailSent->id], 201);
        } catch (\Exception $e) {
            Log::error('Error storing mail', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? []
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Appeler le webhook CheckMail pour récupérer les réponses
     */
    public function checkMail(Request $request)
    {
        try {
            $username = env('N8N_USERNAME');
            $password = env('N8N_PASSWORD');

            if (!$username || !$password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Webhook credentials not configured'
                ], 400);
            }

            // Récupérer les paramètres
            $id = $request->input('id');
            $requestType = $request->input('request_type');
            $messageSubject = $request->input('message_subject');
            $requestId = $request->input('request_id');

            Log::info('CheckMail called', [
                'id' => $id,
                'request_type' => $requestType,
                'message_subject' => $messageSubject,
                'request_id' => $requestId
            ]);

            // L'ID du mail est obligatoire, les autres peuvent être récupérés depuis la base si manquants
            if (!$id) {
                Log::warning('CheckMail missing mail ID', [
                    'id' => $id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'ID du mail manquant'
                ], 400);
            }

            // Si certains paramètres manquent, essayer de les récupérer depuis la base de données
            $mailSent = MailSent::find($id);
            if ($mailSent) {
                $requestType = $requestType ?: $mailSent->request_type;
                $messageSubject = $messageSubject ?: $mailSent->message_subject;
                $requestId = $requestId ?: $mailSent->request_id;
                
                // Si message_subject est toujours vide, essayer de le construire depuis la demande
                if (!$messageSubject && $requestId) {
                    $simRequest = SimRequest::find($requestId);
                    if ($simRequest) {
                        $typeLabels = [
                            'recuperation' => 'Récupération',
                            'creation' => 'Création',
                            'suspension' => 'Suspension',
                            'desactivation' => 'Désactivation',
                            'ajustement' => 'Ajustement',
                        ];
                        $typeLabel = $typeLabels[$requestType] ?? ucfirst($requestType);
                        $messageSubject = "Demande {$typeLabel} - {$simRequest->request_number}";
                    }
                }
                
                // Si message_subject est toujours vide, créer un sujet par défaut
                if (!$messageSubject) {
                    $typeLabels = [
                        'recuperation' => 'Récupération',
                        'creation' => 'Création',
                        'suspension' => 'Suspension',
                        'desactivation' => 'Désactivation',
                        'ajustement' => 'Ajustement',
                    ];
                    $typeLabel = $typeLabels[$requestType] ?? ucfirst($requestType);
                    $messageSubject = "Demande {$typeLabel}";
                }
                
                Log::info('Parameters retrieved from database', [
                    'request_type' => $requestType,
                    'message_subject' => $messageSubject,
                    'request_id' => $requestId,
                    'mail_sent_data' => [
                        'message_subject' => $mailSent->message_subject,
                        'request_type' => $mailSent->request_type,
                        'request_id' => $mailSent->request_id
                    ]
                ]);
            } else {
                Log::warning('MailSent not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Mail non trouvé dans la base de données'
                ], 404);
            }

            // Vérifier que les paramètres essentiels sont présents
            if (!$requestType || !$messageSubject || !$requestId) {
                Log::warning('CheckMail missing essential parameters after retrieval', [
                    'id' => $id,
                    'request_type' => $requestType,
                    'message_subject' => $messageSubject,
                    'request_id' => $requestId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Paramètres manquants. Veuillez vérifier que le mail contient toutes les informations nécessaires.',
                    'received' => [
                        'id' => $id ? 'present' : 'missing',
                        'request_type' => $requestType ? 'present' : 'missing',
                        'message_subject' => $messageSubject ? 'present' : 'missing',
                        'request_id' => $requestId ? 'present' : 'missing'
                    ]
                ], 400);
            }

            // Construire l'URL avec les paramètres
            $baseUrl = 'https://acepmg.it4life.org/webhook/CheckMail';
            $params = [
                'id' => $id,
                'request_type' => $requestType,
                'message_subject' => $messageSubject,
                'request_id' => $requestId,
            ];
            $url = $baseUrl . '?' . http_build_query($params);

            Log::info('Calling CheckMail webhook', [
                'url' => $url,
                'request_id' => $requestId
            ]);

            // Appeler le webhook
            $response = Http::withBasicAuth($username, $password)
                ->withoutVerifying()
                ->timeout(30)
                ->get($url);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('CheckMail webhook response', [
                    'request_id' => $requestId,
                    'response' => $responseData
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $responseData
                ]);
            } else {
                Log::error('CheckMail webhook failed', [
                    'request_id' => $requestId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Webhook request failed',
                    'status' => $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error calling CheckMail webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le statut de la demande en "accepté" après validation manuelle
     */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();

        // Vérifier que c'est un admin ou validator
        if (!$user->isAdmin() && !$user->isValidator()) {
            abort(403, 'Seuls les administrateurs et validateurs peuvent mettre à jour le statut.');
        }

        try {
            $mailSent = MailSent::findOrFail($id);
            
            if (!$mailSent->request_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune demande associée à ce mail.'
                ], 400);
            }

            $simRequest = SimRequest::findOrFail($mailSent->request_id);

            DB::beginTransaction();

            // Mettre à jour le statut de la demande en "accepté"
            $oldStatus = $simRequest->status;
            $simRequest->update([
                'status' => 'accepted',
                'admin_id' => auth()->id(),
                'admin_processed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            // Mettre à jour le mail avec la réponse de l'opérateur et le statut
            $mailSent->update([
                'operator_response' => $request->input('operator_response'),
                'status_after_sent' => 'accepted',
            ]);

            // Créer un historique pour la mise à jour
            $oldData = $simRequest->getOriginal() ?: $simRequest->toArray();
            $newData = array_merge($simRequest->toArray(), [
                'old_status' => $oldStatus,
                'new_status' => 'accepted',
                'admin_id' => auth()->id(),
                'operator_response' => $request->input('operator_response'),
            ]);
            
            SimHistory::create([
                'sim_id' => $simRequest->sim_id,
                'action' => 'status_updated',
                'user_id' => auth()->id(),
                'user_matricule' => auth()->user()->matricule ?? null,
                'request_id' => $simRequest->id,
                'old_data' => $oldData,
                'new_data' => $newData,
                'notes' => $request->input('operator_response'),
            ]);

            DB::commit();

            Log::info('Request status updated to accepted', [
                'request_id' => $simRequest->id,
                'request_number' => $simRequest->request_number,
                'mail_id' => $mailSent->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Statut de la demande mis à jour avec succès.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating request status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }
}
