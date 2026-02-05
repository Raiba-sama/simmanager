@extends('layouts.bootstrap')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
@endphp

<!-- Filtres de période (seulement pour validateurs) -->
@if($isValidator)
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 16px 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0" style="font-weight: 600; color: #1e293b;">Période d'analyse</h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm period-btn {{ $period === 'day' ? 'active' : '' }}" 
                                data-period="day"
                                onclick="changePeriod('day', this)" 
                                style="border-radius: 8px 0 0 8px; border: 1px solid #e2e8f0; {{ $period === 'day' ? 'background: #00574A; color: white;' : 'background: white; color: #64748b;' }}">
                            Aujourd'hui
                        </button>
                        <button type="button" class="btn btn-sm period-btn {{ $period === 'week' ? 'active' : '' }}" 
                                data-period="week"
                                onclick="changePeriod('week', this)" 
                                style="border: 1px solid #e2e8f0; border-left: none; {{ $period === 'week' ? 'background: #00574A; color: white;' : 'background: white; color: #64748b;' }}">
                            Cette semaine
                        </button>
                        <button type="button" class="btn btn-sm period-btn {{ $period === 'month' ? 'active' : '' }}" 
                                data-period="month"
                                onclick="changePeriod('month', this)" 
                                style="border: 1px solid #e2e8f0; border-left: none; {{ $period === 'month' ? 'background: #00574A; color: white;' : 'background: white; color: #64748b;' }}">
                            Ce mois
                        </button>
                        <button type="button" class="btn btn-sm period-btn {{ $period === 'year' ? 'active' : '' }}" 
                                data-period="year"
                                onclick="changePeriod('year', this)" 
                                style="border-radius: 0 8px 8px 0; border: 1px solid #e2e8f0; border-left: none; {{ $period === 'year' ? 'background: #00574A; color: white;' : 'background: white; color: #64748b;' }}">
                            Cette année
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Statistiques de base -->
<div class="row">
    @if($isValidator)
        <!-- Ligne 1 : indicateurs principaux -->
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">SIMS LIBRES</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['sims_libres'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;"><i class="bi bi-phone"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">SIMS ATTRIBUÉES</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['sims_attribuees'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;"><i class="bi bi-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">DEMANDES EN ATTENTE</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['demandes_en_attente'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;"><i class="bi bi-clock-history"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">TOTAL UTILISATEURS</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['total_utilisateurs'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;"><i class="bi bi-people"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Ligne 2 : total SIMs, envoyées, suspendues -->
        <div class="col-md-4 mb-4">
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 600;">TOTAL SIMS</h6>
                            <h4 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $stats['total_sims'] ?? 0 }}</h4>
                        </div>
                        <i class="bi bi-database" style="font-size: 2rem; color: #cbd5e1;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 600;">DEMANDES ENVOYÉES</h6>
                            <h4 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $stats['demandes_envoyees'] ?? 0 }}</h4>
                        </div>
                        <i class="bi bi-send-check" style="font-size: 2rem; color: #06b6d4;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 600;">SIMS SUSPENDUES</h6>
                            <h4 class="mb-0" style="font-weight: 700; color: #1e293b;">{{ $stats['sims_suspendues'] ?? 0 }}</h4>
                        </div>
                        <i class="bi bi-pause-circle" style="font-size: 2rem; color: #f59e0b;"></i>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Statistiques pour User normal -->
        <div class="col-md-4 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Ma SIM</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['mes_sims'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;">
                            <i class="bi bi-phone"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Mes demandes en attente</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['mes_demandes_en_attente'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2" style="opacity: 0.95; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Mes demandes validées</h6>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 2.5rem;">{{ $stats['mes_demandes_validees'] }}</h2>
                        </div>
                        <div style="font-size: 3.5rem; opacity: 0.25;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($user->isAdmin())
<!-- Synchronisation utilisateurs depuis le webhook (admin uniquement) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px 24px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6 class="mb-1" style="font-weight: 600; color: #1e293b;">
                            <i class="bi bi-cloud-download" style="color: #00574A; margin-right: 8px;"></i>
                            Synchronisation utilisateurs (webhook)
                        </h6>
                        <p class="mb-0 text-muted small">Récupérer les utilisateurs depuis l'application tierce. Les nouveaux utilisateurs (par matricule) seront créés avec le rôle renvoyé par le webhook.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.sync-from-webhook') }}" class="mb-0" data-confirm="Synchroniser les utilisateurs depuis le webhook ? Les nouveaux comptes (matricule inexistant) seront créés avec le rôle fourni par l'application tierce." data-confirm-variant="primary" data-confirm-text="Synchroniser">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i>Synchroniser les utilisateurs
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Graphiques (seulement pour validateurs) -->
@if($isValidator)
<div class="row mt-4">
    <!-- Évolution des demandes -->
    <div class="col-md-8 mb-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">
                    <i class="bi bi-graph-up" style="color: #00574A; margin-right: 8px;"></i>
                    Évolution des demandes
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="position: relative; height: 300px;">
                    <canvas id="requestsEvolutionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Répartition par type -->
    <div class="col-md-4 mb-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">
                    <i class="bi bi-pie-chart" style="color: #00574A; margin-right: 8px;"></i>
                    Par type
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="position: relative; height: 300px;">
                    <canvas id="requestsByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Répartition par statut -->
    <div class="col-md-6 mb-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">
                    <i class="bi bi-bar-chart" style="color: #00574A; margin-right: 8px;"></i>
                    Par statut
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="position: relative; height: 300px;">
                    <canvas id="requestsByStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SIMs par statut -->
    <div class="col-md-6 mb-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">
                    <i class="bi bi-phone" style="color: #00574A; margin-right: 8px;"></i>
                    SIMs par statut
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="position: relative; height: 300px;">
                    <canvas id="simsByStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isValidator)
