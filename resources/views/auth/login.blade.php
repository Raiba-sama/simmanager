<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - SIM Manager</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary:       #00574A;
            --primary-dark:  #003d34;
            --primary-mid:   #004a3f;
            --primary-light: #007a68;
            --primary-glow:  rgba(0, 87, 74, 0.14);
            --text-dark:     #0f172a;
            --text-muted:    #64748b;
            --border:        #e2e8f0;
            --bg:            #f8fafc;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            background: white;
        }

        /* ─── Left branding panel ─────────────────────────────── */
        .panel-left {
            width: 46%;
            background: linear-gradient(150deg, var(--primary-dark) 0%, var(--primary) 55%, var(--primary-light) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }

        /* decorative rings */
        .panel-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            border: 70px solid rgba(255,255,255,0.06);
            top: -150px; right: -150px;
        }
        .panel-left::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            border: 50px solid rgba(255,255,255,0.06);
            bottom: -100px; left: -100px;
        }

        .ring-mid {
            position: absolute;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: 55%; right: -40px;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            animation: fadeInLeft 0.6s ease-out;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-24px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .brand-logo {
            height: 82px;
            width: auto;
            margin-bottom: 30px;
            filter: brightness(0) invert(1) drop-shadow(0 6px 18px rgba(0,0,0,0.25));
        }

        .brand-title {
            font-size: 38px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.8px;
            margin-bottom: 10px;
        }

        .brand-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.72);
            line-height: 1.65;
            max-width: 270px;
            margin: 0 auto 40px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
            max-width: 290px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255,255,255,0.10);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 14px;
            padding: 14px 18px;
            color: rgba(255,255,255,0.92);
            transition: background 0.2s;
        }

        .feature-item:hover {
            background: rgba(255,255,255,0.16);
        }

        .feature-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 13px;
            font-weight: 500;
        }

        /* ─── Right form panel ────────────────────────────────── */
        .panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            background: white;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            animation: fadeInRight 0.55s ease-out;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .login-box-header {
            margin-bottom: 34px;
        }

        .login-box-header .eyebrow {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .login-box-header h1 {
            font-size: 29px;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .login-box-header p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ─── Alerts ──────────────────────────────────────────── */
        .alert {
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-success i { color: #10b981; font-size: 16px; margin-top: 1px; }

        .alert-danger { background: #fee2e2; color: #991b1b; }
        .alert-danger i { color: #ef4444; font-size: 16px; margin-top: 1px; }

        .alert ul { margin: 5px 0 0; padding-left: 16px; }

        /* ─── Form fields ─────────────────────────────────────── */
        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            padding: 13px 16px 13px 43px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background: var(--bg);
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .form-input::placeholder {
            color: #b0bec5;
            font-weight: 400;
        }

        .form-input.is-invalid {
            border-color: #ef4444;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%       { transform: translateX(-5px); }
            75%       { transform: translateX(5px); }
        }

        .password-wrapper .form-input { padding-right: 46px; }

        .toggle-password {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 16px;
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: var(--primary); }

        .field-error {
            margin-top: 6px;
            font-size: 12px;
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ─── Options row ─────────────────────────────────────── */
        .form-options {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
        }

        .custom-check {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
        }

        .custom-check input[type="checkbox"] {
            width: 17px; height: 17px;
            accent-color: var(--primary);
            cursor: pointer;
            border-radius: 4px;
        }

        .custom-check label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            cursor: pointer;
        }

        /* ─── Submit button ───────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-mid) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: transform 0.25s, box-shadow 0.25s, opacity 0.25s;
            box-shadow: 0 4px 18px rgba(0, 87, 74, 0.32);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.25s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0, 87, 74, 0.42);
        }

        .btn-login:hover::after { background: rgba(255,255,255,0.06); }

        .btn-login:active { transform: translateY(0); }

        .btn-login:disabled {
            opacity: 0.72;
            cursor: not-allowed;
            transform: none;
        }

        /* ─── Footer ──────────────────────────────────────────── */
        .login-footer {
            text-align: center;
            font-size: 12px;
            color: #b0bec5;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        /* ─── Spinner ─────────────────────────────────────────── */
        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ─── Responsive ──────────────────────────────────────── */
        @media (max-width: 900px) {
            .panel-left { width: 42%; padding: 50px 36px; }
            .feature-list { display: none; }
        }

        @media (max-width: 680px) {
            body { flex-direction: column; }

            .panel-left {
                width: 100%;
                padding: 40px 28px 36px;
                min-height: auto;
            }

            .feature-list { display: none; }
            .brand-title { font-size: 28px; }
            .brand-logo { height: 64px; margin-bottom: 20px; }
            .brand-subtitle { display: none; }

            .panel-right { padding: 40px 24px 48px; }
        }
    </style>
</head>
<body>

    <!-- ═══ Left branding panel ═══════════════════════════════════ -->
    <div class="panel-left">
        <div class="ring-mid"></div>

        <div class="brand-content">
            <img src="{{ asset('images/acep_madagascar_logo-1.png') }}" alt="ACEP Madagascar" class="brand-logo">
            <h2 class="brand-title">SIM Manager</h2>
            <p class="brand-subtitle">Plateforme de gestion des cartes SIM pour ACEP Madagascar</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-phone-fill"></i></div>
                    <span class="feature-text">Gestion centralisée des SIM</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                    <span class="feature-text">Assignation par employé</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <span class="feature-text">Accès sécurisé par rôle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Right form panel ════════════════════════════════════════ -->
    <div class="panel-right">
        <div class="login-box">

            <div class="login-box-header">
                <div class="eyebrow">SIM Manager</div>
                <h1>Connexion</h1>
                <p>Entrez vos identifiants pour accéder à votre espace</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Erreur de connexion</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Matricule -->
                <div class="form-group">
                    <label class="form-label" for="matricule">Matricule</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="matricule"
                            name="matricule"
                            class="form-input @error('matricule') is-invalid @enderror"
                            value="{{ old('matricule') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Entrez votre matricule">
                        <i class="bi bi-person-badge input-icon"></i>
                    </div>
                    @error('matricule')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="input-wrapper password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            required
                            autocomplete="current-password"
                            placeholder="Entrez votre mot de passe">
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="toggle-password" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Se souvenir -->
                <div class="form-options">
                    <div class="custom-check">
                        <input type="checkbox" id="remember_me" name="remember">
                        <label for="remember_me">Se souvenir de moi</label>
                    </div>
                </div>

                <!-- Bouton connexion -->
                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Se connecter
                </button>
            </form>

            <div class="login-footer">
                <i class="bi bi-c-circle"></i> {{ date('Y') }} ACEP Madagascar &mdash; Tous droits réservés &mdash; <small> v1.2.1 </small>
            </div>

        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const password = document.getElementById('password');
            const eyeIcon  = document.getElementById('eyeIcon');
            const isHidden = password.getAttribute('type') === 'password';
            password.setAttribute('type', isHidden ? 'text' : 'password');
            eyeIcon.classList.toggle('bi-eye', !isHidden);
            eyeIcon.classList.toggle('bi-eye-slash', isHidden);
        });

        // Loading state on submit
        document.getElementById('loginForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> Connexion en cours...';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Se connecter';
                }, 10000);
            }
        });
    </script>
</body>
</html>
