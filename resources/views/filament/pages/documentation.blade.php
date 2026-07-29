<x-filament-panels::page>

<div x-data="{ tab: 'user' }" class="space-y-4">

    {{-- Tab switcher --}}
    <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1 w-fit">
        <button
            @click="tab = 'user'"
            :class="tab === 'user'
                ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
            class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-150 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Guide Utilisateur
        </button>
        <button
            @click="tab = 'admin'"
            :class="tab === 'admin'
                ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
            class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-150 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Guide Administrateur
        </button>
    </div>

    {{-- ═══════════════════ GUIDE UTILISATEUR ═══════════════════ --}}
    <div x-show="tab === 'user'" x-transition>

        {{-- Banner --}}
        <div class="rounded-2xl overflow-hidden mb-6" style="background: linear-gradient(135deg,#00574A 0%,#007a68 100%);">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <div class="text-white/70 text-xs font-semibold uppercase tracking-widest mb-1">Documentation</div>
                    <h2 class="text-white text-2xl font-bold">Guide Utilisateur</h2>
                    <p class="text-white/80 text-sm mt-1">Toutes les tâches accessibles depuis l'espace utilisateur</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('docs.user-guide') }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Sections --}}
        <div class="grid grid-cols-1 gap-4">

            @php
            $userSections = [
                [
                    'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
                    'color' => 'blue',
                    'title' => 'Connexion & accès',
                    'steps' => [
                        ['title' => 'Se connecter', 'desc' => 'Accéder à <strong>/login</strong> et saisir votre email et mot de passe fournis par l\'administrateur.'],
                        ['title' => 'Mot de passe oublié', 'desc' => 'Cliquer sur «&nbsp;Mot de passe oublié&nbsp;» et renseigner votre email pour recevoir un lien de réinitialisation.'],
                        ['title' => 'Espace personnel', 'desc' => 'Votre tableau de bord affiche vos demandes en cours, vos missions à venir et vos téléphones assignés.'],
                    ],
                ],
                [
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'color' => 'green',
                    'title' => 'Demandes de carte SIM',
                    'steps' => [
                        ['title' => 'Créer une demande', 'desc' => 'Aller dans <strong>Mes Demandes → Nouvelle demande</strong>. Renseigner le type (Création / Récupération / Suspension / Perte), le bénéficiaire et les justificatifs requis.'],
                        ['title' => 'Suivre l\'état', 'desc' => 'La liste de vos demandes affiche le statut en temps réel : <em>En attente</em>, <em>En cours de traitement</em>, <em>Approuvée</em>, <em>Rejetée</em>.'],
                        ['title' => 'Bordereau de transmission', 'desc' => 'Une fois la demande approuvée, télécharger le bordereau depuis le bouton <strong>Bordereau</strong> en ligne de votre demande.'],
                        ['title' => 'Recherche par ICCID', 'desc' => 'Utiliser le champ filtre ICCID dans la liste des demandes pour retrouver rapidement une demande par numéro de carte.'],
                    ],
                ],
                [
                    'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z',
                    'color' => 'purple',
                    'title' => 'Mes téléphones / SIM',
                    'steps' => [
                        ['title' => 'Consulter mes lignes', 'desc' => 'Accéder à <strong>Mes SIM</strong> pour voir toutes les cartes SIM qui vous sont attribuées, avec numéro de ligne, ICCID et statut.'],
                        ['title' => 'Détail d\'une SIM', 'desc' => 'Cliquer sur une carte SIM pour voir son historique complet (attributions, demandes liées, opérateur).'],
                        ['title' => 'Recherche', 'desc' => 'Utiliser les filtres par numéro de ligne, ICCID, opérateur ou statut pour retrouver une carte précise.'],
                    ],
                ],
                [
                    'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
                    'color' => 'orange',
                    'title' => 'Missions',
                    'steps' => [
                        ['title' => 'Voir les missions', 'desc' => 'Le menu <strong>Missions</strong> liste les missions auxquelles vous participez avec dates, lieu et statut.'],
                        ['title' => 'Calendrier', 'desc' => 'La vue calendrier permet de visualiser les missions du mois avec navigation mois par mois.'],
                        ['title' => 'Créer une demande de mission', 'desc' => 'Compléter le formulaire avec l\'agence concernée, les dates et l\'objet de la mission. Un validateur devra approuver la demande.'],
                    ],
                ],
            ];
            @endphp

            @foreach($userSections as $section)
            @php
            $colors = [
                'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',   'border' => 'border-blue-200 dark:border-blue-800',   'icon' => 'text-blue-600 dark:text-blue-400',   'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300'],
                'green'  => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800', 'icon' => 'text-emerald-600 dark:text-emerald-400', 'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300'],
                'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'border' => 'border-purple-200 dark:border-purple-800', 'icon' => 'text-purple-600 dark:text-purple-400', 'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300'],
                'orange' => ['bg' => 'bg-orange-50 dark:bg-orange-900/20', 'border' => 'border-orange-200 dark:border-orange-800', 'icon' => 'text-orange-600 dark:text-orange-400', 'badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300'],
            ];
            $c = $colors[$section['color']];
            @endphp
            <div class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-3 border-b {{ $c['border'] }}">
                    <div class="{{ $c['icon'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $section['title'] }}</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($section['steps'] as $i => $step)
                    <div class="px-5 py-3 flex gap-3 items-start">
                        <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full {{ $c['badge'] }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                        <div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $step['title'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{!! $step['desc'] !!}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>

    {{-- ═══════════════════ GUIDE ADMINISTRATEUR ═══════════════════ --}}
    <div x-show="tab === 'admin'" x-transition x-cloak>

        {{-- Banner --}}
        <div class="rounded-2xl overflow-hidden mb-6" style="background: linear-gradient(135deg,#1e3a5f 0%,#2d5282 100%);">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <div class="text-white/70 text-xs font-semibold uppercase tracking-widest mb-1">Documentation</div>
                    <h2 class="text-white text-2xl font-bold">Guide Administrateur</h2>
                    <p class="text-white/80 text-sm mt-1">Toutes les tâches de gestion accessibles depuis l'espace admin</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('docs.admin-guide') }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Sections --}}
        <div class="grid grid-cols-1 gap-4">

            @php
            $adminSections = [
                [
                    'icon' => 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18',
                    'color' => 'teal',
                    'title' => 'Gestion des cartes SIM',
                    'steps' => [
                        ['title' => 'Ajouter une SIM', 'desc' => 'Dans <strong>Admin → SIM Cards → Ajouter</strong>, renseigner le numéro de ligne, l\'ICCID, l\'opérateur et le statut initial.'],
                        ['title' => 'Recherche avancée', 'desc' => 'Le tableau de bord SIM supporte les filtres par ICCID, numéro de ligne, opérateur, statut et état d\'attribution. Le champ ICCID déclenche une recherche automatique (500ms de délai).'],
                        ['title' => 'Attribuer une SIM', 'desc' => 'Dans le détail d\'une SIM, utiliser l\'action <strong>Attribuer</strong> pour lier la carte à un collaborateur via son matricule ou son nom.'],
                        ['title' => 'Historique', 'desc' => 'L\'onglet <strong>Historique</strong> dans la fiche SIM liste tous les changements de statut et d\'attribution avec dates et auteurs.'],
                    ],
                ],
                [
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'color' => 'indigo',
                    'title' => 'Validation des demandes SIM',
                    'steps' => [
                        ['title' => 'File de validation', 'desc' => 'Depuis <strong>Admin → Demandes</strong>, les demandes en attente apparaissent en haut. Cliquer sur <strong>Voir</strong> pour examiner le détail et les pièces jointes.'],
                        ['title' => 'Approuver / Rejeter', 'desc' => 'Utiliser les boutons <strong>Approuver</strong> ou <strong>Rejeter</strong> en bas de la fiche de demande. Un motif est requis en cas de rejet.'],
                        ['title' => 'Assigner une SIM', 'desc' => 'Pour une demande de création approuvée, sélectionner la SIM à attribuer dans le champ prévu, puis confirmer. La SIM passe en statut «&nbsp;Attribuée&nbsp;».'],
                        ['title' => 'Bordereau', 'desc' => 'Après attribution, générer le bordereau de transmission depuis le bouton <strong>Bordereau</strong> de la demande. Le réceptionnaire correspond automatiquement au bénéficiaire (création) ou au collaborateur (autre type).'],
                    ],
                ],
                [
                    'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'color' => 'rose',
                    'title' => 'Parc informatique — Équipements',
                    'steps' => [
                        ['title' => 'Ajouter un équipement', 'desc' => 'Dans <strong>Admin → Équipements → Ajouter</strong>, renseigner le type, la marque, le modèle, le numéro de série et le tag actif (étiquette inventaire).'],
                        ['title' => 'Attribuer / Retourner', 'desc' => 'Onglet <strong>Attributions</strong> dans le détail d\'un équipement. Cliquer <strong>Nouvelle attribution</strong> pour affecter à un agent ou une agence. Cliquer <strong>Retour</strong> pour enregistrer le retour avec motif.'],
                        ['title' => 'Envoi en réparation', 'desc' => 'Onglet <strong>Envois en réparation</strong> : cliquer <strong>Envoyer en réparation</strong> pour créer un envoi chez un fournisseur externe. Le statut de l\'équipement passe à <em>En maintenance</em>.'],
                        ['title' => 'Bordereaux réparation', 'desc' => 'Pour chaque envoi, les boutons <strong>Bordereau envoi</strong> et <strong>Bordereau retour</strong> (disponible après retour) génèrent un PDF signable.'],
                        ['title' => 'Export CSV', 'desc' => 'Le bouton <strong>Exporter CSV</strong> dans l\'onglet réparations télécharge l\'historique complet des envois de l\'équipement.'],
                        ['title' => 'Inventaire global', 'desc' => 'La page <strong>Inventaire</strong> offre un tableau filtrable avec export Excel/PDF et des graphiques par statut, type, zone et agence.'],
                    ],
                ],
                [
                    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    'color' => 'amber',
                    'title' => 'Gestion des utilisateurs',
                    'steps' => [
                        ['title' => 'Créer un compte', 'desc' => 'Dans <strong>Admin → Utilisateurs → Ajouter</strong>, renseigner le nom, prénom, matricule, email et rôle (utilisateur, validateur, administrateur).'],
                        ['title' => 'Modifier les rôles', 'desc' => 'Ouvrir le détail d\'un utilisateur et modifier le champ <strong>Rôle</strong>. Les validateurs peuvent approuver les demandes. Seuls les administrateurs accèdent au panneau /admin.'],
                        ['title' => 'Réinitialiser le mot de passe', 'desc' => 'Utiliser l\'action <strong>Envoyer lien de réinitialisation</strong> depuis la fiche utilisateur pour déclencher un email de réinitialisation.'],
                    ],
                ],
                [
                    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'color' => 'cyan',
                    'title' => 'Rapports & exports',
                    'steps' => [
                        ['title' => 'Inventaire Excel/PDF', 'desc' => 'Depuis la page <strong>Inventaire</strong>, appliquer les filtres souhaités puis cliquer <strong>Exporter tout (Excel)</strong> ou <strong>Exporter tout (PDF)</strong> pour générer un rapport filtré.'],
                        ['title' => 'Export des demandes SIM', 'desc' => 'La liste des demandes SIM propose un filtre multi-critères (statut, type, ICCID, date). Utiliser le bouton d\'export pour télécharger les résultats filtrés.'],
                        ['title' => 'Export réparations équipement', 'desc' => 'Dans l\'onglet réparations d\'un équipement, cliquer <strong>Exporter CSV</strong> pour télécharger l\'historique complet des envois en réparation (compatible Excel).'],
                    ],
                ],
            ];
            @endphp

            @foreach($adminSections as $section)
            @php
            $colors = [
                'teal'   => ['bg' => 'bg-teal-50 dark:bg-teal-900/20',   'border' => 'border-teal-200 dark:border-teal-800',   'icon' => 'text-teal-600 dark:text-teal-400',   'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300'],
                'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'border' => 'border-indigo-200 dark:border-indigo-800', 'icon' => 'text-indigo-600 dark:text-indigo-400', 'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300'],
                'rose'   => ['bg' => 'bg-rose-50 dark:bg-rose-900/20',   'border' => 'border-rose-200 dark:border-rose-800',   'icon' => 'text-rose-600 dark:text-rose-400',   'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300'],
                'amber'  => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'border' => 'border-amber-200 dark:border-amber-800', 'icon' => 'text-amber-600 dark:text-amber-400', 'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300'],
                'cyan'   => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/20',   'border' => 'border-cyan-200 dark:border-cyan-800',   'icon' => 'text-cyan-600 dark:text-cyan-400',   'badge' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-300'],
            ];
            $c = $colors[$section['color']];
            @endphp
            <div class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-3 border-b {{ $c['border'] }}">
                    <div class="{{ $c['icon'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $section['title'] }}</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($section['steps'] as $i => $step)
                    <div class="px-5 py-3 flex gap-3 items-start">
                        <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full {{ $c['badge'] }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                        <div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $step['title'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{!! $step['desc'] !!}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>

</div>

</x-filament-panels::page>
