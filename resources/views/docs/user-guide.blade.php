<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Guide Utilisateur — SimManager</title>
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
        .hdr-right .doc-type { font-size: 9pt; color: #374151; font-weight: bold; margin-top: 3px; }

        /* Title band */
        .title-band { background: #00574A; color: white; text-align: center; font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 0; margin-bottom: 6px; }
        .subtitle-band { background: #00574a22; color: #00574A; text-align: center; font-size: 9pt; padding: 5px 0; margin-bottom: 16px; }

        /* Meta */
        .meta { font-size: 8pt; color: #9ca3af; margin-bottom: 16px; text-align: right; }

        /* Section header */
        .sec-hdr { background: #f0fdf4; border-left: 3px solid #00574A; color: #00574A; font-size: 9.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 6px 10px; margin: 18px 0 8px 0; }

        /* Steps table */
        .steps-tbl { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .steps-tbl td { padding: 6px 8px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .step-num { width: 22px; text-align: center; }
        .step-badge { display: inline-block; width: 18px; height: 18px; background: #00574A; color: white; border-radius: 50%; font-size: 8pt; font-weight: bold; text-align: center; line-height: 18px; }
        .step-title { font-weight: bold; font-size: 9pt; color: #1f2937; }
        .step-desc { font-size: 9pt; color: #4b5563; margin-top: 2px; }

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
            <div class="doc-type">Guide Utilisateur</div>
        </td>
    </tr>
</table>

<div class="title-band">GUIDE UTILISATEUR</div>
<div class="subtitle-band">SimManager — Espace Utilisateur &nbsp;·&nbsp; {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>

<div class="meta">Généré le {{ now()->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }}</div>

{{-- ── 1. Connexion ── --}}
<div class="sec-hdr">1. Connexion &amp; Accès</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Se connecter</div>
            <div class="step-desc">Accéder à <strong>/login</strong> et saisir votre email et mot de passe fournis par l'administrateur.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Mot de passe oublié</div>
            <div class="step-desc">Cliquer sur « Mot de passe oublié » et renseigner votre email pour recevoir un lien de réinitialisation.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Espace personnel</div>
            <div class="step-desc">Votre tableau de bord affiche vos demandes en cours, vos missions à venir et vos téléphones assignés.</div>
        </td>
    </tr>
</table>

{{-- ── 2. Demandes SIM ── --}}
<div class="sec-hdr">2. Demandes de Carte SIM</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Créer une demande</div>
            <div class="step-desc">Aller dans <strong>Mes Demandes → Nouvelle demande</strong>. Renseigner le type (Création / Récupération / Suspension / Perte), le bénéficiaire et les justificatifs requis.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Suivre l'état</div>
            <div class="step-desc">La liste de vos demandes affiche le statut en temps réel : En attente, En cours de traitement, Approuvée, Rejetée.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Bordereau de transmission</div>
            <div class="step-desc">Une fois la demande approuvée, télécharger le bordereau depuis le bouton Bordereau en ligne de votre demande.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">4</span></td>
        <td>
            <div class="step-title">Recherche par ICCID</div>
            <div class="step-desc">Utiliser le champ filtre ICCID dans la liste des demandes pour retrouver rapidement une demande par numéro de carte. La recherche se déclenche automatiquement après saisie.</div>
        </td>
    </tr>
</table>

{{-- ── 3. Mes SIM ── --}}
<div class="sec-hdr">3. Mes Téléphones / SIM</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Consulter mes lignes</div>
            <div class="step-desc">Accéder à <strong>Mes SIM</strong> pour voir toutes les cartes SIM qui vous sont attribuées, avec numéro de ligne, ICCID et statut.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Détail d'une SIM</div>
            <div class="step-desc">Cliquer sur une carte SIM pour voir son historique complet (attributions, demandes liées, opérateur).</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Recherche</div>
            <div class="step-desc">Utiliser les filtres par numéro de ligne, ICCID, opérateur ou statut pour retrouver une carte précise.</div>
        </td>
    </tr>
</table>

{{-- ── 4. Missions ── --}}
<div class="sec-hdr">4. Missions</div>
<table class="steps-tbl">
    <tr>
        <td class="step-num"><span class="step-badge">1</span></td>
        <td>
            <div class="step-title">Voir les missions</div>
            <div class="step-desc">Le menu <strong>Missions</strong> liste les missions auxquelles vous participez avec dates, lieu et statut.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">2</span></td>
        <td>
            <div class="step-title">Calendrier</div>
            <div class="step-desc">La vue calendrier permet de visualiser les missions du mois avec navigation mois par mois.</div>
        </td>
    </tr>
    <tr>
        <td class="step-num"><span class="step-badge">3</span></td>
        <td>
            <div class="step-title">Créer une demande de mission</div>
            <div class="step-desc">Compléter le formulaire avec l'agence concernée, les dates et l'objet de la mission. Un validateur devra approuver la demande.</div>
        </td>
    </tr>
</table>

<div class="page-footer">
    Document interne — ACEP Madagascar &nbsp;·&nbsp; SimManager &nbsp;·&nbsp; Guide Utilisateur &nbsp;·&nbsp; Confidentiel
</div>

</div>
</body>
</html>
