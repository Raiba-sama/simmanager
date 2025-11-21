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
                'demandes_en_attente' => SimRequest::enAttente()->count(),
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
                         ->get();

        $labels = [];
        $data = [];
        $colors = [
            'recuperation' => '#f59e0b',
            'creation' => '#10b981',
            'suspension' => '#3b82f6',
            'desactivation' => '#ef4444',
            'ajustement' => '#8b5cf6',
        ];

        $typeLabels = [
            'recuperation' => 'Récupération',
            'creation' => 'Création',
            'suspension' => 'Suspension',
            'desactivation' => 'Désactivation',
            'ajustement' => 'Ajustement',
        ];

        foreach ($results as $result) {
            $labels[] = $typeLabels[$result->request_type] ?? ucfirst($result->request_type);
            $data[] = $result->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors),
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
                         ->get();

        $labels = [];
        $data = [];
        $colors = [
            'en_attente' => '#f59e0b',
            'validee' => '#10b981',
            'rejetee' => '#ef4444',
            'demande_envoyee' => '#06b6d4',
            'pending' => '#06b6d4',
            'accepted' => '#10b981',
            'refused' => '#ef4444',
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

        foreach ($results as $result) {
            $labels[] = $statusLabels[$result->status] ?? ucfirst($result->status);
            $data[] = $result->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors),
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
        $totalRequests = SimRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $validatedRequests = SimRequest::whereBetween('created_at', [$startDate, $endDate])
                                       ->where('status', 'validee')
                                       ->count();
        $rejectedRequests = SimRequest::whereBetween('created_at', [$startDate, $endDate])
                                      ->where('status', 'rejetee')
                                      ->count();

        $validationRate = $totalRequests > 0 ? round(($validatedRequests / $totalRequests) * 100, 1) : 0;

        // Délai moyen de traitement (en jours)
        $avgProcessingTime = SimRequest::whereBetween('created_at', [$startDate, $endDate])
                                       ->whereNotNull('validated_at')
                                       ->selectRaw('AVG(DATEDIFF(validated_at, created_at)) as avg_days')
                                       ->first()
                                       ->avg_days ?? 0;

        // Top demandeurs
        $topRequesters = SimRequest::whereBetween('created_at', [$startDate, $endDate])
                                   ->select('user_id', DB::raw('COUNT(*) as count'))
                                   ->groupBy('user_id')
                                   ->orderBy('count', 'desc')
                                   ->limit(5)
                                   ->with('user')
                                   ->get()
                                   ->map(function ($item) {
                                       return [
                                           'name' => $item->user->full_name ?? 'Utilisateur inconnu',
                                           'count' => $item->count,
                                       ];
                                   });

        return [
            'total_requests' => $totalRequests,
            'validated_requests' => $validatedRequests,
            'rejected_requests' => $rejectedRequests,
            'validation_rate' => $validationRate,
            'avg_processing_time' => round($avgProcessingTime, 1),
            'top_requesters' => $topRequesters,
        ];
    }
}
