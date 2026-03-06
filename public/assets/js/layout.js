/**
 * SISTEMA ESCOLAR — LAYOUT MASTER JS
 * Handles: sidebar collapse/mobile, topbar interactions, alerts, notifications
 */
(function () {
    'use strict';

    /* ─────────────────────────────────────────────────────
       SIDEBAR CONTROLLER
    ───────────────────────────────────────────────────── */
    const Sidebar = {
        el:      null,
        content: null,
        overlay: null,
        toggleBtn: null,
        STORAGE_KEY: 'ci_sidebar_collapsed',

        init() {
            this.el       = document.getElementById('mainSidebar');
            this.content  = document.getElementById('mainContent');
            this.overlay  = document.getElementById('sidebarOverlay');
            this.toggleBtn = document.getElementById('sidebarCollapse');

            if (!this.el || !this.toggleBtn) return;

            // Restore desktop state
            if (!this._isMobile() && localStorage.getItem(this.STORAGE_KEY) === '1') {
                this._collapse(false);
            }

            // Toggle button
            this.toggleBtn.addEventListener('click', () => this.toggle());

            // Overlay click (mobile close)
            this.overlay?.addEventListener('click', () => this._mobileClose());

            // Resize handler
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    if (!this._isMobile()) this._mobileClose();
                }, 150);
            });

            // Escape key
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && this._isMobile()) this._mobileClose();
            });
        },

        toggle() {
            if (this._isMobile()) {
                this._mobileToggle();
            } else {
                this._desktopToggle();
            }
        },

        _desktopToggle() {
            const collapsed = this.el.classList.toggle('is-collapsed');
            this.content?.classList.toggle('sb-collapsed', collapsed);
            localStorage.setItem(this.STORAGE_KEY, collapsed ? '1' : '0');
        },

        _collapse(animate = true) {
            if (!animate) {
                this.el.style.transition = 'none';
                this.content && (this.content.style.transition = 'none');
                requestAnimationFrame(() => {
                    this.el.classList.add('is-collapsed');
                    this.content?.classList.add('sb-collapsed');
                    requestAnimationFrame(() => {
                        this.el.style.transition = '';
                        this.content && (this.content.style.transition = '');
                    });
                });
            } else {
                this.el.classList.add('is-collapsed');
                this.content?.classList.add('sb-collapsed');
            }
        },

        _mobileToggle() {
            const open = this.el.classList.toggle('mobile-open');
            this.overlay?.classList.toggle('show', open);
            document.body.style.overflow = open ? 'hidden' : '';
        },

        _mobileClose() {
            this.el.classList.remove('mobile-open');
            this.overlay?.classList.remove('show');
            document.body.style.overflow = '';
        },

        _isMobile: () => window.innerWidth <= 768
    };

    /* ─────────────────────────────────────────────────────
       AUTO-HIDE ALERTS
    ───────────────────────────────────────────────────── */
    const Alerts = {
        init(delay = 5000) {
            setTimeout(() => {
                document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
                    el.style.transition = 'opacity .4s ease, transform .4s ease, max-height .4s ease';
                    el.style.opacity    = '0';
                    el.style.transform  = 'translateY(-4px)';
                    setTimeout(() => {
                        if (el.parentNode) el.remove();
                    }, 420);
                });
            }, delay);
        }
    };

    /* ─────────────────────────────────────────────────────
       NOTIFICATION BADGE POLLING
    ───────────────────────────────────────────────────── */
    const Notifications = {
        INTERVAL: 60000, // 1 min
        URL: null,

        init(url) {
            if (!url) return;
            this.URL = url;
            setInterval(() => this.poll(), this.INTERVAL);
        },

        poll() {
            if (!this.URL) return;
            fetch(this.URL)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    this._updateDot(data.count);
                })
                .catch(() => {});
        },

        _updateDot(count) {
            const btn  = document.querySelector('.notif-btn');
            let   dot  = btn?.querySelector('.notif-dot');

            if (!btn) return;

            if (count > 0) {
                const label = count > 9 ? '9+' : count;
                if (dot) {
                    dot.textContent = label;
                } else {
                    dot = document.createElement('span');
                    dot.className   = 'notif-dot';
                    dot.textContent = label;
                    btn.appendChild(dot);
                }
            } else {
                dot?.remove();
            }
        }
    };

    /* ─────────────────────────────────────────────────────
       ACTIVE SUBMENU AUTO-EXPAND
       Re-ensures collapse state matches active child
    ───────────────────────────────────────────────────── */
    const Submenus = {
        init() {
            // Expand parent if a child is active
            document.querySelectorAll('.submenu li a.active').forEach(activeLink => {
                const collapse = activeLink.closest('.collapse');
                if (collapse && !collapse.classList.contains('show')) {
                    collapse.classList.add('show');
                    const trigger = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                    if (trigger) trigger.setAttribute('aria-expanded', 'true');
                }
            });
        }
    };

    /* ─────────────────────────────────────────────────────
       RIPPLE EFFECT (nav links)
    ───────────────────────────────────────────────────── */
    const Ripple = {
        init() {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    if (this.dataset.bsToggle) return; // skip dropdowns
                    const r  = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    r.style.cssText = `
                        position:absolute;width:${size}px;height:${size}px;
                        border-radius:50%;background:rgba(255,255,255,.12);
                        transform:scale(0);animation:ripple .45s ease;
                        left:${e.clientX - rect.left - size / 2}px;
                        top:${e.clientY - rect.top - size / 2}px;
                        pointer-events:none;z-index:0;
                    `;
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(r);
                    r.addEventListener('animationend', () => r.remove());
                });
            });

            // Inject keyframe if not present
            if (!document.getElementById('ci-ripple-style')) {
                const s = document.createElement('style');
                s.id = 'ci-ripple-style';
                s.textContent = '@keyframes ripple { to { transform:scale(2.5); opacity:0; } }';
                document.head.appendChild(s);
            }
        }
    };

    /* ─────────────────────────────────────────────────────
       SPINNER (global loading overlay)
    ───────────────────────────────────────────────────── */
    window.CISpinner = {
        show() { document.getElementById('spinnerOverlay')?.classList.add('active'); },
        hide() { document.getElementById('spinnerOverlay')?.classList.remove('active'); }
    };

    /* ─────────────────────────────────────────────────────
       BOOTSTRAP TOOLTIP INIT (for sidebar collapsed state)
    ───────────────────────────────────────────────────── */
    const Tooltips = {
        init() {
            if (typeof bootstrap === 'undefined') return;
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el, { placement: 'right', trigger: 'hover' });
            });
        }
    };

    /* ─────────────────────────────────────────────────────
       TOPBAR — search keyboard shortcut (Ctrl/Cmd + K)
    ───────────────────────────────────────────────────── */
    const Search = {
        init() {
            document.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchBtn = document.querySelector('.topbar-item .topbar-btn[data-bs-toggle="dropdown"]');
                    if (searchBtn && typeof bootstrap !== 'undefined') {
                        const dd = bootstrap.Dropdown.getOrCreateInstance(searchBtn);
                        dd.toggle();
                        setTimeout(() => {
                            document.querySelector('.search-input')?.focus();
                        }, 80);
                    }
                }
            });
        }
    };

    /* ─────────────────────────────────────────────────────
       INIT ALL
    ───────────────────────────────────────────────────── */
    function init() {
        Sidebar.init();
        Alerts.init();
        Submenus.init();
        Ripple.init();
        Tooltips.init();
        Search.init();

        // Notifications polling — pass endpoint URL via data attribute on body
        // <body data-notif-url="<?= site_url('admin/notifications/getUnreadCount') ?>">
        const notifUrl = document.body.dataset.notifUrl;
        Notifications.init(notifUrl);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();