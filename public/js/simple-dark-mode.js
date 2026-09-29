/**
 * =========================================================================
 * نظام تفعيل الوضع الليلي الذكي والموثوق
 * Smart & Robust Dark Mode Controller
 * =========================================================================
 */
(function () {
    // تطبيق الوضع فوراً قبل اكتمال تحميل الصفحة لمنع الوميض الأبيض (No-Flash Script)
    const savedTheme = localStorage.getItem('theme') || 'light';
    const html = document.documentElement;

    if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
    } else {
        html.removeAttribute('data-theme');
        html.setAttribute('data-bs-theme', 'light');
    }

    // دالة تبديل الوضع المعرفة عالمياً
    window.toggleDarkMode = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        if (newTheme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            html.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            html.removeAttribute('data-theme');
            html.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
        }

        updateIcons(newTheme);
        console.log('Theme switched to:', newTheme);
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
        });
    }

    // تهيئة الأيقونات وربط الأحداث عند اكتمال DOM
    document.addEventListener('DOMContentLoaded', () => {
        updateIcons(savedTheme);

        const buttons = document.querySelectorAll('#darkModeToggle, #darkModeMenuToggle');
        buttons.forEach(btn => {
            btn.onclick = window.toggleDarkMode;
        });
    });

    // اختصار لوحة المفاتيح: Alt + D
    document.addEventListener('keydown', function (e) {
        if ((e.altKey || e.metaKey) && (e.key === 'd' || e.key === 'D')) {
            e.preventDefault();
            window.toggleDarkMode();
        }
    });
})();
