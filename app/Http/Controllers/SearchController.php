<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\SimRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, requests, sims, users
        
        if (strlen($query) < 2) {
            return response()->json([
                'requests' => [],
                'sims' => [],
                'users' => [],
                'total' => 0,
            ]);
        }

        $user = auth()->user();
        $results = [
            'requests' => [],
            'sims' => [],
            'users' => [],
            'total' => 0,
        ];

        // Recherche dans les demandes
        if ($type === 'all' || $type === 'requests') {
            $requestsQuery = SimRequest::with(['user', 'sim', 'validator', 'plan'])
                ->where(function($q) use ($query) {
                    $q->where('request_number', 'like', '%' . $query . '%')
                      ->orWhere('phone_number', 'like', '%' . $query . '%')
                      ->orWhere('motif', 'like', '%' . $query . '%')
                      ->orWhere('requested_iccid', 'like', '%' . $query . '%')
                      ->orWhereHas('user', function($userQuery) use ($query) {
                          $userQuery->where('name', 'like', '%' . $query . '%')
                                    ->orWhere('first_name', 'like', '%' . $query . '%')
                                    ->orWhere('email', 'like', '%' . $query . '%')
                                    ->orWhere('matricule', 'like', '%' . $query . '%');
                      });
                })
                ->orderBy('created_at', 'desc')
                ->limit(10);

            // Visibilité : User voit seulement ses demandes
            if (!$user->isValidator()) {
                $requestsQuery->where('user_id', $user->id);
            }

            $results['requests'] = $requestsQuery->get()->map(function($request) {
                $typeLabels = [
                    'recuperation' => 'Récupération',
                    'creation' => 'Création',
                    'suspension' => 'Suspension',
                    'desactivation' => 'Désactivation',
                    'ajustement' => 'Ajustement',
                ];
                
                $statusLabels = [
                    'en_attente' => 'En attente',
                    'validee' => 'Validée',
                    'rejetee' => 'Rejetée',
                    'demande_envoyee' => 'Demande envoyée',
                    'pending' => 'Pending',
                    'accepted' => 'Acceptée',
                    'refused' => 'Refusée',
                ];
                
                return [
                    'id' => $request->id,
                    'request_number' => $request->request_number,
                    'type' => $request->request_type,
                    'type_label' => $typeLabels[$request->request_type] ?? ucfirst($request->request_type),
                    'status' => $request->status,
                    'status_label' => $statusLabels[$request->status] ?? ucfirst($request->status),
                    'user_name' => $request->user->full_name ?? 'N/A',
                    'phone_number' => $request->phone_number ?? ($request->sim->phone_number ?? null),
                    'created_at' => $request->created_at->format('d/m/Y'),
                    'url' => route('sim-requests.show', $request),
                ];
            });
        }

        // Recherche dans les SIMs (seulement pour validators/admins)
        if (($type === 'all' || $type === 'sims') && $user->isValidator()) {
            $results['sims'] = Sim::where(function($q) use ($query) {
                    $q->where('iccid', 'like', '%' . $query . '%')
                      ->orWhere('phone_number', 'like', '%' . $query . '%')
                      ->orWhere('operator', 'like', '%' . $query . '%')
                      ->orWhereHas('assignedUser', function($userQuery) use ($query) {
                          $userQuery->where('name', 'like', '%' . $query . '%')
                                    ->orWhere('first_name', 'like', '%' . $query . '%')
                                    ->orWhere('matricule', 'like', '%' . $query . '%');
                      });
                })
                ->with('assignedUser')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($sim) {
                    $statusLabels = [
                        'libre' => 'Libre',
                        'attribue' => 'Attribuée',
                        'suspendu' => 'Suspendue',
                    ];
                    
                    return [
                        'id' => $sim->id,
                        'iccid' => $sim->iccid,
                        'phone_number' => $sim->phone_number,
                        'status' => $sim->status,
                        'status_label' => $statusLabels[$sim->status] ?? ucfirst($sim->status),
                        'operator' => $sim->operator,
                        'assigned_to' => $sim->assignedUser->full_name ?? 'Non attribuée',
                        'assigned_matricule' => $sim->assignedUser->matricule ?? null,
                        'url' => route('sims.show', $sim),
                    ];
                });
        }

        // Recherche dans les utilisateurs (seulement pour admins)
        if (($type === 'all' || $type === 'users') && $user->isAdmin()) {
            $results['users'] = User::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('first_name', 'like', '%' . $query . '%')
                      ->orWhere('email', 'like', '%' . $query . '%')
                      ->orWhere('matricule', 'like', '%' . $query . '%')
                      ->orWhere('fonction', 'like', '%' . $query . '%')
                      ->orWhere('lieu_affectation', 'like', '%' . $query . '%')
                      ->orWhere('direction', 'like', '%' . $query . '%');
                })
                ->orderBy('name', 'asc')
                ->limit(10)
                ->get()
                ->map(function($user) {
                    $roleLabels = [
                        'admin' => 'Administrateur',
                        'validator' => 'Validateur',
                        'user' => 'Utilisateur',
                    ];
                    
                    return [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'matricule' => $user->matricule,
                        'role' => $user->role,
                        'role_label' => $roleLabels[$user->role] ?? ucfirst($user->role),
                        'fonction' => $user->fonction,
                        'url' => route('profile.edit') . '?user=' . $user->id,
                    ];
                });
        }
        
        // Calculer le total
        $results['total'] = count($results['requests']) + count($results['sims']) + count($results['users']);

        return response()->json($results);
    }
}

