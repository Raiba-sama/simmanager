<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIM Manager')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary:       #00574A;
            --primary-dark:  #003d34;
            --primary-mid:   #004a3f;
            --primary-light: #007a68;
            --primary-glow:  rgba(0, 87, 74, 0.12);
            --sidebar-w:     260px;
            --navbar-h:      64px;
            --text:          #0f172a;
            --muted:         #64748b;
            --border:        #e2e8f0;
            --bg:            #f8fafc;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { width: 100%; overflow-x: hidden; scroll-behavior: smooth; }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ═══════════════════════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            left: 0; top: 0;
            height: 100vh;
            width: var(--sidebar-w);
            background: var(--primary);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
        }

        .sidebar::-webkit-scrollbar { width: 3px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 3px; }

        .sidebar-logo {
            height: var(--navbar-h);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            background: rgba(0,0,0,0.18);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }

        .sidebar-logo img {
            max-height: 40px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .sidebar-menu { padding: 14px 0 8px; flex: 1; }

        .sidebar-section { margin-bottom: 4px; }

        .sidebar-section-title {
            padding: 10px 20px 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
        }

        .sidebar-menu-item { padding: 0 10px; margin-bottom: 1px; }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            color: rgba(255,255,255,0.70);
            text-decoration: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            transition: background 0.18s, color 0.18s;
            position: relative;
        }

        .sidebar-menu-link:hover {
            background: rgba(255,255,255,0.10);
            color: white;
        }

        .sidebar-menu-link.active {
            background: rgba(255,255,255,0.95);
            color: var(--primary);
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0,0,0,0.18);
        }

        .sidebar-menu-link i {
            font-size: 17px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            color: rgba(255,255,255,0.50);
            transition: color 0.18s;
        }

        .sidebar-menu-link:hover i { color: rgba(255,255,255,0.9); }
        .sidebar-menu-link.active i { color: var(--primary); }

        .sidebar-menu-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.20);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }

        .sidebar-menu-link.active .sidebar-menu-badge {
            background: var(--primary);
            color: white;
        }

        .sidebar-submenu { padding: 2px 0 4px 44px; display: none; }

        .sidebar-menu-item.has-submenu.open .sidebar-submenu { display: block; }

        .sidebar-submenu-chevron {
            margin-left: auto;
            font-size: 11px;
            transition: transform 0.22s;
            opacity: 0.55;
            flex-shrink: 0;
        }
        .sidebar-menu-item.has-submenu.open .sidebar-submenu-chevron { transform: rotate(180deg); }

        /* Parent expanded but a child is active → lighter state, not full white */
        .sidebar-menu-link.active-parent {
            background: rgba(255,255,255,0.12);
            color: white;
            font-weight: 600;
        }
        .sidebar-menu-link.active-parent i { color: rgba(255,255,255,0.85); }

        .sidebar-submenu-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            color: rgba(255,255,255,0.58);
            text-decoration: none;
            font-size: 13px;
            border-radius: 8px;
            transition: all 0.18s;
        }

        .sidebar-submenu-item:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.9); }
        .sidebar-submenu-item.active { background: rgba(255,255,255,0.14); color: white; font-weight: 600; }

        .sidebar-submenu-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: rgba(255,255,255,0.30);
            flex-shrink: 0; transition: background 0.18s;
        }

        .sidebar-submenu-item.active .sidebar-submenu-dot { background: #6ee7b7; }

        .sidebar-submenu-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.18);
            color: white; font-size: 10px; font-weight: 700;
            padding: 1px 6px; border-radius: 10px; min-width: 18px; text-align: center;
        }
        .sidebar-submenu-item.active .sidebar-submenu-badge { background: rgba(110,231,183,0.30); color: #6ee7b7; }

        /* User card at bottom */
        .sidebar-user-card {
            margin: 8px 10px 10px;
            padding: 12px 14px;
            background: rgba(0,0,0,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .sidebar-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 13px;
            overflow: hidden; flex-shrink: 0;
        }

        .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .sidebar-user-name {
            font-size: 12.5px; font-weight: 600; color: white;
            line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 11px; color: rgba(255,255,255,0.48); line-height: 1.2;
        }

        .sidebar-logout-btn {
            margin-left: auto;
            background: rgba(255,255,255,0.08);
            border: none;
            color: rgba(255,255,255,0.60);
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.18s;
            flex-shrink: 0;
        }

        .sidebar-logout-btn:hover { background: rgba(239,68,68,0.22); color: #fca5a5; }

        /* ═══════════════════════════════════════════════════════════
           TOP NAVBAR
        ═══════════════════════════════════════════════════════════ */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--navbar-h);
            background: white;
            border-bottom: 1px solid var(--border);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            box-sizing: border-box;
        }

        /* Search */
        .navbar-search { flex: 0 0 360px; position: relative; }

        .search-container { position: relative; }

        .search-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted); font-size: 14px;
            pointer-events: none;
        }

        .search-kbd {
            position: absolute; right: 10px; top: 50%;
            transform: translateY(-50%);
            display: flex; gap: 3px;
            pointer-events: none;
        }

        .kbd-hint {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 1px 5px;
            font-size: 10px;
            font-weight: 600;
            color: var(--muted);
        }

        .search-input {
            width: 100%;
            padding: 8px 72px 8px 36px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--bg);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .search-input::placeholder { color: #b0bec5; }

        .search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0; right: 0;
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.12);
            max-height: 480px;
            overflow-y: auto;
            z-index: 1001;
        }

        .search-results-section { padding: 6px 0; }

        .search-results-section-title {
            padding: 6px 16px;
            font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--muted); background: var(--bg);
            border-bottom: 1px solid var(--border);
        }

        .search-result-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px;
            text-decoration: none; color: var(--text);
            border-bottom: 1px solid #f8fafc;
            transition: background 0.15s;
        }

        .search-result-item:hover { background: var(--bg); }
        .search-result-item:last-child { border-bottom: none; }

        .search-result-item-title { font-weight: 600; font-size: 13.5px; margin-bottom: 2px; }
        .search-result-item-subtitle { font-size: 12px; color: var(--muted); }

        .search-results-empty { padding: 24px; text-align: center; color: var(--muted); }

        .navbar-spacer { flex: 1; }

        /* Notification bell */
        .navbar-notifications { position: relative; }

        .navbar-notif-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--bg);
            border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--muted); font-size: 17px;
            transition: all 0.18s;
        }

        .navbar-notif-btn:hover { border-color: var(--primary); color: var(--primary); background: white; }

        .navbar-notification-badge {
            position: absolute; top: -4px; right: -4px;
            background: #ef4444; color: white;
            font-size: 10px; font-weight: 700;
            padding: 1px 5px; border-radius: 10px;
            min-width: 18px; text-align: center;
            border: 2px solid white;
            display: none;
        }

        .notifications-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 360px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.13);
            z-index: 1001;
            display: flex; flex-direction: column;
            overflow: hidden;
        }

        .notifications-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }

        .notifications-header h6 { margin: 0; font-weight: 700; font-size: 14px; }

        .btn-mark-all-read {
            background: none; border: none; color: var(--primary);
            font-size: 12px; font-weight: 500; cursor: pointer;
            padding: 4px 8px; border-radius: 6px;
            transition: background 0.15s;
        }

        .btn-mark-all-read:hover { background: var(--primary-glow); }

        .notifications-list { overflow-y: auto; max-height: 360px; }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f8fafc;
            cursor: pointer;
            transition: background 0.15s;
            display: flex; gap: 12px;
            position: relative;
        }

        .notification-item:hover { background: var(--bg); }
        .notification-item:last-child { border-bottom: none; }

        .notification-item.unread { background: #f0fdf8; }

        .notification-item.unread::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px; background: var(--primary);
        }

        .notification-item.urgency-urgent { background: #fef2f2 !important; border-left: 3px solid #ef4444 !important; }
        .notification-item.urgency-high   { background: #fffbeb !important; border-left: 3px solid #f59e0b !important; }

        .notification-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 16px; color: white;
        }

        .notification-content { flex: 1; min-width: 0; }
        .notification-title { font-weight: 600; font-size: 13px; margin-bottom: 3px; }
        .notification-message { font-size: 12px; color: var(--muted); line-height: 1.4; margin-bottom: 3px; }
        .notification-time { font-size: 11px; color: #94a3b8; }

        .notifications-footer {
            padding: 10px 16px; border-top: 1px solid var(--border); text-align: center;
        }

        .notification-empty { padding: 36px 20px; text-align: center; color: #94a3b8; }
        .notification-empty i { font-size: 36px; display: block; margin-bottom: 8px; opacity: 0.4; }

        /* User dropdown */
        .navbar-user { position: relative; }

        .navbar-user-toggle {
            display: flex; align-items: center; gap: 10px;
            cursor: pointer; padding: 6px 10px;
            border-radius: 10px; transition: background 0.18s;
        }

        .navbar-user-toggle:hover { background: var(--bg); }

        .navbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 13px;
            overflow: hidden; flex-shrink: 0;
            border: 2px solid rgba(0,87,74,0.2);
        }

        .navbar-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        .navbar-user-name  { font-size: 13.5px; font-weight: 600; color: var(--text); line-height: 1.2; }
        .navbar-user-role  { font-size: 11px; color: var(--muted); line-height: 1.2; }

        .navbar-user-dropdown-menu {
            position: absolute; top: calc(100% + 8px); right: 0;
            background: white; border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.12);
            min-width: 220px; z-index: 1001;
            display: none; overflow: hidden;
            animation: dropdownIn 0.18s ease-out;
        }

        .navbar-user-dropdown-menu.show { display: block; }

        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .dropdown-header-card {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
        }

        .dropdown-header-name { font-weight: 700; font-size: 13.5px; color: var(--text); }

        .dropdown-role-badge {
            display: inline-block; margin-top: 4px;
            padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }

        .role-admin     { background: #fee2e2; color: #991b1b; }
        .role-validator { background: #dbeafe; color: #1d4ed8; }
        .role-user      { background: #d1fae5; color: #065f46; }

        .navbar-user-dropdown-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 16px;
            color: var(--text); text-decoration: none;
            font-size: 13.5px; font-family: 'Poppins', sans-serif;
            transition: background 0.15s, color 0.15s;
            border: none; background: none;
            width: 100%; text-align: left; cursor: pointer;
        }

        .navbar-user-dropdown-item:hover { background: var(--bg); color: var(--primary); }
        .navbar-user-dropdown-item i { width: 18px; font-size: 15px; color: var(--muted); }
        .navbar-user-dropdown-item:hover i { color: var(--primary); }

        .navbar-user-dropdown-divider { height: 1px; background: var(--border); margin: 3px 0; }

        /* ═══════════════════════════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════════════════════════ */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--navbar-h);
            min-height: calc(100vh - var(--navbar-h));
            background: var(--bg);
            padding: 28px 28px 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-width: 0;
            width: calc(100% - var(--sidebar-w));
            overflow-x: hidden;
        }

        .main-content .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            flex: 1;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 21px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
            letter-spacing: -0.3px;
        }

        .page-content-wrapper { animation: fadeInUp 0.35s ease-out; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        .main-footer {
            margin-top: 40px;
            padding: 16px 0;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #b0bec5;
        }

        /* ═══════════════════════════════════════════════════════════
           CARDS, BUTTONS, TABLES, FORMS
        ═══════════════════════════════════════════════════════════ */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            background: white;
            margin-bottom: 20px;
            transition: box-shadow 0.25s, border-color 0.25s;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.09);
            border-color: rgba(0,87,74,0.12);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            border-radius: 12px 12px 0 0;
            font-weight: 600; font-size: 14.5px;
        }

        .card-body { padding: 20px; }

        .btn {
            font-weight: 500; border-radius: 8px;
            padding: 8px 16px; font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            transition: transform 0.18s, box-shadow 0.18s;
        }

        .btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.12); }
        .btn:active { transform: translateY(0); }

        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-mid); border-color: var(--primary-mid); }

        .table tbody tr { transition: background-color 0.15s; }
        .table tbody tr:hover { background-color: #f8fafc !important; }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        .form-check-input:focus   { box-shadow: 0 0 0 3px var(--primary-glow); }

        .form-control.is-invalid { border-color: #ef4444; }
        .form-control.is-valid   { border-color: #10b981; }

        a { transition: color 0.15s; }

        *:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; }

        .badge { display: inline-flex; align-items: center; gap: 3px; }

        .modal-content {
            border-radius: 14px; border: none;
            animation: modalIn 0.25s ease-out;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .dropdown-menu {
            border-radius: 10px;
            border: 1px solid var(--border);
            box-shadow: 0 6px 20px rgba(0,0,0,0.10);
        }

        .pagination .page-link { transition: transform 0.15s, box-shadow 0.15s; }
        .pagination .page-link:hover { transform: translateY(-1px); }

        /* Loading */
        .loading-spinner {
            display: inline-block;
            width: 15px; height: 15px;
            border: 2px solid rgba(0,87,74,0.2);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeletonLoad 1.5s ease-in-out infinite;
            border-radius: 4px;
        }

        @keyframes skeletonLoad {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Toast container */
        #toast-container {
            position: fixed;
            top: 80px; right: 20px;
            z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
            max-width: 380px;
        }

        /* Tooltips */
        [data-tooltip] { position: relative; cursor: help; }
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute; bottom: 100%; left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background: var(--text); color: white;
            border-radius: 6px; font-size: 12px;
            white-space: nowrap; z-index: 1002;
            margin-bottom: 6px; pointer-events: none;
        }

        /* ── Hamburger ──────────────────────────────────────────── */
        .navbar-hamburger {
            display: none;
            align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--bg); border: 1.5px solid var(--border);
            color: var(--muted); font-size: 22px; cursor: pointer;
            flex-shrink: 0; transition: all 0.18s;
        }
        .navbar-hamburger:hover { border-color: var(--primary); color: var(--primary); }

        /* ── Sidebar backdrop ────────────────────────────────────── */
        .sidebar-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.46); z-index: 998;
        }
        .sidebar-backdrop.show { display: block; }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            .navbar-user-name, .navbar-user-role { display: none; }
            .navbar-search { flex: 0 0 260px; }
        }

        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.28s ease;
                z-index: 1000;
            }
            .sidebar.show { transform: translateX(0); }
            .navbar-hamburger { display: flex; }
            .main-content { margin-left: 0; padding: 20px 16px 0; width: 100%; }
            .top-navbar { left: 0; padding: 0 14px; gap: 8px; }
            .navbar-search { flex: 1; }
            .search-kbd { display: none; }
            .card-body { padding: 14px; }
            .modal-dialog { margin: 10px; }
            .page-header { gap: 8px; }
            .page-title { font-size: 18px; }
        }

        @media (max-width: 479px) {
            .main-content { padding: 16px 12px 0; }
            .top-navbar { padding: 0 12px; }
            .navbar-search { display: none; }
            .page-title { font-size: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="d-flex">

    {{-- ═══════════════════════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════════════════════ --}}
    @php
        $authUser   = auth()->user();
        $nameParts  = explode(' ', trim($authUser->full_name ?? ''));
        $initials   = strtoupper(
            substr($nameParts[0] ?? 'U', 0, 1) .
            (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
        );
    @endphp

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-logo">
            <img src="{{ asset('images/acep_madagascar_logo-1.png') }}" alt="ACEP Madagascar">
        </div>

        <nav class="sidebar-menu">

            {{-- Principal --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title">Principal</div>

                <div class="sidebar-menu-item">
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="sidebar-menu-item has-submenu {{ request()->routeIs('sim-requests.*') ? 'open' : '' }}">
                    <a href="{{ route('sim-requests.index') }}"
                       class="sidebar-menu-link {{ request()->routeIs('sim-requests.*') ? 'active-parent' : '' }}">
                        <i class="bi bi-envelope"></i>
                        <span>Demandes</span>
                        @if($authUser->isValidator())
                            @php
                                $pendingCount = \App\Models\SimRequest::where('status', 'en_attente')
                                    ->where('created_by', '!=', auth()->id())
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="sidebar-menu-badge">{{ $pendingCount }}</span>
                            @endif
                        @endif
                        <i class="bi bi-chevron-down sidebar-submenu-chevron"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <a href="{{ route('sim-requests.index', ['status' => 'en_attente']) }}"
                           class="sidebar-submenu-item {{ request('status') === 'en_attente' ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span> En attente
                        </a>
                        <a href="{{ route('sim-requests.index', ['status' => 'validee']) }}"
                           class="sidebar-submenu-item {{ request('status') === 'validee' ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span> Validées
                        </a>
                        <a href="{{ route('sim-requests.index', ['status' => 'rejetee']) }}"
                           class="sidebar-submenu-item {{ request('status') === 'rejetee' ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span> Rejetées
                        </a>
                    </div>
                </div>

                <div class="sidebar-menu-item">
                    <a href="{{ route('sims.index') }}"
                       class="sidebar-menu-link {{ request()->routeIs('sims.*') ? 'active' : '' }}">
                        <i class="bi bi-phone"></i>
                        <span>SIMs</span>
                    </a>
                </div>

                <div class="sidebar-menu-item has-submenu {{ request()->routeIs('notifications.*') ? 'open' : '' }}">
                    @php $unreadCount = $authUser->unreadNotifications()->count(); @endphp
                    <a href="{{ route('notifications.all') }}"
                       class="sidebar-menu-link {{ request()->routeIs('notifications.*') ? 'active-parent' : '' }}">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        @if($unreadCount > 0)
                            <span class="sidebar-menu-badge">{{ $unreadCount }}</span>
                        @endif
                        <i class="bi bi-chevron-down sidebar-submenu-chevron"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <a href="{{ route('notifications.all') }}"
                           class="sidebar-submenu-item {{ request()->routeIs('notifications.all') ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span>
                            Toutes les notifications
                            @if($unreadCount > 0)
                                <span class="sidebar-menu-badge" style="margin-left:auto;">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>

            {{-- Documents & Mail --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title">Documents & Mail</div>

                <div class="sidebar-menu-item">
                    <a href="{{ route('documents.index') }}"
                       class="sidebar-menu-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark"></i>
                        <span>Documents</span>
                    </a>
                </div>

                <div class="sidebar-menu-item">
                    <a href="{{ route('mail-sent.index') }}"
                       class="sidebar-menu-link {{ request()->routeIs('mail-sent.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope-check"></i>
                        <span>Check Mail</span>
                    </a>
                </div>
            </div>

            {{-- Administration --}}
            @if($authUser->isAdmin())
            <div class="sidebar-section">
                <div class="sidebar-section-title">Administration</div>

                @php $isAdminRoute = request()->is('admin/*') || request()->routeIs('sync-users*'); @endphp
                <div class="sidebar-menu-item has-submenu {{ $isAdminRoute ? 'open' : '' }}">
                    <a href="#" onclick="event.preventDefault(); toggleSubmenu(this)"
                       class="sidebar-menu-link {{ $isAdminRoute ? 'active-parent' : '' }}">
                        <i class="bi bi-shield-check"></i>
                        <span>Administration</span>
                        <i class="bi bi-chevron-down sidebar-submenu-chevron"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <a href="/admin/users"
                           class="sidebar-submenu-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span> Utilisateurs
                        </a>
                        <a href="{{ route('sync-users.form') }}"
                           class="sidebar-submenu-item {{ request()->routeIs('sync-users*') ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span> Sync utilisateurs
                        </a>
                        <a href="/admin/activity-logs"
                           class="sidebar-submenu-item {{ request()->is('admin/activity-logs*') ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span> Logs d'activité
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </nav>

        {{-- User card + logout --}}
        <div class="sidebar-user-card">
            <div class="sidebar-avatar">
                @if($authUser->avatar_url)
                    <img src="{{ $authUser->avatar_url }}" alt="{{ $authUser->full_name }}">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div style="flex:1;min-width:0;">
                <div class="sidebar-user-name">{{ $authUser->full_name }}</div>
                <div class="sidebar-user-role">
                    @if($authUser->isAdmin()) Administrateur
                    @elseif($authUser->isValidator()) Validateur
                    @else Utilisateur
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Déconnexion">
                    <i class="bi bi-box-arrow-right" style="font-size:15px;"></i>
                </button>
            </form>
        </div>

    </aside>

    {{-- ═══════════════════════════════════════════════════════════
         TOP NAVBAR
    ═══════════════════════════════════════════════════════════ --}}
    <nav class="top-navbar">

        {{-- Hamburger (mobile) --}}
        <button class="navbar-hamburger" id="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>

        {{-- Global Search --}}
        <div class="navbar-search">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text"
                       id="global-search"
                       class="search-input"
                       placeholder="Rechercher..."
                       autocomplete="off">
                <div class="search-kbd">
                    <span class="kbd-hint">Ctrl</span>
                    <span class="kbd-hint">K</span>
                </div>
                <div id="search-results" class="search-results" style="display:none;"></div>
            </div>
        </div>

        <div class="navbar-spacer"></div>

        {{-- Notifications --}}
        <div class="navbar-notifications" onclick="toggleNotificationsDropdown()">
            <button class="navbar-notif-btn" type="button" title="Notifications">
                <i class="bi bi-bell"></i>
            </button>
            <span class="navbar-notification-badge" id="notification-badge"></span>

            <div class="notifications-dropdown" id="notificationsDropdown" style="display:none;">
                <div class="notifications-header">
                    <h6>Notifications</h6>
                    <button type="button" onclick="markAllAsRead()" class="btn-mark-all-read">
                        Tout marquer comme lu
                    </button>
                </div>
                <div class="notifications-list" id="notifications-list">
                    <div style="padding:20px;text-align:center;color:#94a3b8;font-size:13px;">
                        Chargement...
                    </div>
                </div>
                <div class="notifications-footer">
                    <a href="#" onclick="event.preventDefault();viewAllNotifications();"
                       style="text-decoration:none;color:var(--primary);font-size:12.5px;font-weight:500;">
                        Voir toutes les notifications
                    </a>
                </div>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="navbar-user">
            <div class="navbar-user-toggle" onclick="toggleUserDropdown()">
                <div class="navbar-avatar">
                    @if($authUser->avatar_url)
                        <img src="{{ $authUser->avatar_url }}" alt="{{ $authUser->full_name }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div>
                    <div class="navbar-user-name">{{ $authUser->full_name }}</div>
                    <div class="navbar-user-role">
                        @if($authUser->isAdmin()) Administrateur
                        @elseif($authUser->isValidator()) Validateur
                        @else Utilisateur
                        @endif
                    </div>
                </div>
                <i class="bi bi-chevron-down" style="font-size:11px;color:var(--muted);"></i>
            </div>

            <div class="navbar-user-dropdown-menu" id="userDropdown">
                <div class="dropdown-header-card">
                    <div class="dropdown-header-name">{{ $authUser->full_name }}</div>
                    <span class="dropdown-role-badge
                        @if($authUser->isAdmin()) role-admin
                        @elseif($authUser->isValidator()) role-validator
                        @else role-user
                        @endif">
                        @if($authUser->isAdmin()) Administrateur
                        @elseif($authUser->isValidator()) Validateur
                        @else Utilisateur
                        @endif
                    </span>
                </div>
                <a href="{{ route('profile.edit') }}" class="navbar-user-dropdown-item">
                    <i class="bi bi-person"></i> Mon profil
                </a>
                <a href="{{ route('profile.edit') }}#password" class="navbar-user-dropdown-item">
                    <i class="bi bi-key"></i> Modifier le mot de passe
                </a>
                <a href="{{ route('profile.edit') }}#settings" class="navbar-user-dropdown-item">
                    <i class="bi bi-gear"></i> Paramètres
                </a>
                <div class="navbar-user-dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="navbar-user-dropdown-item" style="color:#ef4444;">
                        <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>

    </nav>

    {{-- ═══════════════════════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════════════════════ --}}
    <main class="main-content">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="page-header">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-actions')
                    <div>@yield('page-actions')</div>
                @endif
            </div>

            {{-- Toast container --}}
            <div id="toast-container"></div>

            {{-- Session alerts → toasts --}}
            @if(session('success'))
                <script>document.addEventListener('DOMContentLoaded',function(){showToast(@json(session('success')),'success');});</script>
            @endif
            @if(session('warning'))
                <script>document.addEventListener('DOMContentLoaded',function(){showToast(@json(session('warning')),'warning');});</script>
            @endif
            @if(session('error'))
                <script>document.addEventListener('DOMContentLoaded',function(){showToast(@json(session('error')),'error');});</script>
            @endif
            @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        @foreach($errors->all() as $error)
                            showToast('{{ $error }}', 'error');
                        @endforeach
                    });
                </script>
            @endif

            {{-- Page Content --}}
            <div class="page-content-wrapper">
                @yield('content')
            </div>

        </div>

        <footer class="main-footer">
            <span>&copy; {{ date('Y') }} ACEP Madagascar &mdash; SIM Manager</span>
            <span>v1.0</span>
        </footer>
    </main>

</div><!-- /.d-flex -->
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="closeSidebar()"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Sidebar toggle (mobile) ──────────────────────────────────────
function toggleSidebar() {
    const s = document.getElementById('sidebar');
    const b = document.getElementById('sidebar-backdrop');
    const open = s.classList.toggle('show');
    b.classList.toggle('show', open);
    document.body.style.overflow = open ? 'hidden' : '';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebar-backdrop').classList.remove('show');
    document.body.style.overflow = '';
}

// ── User Dropdown ────────────────────────────────────────────────
function toggleUserDropdown() {
    document.getElementById('userDropdown').classList.toggle('show');
}

document.addEventListener('click', function(e) {
    const dd     = document.getElementById('userDropdown');
    const toggle = document.querySelector('.navbar-user-toggle');
    if (dd && toggle && !toggle.contains(e.target) && !dd.contains(e.target)) {
        dd.classList.remove('show');
    }
});

// Auto-expand active submenu on load (fallback for dynamic routes)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sidebar-menu-item.has-submenu').forEach(function(item) {
        if (item.querySelector('.sidebar-submenu-item.active')) {
            item.classList.add('open');
        }
    });
});

function toggleSubmenu(link) {
    link.closest('.sidebar-menu-item').classList.toggle('open');
}

// ── Global Search ────────────────────────────────────────────────
let _searchTimer;
const _searchInput   = document.getElementById('global-search');
const _searchResults = document.getElementById('search-results');

if (_searchInput) {
    _searchInput.addEventListener('input', function(e) {
        const raw = e.target.value.trim();
        clearTimeout(_searchTimer);

        if (raw.length < 2) { _searchResults.style.display = 'none'; return; }

        _searchTimer = setTimeout(() => {
            let type = 'all', q = raw;
            if (raw.toLowerCase().startsWith('req:'))       { type = 'requests'; q = raw.slice(4).trim(); }
            else if (raw.toLowerCase().startsWith('sim:'))  { type = 'sims';     q = raw.slice(4).trim(); }
            else if (raw.toLowerCase().startsWith('user:')) { type = 'users';    q = raw.slice(5).trim(); }

            if (q.length < 2) { _searchResults.style.display = 'none'; return; }

            fetch(`{{ route('search') }}?q=${encodeURIComponent(q)}&type=${type}`)
                .then(r => r.json())
                .then(data => _renderSearch(data))
                .catch(err => console.error('Search error:', err));
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!_searchInput.contains(e.target) && !_searchResults.contains(e.target)) {
            _searchResults.style.display = 'none';
        }
    });
}

function _renderSearch(data) {
    const has = (data.requests?.length > 0) || (data.sims?.length > 0) || (data.users?.length > 0);
    if (!has) {
        _searchResults.innerHTML = '<div class="search-results-empty">Aucun résultat trouvé</div>';
        _searchResults.style.display = 'block';
        return;
    }

    const SC = {
        en_attente:'#f59e0b', validee:'#10b981', rejetee:'#ef4444',
        demande_envoyee:'#06b6d4', pending:'#06b6d4',
        libre:'#10b981', attribue:'#3b82f6', suspendu:'#ef4444',
    };
    const RC = { admin:'#ef4444', validator:'#3b82f6', user:'#10b981' };

    let html = '', total = 0;

    if (data.requests?.length) {
        total += data.requests.length;
        html += `<div class="search-results-section">
            <div class="search-results-section-title"><i class="bi bi-envelope"></i> Demandes (${data.requests.length})</div>`;
        data.requests.forEach(i => {
            html += `<a href="${i.url}" class="search-result-item">
                <span style="width:8px;height:8px;border-radius:50%;background:${SC[i.status]||'#6b7280'};flex-shrink:0;"></span>
                <div style="flex:1;min-width:0;">
                    <div class="search-result-item-title">${i.request_number}</div>
                    <div class="search-result-item-subtitle">${i.user_name} &bull; ${i.type_label||i.type} &bull; ${i.status_label||i.status}</div>
                </div></a>`;
        });
        html += '</div>';
    }

    if (data.sims?.length) {
        total += data.sims.length;
        html += `<div class="search-results-section">
            <div class="search-results-section-title"><i class="bi bi-phone"></i> SIMs (${data.sims.length})</div>`;
        data.sims.forEach(i => {
            html += `<a href="${i.url}" class="search-result-item">
                <span style="width:8px;height:8px;border-radius:50%;background:${SC[i.status]||'#6b7280'};flex-shrink:0;"></span>
                <div style="flex:1;min-width:0;">
                    <div class="search-result-item-title">${i.iccid}</div>
                    <div class="search-result-item-subtitle">${i.phone_number||'N/A'} &bull; ${i.status_label||i.status} &bull; ${i.assigned_to||'Non attribuée'}</div>
                </div></a>`;
        });
        html += '</div>';
    }

    if (data.users?.length) {
        total += data.users.length;
        html += `<div class="search-results-section">
            <div class="search-results-section-title"><i class="bi bi-person"></i> Utilisateurs (${data.users.length})</div>`;
        data.users.forEach(i => {
            html += `<a href="${i.url}" class="search-result-item">
                <span style="width:8px;height:8px;border-radius:50%;background:${RC[i.role]||'#6b7280'};flex-shrink:0;"></span>
                <div style="flex:1;min-width:0;">
                    <div class="search-result-item-title">${i.name}</div>
                    <div class="search-result-item-subtitle">${i.email} &bull; ${i.matricule} &bull; ${i.role_label||i.role}</div>
                </div></a>`;
        });
        html += '</div>';
    }

    html += `<div style="padding:8px 12px;border-top:1px solid var(--border);font-size:11px;color:var(--muted);text-align:center;">
        ${total} résultat${total > 1 ? 's' : ''} trouvé${total > 1 ? 's' : ''}
    </div>`;

    _searchResults.innerHTML = html;
    _searchResults.style.display = 'block';
}

// ── Keyboard shortcuts ───────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        _searchInput?.focus(); _searchInput?.select();
    }
    if (e.key === 'Escape') {
        if (_searchResults) _searchResults.style.display = 'none';
        document.getElementById('userDropdown')?.classList.remove('show');
        const nd = document.getElementById('notificationsDropdown');
        if (nd) nd.style.display = 'none';
        closeSidebar();
    }
    if (!['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) {
        if ((e.ctrlKey||e.metaKey) && e.key==='1') { e.preventDefault(); location.href='{{ route('dashboard') }}'; }
        if ((e.ctrlKey||e.metaKey) && e.key==='2') { e.preventDefault(); location.href='{{ route('sim-requests.index') }}'; }
        if ((e.ctrlKey||e.metaKey) && e.key==='3') { e.preventDefault(); location.href='{{ route('sims.index') }}'; }
        if ((e.ctrlKey||e.metaKey) && e.key==='p') { e.preventDefault(); location.href='{{ route('profile.edit') }}'; }
    }
});

