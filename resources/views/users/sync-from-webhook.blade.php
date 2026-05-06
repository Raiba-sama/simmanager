@extends('layouts.bootstrap')

@section('title', 'Synchronisation utilisateurs')
@section('page-title', 'Synchronisation utilisateurs')

@push('styles')
<style>
    .sync-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        padding: 28px;
        max-width: 680px;
    }

    .sync-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: var(--primary-glow);
        display: flex; align-items: center; justify-content: center;
        color: var(--primary);
        font-size: 22px;
        flex-shrink: 0;
    }

    .sync-desc {
        font-size: 13.5px;
        color: var(--muted);
        line-height: 1.75;
        background: #f8fafc;
        border: 1px solid var(--border);
        border-left: 3px solid var(--primary);
        border-radius: 0 9px 9px 0;
        padding: 14px 16px;
        margin: 20px 0 28px;
    }

    .sync-desc strong { color: var(--text); }

    .btn-sync {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 38px;
        padding: 0 18px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.18s;
    }
    .btn-sync:hover { background: var(--primary-dark); color: white; }

    .btn-sync-outline {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 38px;
        padding: 0 16px;
        background: white;
        color: var(--text);
        border: 1.5px solid var(--border);
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        text-decoration: none;
        transition: border-color 0.18s, color 0.18s;
    }
    .btn-sync-outline:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')
<div class="sync-card">

    <div class="d-flex align-items-center gap-3 mb-2">
        <div class="sync-icon">
            <i class="bi bi-cloud-arrow-down"></i>
        </div>
        <div>
            <div style="font-size:15px;font-weight:700;color:var(--text);">Récupérer les utilisateurs depuis l'application tierce</div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px;">Synchronisation via webhook</div>
        </div>
    </div>

    <div class="sync-desc">
        Les utilisateurs dont le <strong>matricule</strong> n'existe pas encore seront créés avec le rôle renvoyé par le webhook.
        Les comptes déjà présents (même matricule) sont <strong>ignorés</strong>.
    </div>

    <form method="POST" action="{{ route('admin.users.sync-from-webhook') }}"
          data-confirm="Lancer la synchronisation depuis le webhook ? Les nouveaux comptes seront créés avec le rôle fourni par l'application tierce."
          data-confirm-variant="primary"
          data-confirm-text="Synchroniser">
        @csrf
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn-sync">
                <i class="bi bi-arrow-repeat"></i> Synchroniser les utilisateurs
            </button>
            <a href="{{ route('dashboard') }}" class="btn-sync-outline">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </form>

</div>
@endsection
