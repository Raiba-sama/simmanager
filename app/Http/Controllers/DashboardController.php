<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\SimRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Les admins peuvent accéder au dashboard Breeze pour valider les requêtes
        // Ils peuvent aussi accéder à /admin pour la gestion administrative
        $isValidator = $user->isValidator();
        $period = $request->get('period', 'month'); // day, week, month, year

        // Calculer les dates selon la période
        $dateRange = $this->getDateRange($period);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Statistiques de base
        $stats = $this->getBasicStats($user, $isValidator);

        // Données pour graphiques
        $chartData = $this->getChartData($user, $isValidator, $startDate, $endDate, $period);

        // Statistiques avancées (seulement pour validateurs/admins)
        $advancedStats = [];
        if ($isValidator) {
            $advancedStats = $this->getAdvancedStats($startDate, $endDate);
        }

        return view('dashboard', compact('stats', 'chartData', 'advancedStats', 'period', 'isValidator'));
    }

    /**
     * API endpoint pour récupérer les données des graphiques
     */
    public function getChartDataApi(Request $request)
    {
        $user = auth()->user();
        $isValidator = $user->isValidator();
        $period = $request->get('period', 'month');

        $dateRange = $this->getDateRange($period);
        $chartData = $this->getChartData($user, $isValidator, $dateRange['start'], $dateRange['end'], $period);

        return response()->json($chartData);
    }

    private function getDateRange($period)
    {
        $endDate = Carbon::now();
        
        switch ($period) {
            case 'day':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
        }

        return [
            'start' => $startDate,
            'end' => $endDate,
        ];
    }

    private function getBasicStats($user, $isValidator)
    {
        if ($isValidator) {
            return [
                'sims_libres' => Sim::libre()->count(),
                'sims_attribuees' => Sim::attribue()->count(),
                'sims_suspendues' => Sim::where('status', 'suspendu')->count(),
                'sims_defectueuses' => Sim::where('status', 'defectueuse')->count(),
                'total_sims' => Sim::count(),
                'demandes_en_attente' => SimRequest::whereIn('status', ['en_attente', 'pending'])->count(),
                'demandes_envoyees' => SimRequest::where('status', 'demande_envoyee')->count(),
                'total_utilisateurs' => User::where('active', true)->count(),
            ];
        } else {
            return [
                'mes_sims' => Sim::where('assigned_to', $user->id)->whereIn('status', ['attribue', 'suspendu'])->count(),
                'mes_demandes_en_attente' => SimRequest::where('user_id', $user->id)->enAttente()->count(),
                'mes_demandes_validees' => SimRequest::where('user_id', $user->id)->validee()->count(),
            ];
        }
    }

    private function getChartData($user, $isValidator, $startDate, $endDate, $period)
    {
        $data = [];

        // Évolution des demandes dans le temps
        $data['requests_evolution'] = $this->getRequestsEvolution($user, $isValidator, $startDate, $endDate, $period);

        // Répartition par type
        $data['requests_by_type'] = $this->getRequestsByType($user, $isValidator, $startDate, $endDate);

        // Répartition par statut
        $data['requests_by_status'] = $this->getRequestsByStatus($user, $isValidator, $startDate, $endDate);

        // SIMs par statut (seulement pour validateurs)
        if ($isValidator) {
            $data['sims_by_status'] = $this->getSimsByStatus();
            // Évolution des SIMs créées (commandes opérateur)
            $data['sims_created_evolution'] = $this->getSimsCreatedEvolution($startDate, $endDate, $period);
        }

        return $data;
    }

    private function getRequestsEvolution($user, $isValidator, $startDate, $endDate, $period)
    {
        $query = SimRequest::whereBetween('created_at', [$startDate, $endDate]);

        if (!$isValidator) {
            $query->where('user_id', $user->id);
        }

        $labels = [];
        $values = [];

        // Grouper selon la période
        switch ($period) {
            case 'day':
                // Pour "Aujourd'hui", on groupe par heure
                $results = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                // Créer toutes les heures de la journée
                $current = $startDate->copy()->startOfDay();
                $end = $endDate->copy();
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                while ($current <= $end) {
                    $key = $current->format('Y-m-d H:00:00');
                    $labels[] = $current->format('H:i');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addHour();
                }
                break;
                
            case 'week':
                // Pour "Cette semaine", on groupe par jour
                $results = $query->selectRaw('DATE(created_at) as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $key = $current->format('Y-m-d');
                    $labels[] = $current->format('d/m');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addDay();
                }
                break;
                
            case 'month':
                // Pour "Ce mois", on groupe par jour
                $results = $query->selectRaw('DATE(created_at) as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $key = $current->format('Y-m-d');
                    $labels[] = $current->format('d/m');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addDay();
                }
                break;
                
            case 'year':
                // Pour "Cette année", on groupe par mois
                $results = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $key = $current->format('Y-m');
                    $labels[] = $current->format('M Y');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addMonth();
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    private function getRequestsByType($user, $isValidator, $startDate, $endDate)
    {
        $query = SimRequest::whereBetween('created_at', [$startDate, $endDate]);

        if (!$isValidator) {
            $query->where('user_id', $user->id);
        }

        $results = $query->select('request_type', DB::raw('COUNT(*) as count'))
                         ->groupBy('request_type')
                         ->get()
                         ->pluck('count', 'request_type');

        $typeOrder = ['recuperation', 'creation', 'suspension', 'desactivation', 'ajustement'];
        $typeLabels = [
            'recuperation' => 'Récupération',
            'creation' => 'Création',
            'suspension' => 'Suspension',
            'desactivation' => 'Désactivation',
            'ajustement' => 'Ajustement',
        ];
        $typeColors = [
            'recuperation' => '#f59e0b',
            'creation' => '#10b981',
            'suspension' => '#3b82f6',
            'desactivation' => '#ef4444',
            'ajustement' => '#8b5cf6',
        ];

        $labels = [];
        $data = [];
        $colors = [];
        foreach ($typeOrder as $type) {
            $labels[] = $typeLabels[$type];
            $data[] = (int) ($results[$type] ?? 0);
            $colors[] = $typeColors[$type];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    private function getRequestsByStatus($user, $isValidator, $startDate, $endDate)
    {
        $query = SimRequest::whereBetween('created_at', [$startDate, $endDate]);

        if (!$isValidator) {
            $query->where('user_id', $user->id);
        }

        $results = $query->select('status', DB::raw('COUNT(*) as count'))
                         ->groupBy('status')
                         ->get()
                         ->pluck('count', 'status');

        $statusOrder = ['en_attente', 'pending', 'validee', 'accepted', 'demande_envoyee', 'rejetee', 'refused'];
        $statusLabels = [
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            'demande_envoyee' => 'Demande envoyée',
            'pending' => 'Pending',
            'accepted' => 'Acceptée',
            'refused' => 'Refusée',
        ];
        $statusColors = [
            'en_attente' => '#f59e0b',
            'pending' => '#94a3b8',
            'validee' => '#10b981',
            'accepted' => '#059669',
            'demande_envoyee' => '#06b6d4',
            'rejetee' => '#ef4444',
            'refused' => '#dc2626',
        ];

        $labels = [];
        $data = [];
        $colors = [];
        foreach ($statusOrder as $status) {
            $labels[] = $statusLabels[$status] ?? ucfirst($status);
            $data[] = (int) ($results[$status] ?? 0);
            $colors[] = $statusColors[$status] ?? '#6b7280';
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    private function getSimsCreatedEvolution($startDate, $endDate, $period)
    {
        $query = Sim::whereBetween('created_at', [$startDate, $endDate]);

        $labels = [];
        $values = [];

        // Grouper selon la période
        switch ($period) {
            case 'day':
                // Pour "Aujourd'hui", on groupe par heure
                $results = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                // Créer toutes les heures de la journée
                $current = $startDate->copy()->startOfDay();
                $end = $endDate->copy();
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                while ($current <= $end) {
                    $key = $current->format('Y-m-d H:00:00');
                    $labels[] = $current->format('H:i');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addHour();
                }
                break;
                
            case 'week':
                // Pour "Cette semaine", on groupe par jour
                $results = $query->selectRaw('DATE(created_at) as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $key = $current->format('Y-m-d');
                    $labels[] = $current->format('d/m');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addDay();
                }
                break;
                
            case 'month':
                // Pour "Ce mois", on groupe par jour
                $results = $query->selectRaw('DATE(created_at) as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $key = $current->format('Y-m-d');
                    $labels[] = $current->format('d/m');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addDay();
                }
                break;
                
            case 'year':
                // Pour "Cette année", on groupe par mois
                $results = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period_key, COUNT(*) as count')
                      ->groupBy('period_key')
                      ->orderBy('period_key')
                      ->get();
                
                $dataMap = [];
                foreach ($results as $result) {
                    $dataMap[$result->period_key] = $result->count;
                }
                
                $current = $startDate->copy();
                while ($current <= $endDate) {
                    $key = $current->format('Y-m');
                    $labels[] = $current->format('M Y');
                    $values[] = $dataMap[$key] ?? 0;
                    $current->addMonth();
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    private function getSimsByStatus()
    {
        $results = Sim::select('status', DB::raw('COUNT(*) as count'))
                      ->groupBy('status')
                      ->get();

        $labels = [];
        $data = [];
        $colors = [
            'libre' => '#10b981',
            'attribue' => '#3b82f6',
            'suspendu' => '#ef4444',
            'defectueuse' => '#f59e0b',
        ];

        $statusLabels = [
            'libre' => 'Libre',
            'attribue' => 'Attribuée',
            'suspendu' => 'Suspendue',
            'defectueuse' => 'Défectueuse',
        ];

        // S'assurer que tous les statuts sont présents même s'ils n'ont pas de données
        $allStatuses = ['libre', 'attribue', 'suspendu', 'defectueuse'];
        $statusCounts = $results->pluck('count', 'status')->toArray();
        
        foreach ($allStatuses as $status) {
            $labels[] = $statusLabels[$status] ?? ucfirst($status);
            $data[] = $statusCounts[$status] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => [
                $colors['libre'],
                $colors['attribue'],
                $colors['suspendu'],
                $colors['defectueuse'],
            ],
        ];
    }

    private function getAdvancedStats($startDate, $endDate)
    {
        $baseQuery = SimRequest::whereBetween('created_at', [$startDate, $endDate]);
        $totalRequests = (clone $baseQuery)->count();

        // Validées = validee (récupération) ou accepted (autres types)
        $validatedRequests = (clone $baseQuery)->whereIn('status', ['validee', 'accepted'])->count();
        $rejectedRequests = (clone $baseQuery)->whereIn('status', ['rejetee', 'refused'])->count();
        $pendingRequests = (clone $baseQuery)->whereIn('status', ['en_attente', 'pending'])->count();
        $sentRequests = (clone $baseQuery)->where('status', 'demande_envoyee')->count();

        $validationRate = $totalRequests > 0 ? round(($validatedRequests / $totalRequests) * 100, 1) : 0;
        $rejectionRate = $totalRequests > 0 ? round(($rejectedRequests / $totalRequests) * 100, 1) : 0;

        // Délai moyen : jours entre création et (validated_at ou admin_processed_at)
        $avgRow = SimRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereRaw('(validated_at IS NOT NULL OR admin_processed_at IS NOT NULL)')
            ->selectRaw('AVG(DATEDIFF(COALESCE(admin_processed_at, validated_at), created_at)) as avg_days')
            ->first();
        $avgProcessingTime = $avgRow && $avgRow->avg_days !== null ? round((float) $avgRow->avg_days, 1) : 0;

        // Top créateurs de demandes (created_by = validateur/admin)
        $topCreators = SimRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('created_by')
            ->select('created_by', DB::raw('COUNT(*) as count'))
            ->groupBy('created_by')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->with('creator')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->creator ? $item->creator->full_name : 'N/A',
                    'count' => $item->count,
                ];
            });

        $topMotifs = SimRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('motif')
            ->where('motif', '!=', '')
            ->select('motif', DB::raw('COUNT(*) as count'))
            ->groupBy('motif')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_requests' => $totalRequests,
            'validated_requests' => $validatedRequests,
            'rejected_requests' => $rejectedRequests,
            'pending_requests' => $pendingRequests,
            'sent_requests' => $sentRequests,
            'validation_rate' => $validationRate,
            'rejection_rate' => $rejectionRate,
            'avg_processing_time' => $avgProcessingTime,
            'top_requesters' => $topCreators,
            'top_motifs' => $topMotifs,
        ];
    }
}
