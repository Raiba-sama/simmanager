<style>
.admin-sidebar-footer {
    padding: 12px 14px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.admin-footer-btn {
    display: flex;
    align-items: center;
    gap: 9px;
    width: 100%;
    padding: 9px 14px;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
    text-decoration: none;
    border: none;
    background: transparent;
}

.admin-footer-btn-dashboard {
    background: rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.90);
}
.admin-footer-btn-dashboard:hover {
    background: rgba(255, 255, 255, 0.20);
    color: white;
}

.admin-footer-btn-logout {
    color: rgba(255, 255, 255, 0.55);
}
.admin-footer-btn-logout:hover {
    background: rgba(239, 68, 68, 0.18);
    color: #fca5a5;
}

.admin-footer-btn svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    opacity: 0.85;
}
</style>

<div class="admin-sidebar-footer">
    <a href="{{ route('dashboard') }}" class="admin-footer-btn admin-footer-btn-dashboard">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>
        Tableau de bord
    </a>

    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="margin:0;">
        @csrf
        <button type="submit" class="admin-footer-btn admin-footer-btn-logout" style="width:100%;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
            </svg>
            Déconnexion
        </button>
    </form>
</div>
