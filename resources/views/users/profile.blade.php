@extends('layouts.bootstrap')

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; margin-bottom: 24px;">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Carte d'informations utilisateur -->
<div class="card mb-4" style="border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #00574A 0%, #007a6b 100%); padding: 32px; position: relative;">
        <div class="d-flex align-items-center gap-4">
            <div style="position: relative;">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar }}" alt="{{ $user->full_name }}" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.2); border: 4px solid white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 600; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1) . substr($user->first_name ?? 'S', 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-grow-1" style="color: white;">
                <h2 class="mb-2" style="font-weight: 700; font-size: 28px; color: white; margin: 0;">
                    {{ $user->full_name }}
                </h2>
                @if($user->fonction)
                    <p class="mb-1" style="font-size: 16px; color: rgba(255,255,255,0.9); margin: 0;">
                        <i class="bi bi-briefcase"></i> {{ $user->fonction }}
                    </p>
                @endif
                <div class="mt-2">
                    @if($user->isAdmin())
                        <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;">
                            <i class="bi bi-shield-check"></i> Administrateur
                        </span>
                    @elseif($user->isValidator())
                        <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;">
                            <i class="bi bi-check-circle"></i> Validateur
                        </span>
                    @else
                        <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;">
                            <i class="bi bi-person"></i> Utilisateur
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 24px;">
        <div class="row g-4">
            <div class="col-md-6">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #00574A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-envelope" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 4px;">Email</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
            @if($user->matricule)
            <div class="col-md-6">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #00574A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-person-badge" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 4px;">Matricule</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $user->matricule }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if($user->direction)
            <div class="col-md-6">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #00574A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-building" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 4px;">Direction</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $user->direction }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if($user->lieu_affectation)
            <div class="col-md-6">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #00574A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-geo-alt" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 4px;">Lieu d'affectation</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $user->lieu_affectation }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if($user->zone_affectation)
            <div class="col-md-6">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #00574A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-map" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 4px;">Zone d'affectation</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $user->zone_affectation }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if($user->numero_flotte)
            <div class="col-md-6">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; border-radius: 8px; background: #f9fafb;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #00574A; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-phone" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 4px;">Numéro flotte</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $user->numero_flotte }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Formulaire de modification -->
<div class="card" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
        <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">
            <i class="bi bi-pencil-square"></i> Modifier mes informations
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label" style="font-weight: 500; color: #1e293b;">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           style="border-radius: 8px; border: 1px solid #e2e8f0;"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label" style="font-weight: 500; color: #1e293b;">Prénom</label>
                    <input type="text" name="first_name" id="first_name" 
                           class="form-control @error('first_name') is-invalid @enderror" 
                           style="border-radius: 8px; border: 1px solid #e2e8f0;"
                           value="{{ old('first_name', $user->first_name) }}">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fonction" class="form-label" style="font-weight: 500; color: #1e293b;">Poste</label>
                    <input type="text" name="fonction" id="fonction" 
                           class="form-control @error('fonction') is-invalid @enderror" 
                           style="border-radius: 8px; border: 1px solid #e2e8f0;"
                           value="{{ old('fonction', $user->fonction) }}">
                    @error('fonction')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="direction" class="form-label" style="font-weight: 500; color: #1e293b;">Direction</label>
                    <input type="text" name="direction" id="direction" 
                           class="form-control @error('direction') is-invalid @enderror" 
                           style="border-radius: 8px; border: 1px solid #e2e8f0;"
                           value="{{ old('direction', $user->direction) }}">
                    @error('direction')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="lieu_affectation" class="form-label" style="font-weight: 500; color: #1e293b;">Lieu d'affectation</label>
                    <input type="text" name="lieu_affectation" id="lieu_affectation" 
                           class="form-control @error('lieu_affectation') is-invalid @enderror" 
                           style="border-radius: 8px; border: 1px solid #e2e8f0;"
                           value="{{ old('lieu_affectation', $user->lieu_affectation) }}">
                    @error('lieu_affectation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="zone_affectation" class="form-label" style="font-weight: 500; color: #1e293b;">Zone d'affectation</label>
                    <input type="text" name="zone_affectation" id="zone_affectation" 
                           class="form-control @error('zone_affectation') is-invalid @enderror" 
                           style="border-radius: 8px; border: 1px solid #e2e8f0;"
                           value="{{ old('zone_affectation', $user->zone_affectation) }}">
                    @error('zone_affectation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="numero_flotte" class="form-label" style="font-weight: 500; color: #1e293b;">Numéro flotte</label>
                <input type="text" name="numero_flotte" id="numero_flotte" 
                       class="form-control @error('numero_flotte') is-invalid @enderror" 
                       style="border-radius: 8px; border: 1px solid #e2e8f0;"
                       value="{{ old('numero_flotte', $user->numero_flotte) }}">
                @error('numero_flotte')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label" style="font-weight: 500; color: #1e293b;">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       style="border-radius: 8px; border: 1px solid #e2e8f0;"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="avatar" class="form-label" style="font-weight: 500; color: #1e293b;">Avatar</label>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <img id="avatar-preview" 
                             @if($user->avatar_url && strpos($user->avatar_url, 'storage/') !== false)
                                 src="{{ asset($user->avatar_url) }}"
                             @elseif($user->avatar_url)
                                 src="{{ $user->avatar_url }}"
                             @else
                                 src="{{ $user->avatar }}"
                             @endif
                             alt="Avatar actuel" 
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="avatar" id="avatar" 
                               class="form-control @error('avatar') is-invalid @enderror" 
                               style="border-radius: 8px; border: 1px solid #e2e8f0;"
                               accept="image/*">
                        @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                        <small class="form-text text-muted" style="color: #64748b;">
                            Formats acceptés: JPG, PNG, GIF (max 5MB). Laissez vide pour conserver l'avatar actuel.
                        </small>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('avatar').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('avatar-preview').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            </script>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="border-radius: 8px;">
                    <i class="bi bi-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                    <i class="bi bi-check-circle"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4" style="border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div class="card-header" style="background: white; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0;">
        <h5 class="mb-0" style="font-weight: 600; color: #1e293b;">
            <i class="bi bi-key"></i> Changer le mot de passe
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="current_password" class="form-label" style="font-weight: 500; color: #1e293b;">Mot de passe actuel <span class="text-danger">*</span></label>
                <input type="password" name="current_password" id="current_password" 
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                       style="border-radius: 8px; border: 1px solid #e2e8f0;"
                       required autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label" style="font-weight: 500; color: #1e293b;">Nouveau mot de passe <span class="text-danger">*</span></label>
                <input type="password" name="password" id="password" 
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       style="border-radius: 8px; border: 1px solid #e2e8f0;"
                       required autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="color: #64748b;">Le mot de passe doit contenir au moins 8 caractères</small>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label" style="font-weight: 500; color: #1e293b;">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="form-control" 
                       style="border-radius: 8px; border: 1px solid #e2e8f0;"
                       required autocomplete="new-password">
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                    <i class="bi bi-key"></i> Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

