<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> — Sistema Escolar</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DatePicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    <!-- TimePicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <style>
    /* ═══════════════════════════════════════════════════════
       DESIGN TOKENS
    ═══════════════════════════════════════════════════════ */
    :root {
        --primary:        #1B2B4B;
        --primary-light:  #243761;
        --accent:         #3B7FE8;
        --accent-hover:   #2C6FD4;
        --success:        #16A87D;
        --danger:         #E84646;
        --warning:        #E8A020;
        --surface:        #F5F7FC;
        --surface-card:   #FFFFFF;
        --border:         #E2E8F4;
        --text-primary:   #1A2238;
        --text-secondary: #6B7A99;
        --text-muted:     #9AA5BE;
        --shadow-sm:      0 1px 4px rgba(27,43,75,0.07);
        --shadow-md:      0 4px 16px rgba(27,43,75,0.10);
        --shadow-lg:      0 8px 32px rgba(27,43,75,0.14);
        --radius:         12px;
        --radius-sm:      8px;
        --sidebar-w:      260px;
        --topbar-h:       60px;
        --font:           'Sora', sans-serif;
        --font-mono:      'JetBrains Mono', monospace;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
        font-family: var(--font);
        background: var(--surface);
        color: var(--text-primary);
        overflow-x: hidden;
        margin: 0;
    }

    /* ═══════════════════════════════════════════════════════
       LAYOUT WRAPPER
    ═══════════════════════════════════════════════════════ */
    .ci-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* ═══════════════════════════════════════════════════════
       SIDEBAR
    ═══════════════════════════════════════════════════════ */
    .sidebar {
        width: var(--sidebar-w);
        background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
        position: fixed;
        left: 0; top: 0;
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1030;
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        display: flex;
        flex-direction: column;
    }
    .sidebar::-webkit-scrollbar { width: 3px; }
    .sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.04); }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 3px; }
    .sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }

    /* Brand */
    .sidebar-header {
        padding: 1.1rem 1.1rem 0.9rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .sidebar-logo {
        width: 42px; height: 42px;
        border-radius: 10px;
        object-fit: contain;
        background: rgba(255,255,255,0.12);
        padding: 5px;
        flex-shrink: 0;
    }
    .sidebar-logo-placeholder {
        width: 42px; height: 42px;
        background: rgba(255,255,255,0.12);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,0.7);
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .sidebar-brand-text { min-width: 0; }
    .sidebar-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar-subtitle {
        font-size: 0.68rem;
        color: rgba(255,255,255,0.5);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        margin-top: 1px;
    }

    /* Nav */
    .sidebar-nav {
        list-style: none;
        margin: 0.5rem 0 0;
        padding: 0 0.6rem;
        flex: 1;
    }
    .nav-item { margin: 1px 0; }

    /* Nav group label */
    .nav-group-label {
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: rgba(255,255,255,0.28);
        padding: 0.9rem 0.7rem 0.3rem;
        display: block;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.46rem 0.75rem;
        color: rgba(255,255,255,0.62);
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: background .18s, color .18s;
        position: relative;
    }
    .nav-link:hover { background: rgba(255,255,255,0.09); color: rgba(255,255,255,.9); }
    .nav-link.active { background: rgba(59,127,232,0.22); color: #fff; }
    .nav-link.active .nav-icon i { color: var(--accent); }

    .nav-icon {
        width: 22px; height: 22px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .nav-icon i { font-size: 0.85rem; color: rgba(255,255,255,0.45); transition: color .18s; }
    .nav-link:hover .nav-icon i { color: rgba(255,255,255,0.8); }

    .nav-label { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .nav-badge {
        background: var(--accent);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        font-family: var(--font-mono);
        padding: 0.1rem 0.45rem;
        border-radius: 50px;
        line-height: 1.4;
        flex-shrink: 0;
    }
    .nav-badge.warning { background: var(--warning); color: var(--primary); }

    .nav-arrow {
        font-size: 0.6rem;
        color: rgba(255,255,255,0.3);
        transition: transform .22s;
        flex-shrink: 0;
        width: auto !important;
    }
    .nav-link[aria-expanded="true"] .nav-arrow { transform: rotate(90deg); color: rgba(255,255,255,0.6); }
    .nav-link.dropdown-toggle { padding-right: 0.6rem; }

    /* Submenu */
    .submenu {
        list-style: none;
        padding: 0.2rem 0 0.2rem 2.1rem;
        margin: 0;
    }
    .submenu li a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.32rem 0.6rem;
        color: rgba(255,255,255,0.52);
        text-decoration: none;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 400;
        transition: background .15s, color .15s;
        position: relative;
    }
    .submenu li a i { font-size: 0.72rem; width: 15px; flex-shrink: 0; color: rgba(255,255,255,0.3); }
    .submenu li a:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); }
    .submenu li a.active { background: rgba(59,127,232,0.18); color: #fff; }
    .submenu li a.active i { color: var(--accent); }
    .sub-badge {
        margin-left: auto;
        font-size: 0.62rem;
        font-weight: 700;
        font-family: var(--font-mono);
        padding: 0.08rem 0.4rem;
        border-radius: 50px;
        background: var(--accent);
        color: #fff;
        line-height: 1.5;
    }
    .sub-badge.warning { background: var(--warning); color: var(--primary); }

    /* Sidebar Footer */
    .sidebar-footer {
        padding: 0.75rem 0.8rem 1rem;
        border-top: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .sidebar-footer-info {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.65rem;
    }
    .footer-chip {
        display: flex; align-items: center; gap: 0.35rem;
        background: rgba(255,255,255,0.07);
        border-radius: 50px;
        padding: 0.22rem 0.7rem;
        font-size: 0.7rem;
        color: rgba(255,255,255,0.45);
    }
    .footer-chip i { font-size: 0.65rem; }
    .btn-logout {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.8rem;
        background: rgba(232,70,70,0.12);
        color: rgba(255,140,140,0.85);
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        transition: all .18s;
        width: 100%;
    }
    .btn-logout:hover { background: var(--danger); color: #fff; }
    .btn-logout i { font-size: 0.8rem; }

    /* ═══════════════════════════════════════════════════════
       CONTENT AREA
    ═══════════════════════════════════════════════════════ */
    .ci-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: margin-left .3s cubic-bezier(.4,0,.2,1);
    }
    .ci-content.expanded { margin-left: 0; }

    /* ═══════════════════════════════════════════════════════
       TOPBAR
    ═══════════════════════════════════════════════════════ */
    .ci-topbar {
        height: var(--topbar-h);
        background: var(--surface-card);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        position: sticky;
        top: 0;
        z-index: 1020;
        box-shadow: var(--shadow-sm);
        gap: 1rem;
    }
    .topbar-left { display: flex; align-items: center; gap: 1rem; min-width: 0; }
    .topbar-right { display: flex; align-items: center; gap: 0.25rem; flex-shrink: 0; }

    /* Toggle */
    .topbar-toggle {
        width: 36px; height: 36px;
        border: 1.5px solid var(--border);
        background: transparent;
        border-radius: 9px;
        color: var(--text-secondary);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all .18s;
        flex-shrink: 0;
    }
    .topbar-toggle:hover { background: var(--surface); color: var(--primary); border-color: var(--primary); }

    /* Breadcrumb */
    .topbar-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        min-width: 0;
        overflow: hidden;
    }
    .topbar-breadcrumb a {
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 500;
        white-space: nowrap;
        transition: color .15s;
    }
    .topbar-breadcrumb a:hover { color: var(--accent); }
    .topbar-breadcrumb .bc-sep { color: var(--border); font-size: 0.65rem; }
    .topbar-breadcrumb .bc-active {
        color: var(--text-primary);
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    /* Topbar Buttons */
    .topbar-btn {
        width: 36px; height: 36px;
        border: 1.5px solid var(--border);
        background: transparent;
        border-radius: 9px;
        color: var(--text-secondary);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all .18s;
        position: relative;
        text-decoration: none;
        padding: 0;
    }
    .topbar-btn:hover { background: var(--surface); color: var(--primary); border-color: var(--primary); }
    .topbar-btn.accent {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        width: auto;
        padding: 0 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        gap: 0.35rem;
        box-shadow: 0 3px 10px rgba(59,127,232,0.3);
    }
    .topbar-btn.accent:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

    /* Notification dot */
    .notif-btn { position: relative; }
    .notif-dot {
        position: absolute;
        top: -4px; right: -4px;
        min-width: 18px; height: 18px;
        background: var(--danger);
        color: #fff;
        font-size: 0.6rem;
        font-weight: 700;
        font-family: var(--font-mono);
        border-radius: 50px;
        display: flex; align-items: center; justify-content: center;
        padding: 0 4px;
        border: 2px solid var(--surface-card);
    }

    /* Topbar Dropdowns */
    .topbar-dropdown {
        border: 1.5px solid var(--border) !important;
        border-radius: var(--radius) !important;
        box-shadow: var(--shadow-lg) !important;
        padding: 0 !important;
        margin-top: 0.5rem !important;
        overflow: hidden;
        min-width: 280px;
        animation: ddFadeIn .15s ease;
    }
    @keyframes ddFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .dropdown-inner { padding: 1rem; }
    .dropdown-section-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: var(--text-muted);
        margin-bottom: 0.6rem;
        display: block;
    }
    .dropdown-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.6rem;
    }
    .mark-all-read {
        font-size: 0.72rem;
        color: var(--accent);
        text-decoration: none;
        font-weight: 600;
    }
    .mark-all-read:hover { text-decoration: underline; }

    /* Dropdown Items */
    .topbar-dd-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.5rem 0.6rem;
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--text-primary);
        text-decoration: none;
        transition: background .15s;
    }
    .topbar-dd-item:hover { background: var(--surface); }
    .topbar-dd-item.danger { color: var(--danger); }
    .topbar-dd-item.danger:hover { background: rgba(232,70,70,0.06); }
    .dd-icon {
        width: 28px; height: 28px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .dd-icon.green  { background: rgba(22,168,125,0.12); color: var(--success); }
    .dd-icon.blue   { background: rgba(59,127,232,0.12); color: var(--accent); }
    .dd-icon.purple { background: rgba(124,77,255,0.12); color: #7C4DFF; }
    .dd-icon.orange { background: rgba(232,160,32,0.12); color: var(--warning); }
    .dd-icon.red    { background: rgba(232,70,70,0.10);  color: var(--danger); }
    .dd-icon.grey   { background: rgba(107,122,153,0.1); color: var(--text-secondary); }
    .dd-icon.teal   { background: rgba(22,168,125,0.1);  color: var(--success); }
    .dd-divider { height: 1px; background: var(--border); margin: 0.5rem 0; }

    /* Search Dropdown */
    .search-dropdown { min-width: 320px; }
    .search-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        transition: border-color .2s;
    }
    .search-input-wrap:focus-within { border-color: var(--accent); }
    .search-icon { padding: 0 0.7rem; color: var(--text-muted); font-size: 0.8rem; flex-shrink: 0; }
    .search-input {
        flex: 1; border: none; background: transparent;
        padding: 0.55rem 0;
        font-family: var(--font); font-size: 0.85rem;
        color: var(--text-primary); outline: none;
    }
    .search-submit {
        width: 36px; height: 36px; border: none;
        background: var(--accent); color: #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 0.75rem;
        transition: background .15s;
        flex-shrink: 0;
    }
    .search-submit:hover { background: var(--accent-hover); }
    .search-quick-links {
        margin-top: 0.6rem;
        display: flex; gap: 0.5rem;
    }
    .search-quick-links a {
        font-size: 0.73rem;
        color: var(--accent);
        text-decoration: none;
        background: rgba(59,127,232,0.08);
        padding: 0.18rem 0.6rem;
        border-radius: 50px;
        font-weight: 600;
        transition: background .15s;
    }
    .search-quick-links a:hover { background: rgba(59,127,232,0.16); }

    /* Notifications */
    .notif-dropdown { min-width: 340px; }
    .notif-dropdown .dropdown-inner { padding: 0; }
    .notif-dropdown .dropdown-head { padding: 0.85rem 1rem 0; }
    .notif-list { max-height: 340px; overflow-y: auto; }
    .notif-list::-webkit-scrollbar { width: 3px; }
    .notif-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.7rem 1rem;
        text-decoration: none;
        transition: background .15s;
        border-bottom: 1px solid var(--border);
        position: relative;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: var(--surface); }
    .notif-item.unread { background: rgba(59,127,232,0.04); }
    .notif-icon-wrap {
        width: 34px; height: 34px; border-radius: 9px;
        background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; flex-shrink: 0;
    }
    .notif-content { flex: 1; min-width: 0; }
    .notif-title { font-size: 0.8rem; font-weight: 600; color: var(--text-primary); }
    .notif-msg { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem; }
    .notif-time { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem; }
    .notif-new-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--accent); flex-shrink: 0; margin-top: 4px;
    }
    .notif-empty {
        display: flex; flex-direction: column; align-items: center;
        padding: 2rem 1rem; gap: 0.5rem;
        color: var(--text-muted); font-size: 0.82rem;
    }
    .notif-empty i { font-size: 1.8rem; opacity: .3; }
    .notif-footer {
        display: block;
        text-align: center;
        padding: 0.7rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--accent);
        text-decoration: none;
        border-top: 1px solid var(--border);
        transition: background .15s;
    }
    .notif-footer:hover { background: var(--surface); }

    /* User Button */
    .user-btn {
        display: flex; align-items: center; gap: 0.6rem;
        border: 1.5px solid var(--border);
        background: transparent;
        border-radius: 10px;
        padding: 0.3rem 0.7rem 0.3rem 0.35rem;
        cursor: pointer;
        transition: all .18s;
        text-align: left;
    }
    .user-btn:hover { border-color: var(--accent); background: var(--surface); }
    .user-avatar { width: 30px; height: 30px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
    .user-info { min-width: 0; }
    .user-name { font-size: 0.78rem; font-weight: 700; color: var(--text-primary); line-height: 1.2; }
    .user-role { font-size: 0.68rem; color: var(--text-muted); }
    .user-chevron { font-size: 0.6rem; color: var(--text-muted); }
    .user-dropdown { min-width: 240px; }
    .user-dd-header {
        display: flex; align-items: center; gap: 0.7rem;
        padding: 0.85rem 0.6rem 0.5rem;
    }
    .user-dd-avatar { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
    .user-dd-name { font-size: 0.85rem; font-weight: 700; color: var(--text-primary); }
    .user-dd-email { font-size: 0.72rem; color: var(--text-muted); }

    /* ═══════════════════════════════════════════════════════
       MAIN CONTENT
    ═══════════════════════════════════════════════════════ */
    .ci-main {
        flex: 1;
        padding: 1.75rem 1.75rem;
        background: var(--surface);
    }

    /* ═══════════════════════════════════════════════════════
       FOOTER
    ═══════════════════════════════════════════════════════ */
    .ci-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1.75rem;
        background: var(--surface-card);
        border-top: 1px solid var(--border);
        font-size: 0.75rem;
        color: var(--text-muted);
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .footer-sep { margin: 0 0.4rem; opacity: 0.4; }
    .footer-version {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 50px;
        padding: 0.18rem 0.65rem;
        font-size: 0.7rem;
        font-family: var(--font-mono);
        color: var(--text-muted);
    }

    /* ═══════════════════════════════════════════════════════
       GLOBAL ALERT STYLES
    ═══════════════════════════════════════════════════════ */
    .alert {
        border: none;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        padding: 0.8rem 1rem;
    }
    .alert-success { background: rgba(22,168,125,0.10); color: #0E7A5A; border-left: 3px solid var(--success); }
    .alert-danger   { background: rgba(232,70,70,0.08);  color: #B03030; border-left: 3px solid var(--danger); }
    .alert-warning  { background: rgba(232,160,32,0.10); color: #8A5A00; border-left: 3px solid var(--warning); }
    .alert-info     { background: rgba(59,127,232,0.08); color: #1E4D8C; border-left: 3px solid var(--accent); }

    /* ═══════════════════════════════════════════════════════
       SPINNER
    ═══════════════════════════════════════════════════════ */
    .spinner-overlay {
        position: fixed; inset: 0;
        background: rgba(255,255,255,0.7);
        z-index: 9999; display: none;
        align-items: center; justify-content: center;
        backdrop-filter: blur(2px);
    }
    .spinner-overlay.active { display: flex; }

    /* ═══════════════════════════════════════════════════════
       BOOTSTRAP OVERRIDES
    ═══════════════════════════════════════════════════════ */
    .card { border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); }
    .breadcrumb { font-size: 0.82rem; background: transparent; padding: 0; margin: 0; }
    .breadcrumb-item a { color: var(--text-secondary); text-decoration: none; transition: color .15s; }
    .breadcrumb-item a:hover { color: var(--accent); }
    .breadcrumb-item.active { color: var(--text-primary); font-weight: 500; }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--border); }

    /* Select2 */
    .select2-container--bootstrap-5 .select2-selection { min-height: 38px; font-family: var(--font); }

    /* Toastr */
    .toast-success { background-color: var(--success) !important; }
    .toast-error   { background-color: var(--danger)  !important; }
    .toast-info    { background-color: var(--accent)  !important; }
    .toast-warning { background-color: var(--warning) !important; }

    /* Desktop collapsed state */
    .sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }
    .ci-content.expanded { margin-left: 0; }

    /* Sidebar overlay backdrop */
    .sidebar-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(27,43,75,0.5);
        z-index: 1029;
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
    }
    .sidebar-overlay.show { display: block; }

    /* ═══════════════════════════════════════════════════════
       RESPONSIVE — MOBILE
    ═══════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
        /* Sidebar hidden off-screen by default */
        .sidebar {
            transform: translateX(calc(-1 * var(--sidebar-w)));
            box-shadow: none;
        }
        /* Slide in when toggled */
        .sidebar.mobile-open {
            transform: translateX(0);
            box-shadow: 6px 0 28px rgba(27,43,75,0.3);
        }
        /* Content always full width on mobile */
        .ci-content { margin-left: 0 !important; }
        .ci-content.expanded { margin-left: 0 !important; }
        .ci-main { padding: 1rem; }
        .ci-topbar { padding: 0 0.85rem; }
        .ci-footer { padding: 0.65rem 1rem; flex-direction: column; text-align: center; gap: 0.25rem; }
    }

    @media (max-width: 480px) {
        .ci-main { padding: 0.75rem; }
        .ci-topbar { padding: 0 0.75rem; gap: 0.5rem; }
    }

    @media print {
        .sidebar, .ci-topbar, .ci-footer, .sidebar-overlay { display: none !important; }
        .ci-content { margin-left: 0 !important; }
        .ci-main { padding: 0 !important; }
    }
    </style>
