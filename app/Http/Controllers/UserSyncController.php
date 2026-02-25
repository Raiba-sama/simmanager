<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserSyncController extends Controller
{
    protected string $webhookUrl = 'https://acepmg.it4life.org/webhook/get_users';

    /** Rôles autorisés (doivent correspondre à l'enum users.role). */
    protected array $allowedRoles = ['admin', 'validator', 'user'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la page de synchronisation (admin uniquement).
     */
    public function showSyncForm(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }
        return view('users.sync-from-webhook');
    }

    /**
     * Synchronise les utilisateurs depuis le webhook externe.
     * Crée les users par matricule s'ils n'existent pas ; le rôle vient du webhook.
     */
    public function syncFromWebhook(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Accès refusé. Réservé aux administrateurs.');
        }

        try {
            $webhookUrl = config('services.webhook_get_users.url', $this->webhookUrl);
            $username = config('services.webhook_get_users.username');
            $password = config('services.webhook_get_users.password');
            $token = config('services.webhook_get_users.token');
            $authBasic = $username !== null && $username !== '' && $password !== null;
            $authBearer = $token !== null && $token !== '';

            Log::info('UserSync: calling webhook', [
                'url' => $webhookUrl,
                'auth_basic' => $authBasic,
                'auth_bearer' => $authBearer,
            ]);

            $http = Http::timeout(30);
            if ($authBasic) {
                $http = $http->withBasicAuth($username, $password);
            } elseif ($authBearer) {
                $http = $http->withToken($token);
            }
            $response = $http->get($webhookUrl);

            if (!$response->successful()) {
                Log::warning('UserSync: webhook failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                    'url' => $webhookUrl,
                    'auth_basic' => $authBasic,
                    'auth_bearer' => $authBearer,
                ]);
                $message = 'Le webhook a répondu avec une erreur (HTTP ' . $response->status() . ').';
                return back()->with('error', $message);
            }

            $data = $response->json();
            $items = is_array($data) ? $data : ($data['users'] ?? $data['data'] ?? []);

            if (!is_array($items)) {
                $message = 'Format de réponse du webhook invalide (liste d\'utilisateurs attendue).';
                return back()->with('error', $message);
            }

            $created = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];

            $defaultPassword = config('app.webhook_sync_default_password', 'ChangeMe123!');
            if (empty($defaultPassword) || $defaultPassword === 'ChangeMe123!') {
                $defaultPassword = null; // on génère un aléatoire par utilisateur si pas de défaut
            }

            foreach ($items as $item) {
                if (!is_array($item)) {
                    if (is_string($item) && trim($item) !== '') {
                        $item = ['matricule' => trim($item)];
                    } else {
                        $skipped++;
                        continue;
                    }
                }
                $matricule = $this->extractMatricule($item);
                if (empty($matricule)) {
                    $skipped++;
                    continue;
                }

                $password = $defaultPassword ?? Str::random(16);

                $existingUser = User::where('matricule', $matricule)->first();
                if ($existingUser) {
                    try {
                        // Mot de passe : passer la valeur brute pour que le cast 'hashed' du modèle le hash une seule fois
                        $updateData = [
                            'password' => $password,
                            'name' => $this->extractName($item),
                            'first_name' => $this->extractFirstName($item),
                            'role' => $this->extractRole($item),
                            'fonction' => $this->extractString($item, ['fonction', 'function'], $existingUser->fonction),
                            'lieu_affectation' => $this->extractString($item, ['lieu_affectation', 'agence', 'agency'], $existingUser->lieu_affectation),
                            'zone_affectation' => $this->extractString($item, ['zone_affectation', 'zone'], $existingUser->zone_affectation),
                            'direction' => $this->extractString($item, ['direction'], $existingUser->direction),
                            'numero_flotte' => $this->extractString($item, ['numero_flotte'], $existingUser->numero_flotte),
                        ];
                        $existingUser->update($updateData);
                        $updated++;
                    } catch (\Exception $e) {
                        Log::error('UserSync: update failed', ['matricule' => $matricule, 'error' => $e->getMessage()]);
                        $errors[] = $matricule . ': ' . $e->getMessage();
                    }
                    continue;
                }

                $email = $this->extractEmail($item, $matricule);
                if (User::where('email', $email)->exists()) {
                    $email = Str::lower($matricule) . '+' . Str::random(4) . '@synced.local';
                }

                $role = $this->extractRole($item);

                try {
                    // Mot de passe : valeur brute pour que le cast 'hashed' du modèle le hash une seule fois
                    User::create([
                        'matricule' => $matricule,
                        'name' => $this->extractName($item),
                        'first_name' => $this->extractFirstName($item),
                        'email' => $email,
                        'password' => $password,
                        'role' => $role,
                        'active' => true,
                        'fonction' => $this->extractString($item, ['fonction', 'function']),
                        'lieu_affectation' => $this->extractString($item, ['lieu_affectation', 'agence', 'agency']),
                        'zone_affectation' => $this->extractString($item, ['zone_affectation', 'zone']),
                        'direction' => $this->extractString($item, ['direction']),
                        'numero_flotte' => $this->extractString($item, ['numero_flotte']),
                    ]);
                    $created++;
                } catch (\Exception $e) {
                    Log::error('UserSync: create failed', ['matricule' => $matricule, 'error' => $e->getMessage()]);
                    $errors[] = $matricule . ': ' . $e->getMessage();
                }
            }

            $message = $created . ' utilisateur(s) créé(s). ' . $updated . ' mot(s) de passe mis à jour. ' . $skipped . ' ignoré(s).';
            if (!empty($errors)) {
                $message .= ' Erreurs : ' . implode(' ; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= '…';
                }
            }

            return back()->with('success', $message);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('UserSync: connection failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Impossible de joindre le webhook. Vérifiez l\'URL et la connexion.');
        } catch (\Exception $e) {
            Log::error('UserSync: error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Erreur lors de la synchronisation : ' . $e->getMessage());
        }
    }

    private function extractMatricule(array $item): ?string
    {
        $value = $item['matricule'] ?? $item['matricule_id'] ?? $item['code'] ?? $item['id'] ?? null;
        return $value !== null && $value !== '' ? trim((string) $value) : null;
    }

    private function extractEmail(array $item, string $matricule): string
    {
        $email = $item['email'] ?? $item['mail'] ?? null;
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return trim($email);
        }
        return Str::lower(Str::slug($matricule)) . '@synced.local';
    }

    private function extractName(array $item): string
    {
        $name = $item['name'] ?? $item['nom'] ?? $item['last_name'] ?? $item['lastname'] ?? '';
        return trim((string) $name) ?: 'Utilisateur';
    }

    private function extractFirstName(array $item): ?string
    {
        $first = $item['first_name'] ?? $item['firstname'] ?? $item['prenom'] ?? $item['prenoms'] ?? '';
        $v = trim((string) $first);
        return $v !== '' ? $v : null;
    }

    private function extractRole(array $item): string
    {
        $value = $item['role'] ?? $item['role_id'] ?? $item['type'] ?? null;
        $role = $value !== null && $value !== '' ? trim(strtolower((string) $value)) : 'user';
        return in_array($role, $this->allowedRoles, true) ? $role : 'user';
    }

    /**
     * Retourne la première valeur non vide trouvée dans $item pour les clés $keys, sinon $default.
     * Évite d'écraser les valeurs existantes avec des chaînes vides venant du webhook.
     */
    private function extractString(array $item, array $keys, ?string $default = null): ?string
    {
        foreach ($keys as $key) {
            $value = $item[$key] ?? null;
            if ($value !== null && $value !== '') {
                $trimmed = trim((string) $value);
                if ($trimmed !== '') {
                    return $trimmed;
                }
            }
        }
        return $default;
    }
}
