@extends('layouts.bootstrap')

@section('title', 'Synchronisation utilisateurs')
@section('page-title', 'Synchronisation utilisateurs (webhook)')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; margin-bottom: 24px;">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px; margin-bottom: 24px;">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 24px;">
                <h6 class="mb-3" style="font-weight: 600; color: #1e293b;">
                    <i class="bi bi-cloud-download" style="color: #00574A; margin-right: 8px;"></i>
                    Récupérer les utilisateurs depuis l'application tierce
                </h6>
                <p class="text-muted mb-4">Les utilisateurs dont le <strong>matricule</strong> n'existe pas encore seront créés avec le rôle renvoyé par le webhook. Les comptes déjà présents (même matricule) sont ignorés.</p>
                <form method="POST" action="{{ route('admin.users.sync-from-webhook') }}" data-confirm="Lancer la synchronisation depuis le webhook ? Les nouveaux comptes seront créés avec le rôle fourni par l'application tierce." data-confirm-variant="primary" data-confirm-text="Synchroniser">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat me-1"></i>Synchroniser les utilisateurs
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ms-2">Retour au tableau de bord</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
