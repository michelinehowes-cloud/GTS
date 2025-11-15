// Dark Mode Management
class DarkModeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    init() {
        this.applyTheme();
        this.createToggleButton();
        this.bindEvents();
    }

    applyTheme() {
        const root = document.documentElement;

        if (this.theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
            this.updateChartsTheme('dark');
        } else {
            root.removeAttribute('data-theme');
            this.updateChartsTheme('light');
        }

        // Update meta theme-color for mobile browsers
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', this.theme === 'dark' ? '#1e3a8a' : '#ffffff');
        }
    }

    toggleTheme() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.theme);
        this.applyTheme();

        // Add smooth transition
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }

    createToggleButton() {
        // Create toggle button with icon only
        this.toggleButton = document.createElement('button');
        this.toggleButton.id = 'darkModeToggle';
        this.toggleButton.className = 'btn btn-outline-secondary btn-sm me-2';
        this.updateButtonIcon();

        // Add to navbar - find the right side container (where user info is)
        const navbarMain = document.getElementById('navbarMain');
        if (navbarMain) {
            // Find the right side container with user info (second d-flex container)
            const rightSideContainer = navbarMain.querySelector('.container-fluid > .d-flex.align-items-center:last-child');
            if (rightSideContainer) {
                // Insert before the user avatar
                const userAvatar = rightSideContainer.querySelector('.user-avatar');
                if (userAvatar) {
                    rightSideContainer.insertBefore(this.toggleButton, userAvatar);
                } else {
                    // If no user avatar, append to the container
                    rightSideContainer.appendChild(this.toggleButton);
                }
            }
        }

        // Bind click event
        this.toggleButton.addEventListener('click', () => this.toggleTheme());
    }

    updateButtonIcon() {
        if (this.toggleButton) {
            this.toggleButton.innerHTML = `<i class="fas ${this.theme === 'dark' ? 'fa-sun' : 'fa-moon'}"></i>`;
            this.toggleButton.title = this.theme === 'dark' ? 'التبديل إلى الوضع الفاتح' : 'التبديل إلى الوضع المظلم';
        }
    }

    bindEvents() {
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.theme = e.matches ? 'dark' : 'light';
                this.applyTheme();
            }
        });
    }

    updateChartsTheme(theme) {
        // Update Chart.js theme if charts exist
        if (typeof Chart !== 'undefined') {
            Chart.helpers.each(Chart.instances, function(instance) {
                if (instance.config.options.plugins && instance.config.options.plugins.legend) {
                    instance.config.options.plugins.legend.labels.color = theme === 'dark' ? '#e5e7eb' : '#374151';
                }
                if (instance.config.options.scales) {
                    if (instance.config.options.scales.x && instance.config.options.scales.x.ticks) {
                        instance.config.options.scales.x.ticks.color = theme === 'dark' ? '#e5e7eb' : '#374151';
                    }
                    if (instance.config.options.scales.y && instance.config.options.scales.y.ticks) {
                        instance.config.options.scales.y.ticks.color = theme === 'dark' ? '#e5e7eb' : '#374151';
                    }
                }
                instance.update();
            });
        }
    }
}

// Initialize dark mode when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new DarkModeManager();
});

// Export for potential use in other scripts
window.DarkModeManager = DarkModeManager;
