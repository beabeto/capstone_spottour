(function () {
    var key = 'spottour-theme';
    var moon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21 14.5A8.5 8.5 0 0 1 9.5 3 7 7 0 1 0 21 14.5z"/></svg>';
    var sun = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4" fill="currentColor"/><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2.5v2.2M12 19.3v2.2M4.8 4.8l1.6 1.6M17.6 17.6l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.8 19.2l1.6-1.6M17.6 6.4l1.6-1.6"/></g></svg>';

    function place() {
        document.querySelectorAll('.theme-toggle').forEach(function (button) {
            var slot = document.querySelector('.topbar-actions, .nav-right');
            if (slot) {
                button.classList.remove('is-floating');
                if (button.parentElement !== slot) slot.insertBefore(button, slot.firstChild);
            } else {
                button.classList.add('is-floating');
            }
        });
    }

    function apply(theme) {
        var dark = theme === 'dark';
        if (dark) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        document.querySelectorAll('.theme-toggle').forEach(function (button) {
            button.setAttribute('aria-pressed', dark ? 'true' : 'false');
            button.setAttribute('aria-label', dark ? 'Light mode' : 'Dark mode');
            button.innerHTML = dark ? sun : moon;
        });
        paintCharts(dark);
    }

    function paintCharts(dark) {
        if (!window.Chart || !Chart.getChart) return;
        var ink = dark ? '#d7ebe4' : '#405a53';
        var grid = dark ? 'rgba(183,205,198,.2)' : 'rgba(64,90,83,.12)';
        Chart.defaults.color = ink;
        Chart.defaults.borderColor = grid;
        document.querySelectorAll('canvas').forEach(function (canvas) {
            var chart = Chart.getChart(canvas);
            if (!chart || !chart.options) return;
            Object.keys(chart.options.scales || {}).forEach(function (axis) {
                var scale = chart.options.scales[axis];
                scale.ticks = Object.assign({}, scale.ticks, { color: ink });
                scale.grid = Object.assign({}, scale.grid, { color: grid });
            });
            if (chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels = Object.assign({}, chart.options.plugins.legend.labels, { color: ink });
            }
            chart.update();
        });
    }

    function start() {
        place();
        var saved = 'light';
        try {
            saved = localStorage.getItem(key) || 'light';
        } catch (error) {}
        apply(saved);
    }

    function keepReportOnOnePage() {
        document.querySelectorAll('form.report-toolbar').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                location.replace(form.action + '?' + new URLSearchParams(new FormData(form)).toString());
            });
        });
    }

    function workspaceSidebars() {
        return Array.prototype.slice.call(document.querySelectorAll('.sidebar, .owner-sidebar'));
    }

    function syncWorkspaceScrim() {
        var open = workspaceSidebars().some(function (sidebar) {
            return sidebar.classList.contains('show');
        });
        var scrim = document.querySelector('.workspace-scrim');
        if (!scrim) {
            if (!open) return;
            scrim = document.createElement('button');
            scrim.type = 'button';
            scrim.className = 'workspace-scrim';
            scrim.setAttribute('aria-label', 'Close menu');
            scrim.addEventListener('click', function () {
                workspaceSidebars().forEach(function (sidebar) {
                    sidebar.classList.remove('show');
                });
                syncWorkspaceScrim();
            });
            document.body.appendChild(scrim);
        }
        scrim.classList.toggle('is-visible', open);
        document.body.style.overflow = open && window.matchMedia('(max-width: 900px)').matches ? 'hidden' : '';
    }

    function watchWorkspaceMenus() {
        workspaceSidebars().forEach(function (sidebar) {
            if (sidebar.dataset.scrimBound) return;
            sidebar.dataset.scrimBound = '1';
            var observer = new MutationObserver(syncWorkspaceScrim);
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        });
        syncWorkspaceScrim();
        window.addEventListener('resize', function () {
            if (!window.matchMedia('(max-width: 900px)').matches) {
                workspaceSidebars().forEach(function (sidebar) {
                    sidebar.classList.remove('show');
                });
            }
            syncWorkspaceScrim();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            start();
            keepReportOnOnePage();
            watchWorkspaceMenus();
        });
    } else {
        start();
        keepReportOnOnePage();
        watchWorkspaceMenus();
    }

    window.spottourToggleTheme = function () {
        var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem(key, next);
        } catch (error) {}
        apply(next);
    };

    window.toggleNotificationMenu = window.toggleNotificationMenu || function () {
        var menu = document.getElementById('notificationMenu');
        var trigger = document.querySelector('.notification-icon');
        if (!menu || !trigger) return;
        var userMenu = document.getElementById('userMenu');
        if (userMenu) {
            userMenu.classList.remove('show-menu');
            var userTrigger = document.querySelector('.user-info');
            if (userTrigger) userTrigger.setAttribute('aria-expanded', 'false');
        }
        menu.classList.toggle('show-notification-menu');
        trigger.setAttribute('aria-expanded', menu.classList.contains('show-notification-menu') ? 'true' : 'false');
    };

    document.addEventListener('click', function (event) {
        var back = event.target.closest && event.target.closest('.back-btn button, .back-btn a, .back-link');
        if (!back) return;
        event.preventDefault();
        event.stopPropagation();
        var previous = null;
        try {
            previous = document.referrer ? new URL(document.referrer, location.href) : null;
        } catch (error) {}
        if (previous && previous.origin === location.origin && previous.pathname === location.pathname && history.length > 2) {
            history.go(-2);
            return;
        }
        if (history.length > 1) {
            history.back();
            return;
        }
        location.assign(location.pathname.indexOf('/admin') === 0 ? '/admin' : (location.pathname.indexOf('/owner') === 0 ? '/owner' : (location.pathname.indexOf('/user') === 0 ? '/user' : '/')));
    }, true);

    document.addEventListener('click', function (event) {
        var menu = document.getElementById('notificationMenu');
        if (!menu || !menu.classList.contains('show-notification-menu')) return;
        if (event.target.closest && event.target.closest('.notification')) return;
        menu.classList.remove('show-notification-menu');
        var trigger = document.querySelector('.notification-icon');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
})();
