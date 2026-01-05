<style>
    /* Style pour le bouton de déconnexion personnalisé */
    .custom-logout-button {
        position: fixed !important;
        bottom: 20px !important;
        right: 20px !important;
        z-index: 99999 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
    }
    .custom-logout-button form {
        margin: 0 !important;
    }
    .custom-logout-button button,
    .custom-logout-button a {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 12px 20px !important;
        background-color: #ef4444 !important;
        color: white !important;
        text-decoration: none !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s !important;
        border: none !important;
        cursor: pointer !important;
        font-size: 14px !important;
        pointer-events: auto !important;
    }
    .custom-logout-button button:hover,
    .custom-logout-button a:hover {
        background-color: #dc2626 !important;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px) !important;
    }
    .custom-logout-button a[href*="dashboard"] {
        background-color: #00574A !important;
    }
    .custom-logout-button a[href*="dashboard"]:hover {
        background-color: #004d42 !important;
    }
</style>

<div class="custom-logout-button">
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="margin: 0;">
        @csrf
        <button type="submit" style="width: 100%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
            </svg>
            Déconnexion
        </button>
    </form>
    <a href="{{ route('dashboard') }}" style="width: 100%;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>
        Aller au Dashboard
    </a>
</div>

