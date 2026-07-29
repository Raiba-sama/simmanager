<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Guide Administrateur — SimManager</title>
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #1a1a1a; }
        .page-wrap { padding: 18mm 22mm 20mm 22mm; }

        /* Header */
        .hdr { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .hdr td { vertical-align: middle; padding: 0; }
        .hdr-right { text-align: right; }
        .hdr-right .doc-label { font-size: 8pt; color: #6b7280; text-transform: uppercase; letter-spacing: .8px; }
        .hdr-right .doc-type { font-size: 9pt; color: #1e3a5f; font-weight: bold; margin-top: 3px; }

        /* Title band */
        .title-band { background: #1e3a5f; color: white; text-align: center; font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 0; margin-bottom: 6px; }
        .subtitle-band { background: #1e3a5f22; color: #1e3a5f; text-align: center; font-size: 9pt; padding: 5px 0; margin-bottom: 16px; }

        /* Meta */
        .meta { font-size: 8pt; color: #9ca3af; margin-bottom: 16px; text-align: right; }

        /* Section header */
        .sec-hdr { background: #eff6ff; border-left: 3px solid #1e3a5f; color: #1e3a5f; font-size: 9.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 6px 10px; margin: 18px 0 8px 0; }

        /* Steps table */
        .steps-tbl { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .steps-tbl td { padding: 6px 8px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .step-num { width: 22px; text-align: center; }
        .step-badge { display: inline-block; width: 18px; height: 18px; background: #1e3a5f; color: white; border-radius: 50%; font-size: 8pt; font-weight: bold; text-align: center; line-height: 18px; }
        .step-title { font-weight: bold; font-size: 9pt; color: #1f2937; }
        .step-desc { font-size: 9pt; color: #4b5563; margin-top: 2px; }

        /* Warning note */
        .note-box { background: #fffbeb; border: 1px solid #fcd34d; border-left: 3px solid #f59e0b; padding: 7px 10px; font-size: 8.5pt; color: #92400e; margin: 10px 0; }

        /* Footer */
        .page-footer { border-top: 1px solid #e5e7eb; padding: 5px 0 0 0; margin-top: 20px; font-size: 7.5pt; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
<div class="page-wrap">

<table class="hdr">
    <tr>
        <td style="width:110px;">
            <img src="{{ public_path('images/acep_madagascar_logo-1.png') }}" style="width:90px; height:auto;">
        </td>
        <td class="hdr-right">
            <div class="doc-label">Documentation technique</div>
            <div class="doc-type">Guide Administrateur</div>
        </td>
    </tr>
</table>

<div class="title-band">GUIDE ADMINISTRATEUR</div>
<div class="subtitle-band">SimManager — Espace Admin &nbsp;·&nbsp; {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>

<div class="meta">Généré le {{ now()->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }}</div>

<div class="note-box">Accès restreint — Ce document est destiné aux administrateurs et validateurs du système SimManager. Ne pas distribuer.</div>

{{-- ── 1. SIM ── --}}
<div class="sec-hdr">1. Gestion des Cartes SIM</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Ajouter une SIM</div>
            <div class="step-desc">Dans <strong>Admin &gt; SIM Cards &gt; Ajouter</strong>, renseigner le numéro de ligne, l'ICCID, l'opérateur et le statut initial.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Recherche avancée</div>
            <div class="step-desc">Le tableau de bord SIM supporte les filtres par ICCID, numéro de ligne, opérateur, statut et état d'attribution. Le champ ICCID déclenche une recherche automatique (500ms de délai).</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Attribuer une SIM</div>
            <div class="step-desc">Dans le détail d'une SIM, utiliser l'action <strong>Attribuer</strong> pour lier la carte à un collaborateur via son matricule ou son nom.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">4</span></td>
        <td>
            <div class="step-title">Historique</div>
            <div class="step-desc">L'onglet Historique dans la fiche SIM liste tous les changements de statut et d'attribution avec dates et auteurs.</div>
        </td>
    </tr>
</table>

{{-- ── 2. Demandes ── --}}
<div class="sec-hdr">2. Validation des Demandes SIM</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">File de validation</div>
            <div class="step-desc">Depuis <strong>Admin &gt; Demandes</strong>, les demandes en attente apparaissent en haut. Cliquer sur Voir pour examiner le détail et les pièces jointes.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Approuver / Rejeter</div>
            <div class="step-desc">Utiliser les boutons <strong>Approuver</strong> ou <strong>Rejeter</strong> en bas de la fiche. Un motif est requis en cas de rejet.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Assigner une SIM</div>
            <div class="step-desc">Pour une demande de création approuvée, sélectionner la SIM à attribuer dans le champ prévu, puis confirmer. La SIM passe en statut « Attribuée ».</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">4</span></td>
        <td>
            <div class="step-title">Bordereau de transmission</div>
            <div class="step-desc">Après attribution, générer le bordereau depuis le bouton Bordereau de la demande. Le réceptionnaire correspond automatiquement au bénéficiaire (création) ou au collaborateur (autre type).</div>
        </td>
    </tr>
</table>

{{-- ── 3. Équipements ── --}}
<div class="sec-hdr">3. Parc Informatique — Équipements</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Ajouter un équipement</div>
            <div class="step-desc">Dans <strong>Admin &gt; Équipements &gt; Ajouter</strong>, renseigner le type, la marque, le modèle, le numéro de série et le tag actif (étiquette inventaire).</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Attribuer / Retourner</div>
            <div class="step-desc">Onglet <strong>Attributions</strong> dans le détail d'un équipement. Cliquer Nouvelle attribution pour affecter à un agent ou une agence. Cliquer Retour pour enregistrer le retour avec motif.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Envoi en réparation</div>
            <div class="step-desc">Onglet <strong>Envois en réparation</strong> : cliquer Envoyer en réparation pour créer un envoi chez un fournisseur externe. Le statut de l'équipement passe à En maintenance.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">4</span></td>
        <td>
            <div class="step-title">Bordereaux réparation</div>
            <div class="step-desc">Pour chaque envoi, les boutons <strong>Bordereau envoi</strong> et <strong>Bordereau retour</strong> (disponible après retour) génèrent un PDF signable en A4.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">5</span></td>
        <td>
            <div class="step-title">Export CSV</div>
            <div class="step-desc">Le bouton <strong>Exporter CSV</strong> dans l'onglet réparations télécharge l'historique complet des envois de l'équipement (compatible Excel).</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">6</span></td>
        <td>
            <div class="step-title">Inventaire global</div>
            <div class="step-desc">La page <strong>Inventaire</strong> offre un tableau filtrable avec export Excel/PDF et des graphiques par statut, type, zone et agence.</div>
        </td>
    </tr>
</table>

{{-- ── 4. Utilisateurs ── --}}
<div class="sec-hdr">4. Gestion des Utilisateurs</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Créer un compte</div>
            <div class="step-desc">Dans <strong>Admin &gt; Utilisateurs &gt; Ajouter</strong>, renseigner le nom, prénom, matricule, email et rôle (utilisateur, validateur, administrateur).</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Modifier les rôles</div>
            <div class="step-desc">Ouvrir le détail d'un utilisateur et modifier le champ Rôle. Les validateurs peuvent approuver les demandes. Seuls les administrateurs accèdent au panneau /admin.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Réinitialiser le mot de passe</div>
            <div class="step-desc">Utiliser l'action Envoyer lien de réinitialisation depuis la fiche utilisateur pour déclencher un email de réinitialisation.</div>
        </td>
    </tr>
</table>

{{-- ── 5. Rapports ── --}}
<div class="sec-hdr">5. Rapports &amp; Exports</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Inventaire Excel / PDF</div>
            <div class="step-desc">Depuis la page <strong>Inventaire</strong>, appliquer les filtres souhaités puis cliquer Exporter tout (Excel) ou Exporter tout (PDF) pour générer un rapport filtré.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Export des demandes SIM</div>
            <div class="step-desc">La liste des demandes SIM propose un filtre multi-critères (statut, type, ICCID, date). Utiliser le bouton d'export pour télécharger les résultats filtrés.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Export réparations équipement</div>
            <div class="step-desc">Dans l'onglet réparations d'un équipement, cliquer <strong>Exporter CSV</strong> pour télécharger l'historique complet des envois en réparation.</div>
        </td>
    </tr>
</table>

<div class="page-footer">
    Document interne — ACEP Madagascar &nbsp;·&nbsp; SimManager &nbsp;·&nbsp; Guide Administrateur &nbsp;·&nbsp; Confidentiel — Accès restreint
</div>

</div>
</body>
</html>
