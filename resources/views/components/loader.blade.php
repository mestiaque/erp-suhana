<!-- Loader Component - Multi Design (ERP/Payroll) -->

<script>
    (function () {
        if (window.location.href.indexOf('print') !== -1 || window.location.href.indexOf('Print') !== -1) {
            window.XLoaderOverride = true;
        }
    })();
</script>

<style>
    .x-loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    #x-inline-loader {
        display: none;
    }

    .x-loader-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .x-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .x-loader-text {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #35507d;
    }

    .x-loader-design {
        display: none;
    }

    .x-loader-design.active {
        display: block;
    }

    /* Attendance Matrix */
    .x-matrix {
        display: grid;
        grid-template-columns: repeat(4, 14px);
        gap: 6px;
    }

    .x-matrix-cell {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        background: #6366f1;
        animation: x-matrix-pulse 1.2s infinite ease-in-out;
    }

    .x-matrix-cell:nth-child(4n+1) { animation-delay: 0s; }
    .x-matrix-cell:nth-child(4n+2) { animation-delay: 0.15s; }
    .x-matrix-cell:nth-child(4n+3) { animation-delay: 0.3s; }
    .x-matrix-cell:nth-child(4n+4) { animation-delay: 0.45s; }

    @keyframes x-matrix-pulse {
        0%, 100% { opacity: 0.3; transform: scale(0.85); }
        50% { opacity: 1; transform: scale(1); background: #6366f1; }
    }

    /* Biometric Wave */
    .x-bio {
        width: 86px;
        height: 86px;
        position: relative;
    }

.x-bio-ring {
    position: absolute;
    border-radius: 50%;
    border: 3px solid;
    border-color: #3b82f6 #3b82f6 transparent transparent;
    animation: x-bio-spin 1.4s linear infinite;
    box-shadow: 0 0 8px #3b82f6, 0 0 16px #3b82f6;
}

.x-bio-ring.r1 {
    inset: 6px;
}

.x-bio-ring.r2 {
    inset: 16px;
    border-color: #38bdf8 #38bdf8 transparent transparent;
    animation-duration: 1.1s;
    animation-direction: reverse;
    box-shadow: 0 0 8px #38bdf8, 0 0 18px #38bdf8;
}

.x-bio-ring.r3 {
    inset: 26px;
    border-color: #0ea5e9 #0ea5e9 transparent transparent;
    animation-duration: 0.9s;
    box-shadow: 0 0 10px #0ea5e9, 0 0 20px #0ea5e9;
}

    @keyframes x-bio-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<!-- Full Page Loader -->
<div id="x-page-loader" class="x-loader-overlay">
    <div class="x-loader" data-loader-scope="full">
        <div class="x-loader-design x-design-attendance-matrix" data-loader-design="attendance-matrix">
            <div class="x-matrix">
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
            </div>
        </div>

        <div class="x-loader-design x-design-biometric-wave" data-loader-design="biometric-wave">
            <div class="x-bio"><div class="x-bio-ring r1"></div><div class="x-bio-ring r2"></div><div class="x-bio-ring r3"></div></div>
        </div>

        <div class="x-loader-text">Processing...</div>
    </div>
</div>

<!-- Inline Loader -->
<div id="x-inline-loader" class="x-loader-overlay" style="position: absolute; inset: 0; background: rgba(255, 255, 255, 0.85);">
    <div class="x-loader x-loader-inline" data-loader-scope="inline">
        <div class="x-loader-design x-design-attendance-matrix" data-loader-design="attendance-matrix">
            <div class="x-matrix">
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
            </div>
        </div>
        <div class="x-loader-design x-design-biometric-wave" data-loader-design="biometric-wave">
            <div class="x-bio"><div class="x-bio-ring r1"></div><div class="x-bio-ring r2"></div><div class="x-bio-ring r3"></div></div>
        </div>
    </div>
</div>

<script>
    const XLoader = {
        shown: false,
        currentDesign: 'attendance-matrix',
        allowedDesigns: ['attendance-matrix', 'biometric-wave'],

        readSavedDesign: function () {
            try {
                const saved = localStorage.getItem('x_loader_design');
                return this.allowedDesigns.includes(saved) ? saved : this.currentDesign;
            } catch (e) {
                return this.currentDesign;
            }
        },

        setDesign: function (design) {
            const selected = this.allowedDesigns.includes(design) ? design : 'attendance-matrix';
            this.currentDesign = selected;

            try {
                localStorage.setItem('x_loader_design', selected);
            } catch (e) {}

            const scopes = document.querySelectorAll('[data-loader-scope]');
            scopes.forEach(function (scopeEl) {
                const variants = scopeEl.querySelectorAll('[data-loader-design]');
                variants.forEach(function (el) {
                    if (el.getAttribute('data-loader-design') === selected) {
                        el.classList.add('active');
                    } else {
                        el.classList.remove('active');
                    }
                });
            });
        },

        show: function () {
            if (this.shown || window.XLoaderOverride) return;
            this.shown = true;
            const loader = document.getElementById('x-page-loader');
            if (loader) loader.classList.add('active');
        },

        hide: function () {
            this.shown = false;
            const loader = document.getElementById('x-page-loader');
            if (loader) loader.classList.remove('active');
        },

        showInline: function (elementId) {
            const loader = document.getElementById('x-inline-loader');
            const target = document.getElementById(elementId);
            if (loader && target) {
                loader.style.display = 'flex';
                loader.style.position = 'absolute';
                loader.style.top = '0';
                loader.style.left = '0';
                loader.style.width = '100%';
                loader.style.height = '100%';
                loader.classList.add('active');
                target.style.position = 'relative';
                target.appendChild(loader);
            }
        },

        hideInline: function () {
            const loader = document.getElementById('x-inline-loader');
            if (loader) {
                loader.classList.remove('active');
                loader.style.display = 'none';
                document.body.appendChild(loader);
            }
        }
    };

    window.XLoader = XLoader;
    XLoader.setDesign(XLoader.readSavedDesign());

    window.addEventListener('storage', function (e) {
        if (e.key === 'x_loader_design' && e.newValue) {
            XLoader.setDesign(e.newValue);
        }
    });

    window.addEventListener('beforeunload', function () {
        if (window.XLoaderOverride) return;
        XLoader.show();
    });

    window.addEventListener('load', function () {
        setTimeout(function () {
            XLoader.hide();
        }, 350);
    });

    document.addEventListener('click', function (e) {
        if (window.XLoaderOverride) return;
        const target = e.target.closest('a');
        if (target) {
            const href = target.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript') && !target.classList.contains('no-loader') && !target.target) {
                XLoader.show();
            }
        }
    });

    document.addEventListener('submit', function (e) {
        if (window.XLoaderOverride) return;
        const target = e.target;
        if (!target.classList.contains('no-loader') && !target.classList.contains('ajax-form')) {
            XLoader.show();
        }
    });
</script>
