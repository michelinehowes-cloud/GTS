/**
 * =========================================================================
 * نظام تفعيل الوضع الليلي الذكي والموثوق
 * Smart & Robust Dark Mode Controller
 * =========================================================================
 */
(function () {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const html = document.documentElement;

    function applyThemeToDOM(theme) {
        const isDark = theme === 'dark';
        if (isDark) {
            html.setAttribute('data-theme', 'dark');
            html.setAttribute('data-bs-theme', 'dark');
            html.classList.add('dark-mode');
            if (document.body) {
                document.body.classList.add('dark-mode');
            }
        } else {
            html.removeAttribute('data-theme');
            html.setAttribute('data-bs-theme', 'light');
            html.classList.remove('dark-mode');
            if (document.body) {
                document.body.classList.remove('dark-mode');
            }
        }
        updateIcons(theme);
    }

    // تطبيق فوري على عنصر html لمنع الوميض
    if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
        html.classList.add('dark-mode');
    } else {
        html.removeAttribute('data-theme');
        html.setAttribute('data-bs-theme', 'light');
        html.classList.remove('dark-mode');
    }

    // دالة تبديل الوضع المعرفة عالمياً
    window.toggleDarkMode = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const currentTheme = html.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        localStorage.setItem('theme', newTheme);
        applyThemeToDOM(newTheme);

        console.log('Theme switched to:', newTheme);
        try {
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
        } catch (err) {}
    };

    // مرادف للدالة القديمة لضمان التوافق التام
    window.toggleThemeGlobal = window.toggleDarkMode;

    function updateIcons(theme) {
        const isDark = theme === 'dark';
        const buttons = document.querySelectorAll('#darkModeToggle, #darkModeMenuToggle, .dark-mode-trigger, [onclick*="toggleDarkMode"], [onclick*="toggleThemeGlobal"]');

        buttons.forEach(btn => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-moon', 'fa-sun', 'text-warning', 'text-white', 'text-amber-400');
                if (isDark) {
                    icon.classList.add('fa-sun');
                    icon.classList.add('text-warning');
                } else {
                    icon.classList.add('fa-moon');
                }
            }
            if (btn.hasAttribute('title') && (btn.getAttribute('title').includes('الوضع') || btn.getAttribute('title').includes('ليلي'))) {
                btn.setAttribute('title', isDark ? 'الوضع النهاري' : 'الوضع الليلي');
            }
        });
    }

    // تهيئة الأيقونات وتطبيق الكلاس على body عند اكتمال DOM
    document.addEventListener('DOMContentLoaded', () => {
        applyThemeToDOM(savedTheme);

        const buttons = document.querySelectorAll('#darkModeToggle, #darkModeMenuToggle, .dark-mode-trigger');
        buttons.forEach(btn => {
            btn.onclick = window.toggleDarkMode;
        });
    });

    // اختصار لوحة المفاتيح: Alt + D
    document.addEventListener('keydown', function (e) {
        if ((e.altKey || e.metaKey) && (e.key === 'd' || e.key === 'D')) {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;
            e.preventDefault();
            window.toggleDarkMode();
        }
    });
})();