// ── Toast System ─────────────────────────────────────────────────
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const C = {
        success: { border:'#10b981', icon:'bi-check-circle-fill',          bg:'#f0fdf4' },
        error:   { border:'#ef4444', icon:'bi-x-circle-fill',               bg:'#fef2f2' },
        warning: { border:'#f59e0b', icon:'bi-exclamation-triangle-fill',   bg:'#fffbeb' },
        info:    { border:'#3b82f6', icon:'bi-info-circle-fill',            bg:'#eff6ff' },
    };
    const cfg = C[type] || C.info;

    const toast = document.createElement('div');
    toast.style.cssText = `background:${cfg.bg};border-left:4px solid ${cfg.border};border-radius:12px;
        box-shadow:0 4px 16px rgba(0,0,0,0.12);padding:13px 16px;
        display:flex;align-items:center;gap:12px;
        min-width:280px;max-width:380px;animation:_toastIn 0.3s ease-out;`;

    const ic = document.createElement('i');
    ic.className = `bi ${cfg.icon}`;
    ic.style.cssText = `font-size:18px;color:${cfg.border};flex-shrink:0;`;

    const tx = document.createElement('div');
    tx.style.cssText = 'flex:1;color:#1e293b;font-size:13.5px;font-weight:500;line-height:1.5;';
    tx.textContent = message;

    const cl = document.createElement('button');
    cl.innerHTML = '<i class="bi bi-x"></i>';
    cl.style.cssText = 'background:none;border:none;color:#94a3b8;cursor:pointer;padding:2px;font-size:16px;flex-shrink:0;';
    cl.onclick = () => _removeToast(toast);

    toast.append(ic, tx, cl);
    container.appendChild(toast);
    if (duration > 0) setTimeout(() => _removeToast(toast), duration);
}

