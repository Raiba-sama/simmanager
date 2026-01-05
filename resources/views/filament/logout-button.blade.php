@push('styles')
<style>
    /* Style pour le bouton de déconnexion personnalisé */
    .custom-logout-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
    }
    .custom-logout-button a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background-color: #ef4444;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.2s;
    }
    .custom-logout-button a:hover {
        background-color: #dc2626;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    .custom-logout-button a[href*="dashboard"]:hover {
        background-color: #004d42;
    }
</style>
@endpush

<div class="custom-logout-button">
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="margin: 0;">
        @csrf
        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
            </svg>
            Déconnexion
        </a>
    </form>
    <div style="margin-top: 10px;">
        <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background-color: #00574A; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            Aller au Dashboard
        </a>
    </div>
</div>

