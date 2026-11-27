/*
 * Sidebar v2 behaviour (styles in public/css/sidebar.css):
 *  - wraps each menu label in <span class="nav-text"> so it can be shown on one line / hidden in the icon rail
 *  - full label as a tooltip (useful when truncated or in the rail)
 *  - opens the group that contains the current page and highlights its link
 *  - quick search over all menu links
 *  - in the icon rail, clicking a group icon expands the sidebar and opens that group
 */
(function () {
    'use strict';

    function init() {
        var sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        var links = sidebar.querySelectorAll('.nav > .nav-item > .nav-link');

        // 1. One-line labels + tooltips
        links.forEach(function (link) {
            if (link.querySelector('.nav-text')) return;
            var label = '';
            Array.prototype.slice.call(link.childNodes).forEach(function (node) {
                if (node.nodeType === 3 && node.textContent.trim() !== '') {
                    label += (label ? ' ' : '') + node.textContent.trim();
                    var span = document.createElement('span');
                    span.className = 'nav-text';
                    span.textContent = node.textContent.trim();
                    link.replaceChild(span, node);
                }
            });
            if (label && !link.hasAttribute('title')) link.setAttribute('title', label);
        });

        sidebar.querySelectorAll('.submenu-item').forEach(function (item) {
            if (!item.hasAttribute('title')) item.setAttribute('title', item.textContent.trim());
        });

        // 2. Open the group of the current page
        var path = window.location.pathname.replace(/\/+$/, '');
        var current = sidebar.querySelector('.submenu-item.active');
        if (!current) {
            sidebar.querySelectorAll('.submenu-item[href]').forEach(function (item) {
                if (current) return;
                try {
                    var itemPath = new URL(item.href, window.location.origin).pathname.replace(/\/+$/, '');
                    if (itemPath && itemPath === path) current = item;
                } catch (e) { /* ignore malformed hrefs */ }
            });
            if (current) current.classList.add('active');
        }
        if (current) {
            var submenu = current.closest('.submenu');
            if (submenu) {
                submenu.classList.add('show');
                var group = submenu.closest('.menu-group');
                if (group) group.classList.add('active');
            }
            var content = sidebar.querySelector('.sidebar-content');
            if (content) {
                var top = current.getBoundingClientRect().top - content.getBoundingClientRect().top;
                if (top > content.clientHeight - 60) content.scrollTop = top - content.clientHeight / 2;
            }
        }

        // 3. Quick search
        var input = document.getElementById('sidebarSearch');
        var empty = document.getElementById('sidebarSearchEmpty');
        var openBeforeSearch = null;

        function normalize(text) {
            return (text || '')
                .toLowerCase()
                .replace(/[ً-ْـ]/g, '') // Arabic diacritics and tatweel
                .replace(/[أإآ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function runSearch() {
            var q = normalize(input.value);
            var items = sidebar.querySelectorAll('.nav > .nav-item');
            var anyMatch = false;

            if (q && openBeforeSearch === null) {
                openBeforeSearch = Array.prototype.slice.call(sidebar.querySelectorAll('.submenu.show'));
            }

            items.forEach(function (li) {
                var link = li.querySelector(':scope > .nav-link');
                var sub = li.querySelector(':scope > .submenu');
                var subItems = sub ? sub.querySelectorAll('.submenu-item') : [];

                if (!q) {
                    li.classList.remove('sidebar-search-hidden');
                    subItems.forEach(function (a) { a.classList.remove('sidebar-search-hidden'); });
                    return;
                }

                var linkMatch = link && normalize(link.textContent).indexOf(q) !== -1;
                var subMatch = false;
                subItems.forEach(function (a) {
                    var m = linkMatch || normalize(a.textContent).indexOf(q) !== -1;
                    a.classList.toggle('sidebar-search-hidden', !m);
                    if (m) subMatch = true;
                });

                var visible = linkMatch || subMatch;
                li.classList.toggle('sidebar-search-hidden', !visible);
                if (sub) sub.classList.toggle('show', visible && subItems.length > 0);
                if (visible) anyMatch = true;
            });

            if (!q && openBeforeSearch !== null) {
                sidebar.querySelectorAll('.submenu.show').forEach(function (s) { s.classList.remove('show'); });
                openBeforeSearch.forEach(function (s) { s.classList.add('show'); });
                openBeforeSearch = null;
            }

            if (empty) empty.hidden = !q || anyMatch;
        }

        if (input) {
            input.addEventListener('input', runSearch);
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    input.value = '';
                    runSearch();
                    input.blur();
                } else if (e.key === 'Enter') {
                    var first = sidebar.querySelector('.nav > .nav-item:not(.sidebar-search-hidden) .submenu-item:not(.sidebar-search-hidden), .nav > .nav-item:not(.sidebar-search-hidden) > .nav-link:not([href="#"])');
                    if (first && first.href) window.location.href = first.href;
                }
            });
        }

        // 4. Icon rail: clicking a group icon expands the sidebar first
        sidebar.querySelectorAll('.menu-group > .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth > 768 && sidebar.classList.contains('collapsed')) {
                    var toggle = document.getElementById('toggleSidebar');
                    if (toggle) toggle.click();
                    // The group's own toggle runs right after; make sure it ends up open, not toggled closed
                    var group = link.closest('.menu-group');
                    var sub = group && group.querySelector('.submenu');
                    setTimeout(function () {
                        if (sub) sub.classList.add('show');
                        if (group) group.classList.add('active');
                    }, 0);
                }
            }, true);
        });

        // Keep the rail toggle icon pointing the right way
        var toggleBtn = document.getElementById('toggleSidebar');
        if (toggleBtn) {
            var syncTitle = function () {
                var collapsed = sidebar.classList.contains('collapsed');
                toggleBtn.title = collapsed ? 'توسيع القائمة الجانبية' : 'تصغير القائمة الجانبية';
            };
            syncTitle();
            toggleBtn.addEventListener('click', function () { setTimeout(syncTitle, 0); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
