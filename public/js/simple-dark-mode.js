/**
 * Simple and Robust Dark Mode Toggler
 */
(function () {
    // 1. Define the Global Function IMMEDIATELY
    window.toggleDarkMode = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        // Apply
        if (newTheme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            html.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        }

        // Update Icons
        updateIcons(newTheme);

        console.log('Dark Mode Toggled to:', newTheme);
    };

    function updateIcons(theme) {
        const isDark = theme === 'dark';
        const buttons = document.querySelectorAll('#darkModeToggle, #darkModeMenuToggle, .dark-mode-trigger');

        buttons.forEach(btn => {
            const icon = btn.querySelector('i');
            if (icon) {
                // Remove all possible icon classes to be safe
                icon.classList.remove('fa-moon', 'fa-sun', 'text-warning', 'text-white');

                if (isDark) {
                    icon.classList.add('fa-sun');
                    icon.classList.add('text-warning'); // Make sun yellow
                } else {
                    icon.classList.add('fa-moon');
                }
            }
        });
    }

    // 2. Initialize on Load
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    // Update icons immediately
    document.addEventListener('DOMContentLoaded', () => {
        updateIcons(savedTheme);

        // Attach Event Listeners (Backup for onclick)
        const buttons = document.querySelectorAll('#darkModeToggle, #darkModeMenuToggle');
        buttons.forEach(btn => {
            btn.onclick = window.toggleDarkMode;
        });
    });

})();