</head>
<body>
<div class="ci-wrapper">

    <!-- Sidebar -->
    <?= view('admin/layouts/sidebar') ?>

    <!-- Mobile overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Content -->
    <div class="ci-content" id="mainContent">

        <!-- Topbar -->
        <?= view('admin/layouts/header') ?>

        <!-- Page Content -->
        <main class="ci-main">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <?= view('admin/layouts/footer') ?>
    </div>
</div>

<!-- Loading Spinner -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-border" style="color:var(--accent);width:2.5rem;height:2.5rem;" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/localization/messages_pt_BR.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
(function () {
    'use strict';

    const sidebar  = document.getElementById('mainSidebar');
    const content  = document.getElementById('mainContent');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarCollapse');

    const isMobile = () => window.innerWidth <= 768;

    // Restore state
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        content.classList.add('expanded');
    }

    toggleBtn.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        } else {
            const collapsed = sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded', collapsed);
            localStorage.setItem('sidebarCollapsed', collapsed);
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        }
    });

    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);

    // Notification counter update
    setInterval(function () {
        fetch('<?= site_url('admin/notifications/getUnreadCount') ?>')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const dot = document.querySelector('.notif-dot');
                const btn = document.querySelector('.notif-btn');
                if (data.count > 0) {
                    if (dot) {
                        dot.textContent = data.count > 9 ? '9+' : data.count;
                    } else {
                        const d = document.createElement('span');
                        d.className = 'notif-dot';
                        d.textContent = data.count > 9 ? '9+' : data.count;
                        btn.appendChild(d);
                    }
                } else {
                    if (dot) dot.remove();
                }
            })
            .catch(() => {});
    }, 60000);

})();
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>