<!-- Évolution des SIMs créées (Commandes opérateur) -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">
                    <i class="bi bi-graph-up" style="color: #00574A; margin-right: 8px;"></i>
                    Évolution des SIMs créées (Commandes opérateur)
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div style="position: relative; height: 300px;">
                    <canvas id="simsCreatedEvolutionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endif

@if($isValidator)
<!-- Liens rapides (validateurs) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px 24px;">
                <h6 class="mb-3" style="font-weight: 600; color: #1e293b;"><i class="bi bi-link-45deg me-2" style="color: #00574A;"></i>Liens rapides</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sim-requests.index') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-list-ul me-1"></i>Toutes les demandes</a>
                    <a href="{{ route('sim-requests.index', ['status' => 'en_attente']) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-clock me-1"></i>En attente</a>
                    <a href="{{ route('sim-requests.index', ['status' => 'pending']) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-hourglass-split me-1"></i>Pending</a>
                    <a href="{{ route('sim-requests.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Nouvelle demande</a>
                    @if($user->isAdmin())
                    <a href="{{ route('sims.index') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-phone me-1"></i>Gestion SIMs</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($isValidator)
<!-- Statistiques avancées -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">
                    <i class="bi bi-speedometer2" style="color: #00574A; margin-right: 8px;"></i>
                    Statistiques avancées (période sélectionnée)
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="row">
                    <div class="col-md-2 col-4 mb-3">
                        <div style="text-align: center; padding: 16px; background: #f9fafb; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #1e293b;">{{ $advancedStats['total_requests'] ?? 0 }}</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Total demandes</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3">
                        <div style="text-align: center; padding: 16px; background: #ecfdf5; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #059669;">{{ $advancedStats['validated_requests'] ?? 0 }}</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Validées / Acceptées</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3">
                        <div style="text-align: center; padding: 16px; background: #fef2f2; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #dc2626;">{{ $advancedStats['rejected_requests'] ?? 0 }}</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Rejetées / Refusées</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3">
                        <div style="text-align: center; padding: 16px; background: #fffbeb; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #d97706;">{{ $advancedStats['pending_requests'] ?? 0 }}</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">En attente</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3">
                        <div style="text-align: center; padding: 16px; background: #ecfeff; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #0891b2;">{{ $advancedStats['sent_requests'] ?? 0 }}</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Envoyées</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3">
                        <div style="text-align: center; padding: 16px; background: #f0fdf4; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #00574A;">{{ $advancedStats['validation_rate'] ?? 0 }}%</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Taux validation</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div style="text-align: center; padding: 16px; background: #fef2f2; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #ef4444;">{{ $advancedStats['rejection_rate'] ?? 0 }}%</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Taux de rejet</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div style="text-align: center; padding: 16px; background: #f9fafb; border-radius: 8px;">
                            <div style="font-size: 26px; font-weight: 700; color: #00574A;">{{ $advancedStats['avg_processing_time'] ?? 0 }}</div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">Délai moyen (jours)</div>
                        </div>
                    </div>
                </div>
                
                @if(isset($advancedStats['top_requesters']) && $advancedStats['top_requesters']->count() > 0)
                <div class="mt-4">
                    <h6 style="font-weight: 600; color: #1e293b; margin-bottom: 16px;">Top 5 créateurs de demandes</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="background: white;">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase;">Rang</th>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase;">Utilisateur</th>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; text-align: right;">Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($advancedStats['top_requesters'] as $index => $requester)
                                <tr>
                                    <td style="padding: 12px; color: #64748b; font-weight: 600;">#{{ $index + 1 }}</td>
                                    <td style="padding: 12px; color: #1e293b; font-weight: 500;">{{ $requester['name'] }}</td>
                                    <td style="padding: 12px; text-align: right; color: #00574A; font-weight: 600;">{{ $requester['count'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if(isset($advancedStats['top_motifs']) && $advancedStats['top_motifs']->count() > 0)
                <div class="mt-4">
                    <h6 style="font-weight: 600; color: #1e293b; margin-bottom: 16px;">Top 5 motifs</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="background: white;">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase;">Motif</th>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; text-align: right;">Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($advancedStats['top_motifs'] as $item)
                                <tr>
                                    <td style="padding: 12px; color: #1e293b; font-weight: 500;">{{ \Illuminate\Support\Str::limit($item->motif, 60) }}</td>
                                    <td style="padding: 12px; text-align: right; color: #00574A; font-weight: 600;">{{ $item->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if(isset($advancedStats['top_recuperation_numbers']) && $advancedStats['top_recuperation_numbers']->count() > 0)
                <div class="mt-4">
                    <h6 style="font-weight: 600; color: #1e293b; margin-bottom: 16px;"><i class="bi bi-telephone me-1" style="color: #00574A;"></i>Top 10 numéros les plus récupérés</h6>
                    <p class="text-muted small mb-2">Numéros de ligne les plus souvent concernés par une demande de récupération (toutes périodes).</p>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="background: white;">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase;">Rang</th>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase;">Numéro</th>
                                    <th style="padding: 12px; font-weight: 600; font-size: 12px; color: #6b7280; text-transform: uppercase; text-align: right;">Nb récupérations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($advancedStats['top_recuperation_numbers'] as $index => $row)
                                <tr>
                                    <td style="padding: 12px; color: #64748b; font-weight: 600;">#{{ $index + 1 }}</td>
                                    <td style="padding: 12px; color: #1e293b; font-weight: 500;">{{ $row->phone_number }}</td>
                                    <td style="padding: 12px; text-align: right; color: #00574A; font-weight: 600;">{{ $row->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Dernières demandes -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0" style="font-weight: 600; font-size: 18px; color: #1a1a1a;">Dernières demandes</h5>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="margin: 0;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">N° Demande</th>
                                <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Utilisateur</th>
                                <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Type</th>
                                <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                                <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                                <th style="padding: 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $query = \App\Models\SimRequest::with('user')->latest();
                                if (!$isValidator) {
                                    $query->where('user_id', $user->id);
                                }
                                $recentRequests = $query->limit(10)->get();
                            @endphp
                            @forelse($recentRequests as $request)
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <td style="padding: 16px; font-weight: 600; color: #1a1a1a;">{{ $request->request_number }}</td>
                                    <td style="padding: 16px; color: #4b5563;">
                                        @if($request->isCreation() && $request->beneficiary_name)
                                            {{ $request->beneficiary_name }} {{ $request->beneficiary_first_name ?? '' }}
                                        @else
                                            {{ $request->user?->full_name ?? '—' }}
                                        @endif
                                    </td>
                                    <td style="padding: 16px;">
                                        @php
                                            $typeLabels = [
                                                'recuperation' => 'Récupération',
                                                'creation' => 'Création',
                                                'suspension' => 'Suspension',
                                                'desactivation' => 'Désactivation',
                                                'ajustement' => 'Ajustement',
                                            ];
                                        @endphp
                                        <span class="badge" style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">{{ $typeLabels[$request->request_type] ?? ucfirst($request->request_type) }}</span>
                                    </td>
                                    <td style="padding: 16px;">
                                        @php
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
                                                'en_attente' => ['bg' => '#f59e0b', 'text' => 'white'],
                                                'validee' => ['bg' => '#10b981', 'text' => 'white'],
                                                'rejetee' => ['bg' => '#ef4444', 'text' => 'white'],
                                                'demande_envoyee' => ['bg' => '#06b6d4', 'text' => 'white'],
                                                'pending' => ['bg' => '#06b6d4', 'text' => 'white'],
                                                'accepted' => ['bg' => '#10b981', 'text' => 'white'],
                                                'refused' => ['bg' => '#ef4444', 'text' => 'white'],
                                            ];
                                            $statusColor = $statusColors[$request->status] ?? ['bg' => '#6b7280', 'text' => 'white'];
                                        @endphp
                                        <span class="badge" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                            {{ $statusLabels[$request->status] ?? ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 16px; color: #6b7280; font-size: 14px;">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td style="padding: 16px;">
                                        <a href="{{ route('sim-requests.show', $request) }}" class="btn btn-sm" style="background: transparent; border: 1px solid #e5e7eb; color: #3b82f6; padding: 6px 12px; border-radius: 6px;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center" style="padding: 40px; color: #9ca3af;">Aucune demande</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if($isValidator)
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    let charts = {};
    const currentPeriod = '{{ $period }}';
    
    // Données initiales
    const chartData = @json($chartData);
    
    // Initialiser les graphiques
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
    });
    
    function initCharts() {
        // Graphique d'évolution des demandes
        const evolutionCtx = document.getElementById('requestsEvolutionChart');
        if (evolutionCtx) {
            // Vérifier si on a des données
            if (!chartData.requests_evolution || !chartData.requests_evolution.labels || chartData.requests_evolution.labels.length === 0) {
                // Afficher un message si pas de données
                evolutionCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #9ca3af;"><i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5; margin-bottom: 12px;"></i><div>Aucune donnée disponible pour cette période</div></div>';
                return;
            }
            
            charts.evolution = new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: chartData.requests_evolution.labels || [],
                    datasets: [{
                        label: 'Nombre de demandes',
                        data: chartData.requests_evolution.data || [],
                        borderColor: '#00574A',
                        backgroundColor: 'rgba(0, 87, 74, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }
        
        // Graphique par type (camembert)
        const typeCtx = document.getElementById('requestsByTypeChart');
        if (typeCtx && chartData.requests_by_type) {
            const typeLabels = chartData.requests_by_type.labels || [];
            const typeData = chartData.requests_by_type.data || [];
            const typeColors = chartData.requests_by_type.colors || [];
            const typeSum = typeData.reduce((a, b) => a + b, 0);
            charts.type = new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: typeSum > 0 ? typeLabels : ['Aucune donnée'],
                    datasets: [{
                        data: typeSum > 0 ? typeData : [1],
                        backgroundColor: typeSum > 0 ? typeColors : ['#e5e7eb'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Graphique par statut (barres)
        const statusCtx = document.getElementById('requestsByStatusChart');
        if (statusCtx && chartData.requests_by_status) {
            charts.status = new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: chartData.requests_by_status.labels,
                    datasets: [{
                        label: 'Nombre',
                        data: chartData.requests_by_status.data,
                        backgroundColor: chartData.requests_by_status.colors,
                        borderWidth: 0,
                        barThickness: 30,
                        maxBarThickness: 40,
                        categoryPercentage: 0.6,
                        barPercentage: 0.8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: {
                                    size: 12
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const dataset = data.datasets[0];
                                            const value = dataset.data[i];
                                            const backgroundColor = Array.isArray(dataset.backgroundColor) 
                                                ? dataset.backgroundColor[i] 
                                                : dataset.backgroundColor;
                                            return {
                                                text: `${label}: ${value}`,
                                                fillStyle: backgroundColor,
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            categoryPercentage: 0.6,
                            barPercentage: 0.8,
                        }
                    }
                }
            });
        }
        
        // Graphique SIMs par statut
        const simsCtx = document.getElementById('simsByStatusChart');
        if (simsCtx && chartData.sims_by_status) {
            charts.sims = new Chart(simsCtx, {
                type: 'bar',
                data: {
                    labels: chartData.sims_by_status.labels,
                    datasets: [{
                        label: 'Nombre',
                        data: chartData.sims_by_status.data,
                        backgroundColor: chartData.sims_by_status.colors,
                        borderWidth: 0,
                        barThickness: 30,
                        maxBarThickness: 40,
                        categoryPercentage: 0.6,
                        barPercentage: 0.8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: {
                                    size: 12
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const dataset = data.datasets[0];
                                            const value = dataset.data[i];
                                            const backgroundColor = Array.isArray(dataset.backgroundColor) 
                                                ? dataset.backgroundColor[i] 
                                                : dataset.backgroundColor;
                                            return {
                                                text: `${label}: ${value}`,
                                                fillStyle: backgroundColor,
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            categoryPercentage: 0.6,
                            barPercentage: 0.8,
                        }
                    }
                }
            });
        }
        
        // Graphique évolution des SIMs créées (commandes opérateur)
        const simsCreatedCtx = document.getElementById('simsCreatedEvolutionChart');
        if (simsCreatedCtx && chartData.sims_created_evolution) {
            // Vérifier si on a des données
            if (!chartData.sims_created_evolution.labels || chartData.sims_created_evolution.labels.length === 0) {
                simsCreatedCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #9ca3af;"><i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5; margin-bottom: 12px;"></i><div>Aucune donnée disponible pour cette période</div></div>';
            } else {
                charts.simsCreated = new Chart(simsCreatedCtx, {
                    type: 'line',
                    data: {
                        labels: chartData.sims_created_evolution.labels || [],
                        datasets: [{
                            label: 'Nombre de SIMs créées',
                            data: chartData.sims_created_evolution.data || [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    padding: 12,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }
        }
    }
    
    function changePeriod(period, clickedBtn) {
        // Mettre à jour les boutons
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.style.background = 'white';
            btn.style.color = '#64748b';
        });
        clickedBtn.classList.add('active');
        clickedBtn.style.background = '#00574A';
        clickedBtn.style.color = 'white';
        
        // Charger les nouvelles données
        fetch(`{{ route('dashboard.chart-data') }}?period=${period}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les graphiques
            if (charts.evolution && data.requests_evolution) {
                charts.evolution.data.labels = data.requests_evolution.labels;
                charts.evolution.data.datasets[0].data = data.requests_evolution.data;
                charts.evolution.update();
            }
            
            if (charts.type && data.requests_by_type) {
                const d = data.requests_by_type.data || [];
                const sum = d.reduce((a, b) => a + b, 0);
                if (sum > 0) {
                    charts.type.data.labels = data.requests_by_type.labels;
                    charts.type.data.datasets[0].data = data.requests_by_type.data;
                    charts.type.data.datasets[0].backgroundColor = data.requests_by_type.colors;
                } else {
                    charts.type.data.labels = ['Aucune donnée'];
                    charts.type.data.datasets[0].data = [1];
                    charts.type.data.datasets[0].backgroundColor = ['#e5e7eb'];
                }
                charts.type.update();
            }
            
            if (charts.status && data.requests_by_status) {
                charts.status.data.labels = data.requests_by_status.labels;
                charts.status.data.datasets[0].data = data.requests_by_status.data;
                charts.status.data.datasets[0].backgroundColor = data.requests_by_status.colors;
                charts.status.update();
            }
            
            if (charts.sims && data.sims_by_status) {
                charts.sims.data.labels = data.sims_by_status.labels;
                charts.sims.data.datasets[0].data = data.sims_by_status.data;
                charts.sims.data.datasets[0].backgroundColor = data.sims_by_status.colors;
                charts.sims.update();
            }
            
            if (charts.simsCreated && data.sims_created_evolution) {
                charts.simsCreated.data.labels = data.sims_created_evolution.labels;
                charts.simsCreated.data.datasets[0].data = data.sims_created_evolution.data;
                charts.simsCreated.update();
            }
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
    }
</script>
@endif
@endpush
@endsection
