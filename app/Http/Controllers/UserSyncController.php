<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
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
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
            }
            return back()->with('error', 'Accès refusé. Réservé aux administrateurs.');
        }

        try {
            $webhookUrl = config('services.webhook_get_users.url', $this->webhookUrl);
            $hasToken = (bool) config('services.webhook_get_users.token');
            Log::info('UserSync: calling webhook', [
                'url' => $webhookUrl,
                'token_set' => $hasToken,
                'token_length' => $hasToken ? strlen(config('services.webhook_get_users.token')) : 0,
            ]);

            $http = Http::timeout(30);
            if ($hasToken) {
                $http = $http->withToken(config('services.webhook_get_users.token'));
            }
            $response = $http->get($webhookUrl);

            if (!$response->successful()) {
                Log::warning('UserSync: webhook failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                    'url' => $webhookUrl,
                    'token_set' => $hasToken,
                ]);
                $message = 'Le webhook a répondu avec une erreur (HTTP ' . $response->status() . ').';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            $data = $response->json();
            $items = is_array($data) ? $data : ($data['users'] ?? $data['data'] ?? []);

            if (!is_array($items)) {
                $message = 'Format de réponse du webhook invalide (liste d\'utilisateurs attendue).';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($items as $item) {
                $matricule = $this->extractMatricule($item);
                if (empty($matricule)) {
                    $skipped++;
                    continue;
                }

                if (User::where('matricule', $matricule)->exists()) {
                    $skipped++;
                    continue;
                }

                $email = $this->extractEmail($item, $matricule);
                if (User::where('email', $email)->exists()) {
                    $email = Str::lower($matricule) . '+' . Str::random(4) . '@synced.local';
                }

                $password = config('app.webhook_sync_default_password', 'ChangeMe123!');
                if (empty($password) || $password === 'ChangeMe123!') {
                    $password = Str::random(16);
                }

                $role = $this->extractRole($item);

                try {
                    User::create([
                        'matricule' => $matricule,
                        'name' => $this->extractName($item),
                        'first_name' => $this->extractFirstName($item),
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => $role,
                        'active' => true,
                        'fonction' => $item['fonction'] ?? $item['function'] ?? null,
                        'lieu_affectation' => $item['lieu_affectation'] ?? $item['agence'] ?? $item['agency'] ?? null,
                        'zone_affectation' => $item['zone_affectation'] ?? $item['zone'] ?? null,
                        'direction' => $item['direction'] ?? null,
                        'numero_flotte' => $item['numero_flotte'] ?? $item['numero_flotte'] ?? null,
                    ]);
                    $created++;
                } catch (\Exception $e) {
                    Log::error('UserSync: create failed', ['matricule' => $matricule, 'error' => $e->getMessage()]);
                    $errors[] = $matricule . ': ' . $e->getMessage();
                }
            }

            $message = $created . ' utilisateur(s) créé(s). ' . $skipped . ' déjà existant(s) ou ignoré(s).';
            if (!empty($errors)) {
                $message .= ' Erreurs : ' . implode(' ; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= '…';
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'created' => $created,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ]);
            }

            return back()->with('success', $message);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('UserSync: connection failed', ['error' => $e->getMessage()]);
            $message = 'Impossible de joindre le webhook. Vérifiez l\'URL et la connexion.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 502);
            }
            return back()->with('error', $message);
        } catch (\Exception $e) {
            Log::error('UserSync: error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $message = 'Erreur lors de la synchronisation : ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->with('error', $message);
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
}
