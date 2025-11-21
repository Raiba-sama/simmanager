<?php

namespace App\Http\Controllers;

use App\Models\SimRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des bordereaux de transmission
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Récupérer les demandes qui ont été acceptées (validees ou demande_envoyee)
        $query = SimRequest::with(['user', 'sim', 'plan', 'creator'])
            ->whereIn('status', ['validee', 'demande_envoyee', 'accepted'])
            ->orderBy('validated_at', 'desc')
            ->orderBy('admin_processed_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        if ($request->filled('date_from')) {
            $query->where(function($q) use ($request) {
                $q->where(function($q2) use ($request) {
                    $q2->whereNotNull('validated_at')
                       ->whereDate('validated_at', '>=', $request->date_from);
                })
                ->orWhere(function($q2) use ($request) {
                    $q2->whereNull('validated_at')
                       ->whereNotNull('admin_processed_at')
                       ->whereDate('admin_processed_at', '>=', $request->date_from);
                });
            });
        }

        if ($request->filled('date_to')) {
            $query->where(function($q) use ($request) {
                $q->where(function($q2) use ($request) {
                    $q2->whereNotNull('validated_at')
                       ->whereDate('validated_at', '<=', $request->date_to);
                })
                ->orWhere(function($q2) use ($request) {
                    $q2->whereNull('validated_at')
                       ->whereNotNull('admin_processed_at')
                       ->whereDate('admin_processed_at', '<=', $request->date_to);
                });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('requested_iccid', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('matricule', 'like', "%{$search}%");
                  })
                  ->orWhereHas('sim', function($q2) use ($search) {
                      $q2->where('iccid', 'like', "%{$search}%");
                  });
            });
        }

        // Si l'utilisateur n'est pas admin/validator, ne voir que ses propres demandes
        if (!$user->isValidator()) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        $bordereaux = $query->paginate(20)->withQueryString();

        // Statistiques (avec les mêmes filtres de permissions)
        $statsQuery = SimRequest::whereIn('status', ['validee', 'demande_envoyee', 'accepted']);
        
        if (!$user->isValidator()) {
            $statsQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'this_month' => (clone $statsQuery)
                ->where(function($q) {
                    $q->where(function($q2) {
                        $q2->whereMonth('validated_at', now()->month)
                           ->whereYear('validated_at', now()->year);
                    })
                    ->orWhere(function($q2) {
                        $q2->whereNull('validated_at')
                           ->whereMonth('admin_processed_at', now()->month)
                           ->whereYear('admin_processed_at', now()->year);
                    });
                })
                ->count(),
            'this_week' => (clone $statsQuery)
                ->where(function($q) {
                    $q->whereBetween('validated_at', [now()->startOfWeek(), now()->endOfWeek()])
                      ->orWhere(function($q2) {
                          $q2->whereNull('validated_at')
                             ->whereBetween('admin_processed_at', [now()->startOfWeek(), now()->endOfWeek()]);
                      });
                })
                ->count(),
        ];

        return view('documents.index', compact('bordereaux', 'stats'));
    }

    /**
     * Afficher un bordereau de transmission
     */
    public function show(SimRequest $simRequest)
    {
        // Vérifier que la demande est acceptée
        if (!in_array($simRequest->status, ['validee', 'demande_envoyee', 'accepted'])) {
            return redirect()->route('documents.index')
                ->with('error', 'Ce bordereau n\'est pas encore disponible. La demande doit être acceptée.');
        }

        // Vérifier les permissions
        $user = auth()->user();
        if (!$user->isAdmin() && $simRequest->user_id !== $user->id && $simRequest->created_by !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à ce bordereau.');
        }

        return view('sim-requests.bordereau', compact('simRequest'));
    }
}

