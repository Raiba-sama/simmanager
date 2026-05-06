<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ── Police Poppins sur tout le panel ───────────────────────── */
:root {
    --font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, sans-serif;
}
body,
.fi-body,
.fi-sidebar,
.fi-topbar,
.fi-main,
input, select, textarea, button {
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
}

/* ── Sidebar : alignement avec le thème app ─────────────────── */
.fi-sidebar-header {
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

/* ── Boutons primaires : s'assurer du bon vert ──────────────── */
.fi-btn-color-primary {
    --tw-ring-color: rgba(0, 87, 74, 0.4) !important;
}

/* ── Tables : hover plus subtil ─────────────────────────────── */
.fi-ta-row:hover {
    background-color: rgba(0, 87, 74, 0.04) !important;
}

/* ── Badges & statuts ───────────────────────────────────────── */
.fi-badge {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
}

/* ── Formulaires : inputs plus cohérents ────────────────────── */
.fi-fo-field-wrp-label label {
    font-weight: 600 !important;
    font-size: 0.8rem !important;
}

/* ── Pagination ─────────────────────────────────────────────── */
.fi-pagination {
    font-family: 'Poppins', sans-serif !important;
}
</style>
