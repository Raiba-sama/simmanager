<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - SIM Manager</title>
    
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
            --primary-light: rgba(0, 87, 74, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 87, 74, 0.15);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #006b5c 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .login-header img {
            height: 70px;
            width: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            position: relative;
            z-index: 1;
        }
        
        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }
        
        .login-body {
            padding: 40px 35px;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label i {
            color: var(--primary-color);
            font-size: 16px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px var(--primary-light);
            outline: none;
        }
        
        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        
        .input-group .form-control {
            border-right: none;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .input-group .btn {
            border-left: none;
            border: 2px solid #e2e8f0;
            border-left: none;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            background: white;
            color: #64748b;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        
        .input-group .btn:hover {
            background: #f8fafc;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 4px var(--primary-light);
        }
        
        .form-check-label {
            font-size: 14px;
            color: #475569;
            cursor: pointer;
            margin-left: 8px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #006b5c 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 24px;
            font-weight: 600;
            font-size: 15px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 87, 74, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
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
        
        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 87, 74, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        .login-footer {
            padding: 20px 35px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .login-footer p {
            margin: 0;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .login-footer i {
            font-size: 12px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 14px 16px;
            font-size: 14px;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .alert-success i {
            color: #10b981;
            margin-right: 8px;
        }
        
        .invalid-feedback {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .invalid-feedback i {
            font-size: 14px;
        }
        
        .form-control.is-invalid {
            border-color: #ef4444;
            animation: shake 0.4s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @media (max-width: 576px) {
            .login-card {
                border-radius: 16px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-header img {
                height: 60px;
            }
            
            .login-header h2 {
                font-size: 24px;
            }
            
            .login-body {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('images/acep_madagascar_logo-1.png') }}" alt="ACEP Madagascar">
                <h2>SIM Manager</h2>
                <p>Gestion de Cartes SIM</p>
            </div>

            <div class="login-body">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Erreur de connexion</strong>
                        <ul class="mb-0 mt-2" style="padding-left: 20px; font-size: 13px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Matricule -->
                    <div class="mb-4">
                        <label for="matricule" class="form-label">
                            <i class="bi bi-person-badge"></i> Matricule
                        </label>
                        <input 
                            type="text" 
                            id="matricule" 
                            name="matricule" 
                            class="form-control @error('matricule') is-invalid @enderror" 
                            value="{{ old('matricule') }}" 
                            required 
                            autofocus 
                            autocomplete="username" 
                            placeholder="Entrez votre matricule">
                        @error('matricule')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Mot de passe
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                required 
                                autocomplete="current-password"
                                placeholder="Entrez votre mot de passe">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="remember_me" 
                                name="remember">
                            <label class="form-check-label" for="remember_me">
                                Se souvenir de moi
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                    </div>
                </form>
            </div>

            <div class="login-footer">
                <p style="margin: 0; color: #94a3b8; font-size: 13px; font-weight: 400;">
                    <i class="bi bi-c-circle"></i> {{ date('Y') }} ACEP Madagascar. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });

        // Form submission loading state
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Connexion...';
                
                // Réactiver après 10 secondes au cas où
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });

        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField && !emailField.value) {
                emailField.focus();
            }
        });
    </script>
</body>
</html>
