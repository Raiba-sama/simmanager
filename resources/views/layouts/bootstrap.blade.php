<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion de Cartes SIM')</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #00574A;
            --primary-hover: #004a3f;
            --sidebar-width: 260px;
            --navbar-height: 64px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        /* Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            width: calc(100% - var(--sidebar-width));
            box-sizing: border-box;
        }
        
        .navbar-search {
            flex: 0 0 400px;
            max-width: 400px;
            position: relative;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 16px;
            pointer-events: none;
        }
        
        .search-input {
            width: 100%;
            padding: 8px 12px 8px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #f9fafb;
            transition: all 0.2s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #00574A;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 87, 74, 0.1);
        }
        
        .search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 500px;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .search-results-section {
            padding: 12px 0;
        }
        
        .search-results-section-title {
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background: #f9fafb;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #1e293b;
            transition: background 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        .search-result-item:hover {
            background: #f9fafb;
        }
        
        .search-result-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        
        .search-result-item-icon.requests {
            background: #dbeafe;
            color: #3b82f6;
        }
        
        .search-result-item-icon.sims {
            background: #dcfce7;
            color: #22c55e;
        }
        
        .search-result-item-icon.users {
            background: #fef3c7;
            color: #f59e0b;
        }
        
        .search-result-item-content {
            flex: 1;
            min-width: 0;
        }
        
        .search-result-item-title {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .search-result-item-subtitle {
            font-size: 12px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .search-results-empty {
            padding: 24px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        
        .navbar-user-dropdown {
            position: relative;
        }
        
        .navbar-user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        
        .navbar-user-dropdown-toggle:hover {
            background: #f1f5f9;
        }
        
        .navbar-user-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 220px;
            z-index: 1000;
            display: none;
            overflow: hidden;
        }
        
        .navbar-user-dropdown-menu.show {
            display: block;
        }
        
        .navbar-user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #1e293b;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .navbar-user-dropdown-item:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        
        .navbar-user-dropdown-item i {
            width: 20px;
            color: #64748b;
        }
        
        .navbar-user-dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 4px 0;
        }
        
        .navbar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .navbar-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .navbar-user-info {
            display: flex;
            flex-direction: column;
        }
        
        .navbar-user-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.2;
        }
        
        .navbar-user-role {
            font-size: 11px;
            color: #64748b;
            line-height: 1.2;
        }
        
        .navbar-notifications {
            position: relative;
            margin-right: 16px;
        }
        
        .navbar-notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        .notifications-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .notifications-header {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notifications-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            gap: 12px;
            position: relative;
        }
        
        .notification-item:hover {
            background: #f9fafb;
        }
        
        .notification-item.unread {
            background: #f0f9ff;
        }
        
        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #00574A;
        }
        
        .notification-item.urgency-urgent {
            background: #fef2f2 !important;
            border-left: 3px solid #ef4444 !important;
        }
        
        .notification-item.urgency-high {
            background: #fffbeb !important;
            border-left: 3px solid #f59e0b !important;
        }
        
        .notification-item.urgency-normal {
            background: #eff6ff !important;
            border-left: 3px solid #3b82f6 !important;
        }
        
        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
            color: white;
        }
        
        .notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 4px;
        }
        
        .notification-message {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        
        .notification-time {
            font-size: 11px;
            color: #94a3b8;
        }
        
        .notifications-footer {
            padding: 12px 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #94a3b8;
        }
        
        .notification-empty i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
            z-index: 1000;
            overflow-y: auto;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-logo {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            height: var(--navbar-height);
            box-sizing: border-box;
        }
        
        .sidebar-logo img {
            max-width: 100%;
            height: auto;
            max-height: 50px;
            object-fit: contain;
        }
        
        .sidebar-menu {
            padding: 12px 0;
            flex: 1;
            overflow-y: auto;
        }
        
        .sidebar-menu-item {
            padding: 0 12px;
            margin-bottom: 2px;
        }
        
        .sidebar-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .sidebar-menu-link:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        
        .sidebar-menu-link.active {
            background: var(--primary-color);
            color: white;
        }
        
        .sidebar-menu-link.active i {
            color: white;
        }
        
        .sidebar-menu-link i {
            font-size: 18px;
            width: 20px;
            color: #64748b;
            transition: color 0.2s;
        }
        
        .sidebar-menu-link.active i {
            color: white;
        }
        
        .sidebar-menu-badge {
            margin-left: auto;
            background: var(--primary-color);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }
        
        .sidebar-submenu {
            padding-left: 44px;
            margin-top: 2px;
            display: none;
        }
        
        .sidebar-menu-item.has-submenu.active .sidebar-submenu {
            display: block;
        }
        
        .sidebar-submenu-item {
            padding: 8px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        
        .sidebar-submenu-item:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        
        .sidebar-submenu-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .sidebar-submenu-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--primary-color);
        }
        
        .sidebar-logout {
            margin-top: auto;
            padding: 12px;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        
        .sidebar-logout-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .sidebar-logout-link:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            min-height: calc(100vh - var(--navbar-height));
            background: #f8fafc;
            padding: 24px;
            width: calc(100% - var(--sidebar-width));
            max-width: none;
            box-sizing: border-box;
        }
        
        .main-content .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        .page-header {
            margin-bottom: 24px;
        }
        
        .page-title {
            font-size: 22px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            background: white;
            margin-bottom: 20px;
            transition: box-shadow 0.2s;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: 10px 10px 0 0;
            font-weight: 600;
            font-size: 15px;
            color: #1e293b;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Buttons */
        .btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-navbar {
                left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <img src="{{ asset('images/acep_madagascar_logo-1.png') }}" alt="ACEP Madagascar Logo">
            </div>
            
            <!-- Menu -->
            <nav class="sidebar-menu">
                <div class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="sidebar-menu-item {{ request()->routeIs('sim-requests.*') ? 'active has-submenu' : '' }}">
                    <a href="{{ route('sim-requests.index') }}" class="sidebar-menu-link {{ request()->routeIs('sim-requests.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope"></i>
                        <span>Demandes</span>
                        @if(auth()->user()->isValidator())
                            @php
                                $pendingCount = \App\Models\SimRequest::where('status', 'en_attente')
                                    ->where('request_type', 'recuperation')
                                    ->where('created_by', '!=', auth()->id())
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="sidebar-menu-badge">{{ $pendingCount }}</span>
                            @endif
                        @endif
                    </a>
                    <div class="sidebar-submenu">
                        <a href="{{ route('sim-requests.index', ['status' => 'en_attente']) }}" class="sidebar-submenu-item {{ request('status') === 'en_attente' ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span>
                            <span>En attente</span>
                        </a>
                        <a href="{{ route('sim-requests.index', ['status' => 'validee']) }}" class="sidebar-submenu-item {{ request('status') === 'validee' ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span>
                            <span>Validées</span>
                        </a>
                        <a href="{{ route('sim-requests.index', ['status' => 'rejetee']) }}" class="sidebar-submenu-item {{ request('status') === 'rejetee' ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span>
                            <span>Rejetées</span>
                        </a>
                    </div>
                </div>
                
                <div class="sidebar-menu-item {{ request()->routeIs('sims.*') ? 'active' : '' }}">
                    <a href="{{ route('sims.index') }}" class="sidebar-menu-link {{ request()->routeIs('sims.*') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <span>SIMs</span>
                    </a>
                </div>
                
                <div class="sidebar-menu-item {{ request()->routeIs('notifications.*') ? 'active has-submenu' : '' }}">
                    <a href="{{ route('notifications.all') }}" class="sidebar-menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        @php
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="sidebar-menu-badge">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <div class="sidebar-submenu">
                        <a href="{{ route('notifications.all') }}" class="sidebar-submenu-item {{ request()->routeIs('notifications.all') ? 'active' : '' }}">
                            <span class="sidebar-submenu-dot"></span>
                            <span>Toutes les notifications</span>
                            @if($unreadCount > 0)
                                <span class="sidebar-menu-badge" style="margin-left: auto;">{{ $unreadCount }}</span>
                            @endif
                        </a>
            </div>
        </div>
                
                <div class="sidebar-menu-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <a href="{{ route('documents.index') }}" class="sidebar-menu-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark"></i>
                        <span>Documents</span>
                    </a>
                </div>
                
                <div class="sidebar-menu-item {{ request()->routeIs('mail-sent.*') ? 'active' : '' }}">
                    <a href="{{ route('mail-sent.index') }}" class="sidebar-menu-link {{ request()->routeIs('mail-sent.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope-check"></i>
                        <span>Check Mail</span>
                    </a>
                </div>
                
                        @if(auth()->user()->isAdmin())
                <div class="sidebar-menu-item">
                    <a href="/admin/users" class="sidebar-menu-link">
                        <i class="bi bi-people"></i>
                        <span>Utilisateurs</span>
                    </a>
                </div>
                <div class="sidebar-menu-item {{ request()->routeIs('sync-users*') ? 'active' : '' }}">
                    <a href="{{ route('sync-users.form') }}" class="sidebar-menu-link {{ request()->routeIs('sync-users*') ? 'active' : '' }}">
                        <i class="bi bi-cloud-download"></i>
                        <span>Sync utilisateurs (webhook)</span>
                    </a>
                </div>
                <div class="sidebar-menu-item">
                    <a href="/admin/activity-logs" class="sidebar-menu-link">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Logs</span>
                    </a>
                </div>
                        @endif
            </nav>
            
            <!-- Logout -->
            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                    @csrf
                    <button type="submit" class="sidebar-logout-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Log Out</span>
                                    </button>
                                </form>
            </div>
        </aside>
        
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <!-- Global Search -->
            <div class="navbar-search">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" 
                           id="global-search" 
                           class="search-input" 
                           placeholder="Rechercher des demandes, SIMs, utilisateurs... (Ctrl+K)" 
                           autocomplete="off"
                           title="Appuyez sur Ctrl+K pour rechercher rapidement">
                    <div id="search-results" class="search-results" style="display: none;"></div>
                </div>
            </div>
            <div style="flex: 1;"></div>
            <div class="navbar-user">
                <div class="navbar-notifications" onclick="toggleNotificationsDropdown()">
                    <i class="bi bi-bell" style="font-size: 20px; color: #64748b; cursor: pointer;"></i>
                    <span class="navbar-notification-badge" id="notification-badge">0</span>
                    <div class="notifications-dropdown" id="notificationsDropdown" style="display: none;">
                        <div class="notifications-header">
                            <h6 style="margin: 0; font-weight: 600; color: #1e293b;">Notifications</h6>
                            <button type="button" onclick="markAllAsRead()" class="btn-mark-all-read" style="background: none; border: none; color: #00574A; font-size: 12px; cursor: pointer; padding: 0;">
                                Tout marquer comme lu
                            </button>
                        </div>
                        <div class="notifications-list" id="notifications-list">
                            <div class="notification-loading" style="padding: 20px; text-align: center; color: #64748b;">
                                <i class="bi bi-arrow-repeat spin"></i> Chargement...
                            </div>
                        </div>
                        <div class="notifications-footer">
                            <a href="#" onclick="event.preventDefault(); viewAllNotifications();" style="text-decoration: none; color: #00574A; font-size: 12px; font-weight: 500;">
                                Voir toutes les notifications
                            </a>
                        </div>
                    </div>
                </div>
                <div class="navbar-user-dropdown">
                    <div class="navbar-user-dropdown-toggle" onclick="toggleUserDropdown()">
                        @php
                            $user = auth()->user();
                            $userInitials = strtoupper(substr($user->name ?? 'A', 0, 1) . substr($user->first_name ?? 'S', 0, 1));
                        @endphp
                        <div class="navbar-user-avatar">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar }}" alt="{{ $user->full_name }}" 
                                     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                {{ $userInitials }}
                            @endif
                        </div>
                        <div class="navbar-user-info">
                            <div class="navbar-user-name">{{ $user->full_name }}</div>
                            <div class="navbar-user-role">
                                @if($user->isAdmin())
                                    Administrateur
                                @elseif($user->isValidator())
                                    Validateur
                                @else
                                    Utilisateur
                                @endif
                            </div>
                        </div>
                        <i class="bi bi-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="navbar-user-dropdown-menu" id="userDropdown">
                        <a href="{{ route('profile.edit') }}" class="navbar-user-dropdown-item">
                            <i class="bi bi-person"></i>
                            <span>Mon profil</span>
                        </a>
                        <a href="{{ route('profile.edit') }}#password" class="navbar-user-dropdown-item">
                            <i class="bi bi-key"></i>
                            <span>Modifier le mot de passe</span>
                        </a>
                        <div class="navbar-user-dropdown-divider"></div>
                        <a href="{{ route('profile.edit') }}#settings" class="navbar-user-dropdown-item">
                            <i class="bi bi-gear"></i>
                            <span>Paramètres</span>
                        </a>
                        <div class="navbar-user-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="navbar-user-dropdown-item">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
                </div>
            </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    <div class="mt-3">
                        @yield('page-actions')
                    </div>
                </div>

                <!-- Toast Container -->
                <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; max-width: 400px;"></div>
                
                <!-- Alerts (convertis en toasts) -->
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast(@json(session('success')), 'success');
                        });
                    </script>
                @endif

                @if(session('warning'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast(@json(session('warning')), 'warning');
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast(@json(session('error')), 'error');
                        });
                    </script>
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

                <!-- Content -->
                @yield('content')
            </div>
            </main>
        </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // User Dropdown Toggle
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.querySelector('.navbar-user-dropdown-toggle');
            
            if (dropdown && toggle && !toggle.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        // Auto-expand submenu if active
        document.addEventListener('DOMContentLoaded', function() {
            const activeSubmenu = document.querySelector('.sidebar-submenu-item.active');
            if (activeSubmenu) {
                const parent = activeSubmenu.closest('.sidebar-menu-item');
                if (parent) {
                    parent.classList.add('active');
                }
            }
        });
        
        // Global Search
        let searchTimeout;
        const searchInput = document.getElementById('global-search');
        const searchResults = document.getElementById('search-results');
        
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    // Détecter si l'utilisateur veut filtrer par type (ex: "req:123" ou "sim:123")
                    let searchType = 'all';
                    let searchQuery = query;
                    
                    if (query.toLowerCase().startsWith('req:')) {
                        searchType = 'requests';
                        searchQuery = query.substring(4).trim();
                    } else if (query.toLowerCase().startsWith('sim:')) {
                        searchType = 'sims';
                        searchQuery = query.substring(4).trim();
                    } else if (query.toLowerCase().startsWith('user:')) {
                        searchType = 'users';
                        searchQuery = query.substring(5).trim();
                    }
                    
                    if (searchQuery.length < 2) {
                        searchResults.style.display = 'none';
                        return;
                    }
                    
                    fetch(`{{ route('search') }}?q=${encodeURIComponent(searchQuery)}&type=${searchType}`)
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });
            
            // Close search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }
        
        function displaySearchResults(data) {
            const hasResults = (data.requests && data.requests.length > 0) ||
                              (data.sims && data.sims.length > 0) ||
                              (data.users && data.users.length > 0);
            
            if (!hasResults) {
                searchResults.innerHTML = '<div class="search-results-empty">Aucun résultat trouvé</div>';
                searchResults.style.display = 'block';
                return;
            }
            
            let html = '';
            
            let totalResults = 0;
            
            // Demandes
            if (data.requests && data.requests.length > 0) {
                totalResults += data.requests.length;
                html += '<div class="search-results-section">';
                html += `<div class="search-results-section-title"><i class="bi bi-envelope"></i> Demandes <span style="color: #94a3b8; font-size: 11px;">(${data.requests.length})</span></div>`;
                data.requests.forEach(item => {
                    const statusColors = {
                        'en_attente': '#f59e0b',
                        'validee': '#10b981',
                        'rejetee': '#ef4444',
                        'demande_envoyee': '#06b6d4',
                        'pending': '#06b6d4',
                        'accepted': '#10b981',
                        'refused': '#ef4444',
                    };
                    const statusColor = statusColors[item.status] || '#6b7280';
                    html += `<a href="${item.url}" class="search-result-item">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: ${statusColor}; flex-shrink: 0;"></span>
                            <div style="flex: 1;">
                                <div class="search-result-item-title">${item.request_number}</div>
                                <div class="search-result-item-subtitle">${item.user_name} • ${item.type_label || item.type} • ${item.status_label || item.status} • ${item.created_at}</div>
                                ${item.phone_number ? `<div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">📱 ${item.phone_number}</div>` : ''}
    </div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // SIMs
            if (data.sims && data.sims.length > 0) {
                totalResults += data.sims.length;
                html += '<div class="search-results-section">';
                html += `<div class="search-results-section-title"><i class="bi bi-phone"></i> SIMs <span style="color: #94a3b8; font-size: 11px;">(${data.sims.length})</span></div>`;
                data.sims.forEach(item => {
                    const statusColors = {
                        'libre': '#10b981',
                        'attribue': '#3b82f6',
                        'suspendu': '#ef4444',
                    };
                    const statusColor = statusColors[item.status] || '#6b7280';
                    html += `<a href="${item.url}" class="search-result-item">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: ${statusColor}; flex-shrink: 0;"></span>
                            <div style="flex: 1;">
                                <div class="search-result-item-title">${item.iccid}</div>
                                <div class="search-result-item-subtitle">${item.phone_number || 'N/A'} • ${item.status_label || item.status} • ${item.assigned_to || 'Non attribuée'}</div>
                                ${item.operator ? `<div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">📡 ${item.operator}</div>` : ''}
                </div>
            </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // Utilisateurs
            if (data.users && data.users.length > 0) {
                totalResults += data.users.length;
                html += '<div class="search-results-section">';
                html += `<div class="search-results-section-title"><i class="bi bi-person"></i> Utilisateurs <span style="color: #94a3b8; font-size: 11px;">(${data.users.length})</span></div>`;
                data.users.forEach(item => {
                    const roleColors = {
                        'admin': '#ef4444',
                        'validator': '#3b82f6',
                        'user': '#10b981',
                    };
                    const roleColor = roleColors[item.role] || '#6b7280';
                    html += `<a href="${item.url}" class="search-result-item">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: ${roleColor}; flex-shrink: 0;"></span>
                            <div style="flex: 1;">
                                <div class="search-result-item-title">${item.name}</div>
                                <div class="search-result-item-subtitle">${item.email} • ${item.matricule} • ${item.role_label || item.role}</div>
                                ${item.fonction ? `<div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">💼 ${item.fonction}</div>` : ''}
        </div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // Ajouter un footer avec le total
            if (totalResults > 0) {
                html += `<div style="padding: 8px 12px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; text-align: center;">
                    ${totalResults} résultat${totalResults > 1 ? 's' : ''} trouvé${totalResults > 1 ? 's' : ''}
                </div>`;
            }
            
            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }
        
        // Raccourcis clavier globaux
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K pour focus sur la recherche
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('global-search');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Échap pour fermer les dropdowns
            if (e.key === 'Escape') {
                // Fermer la recherche
                const searchResults = document.getElementById('search-results');
                if (searchResults) {
                    searchResults.style.display = 'none';
                }
                
                // Fermer le dropdown utilisateur
                const userDropdown = document.getElementById('userDropdown');
                if (userDropdown) {
                    userDropdown.classList.remove('show');
                }
                
                // Fermer les notifications
                const notificationsDropdown = document.getElementById('notificationsDropdown');
                if (notificationsDropdown) {
                    notificationsDropdown.style.display = 'none';
                }
            }
            
            // Raccourcis de navigation (seulement si pas dans un input/textarea)
            if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
                // Ctrl/Cmd + 1 pour Dashboard
                if ((e.ctrlKey || e.metaKey) && e.key === '1') {
                    e.preventDefault();
                    window.location.href = '{{ route('dashboard') }}';
                }
                
                // Ctrl/Cmd + 2 pour Demandes
                if ((e.ctrlKey || e.metaKey) && e.key === '2') {
                    e.preventDefault();
                    window.location.href = '{{ route('sim-requests.index') }}';
                }
                
                // Ctrl/Cmd + 3 pour SIMs
                if ((e.ctrlKey || e.metaKey) && e.key === '3') {
                    e.preventDefault();
                    window.location.href = '{{ route('sims.index') }}';
                }
                
                // Ctrl/Cmd + P pour Profil
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    window.location.href = '{{ route('profile.edit') }}';
                }
            }
        });
        
        // Afficher les raccourcis dans la console (pour debug)
        console.log('%cRaccourcis clavier disponibles:', 'color: #00574A; font-weight: bold;');
        console.log('Ctrl/Cmd + K : Focus sur la recherche');
        console.log('Ctrl/Cmd + 1 : Dashboard');
        console.log('Ctrl/Cmd + 2 : Demandes');
        console.log('Ctrl/Cmd + 3 : SIMs');
        console.log('Ctrl/Cmd + P : Profil');
        console.log('Échap : Fermer les menus/dropdowns');
        
        // Toast Notification System
        function showToast(message, type = 'info', duration = 5000) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.style.cssText = `
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 16px rgba(0,0,0,0.15);
                padding: 16px 20px;
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 300px;
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
                border-left: 4px solid;
                position: relative;
                overflow: hidden;
            `;
            
            // Couleurs selon le type
            const colors = {
                success: { border: '#10b981', icon: 'bi-check-circle-fill', bg: '#f0fdf4' },
                error: { border: '#ef4444', icon: 'bi-x-circle-fill', bg: '#fef2f2' },
                warning: { border: '#f59e0b', icon: 'bi-exclamation-triangle-fill', bg: '#fffbeb' },
                info: { border: '#3b82f6', icon: 'bi-info-circle-fill', bg: '#eff6ff' }
            };
            
            const config = colors[type] || colors.info;
            toast.style.borderLeftColor = config.border;
            toast.style.background = config.bg;
            
            // Icône
            const icon = document.createElement('i');
            icon.className = `bi ${config.icon}`;
            icon.style.cssText = `font-size: 20px; color: ${config.border}; flex-shrink: 0;`;
            
            // Message
            const messageDiv = document.createElement('div');
            messageDiv.style.cssText = 'flex: 1; color: #1e293b; font-size: 14px; font-weight: 500; line-height: 1.5; white-space: pre-line;';
            messageDiv.textContent = message;
            
            // Bouton fermer
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '<i class="bi bi-x"></i>';
            closeBtn.style.cssText = `
                background: transparent;
                border: none;
                color: #64748b;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 4px;
                transition: all 0.2s;
                flex-shrink: 0;
            `;
            closeBtn.onmouseover = function() { this.style.background = '#e2e8f0'; };
            closeBtn.onmouseout = function() { this.style.background = 'transparent'; };
            closeBtn.onclick = function() { removeToast(toast); };
            
            toast.appendChild(icon);
            toast.appendChild(messageDiv);
            toast.appendChild(closeBtn);
            container.appendChild(toast);
            
            // Auto-remove après la durée spécifiée
            if (duration > 0) {
                setTimeout(() => {
                    removeToast(toast);
                }, duration);
            }
        }
        
        function removeToast(toast) {
            toast.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
        
        // Ajouter les animations CSS
        const toastStyle = document.createElement('style');
        toastStyle.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .toast-notification:hover {
                box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
            }
        `;
        document.head.appendChild(toastStyle);
        
        // Exposer la fonction globalement pour utilisation dans les scripts
        window.showToast = showToast;
        
        // Notifications System
        let notificationsDropdown = document.getElementById('notificationsDropdown');
        let notificationBadge = document.getElementById('notification-badge');
        let notificationsList = document.getElementById('notifications-list');
        let notificationRefreshInterval;
        
        function toggleNotificationsDropdown() {
            if (notificationsDropdown.style.display === 'none') {
                notificationsDropdown.style.display = 'flex';
                loadNotifications();
                startNotificationRefresh();
            } else {
                notificationsDropdown.style.display = 'none';
                stopNotificationRefresh();
            }
        }
        
        function loadNotifications() {
            fetch('{{ route('notifications.index') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }
        
        function updateNotificationBadge(count) {
            if (notificationBadge) {
                notificationBadge.textContent = count;
                if (count > 0) {
                    notificationBadge.style.display = 'block';
                } else {
                    notificationBadge.style.display = 'none';
                }
            }
        }
        
        function renderNotifications(notifications) {
            if (!notificationsList) return;
            
            if (notifications.length === 0) {
                notificationsList.innerHTML = `
                    <div class="notification-empty">
                        <i class="bi bi-bell-slash"></i>
                        <div>Aucune notification</div>
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifications.forEach(notification => {
                const data = notification.data;
                const isUnread = !notification.read_at;
                const timeAgo = getTimeAgo(notification.created_at);
                
                // Style spécial pour les rappels urgents
                const isReminder = data.type === 'request_reminder';
                const urgencyClass = isReminder && data.urgency_level ? `urgency-${data.urgency_level}` : '';
                const urgencyStyle = isReminder && data.urgency_level === 'urgent' ? 'border-left: 3px solid #ef4444;' : '';
                
                let notificationUrl = data.url || '#';
                
                // S'assurer que l'URL est relative (sans domaine)
                if (notificationUrl && notificationUrl !== '#' && notificationUrl.startsWith('http')) {
                    try {
                        const urlObj = new URL(notificationUrl);
                        notificationUrl = urlObj.pathname + urlObj.search;
                    } catch (e) {
                        console.warn('Error parsing notification URL:', e);
                    }
                }
                
                console.log('Rendering notification:', { id: notification.id, url: notificationUrl, originalUrl: data.url });
                
                // Échapper l'URL pour l'attribut HTML (mais pas trop pour éviter les problèmes)
                const safeUrl = notificationUrl.replace(/"/g, '&quot;');
                html += `
                    <div class="notification-item ${isUnread ? 'unread' : ''} ${urgencyClass}" 
                         style="${urgencyStyle}; cursor: pointer;" 
                         data-url="${safeUrl}"
                         onclick="markAsRead('${notification.id}', '${safeUrl}', event)">
                        <div class="notification-icon" style="background: ${data.color || '#00574A'};">
                            <i class="bi ${data.icon || 'bi-bell'}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title" style="${isReminder && data.urgency_level === 'urgent' ? 'font-weight: 700; color: #ef4444;' : ''}">${data.title || 'Notification'}</div>
                            <div class="notification-message">${data.message || ''}</div>
                            ${isReminder && data.days_pending ? `<div style="font-size: 11px; color: #64748b; margin-top: 4px;"><i class="bi bi-clock-history"></i> En attente depuis ${data.days_pending} jour(s)</div>` : ''}
                            <div class="notification-time">${timeAgo}</div>
                        </div>
                    </div>
                `;
            });
            
            notificationsList.innerHTML = html;
        }
        
        function markAsRead(notificationId, url, event) {
            // Empêcher la propagation de l'événement
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Récupérer l'élément cliqué
            const clickedElement = event ? event.currentTarget : null;
            
            // Si l'URL n'est pas fournie ou est invalide, la récupérer depuis l'élément DOM
            if (!url || url === '#' || url === 'undefined' || url === 'null' || url.trim() === '') {
                if (clickedElement) {
                    url = clickedElement.getAttribute('data-url') || url;
                }
            }
            
            // Décoder l'URL si elle a été échappée
            if (url && url !== '#') {
                url = url.replace(/\\'/g, "'").replace(/&quot;/g, '"');
            }
            
            console.log('markAsRead called:', { notificationId, url, clickedElement: !!clickedElement });
            
            // Si toujours pas d'URL valide, ne rien faire
            if (!url || url === '#' || url === 'undefined' || url === 'null' || url.trim() === '') {
                console.warn('No valid URL for notification:', notificationId, 'URL was:', url);
                return;
            }
            
            // Marquer comme lu et rediriger (comme Facebook)
            fetch(`{{ route('notifications.mark-read', '') }}/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Notification marked as read, response:', data);
                if (data.success) {
                    // Mettre à jour l'interface immédiatement
                    loadNotifications();
                    updateNotificationBadge();
                    
                    // Rediriger vers l'URL (priorité à l'URL du serveur)
                    let redirectUrl = data.url || url;
                    console.log('Redirect URL (before processing):', redirectUrl);
                    
                    // S'assurer que l'URL est relative si elle contient un domaine complet
                    if (redirectUrl && redirectUrl.startsWith('http')) {
                        try {
                            const urlObj = new URL(redirectUrl);
                            redirectUrl = urlObj.pathname + urlObj.search;
                            console.log('Converted to relative URL:', redirectUrl);
                        } catch (e) {
                            console.warn('Error parsing URL:', e);
                        }
                    }
                    
                    if (redirectUrl && redirectUrl !== '#' && redirectUrl !== 'undefined' && redirectUrl !== 'null' && redirectUrl.trim() !== '') {
                        // Fermer le dropdown des notifications
                        if (notificationsDropdown) {
                            notificationsDropdown.style.display = 'none';
                        }
                        // Rediriger
                        console.log('Redirecting to:', redirectUrl);
                        window.location.href = redirectUrl;
                    } else {
                        console.warn('Invalid redirect URL:', redirectUrl, 'Original data:', data);
                    }
                } else {
                    console.error('Failed to mark notification as read:', data);
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // En cas d'erreur, essayer quand même de rediriger si l'URL est disponible
                if (url && url !== '#' && url !== 'undefined' && url !== 'null' && url.trim() !== '') {
                    console.log('Fallback redirect to:', url);
                    window.location.href = url;
                }
            });
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        }
        
        function viewAllNotifications() {
            window.location.href = '{{ route('notifications.all') }}';
        }
        
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'À l\'instant';
            if (diffInSeconds < 3600) return `Il y a ${Math.floor(diffInSeconds / 60)} min`;
            if (diffInSeconds < 86400) return `Il y a ${Math.floor(diffInSeconds / 3600)} h`;
            if (diffInSeconds < 604800) return `Il y a ${Math.floor(diffInSeconds / 86400)} j`;
            
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        }
        
        function startNotificationRefresh() {
            // Rafraîchir le badge toutes les 30 secondes
            notificationRefreshInterval = setInterval(() => {
                fetch('{{ route('notifications.unread-count') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.count);
                })
                .catch(error => {
                    console.error('Error refreshing notification count:', error);
                });
            }, 30000);
        }
        
        function stopNotificationRefresh() {
            if (notificationRefreshInterval) {
                clearInterval(notificationRefreshInterval);
            }
        }
        
        // Charger le badge au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route('notifications.unread-count') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.count);
            })
            .catch(error => {
                console.error('Error loading notification count:', error);
            });
            
            // Fermer le dropdown si on clique en dehors
            document.addEventListener('click', function(e) {
                const notificationsContainer = document.querySelector('.navbar-notifications');
                if (notificationsContainer && !notificationsContainer.contains(e.target)) {
                    if (notificationsDropdown) {
                        notificationsDropdown.style.display = 'none';
                        stopNotificationRefresh();
                    }
                }
            });
        });
        
    </script>

    <!-- UI/UX Enhancements -->
    <style>
        /* Animations fluides pour les interactions */
        * {
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
        }

        /* Amélioration des boutons avec feedback visuel */
        .btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:active::before {
            width: 300px;
            height: 300px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Amélioration des cartes avec effet de survol */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-color: rgba(0, 87, 74, 0.1);
        }

        /* Amélioration des tableaux */
        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc !important;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Amélioration des inputs avec focus */
        .form-control:focus,
        .form-select:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 3px rgba(0, 87, 74, 0.1);
        }

        /* Indicateur de chargement amélioré */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0, 87, 74, 0.2);
            border-radius: 50%;
            border-top-color: #00574A;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Skeleton loader pour le contenu en chargement */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Tooltips personnalisés */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }

        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 12px;
            background: #1e293b;
            color: white;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 8px;
            opacity: 0;
            animation: tooltipFadeIn 0.3s ease forwards;
            pointer-events: none;
        }

        [data-tooltip]:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: #1e293b;
            margin-bottom: 3px;
            opacity: 0;
            animation: tooltipFadeIn 0.3s ease forwards;
            pointer-events: none;
        }

        @keyframes tooltipFadeIn {
            to {
                opacity: 1;
            }
        }

        /* Amélioration des badges avec animation */
        .badge {
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        /* Amélioration des modals */
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Amélioration des dropdowns */
        .dropdown-menu {
            animation: dropdownFadeIn 0.2s ease-out;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Amélioration de la validation des formulaires */
        .form-control.is-invalid {
            border-color: #ef4444;
            animation: shake 0.4s ease;
        }

        .form-control.is-valid {
            border-color: #10b981;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Amélioration des liens */
        a {
            transition: color 0.2s ease;
        }

        a:hover {
            color: var(--primary-color);
        }

        /* Amélioration des icônes */
        i.bi {
            transition: transform 0.2s ease;
        }

        .btn:hover i.bi {
            transform: scale(1.1);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Amélioration de la pagination */
        .pagination .page-link {
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Amélioration des checkboxes et radios */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 87, 74, 0.25);
        }

        /* Amélioration de la responsivité */
        @media (max-width: 768px) {
            .card:hover {
                transform: none;
            }
            
            .table tbody tr:hover {
                transform: none;
            }

            /* Amélioration des tableaux sur mobile */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px 4px;
                white-space: nowrap;
            }

            /* Amélioration des boutons sur mobile */
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                border-radius: 8px !important;
                margin-bottom: 4px;
            }

            /* Amélioration des modals sur mobile */
            .modal-dialog {
                margin: 10px;
            }

            .modal-content {
                border-radius: 12px;
            }

            /* Amélioration de la sidebar sur mobile */
            #sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            #sidebar.show {
                transform: translateX(0);
            }

            /* Amélioration de la navbar sur mobile */
            .top-navbar {
                left: 0;
                width: 100%;
            }

            .navbar-search {
                flex: 1;
                max-width: none;
            }

            /* Amélioration des cartes sur mobile */
            .card {
                margin-bottom: 16px;
            }

            .card-body {
                padding: 16px;
            }

            /* Amélioration des filtres sur mobile */
            .row.g-3 > div {
                margin-bottom: 12px;
            }
        }

        /* Amélioration des états de focus pour l'accessibilité */
        *:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Amélioration des transitions de page */
        .main-content {
            animation: fadeInUp 0.4s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Amélioration des confirmations visuelles */
        .confirmation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.2s ease;
        }

        .confirmation-dialog {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>

    <!-- Modal de confirmation global -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-confirm-title>Confirmer l'action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
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
        // Amélioration des tooltips avec Bootstrap si disponible
        document.addEventListener('DOMContentLoaded', function() {
            let confirmModal = null;
            let confirmAction = null;
            const confirmModalEl = document.getElementById('confirmModal');
            const confirmTitleEl = confirmModalEl ? confirmModalEl.querySelector('[data-confirm-title]') : null;
            const confirmMessageEl = confirmModalEl ? confirmModalEl.querySelector('[data-confirm-message]') : null;
            const confirmActionBtn = confirmModalEl ? confirmModalEl.querySelector('[data-confirm-action]') : null;

            if (confirmModalEl && typeof bootstrap !== 'undefined') {
                confirmModal = new bootstrap.Modal(confirmModalEl);

                if (confirmActionBtn) {
                    confirmActionBtn.addEventListener('click', function() {
                        confirmModal.hide();
                        if (typeof confirmAction === 'function') {
                            const action = confirmAction;
                            confirmAction = null;
                            action();
                        }
                    });
                }

                confirmModalEl.addEventListener('hidden.bs.modal', function() {
                    confirmAction = null;
                });
            }

            window.showConfirmModal = function(message, onConfirm, options = {}) {
                if (!confirmModal) {
                    if (window.showToast) {
                        showToast('Impossible d\'afficher la confirmation.', 'error');
                    }
                    return;
                }

                if (confirmTitleEl) {
                    confirmTitleEl.textContent = options.title || 'Confirmer l\'action';
                }
                if (confirmMessageEl) {
                    confirmMessageEl.textContent = message || 'Êtes-vous sûr de vouloir effectuer cette action ?';
                }
                if (confirmActionBtn) {
                    confirmActionBtn.textContent = options.confirmText || 'Confirmer';
                    const variant = options.confirmVariant || 'primary';
                    confirmActionBtn.className = `btn btn-${variant}`;
                }

                confirmAction = onConfirm;
                confirmModal.show();
            };

            // Initialiser les tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Ajouter des tooltips automatiques aux boutons sans texte
            document.querySelectorAll('.btn[title]').forEach(btn => {
                if (!btn.getAttribute('data-bs-toggle')) {
                    btn.setAttribute('data-bs-toggle', 'tooltip');
                    new bootstrap.Tooltip(btn);
                }
            });

            // Amélioration des confirmations de suppression
            document.querySelectorAll('form[data-confirm]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const message = form.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir effectuer cette action ?';
                    const confirmText = form.getAttribute('data-confirm-text') || 'Confirmer';
                    const confirmVariant = form.getAttribute('data-confirm-variant') || 'primary';
                    window.showConfirmModal(message, () => form.submit(), {
                        confirmText,
                        confirmVariant,
                        title: 'Confirmation',
                    });
                });
            });

            // Amélioration de la validation en temps réel
            document.querySelectorAll('.form-control[required]').forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '' && this.hasAttribute('required')) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });

            // Amélioration des indicateurs de chargement pour les formulaires
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="loading-spinner"></span> En cours...';
                        
                        // Réactiver après 10 secondes au cas où
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 10000);
                    }
                });
            });

            // Amélioration des actions de suppression avec confirmation visuelle
            document.querySelectorAll('form[action*="destroy"], form[action*="delete"], form[action*="cancel"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.hasAttribute('data-confirm')) {
                        return;
                    }
                    e.preventDefault();
                    window.showConfirmModal(
                        'Êtes-vous sûr de vouloir effectuer cette action ? Cette action est irréversible.',
                        () => form.submit(),
                        {
                            confirmText: 'Confirmer',
                            confirmVariant: 'danger',
                            title: 'Confirmer l\'action',
                        }
                    );
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>

