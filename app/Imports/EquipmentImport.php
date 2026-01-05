<?php

namespace App\Imports;

use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\EquipmentType;
use App\Models\User;
use App\Models\TransmissionSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EquipmentImport implements ToCollection, WithHeadingRow
{
    protected $imported = 0;
    protected $skipped = 0;
    protected $errors = [];
    protected $currentRow = 0;
    protected $headerMap = []; // Mapping des indices vers les noms de colonnes normalisés

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            $isFirstRow = true;
            
            foreach ($rows as $row) {
                $this->currentRow++;
                
                // La première ligne contient les en-têtes
                if ($isFirstRow) {
                    $this->buildHeaderMap($row);
                    $isFirstRow = false;
                    continue; // Ignorer la ligne d'en-têtes
                }
                
                try {
                    $this->importRow($row);
                } catch (\Exception $e) {
                    $this->errors[] = "Ligne {$this->currentRow}: " . $e->getMessage();
                    Log::error("Erreur import équipement ligne {$this->currentRow}", [
                        'row' => $row->toArray(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    protected function buildHeaderMap(Collection $headerRow)
    {
        // Créer un mapping des indices vers les noms de colonnes normalisés
        foreach ($headerRow as $index => $headerName) {
            if (!empty($headerName)) {
                $normalizedKey = $this->normalizeKey($headerName);
                $this->headerMap[$index] = $normalizedKey;
            }
        }
        
        Log::info("Header map créé", ['header_map' => $this->headerMap]);
    }

    protected function importRow(Collection $row)
    {
        // Nettoyer les clés (supprimer espaces, caractères spéciaux)
        $data = $this->normalizeRow($row);
        
        // Vérifier si la ligne est vraiment vide (aucune donnée utile)
        $hasData = false;
        foreach ($data as $value) {
            if (!empty($value) && trim($value) !== '') {
                $hasData = true;
                break;
            }
        }
        
        if (!$hasData) {
            // Ligne complètement vide, on l'ignore sans compter comme "skipped"
            return;
        }
        
        // Vérifier si l'équipement existe déjà (par serial_number ou asset_tag)
        // Essayer plusieurs variantes pour SN
        $serialNumber = null;
        if (!empty($data['sn'])) {
            $serialNumber = trim($data['sn']);
        } elseif (!empty($data['serial_number'])) {
            $serialNumber = trim($data['serial_number']);
        } elseif (!empty($data['serial'])) {
            $serialNumber = trim($data['serial']);
        }
        
        // Essayer plusieurs variantes pour N° Inventaire
        $assetTag = null;
        if (!empty($data['n_inventaire'])) {
            $assetTag = trim($data['n_inventaire']);
        } elseif (!empty($data['asset_tag'])) {
            $assetTag = trim($data['asset_tag']);
        } elseif (!empty($data['inventaire'])) {
            $assetTag = trim($data['inventaire']);
        } elseif (!empty($data['colonne_1'])) {
            // "Colonne 1" peut contenir le N° Inventaire
            $assetTag = trim($data['colonne_1']);
        }
        
        // Si ni SN ni N° Inventaire, on ne peut pas créer l'équipement
        if (empty($serialNumber) && empty($assetTag)) {
            // Logger pour debug
            if ($this->currentRow <= 10) {
                Log::warning("Ligne {$this->currentRow}: Aucun SN ou N° Inventaire", [
                    'data' => $data,
                    'row_keys' => array_keys($data)
                ]);
            }
            $this->skipped++;
            return;
        }
        
        // Vérifier existence uniquement si on a au moins un identifiant
        $existingEquipment = null;
        if ($serialNumber || $assetTag) {
            $existingEquipment = Equipment::where(function($query) use ($serialNumber, $assetTag) {
                if ($serialNumber) {
                    $query->where('serial_number', $serialNumber);
                }
                if ($assetTag) {
                    if ($serialNumber) {
                        $query->orWhere('asset_tag', $assetTag);
                    } else {
                        $query->where('asset_tag', $assetTag);
                    }
                }
            })->first();
        }
        
        if ($existingEquipment) {
            $this->skipped++;
            return;
        }
        
        // Déterminer le type d'équipement (par défaut: Desktop/Laptop)
        $equipmentType = $this->getEquipmentType($data);
        
        // Créer les spécifications
        $specifications = $this->buildSpecifications($data);
        
        // Créer l'équipement
        $equipment = Equipment::create([
            'equipment_type_id' => $equipmentType->id,
            'brand' => $data['marque'] ?? null,
            'model' => $data['modele'] ?? null,
            'serial_number' => $serialNumber,
            'asset_tag' => $assetTag,
            'purchase_date' => $this->parseDate($data['date_configuration'] ?? null),
            'status' => $this->mapStatus($data['status'] ?? 'Livré'),
            'condition' => 'good',
            'specifications' => $specifications,
            'notes' => $this->buildNotes($data),
            'created_by' => auth()->id() ?? 1,
        ]);
        
        // Créer l'attribution si un utilisateur est spécifié
        if (!empty($data['user_mle'])) {
            $user = User::where('matricule', trim($data['user_mle']))->first();
            
            if ($user) {
                // Chercher ou créer le bon de transmission
                $transmissionSheet = $this->getOrCreateTransmissionSheet($data, $user);
                
                $assignedAt = $this->parseDate($data['livraison_date'] ?? $data['date_configuration'] ?? now());
                
                EquipmentAssignment::create([
                    'equipment_id' => $equipment->id,
                    'assigned_to_user_id' => $user->id,
                    'assigned_by' => auth()->id() ?? 1,
                    'assigned_at' => $assignedAt ?? now(),
                    'notes' => $this->buildAssignmentNotes($data),
                    'transmission_sheet_id' => $transmissionSheet?->id,
                ]);
                
                // Mettre à jour le statut de l'équipement
                $equipment->update(['status' => 'assigned']);
            } else {
                // Utilisateur non trouvé - ajouter une note
                $notes = $equipment->notes ?? '';
                $notes .= "\n⚠ Utilisateur non trouvé (MLE: {$data['user_mle']})";
                $equipment->update(['notes' => trim($notes)]);
            }
        }
        
        $this->imported++;
    }

    protected function normalizeRow(Collection $row): array
    {
        $normalized = [];
        
        // Mapper les colonnes avec différentes variantes possibles
        $mapping = [
            'sn' => ['sn', 'serial_number', 'serial', 'numéro_serie', 'numero_serie', 's_n', 's_n_'],
            'marque' => ['marque', 'brand', 'manufacturer'],
            'modele' => ['modele', 'model', 'modèle'],
            'n_inventaire' => ['n_inventaire', 'asset_tag', 'inventaire', 'n_inventaire', 'numero_inventaire', 'n_inventaire', 'colonne_1'],
            'ram' => ['ram', 'memory'],
            'cpu' => ['cpu', 'processeur'],
            'disque' => ['disque', 'disk', 'stockage'],
            'os_installed' => ['os_installed', 'os', 'operating_system', 'os_installed'],
            'cle_activation_win' => ['cle_activation_win', 'windows_key', 'licence_windows', 'clé_dactivation_win', 'cle_dactivation_win'],
            'office' => ['office', 'microsoft_office'],
            'licence_office' => ['licence_office', 'office_licence', 'licence_office'],
            'hostname' => ['hostname', 'nom_machine'],
            'user_mle' => ['user_mle', 'mle', 'matricule', 'user_matricule'],
            'user_name' => ['user_name', 'nom_utilisateur', 'user', 'user_name'],
            'user_status' => ['user_status', 'user_status'],
            'direction' => ['direction', 'dir'],
            'livraison_date' => ['livraison_date', 'date_livraison', 'delivery_date'],
            'bt_n' => ['bt_n', 'bt_no', 'bon_transmission', 'numero_bt', 'bt_n'],
            'status' => ['status', 'statut', 'etat'],
            'date_configuration' => ['date_configuration', 'date_config', 'config_date'],
            'localisation' => ['localisation', 'location', 'lieu'],
        ];
        
        // Créer un tableau associatif à partir de la ligne en utilisant le headerMap
        $normalizedRow = [];
        foreach ($row as $index => $value) {
            if (isset($this->headerMap[$index])) {
                $normalizedRow[$this->headerMap[$index]] = $value;
            }
        }
        
        // Debug: logger les clés normalisées pour les premières lignes
        if ($this->currentRow <= 3) {
            Log::info("Import équipement - Ligne {$this->currentRow} - Données normalisées", [
                'header_map' => $this->headerMap,
                'normalized_row' => $normalizedRow
            ]);
        }
        
        // Maintenant mapper les colonnes
        foreach ($mapping as $key => $variants) {
            foreach ($variants as $variant) {
                $normalizedKey = $this->normalizeKey($variant);
                
                if (isset($normalizedRow[$normalizedKey])) {
                    $value = $normalizedRow[$normalizedKey];
                    // Accepter la valeur même si elle est vide pour le debug
                    $normalized[$key] = $value;
                    break;
                }
            }
        }
        
        return $normalized;
    }

    protected function normalizeKey(string $key): string
    {
        // Convertir en minuscules
        $key = mb_strtolower($key, 'UTF-8');
        
        // Remplacer les caractères accentués
        $key = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ç'],
            ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'c'],
            $key
        );
        
        // Remplacer espaces, tirets, caractères spéciaux par underscore
        $key = str_replace([' ', '-', '°', 'n°', 'nº', 'no', 'no.', 'nº', 'n°'], '_', $key);
        
        // Supprimer les caractères non alphanumériques sauf underscore
        $key = preg_replace('/[^a-z0-9_]/', '', $key);
        
        // Supprimer les underscores multiples
        $key = preg_replace('/_+/', '_', $key);
        
        // Supprimer les underscores en début/fin
        $key = trim($key, '_');
        
        return $key;
    }

    protected function getEquipmentType(array $data): EquipmentType
    {
        // Déterminer le type selon le modèle ou la marque
        $model = strtolower($data['modele'] ?? '');
        $brand = strtolower($data['marque'] ?? '');
        
        // Chercher un type existant ou créer un type par défaut
        if (str_contains($model, 'laptop') || str_contains($model, 'portable') || 
            str_contains($model, 'notebook') || str_contains($model, 'aspire lite')) {
            return EquipmentType::firstOrCreate(
                ['name' => 'Laptop'],
                ['category' => 'computer', 'description' => 'Ordinateur portable']
            );
        }
        
        if (str_contains($model, 'desktop') || str_contains($model, 'tour')) {
            return EquipmentType::firstOrCreate(
                ['name' => 'Desktop'],
                ['category' => 'computer', 'description' => 'Ordinateur de bureau']
            );
        }
        
        // Par défaut: Desktop
        return EquipmentType::firstOrCreate(
            ['name' => 'Desktop'],
            ['category' => 'computer', 'description' => 'Ordinateur de bureau']
        );
    }

    protected function buildSpecifications(array $data): array
    {
        $specs = [];
        
        if (!empty($data['ram'])) $specs['ram'] = $data['ram'];
        if (!empty($data['cpu'])) $specs['cpu'] = $data['cpu'];
        if (!empty($data['disque'])) $specs['disque'] = $data['disque'];
        if (!empty($data['os_installed'])) $specs['os'] = $data['os_installed'];
        if (!empty($data['cle_activation_win'])) $specs['windows_key'] = $data['cle_activation_win'];
        if (!empty($data['office'])) $specs['office'] = $data['office'];
        if (!empty($data['licence_office'])) $specs['office_licence'] = $data['licence_office'];
        if (!empty($data['hostname'])) $specs['hostname'] = $data['hostname'];
        
        return $specs;
    }

    protected function buildNotes(array $data): ?string
    {
        $notes = [];
        
        if (!empty($data['localisation'])) {
            $notes[] = "Localisation: " . $data['localisation'];
        }
        
        if (!empty($data['direction'])) {
            $notes[] = "Direction: " . $data['direction'];
        }
        
        if (!empty($data['user_name'])) {
            $notes[] = "Utilisateur: " . $data['user_name'];
        }
        
        return !empty($notes) ? implode("\n", $notes) : null;
    }

    protected function buildAssignmentNotes(array $data): ?string
    {
        $notes = [];
        
        if (!empty($data['bt_n'])) {
            $notes[] = "BT N°: " . $data['bt_n'];
        }
        
        if (!empty($data['localisation'])) {
            $notes[] = "Localisation: " . $data['localisation'];
        }
        
        return !empty($notes) ? implode("\n", $notes) : null;
    }

    protected function getOrCreateTransmissionSheet(array $data, User $user): ?TransmissionSheet
    {
        if (empty($data['bt_n'])) {
            return null;
        }
        
        // Chercher un bon de transmission existant
        $sheet = TransmissionSheet::where('sheet_number', $data['bt_n'])->first();
        
        if (!$sheet) {
            // Créer un nouveau bon de transmission
            $transmissionDate = $this->parseDate($data['livraison_date'] ?? now());
            
            $sheet = TransmissionSheet::create([
                'sheet_number' => $data['bt_n'],
                'type' => 'assignment',
                'created_by' => auth()->id() ?? 1,
                'to_user_id' => $user->id,
                'transmission_date' => $transmissionDate ? $transmissionDate->format('Y-m-d') : now()->format('Y-m-d'),
                'status' => 'completed',
                'notes' => "Import automatique depuis Excel",
            ]);
        }
        
        return $sheet;
    }

    protected function parseDate($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }
        
        // Si c'est déjà un objet Carbon ou DateTime
        if ($date instanceof Carbon) {
            return $date;
        }
        
        if ($date instanceof \DateTime) {
            return Carbon::instance($date);
        }
        
        try {
            // Si c'est un nombre, c'est probablement une date Excel
            if (is_numeric($date) && $date > 1) {
                try {
                    // Les dates Excel sont généralement > 1 (nombre de jours depuis 1900-01-01)
                    // Si c'est une date Excel valide (entre 1 et ~100000)
                    if ($date > 1 && $date < 100000) {
                        // Utiliser la bibliothèque PhpSpreadsheet si disponible
                        if (class_exists(\PhpOffice\PhpSpreadsheet\Shared\Date::class)) {
                            $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
                            return Carbon::instance($dateTime);
                        }
                    }
                } catch (\Exception $e) {
                    // Si ce n'est pas une date Excel valide, continuer avec les autres formats
                }
            }
            
            // Si c'est une chaîne, essayer différents formats
            if (is_string($date)) {
                // Formats français et internationaux
                $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'd/m/y', 'd-m-y', 'd.m.Y', 'Y.m.d'];
                foreach ($formats as $format) {
                    try {
                        $parsed = Carbon::createFromFormat($format, trim($date));
                        if ($parsed) {
                            return $parsed;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                // Essayer de parser automatiquement
                try {
                    return Carbon::parse($date);
                } catch (\Exception $e) {
                    // Ignorer
                }
            }
        } catch (\Exception $e) {
            Log::warning("Impossible de parser la date: {$date}", ['error' => $e->getMessage()]);
        }
        
        return null;
    }

    protected function mapStatus(string $status): string
    {
        $status = strtolower(trim($status));
        
        return match($status) {
            'livré', 'livre', 'delivered', 'assigned', 'attribué' => 'assigned',
            'disponible', 'available', 'libre' => 'available',
            'maintenance', 'en maintenance' => 'maintenance',
            'retiré', 'retire', 'retired' => 'retired',
            'perdu', 'lost' => 'lost',
            'endommagé', 'damaged' => 'damaged',
            default => 'available',
        };
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