function _removeToast(toast) {
    toast.style.animation = '_toastOut 0.28s ease-in';
    setTimeout(() => toast.parentNode?.removeChild(toast), 260);
}

const _ts = document.createElement('style');
_ts.textContent = `
    @keyframes _toastIn  { from { transform:translateX(100%); opacity:0; } to { transform:translateX(0); opacity:1; } }
    @keyframes _toastOut { from { transform:translateX(0); opacity:1; }   to { transform:translateX(100%); opacity:0; } }
`;
document.head.appendChild(_ts);
window.showToast = showToast;

// ── Notification System ──────────────────────────────────────────
const _notifDropdown  = document.getElementById('notificationsDropdown');
const _notifBadge     = document.getElementById('notification-badge');
const _notifList      = document.getElementById('notifications-list');
let   _notifInterval;

function toggleNotificationsDropdown() {
    const visible = _notifDropdown.style.display !== 'none';
    if (!visible) {
        _notifDropdown.style.display = 'flex';
        _loadNotifications();
        _notifInterval = setInterval(_refreshBadge, 30000);
    } else {
        _notifDropdown.style.display = 'none';
        clearInterval(_notifInterval);
    }
}

function _loadNotifications() {
    fetch('{{ route('notifications.index') }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => { _updateBadge(data.unread_count); _renderNotifications(data.notifications); })
    .catch(() => {});
}

function _updateBadge(count) {
    if (!_notifBadge) return;
    if (count > 0) {
        _notifBadge.textContent = count > 99 ? '99+' : count;
        _notifBadge.style.display = 'block';
    } else {
        _notifBadge.style.display = 'none';
    }
}

function _refreshBadge() {
    fetch('{{ route('notifications.unread-count') }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => _updateBadge(data.count))
    .catch(() => {});
}

function _renderNotifications(notifications) {
    if (!_notifList) return;
    if (!notifications.length) {
        _notifList.innerHTML = `<div class="notification-empty">
            <i class="bi bi-bell-slash"></i><div>Aucune notification</div>
        </div>`;
        return;
    }

    let html = '';
    notifications.forEach(n => {
        const d = n.data;
        const isUnread   = !n.read_at;
        const isReminder = d.type === 'request_reminder';
        const urgency    = isReminder && d.urgency_level ? `urgency-${d.urgency_level}` : '';

        let url = d.url || '#';
        if (url !== '#' && url.startsWith('http')) {
            try { const u = new URL(url); url = u.pathname + u.search; } catch(e) {}
        }
        const safeUrl = url.replace(/"/g, '&quot;');

        html += `<div class="notification-item ${isUnread ? 'unread' : ''} ${urgency}"
                     data-url="${safeUrl}"
                     onclick="markAsRead('${n.id}','${safeUrl}',event)">
            <div class="notification-icon" style="background:${d.color||'var(--primary)'};">
                <i class="bi ${d.icon||'bi-bell'}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${d.title||'Notification'}</div>
                <div class="notification-message">${d.message||''}</div>
                ${isReminder && d.days_pending
                    ? `<div style="font-size:11px;color:#94a3b8;margin-top:2px;"><i class="bi bi-clock-history"></i> En attente depuis ${d.days_pending} jour(s)</div>`
                    : ''}
                <div class="notification-time">${_timeAgo(n.created_at)}</div>
            </div>
        </div>`;
    });
    _notifList.innerHTML = html;
}

function markAsRead(id, url, event) {
    if (event) { event.preventDefault(); event.stopPropagation(); }
    if (!url || url === '#') url = event?.currentTarget?.getAttribute('data-url') || url;
    if (url) url = url.replace(/\\'/g,"'").replace(/&quot;/g,'"');

    fetch(`{{ route('notifications.mark-read', '') }}/${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(data => {
        if (data.success) {
            _loadNotifications();
            let dest = data.url || url;
            if (dest?.startsWith('http')) {
                try { const u = new URL(dest); dest = u.pathname + u.search; } catch(e) {}
            }
            if (dest && dest !== '#' && dest !== 'undefined') {
                _notifDropdown.style.display = 'none';
                location.href = dest;
            }
        }
    })
    .catch(() => { if (url && url !== '#') location.href = url; });
}

function markAllAsRead() {
    fetch('{{ route('notifications.mark-all-read') }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) _loadNotifications(); })
    .catch(() => {});
}

function viewAllNotifications() { location.href = '{{ route('notifications.all') }}'; }

function _timeAgo(ds) {
    const s = Math.floor((Date.now() - new Date(ds)) / 1000);
    if (s < 60)     return 'À l\'instant';
    if (s < 3600)   return `Il y a ${Math.floor(s/60)} min`;
    if (s < 86400)  return `Il y a ${Math.floor(s/3600)} h`;
    if (s < 604800) return `Il y a ${Math.floor(s/86400)} j`;
    return new Date(ds).toLocaleDateString('fr-FR', { day:'numeric', month:'short' });
}

// Load badge on page load + close on outside click
document.addEventListener('DOMContentLoaded', function() {
    _refreshBadge();

    document.addEventListener('click', function(e) {
        const nc = document.querySelector('.navbar-notifications');
        if (nc && !nc.contains(e.target)) {
            _notifDropdown.style.display = 'none';
            clearInterval(_notifInterval);
        }
    });
});
</script>

<!-- Global Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" data-confirm-title>Confirmer l'action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" data-confirm-message>Êtes-vous sûr de vouloir effectuer cette action ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" data-confirm-action>Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('confirmModal');
    let modal = null, confirmCb = null;

    if (modalEl && typeof bootstrap !== 'undefined') {
        modal = new bootstrap.Modal(modalEl);

        modalEl.querySelector('[data-confirm-action]')?.addEventListener('click', function() {
            modal.hide();
            if (typeof confirmCb === 'function') { const cb = confirmCb; confirmCb = null; cb(); }
        });

        modalEl.addEventListener('hidden.bs.modal', () => { confirmCb = null; });
    }

    window.showConfirmModal = function(message, onConfirm, opts = {}) {
        if (!modal) return;
        const titleEl  = modalEl.querySelector('[data-confirm-title]');
        const msgEl    = modalEl.querySelector('[data-confirm-message]');
        const actionEl = modalEl.querySelector('[data-confirm-action]');
        if (titleEl)  titleEl.textContent  = opts.title       || 'Confirmer l\'action';
        if (msgEl)    msgEl.textContent    = message          || 'Êtes-vous sûr ?';
        if (actionEl) {
            actionEl.textContent = opts.confirmText    || 'Confirmer';
            actionEl.className   = `btn btn-${opts.confirmVariant || 'primary'}`;
        }
        confirmCb = onConfirm;
        modal.show();
    };

    // Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    document.querySelectorAll('.btn[title]').forEach(btn => {
        if (!btn.getAttribute('data-bs-toggle')) {
            btn.setAttribute('data-bs-toggle', 'tooltip');
            new bootstrap.Tooltip(btn);
        }
    });

    // Explicit data-confirm forms only
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            window.showConfirmModal(
                form.getAttribute('data-confirm') || 'Êtes-vous sûr ?',
                () => form.submit(),
                {
                    confirmText:    form.getAttribute('data-confirm-text')    || 'Confirmer',
                    confirmVariant: form.getAttribute('data-confirm-variant') || 'primary',
                    title: 'Confirmation',
                }
            );
        });
    });

    // Submit loading state
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                const orig = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> En cours...';
                setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 10000);
            }
        });
    });
});
</script>

@stack('scripts')
</body>
</html>
