// Advanced Dark Mode Management System
class DarkModeManager {
    constructor(options = {}) {
        this.defaultOptions = {
            storageKey: 'theme',
            enableSystemPreference: true,
            enableTransitions: true,
            enableKeyboardShortcut: true,
            debug: false,
            ...options
        };

        this.theme = this.getStoredTheme();
        this.isTransitioning = false;
        this.observer = null;
        this.mediaQuery = null;
        this.components = new Map();
        
        this.log('DarkModeManager initialized');
        this.init();
    }

    init() {
        this.applyTheme();
        this.createToggleButton();
        this.bindEvents();
        this.setupMutationObserver();
        this.addTransitionStyles();
        this.initializeComponents();
    }

    getStoredTheme() {
        // Try to get theme from storage
        const stored = localStorage.getItem(this.defaultOptions.storageKey);
        if (stored) return stored;

        // Try to get from session storage as fallback
        const sessionStored = sessionStorage.getItem(this.defaultOptions.storageKey);
        if (sessionStored) return sessionStored;

        // Check system preference if enabled
        if (this.defaultOptions.enableSystemPreference) {
            return this.getSystemTheme();
        }

        return 'light';
    }

    getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    applyTheme(force = false) {
        if (this.isTransitioning && !force) return;
        
        const root = document.documentElement;
        const currentTheme = root.getAttribute('data-theme') || 'light';
        
        if (currentTheme !== this.theme || force) {
            this.startThemeTransition(currentTheme, this.theme);
        } else {
            this.updateDOMTheme();
        }
    }

    startThemeTransition(fromTheme, toTheme) {
        this.isTransitioning = true;
        
        // Dispatch transition start event
        this.dispatchEvent('themeTransitionStart', { fromTheme, toTheme });

        // Add transition class to body
        if (this.defaultOptions.enableTransitions) {
            document.body.classList.add('theme-transitioning');
        }

        // Use requestAnimationFrame for smooth transition
        requestAnimationFrame(() => {
            this.updateDOMTheme();
            
            if (this.defaultOptions.enableTransitions) {
                setTimeout(() => {
                    document.body.classList.remove('theme-transitioning');
                    this.isTransitioning = false;
                    this.dispatchEvent('themeTransitionEnd', { 
                        fromTheme, 
                        toTheme 
                    });
                }, 300);
            } else {
                this.isTransitioning = false;
                this.dispatchEvent('themeTransitionEnd', { fromTheme, toTheme });
            }
        });
    }

    updateDOMTheme() {
        const root = document.documentElement;
        const oldTheme = root.getAttribute('data-theme') || 'light';
        
        // Update DOM attributes and classes
        if (this.theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
            root.classList.add('dark-theme');
            root.classList.remove('light-theme');
        } else {
            root.removeAttribute('data-theme');
            root.classList.add('light-theme');
            root.classList.remove('dark-theme');
        }

        // Update various theme-dependent elements
        this.updateMetaThemeColor();
        this.updateButtonIcon();
        this.updateFavicon();
        this.updateAllComponents();
        
        // Dispatch theme change event
        this.dispatchThemeChangeEvent(oldTheme);
    }

    updateMetaThemeColor() {
        const colors = {
            light: '#ffffff',
            dark: '#1e293b'
        };

        // Update theme-color meta tag
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (!metaThemeColor) {
            metaThemeColor = document.createElement('meta');
            metaThemeColor.name = 'theme-color';
            document.head.appendChild(metaThemeColor);
        }
        metaThemeColor.setAttribute('content', colors[this.theme]);

        // Update apple-mobile-web-app-status-bar-style for iOS
        let appleMeta = document.querySelector('meta[name="apple-mobile-web-app-status-bar-style"]');
        if (!appleMeta) {
            appleMeta = document.createElement('meta');
            appleMeta.name = 'apple-mobile-web-app-status-bar-style';
            document.head.appendChild(appleMeta);
        }
        appleMeta.setAttribute('content', this.theme === 'dark' ? 'black-translucent' : 'default');
    }

    updateFavicon() {
        // Switch favicon based on theme if multiple favicons are provided
        const lightFavicon = document.querySelector('link[rel="icon"][media="(prefers-color-scheme: light)"]');
        const darkFavicon = document.querySelector('link[rel="icon"][media="(prefers-color-scheme: dark)"]');
        
        if (lightFavicon && darkFavicon) {
            lightFavicon.media = this.theme === 'light' ? 'all' : 'not all';
            darkFavicon.media = this.theme === 'dark' ? 'all' : 'not all';
        }
    }

    toggleTheme() {
        const previousTheme = this.theme;
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        
        // Save to both localStorage and sessionStorage for redundancy
        localStorage.setItem(this.defaultOptions.storageKey, this.theme);
        sessionStorage.setItem(this.defaultOptions.storageKey, this.theme);
        
        this.applyTheme();
        this.trackThemeChange(previousTheme);
    }

    createToggleButton() {
        // Check if button already exists
        const existingButton = document.getElementById('darkModeToggle');
        if (existingButton) {
            this.toggleButton = existingButton;
            this.updateButtonIcon();
            return;
        }

        // Create new toggle button
        this.toggleButton = document.createElement('button');
        this.toggleButton.id = 'darkModeToggle';
        this.toggleButton.className = 'theme-toggle-btn btn btn-outline-secondary btn-sm me-2';
        this.toggleButton.setAttribute('aria-pressed', this.theme === 'dark');
        this.toggleButton.setAttribute('role', 'switch');
        
        this.updateButtonIcon();
        this.insertToggleButton();
        
        // Bind click event with debouncing
        this.toggleButton.addEventListener('click', this.debounce(() => this.toggleTheme(), 300));
    }

    insertToggleButton() {
        const insertionStrategies = [
            // Strategy 1: Navbar right side (Bootstrap)
            () => {
                const navbarMain = document.getElementById('navbarMain');
                if (!navbarMain) return null;
                
                const rightSideContainer = navbarMain.querySelector('.container-fluid > .d-flex.align-items-center:last-child');
                if (rightSideContainer) {
                    const userAvatar = rightSideContainer.querySelector('.user-avatar, .navbar-user');
                    return userAvatar ? 
                        rightSideContainer.insertBefore(this.toggleButton, userAvatar) : 
                        rightSideContainer.appendChild(this.toggleButton);
                }
                return null;
            },
            
            // Strategy 2: Navbar end (Bootstrap 5)
            () => {
                const navbarNav = document.querySelector('.navbar-nav.ms-auto, .navbar-nav.me-auto');
                if (navbarNav) {
                    const listItem = document.createElement('li');
                    listItem.className = 'nav-item';
                    listItem.appendChild(this.toggleButton);
                    return navbarNav.appendChild(listItem);
                }
                return null;
            },
            
            // Strategy 3: Any navbar container
            () => {
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    const navbarCollapse = navbar.querySelector('.navbar-collapse');
                    return navbarCollapse ? 
                        navbarCollapse.appendChild(this.toggleButton) : 
                        navbar.appendChild(this.toggleButton);
                }
                return null;
            },
            
            // Strategy 4: Header area
            () => {
                const header = document.querySelector('header, .header');
                if (header) {
                    return header.appendChild(this.toggleButton);
                }
                return null;
            },
            
            // Strategy 5: Fixed position as last resort
            () => {
                this.toggleButton.classList.add('theme-toggle-fixed');
                Object.assign(this.toggleButton.style, {
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    zIndex: '1000',
                    borderRadius: '50%',
                    width: '50px',
                    height: '50px'
                });
                return document.body.appendChild(this.toggleButton);
            }
        ];

        for (const strategy of insertionStrategies) {
            try {
                if (strategy() !== null) {
                    this.log('Toggle button inserted using strategy');
                    break;
                }
            } catch (error) {
                this.log('Insertion strategy failed:', error);
                continue;
            }
        }
    }

    updateButtonIcon() {
        if (!this.toggleButton) return;
        
        const icons = {
            light: {
                icon: 'fa-moon',
                label: 'التبديل إلى الوضع المظلم'
            },
            dark: {
                icon: 'fa-sun',
                label: 'التبديل إلى الوضع الفاتح'
            }
        };
        
        const current = icons[this.theme];
        this.toggleButton.innerHTML = `<i class="fas ${current.icon}" aria-hidden="true"></i>`;
        this.toggleButton.setAttribute('aria-label', current.label);
        this.toggleButton.setAttribute('title', current.label);
        this.toggleButton.setAttribute('aria-pressed', this.theme === 'dark');
    }

    bindEvents() {
        // System theme change listener
        if (this.defaultOptions.enableSystemPreference) {
            this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            this.handleSystemThemeChange = (e) => {
                if (!localStorage.getItem(this.defaultOptions.storageKey)) {
                    this.theme = e.matches ? 'dark' : 'light';
                    this.applyTheme();
                }
            };
            this.mediaQuery.addEventListener('change', this.handleSystemThemeChange);
        }

        // Keyboard shortcut
        if (this.defaultOptions.enableKeyboardShortcut) {
            this.handleKeydown = (e) => {
                if ((e.altKey || e.metaKey) && e.key === 'd') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            };
            document.addEventListener('keydown', this.handleKeydown);
        }

        // Visibility change (tab switch)
        this.handleVisibilityChange = () => {
            if (!document.hidden) {
                this.checkThemeConsistency();
            }
        };
        document.addEventListener('visibilitychange', this.handleVisibilityChange);
    }

    setupMutationObserver() {
        // Watch for DOM changes that might affect the toggle button
        this.observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                if (mutation.type === 'childList') {
                    // Check if toggle button was removed
                    if (!document.body.contains(this.toggleButton)) {
                        this.insertToggleButton();
                    }
                    
                    // Check for new components that need theme updates
                    this.initializeComponents();
                }
            }
        });

        this.observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    addTransitionStyles() {
        if (!this.defaultOptions.enableTransitions) return;
        if (document.getElementById('theme-transition-styles')) return;

        const style = document.createElement('style');
        style.id = 'theme-transition-styles';
        style.textContent = `
            .theme-transitioning * {
                transition: background-color 0.3s ease, 
                          color 0.3s ease, 
                          border-color 0.3s ease,
                          fill 0.3s ease,
                          stroke 0.3s ease,
                          box-shadow 0.3s ease !important;
            }
            
            .theme-toggle-btn {
                transition: all 0.3s ease;
                border: 1px solid var(--border-color, currentColor);
            }
            
            .theme-toggle-fixed {
                backdrop-filter: blur(10px);
                background: var(--bg-transparent, rgba(255, 255, 255, 0.8));
            }
            
            [data-theme="dark"] .theme-toggle-fixed {
                background: var(--bg-transparent-dark, rgba(0, 0, 0, 0.8));
            }
            
            /* Reduced motion support */
            @media (prefers-reduced-motion: reduce) {
                .theme-transitioning * {
                    transition: none !important;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Component Management System
    initializeComponents() {
        this.registerComponent('charts', () => this.updateChartsTheme(this.theme));
        this.registerComponent('dataTables', () => this.updateDataTablesTheme());
        this.registerComponent('flatpickr', () => this.updateFlatpickrTheme());
        this.registerComponent('select2', () => this.updateSelect2Theme());
    }

    registerComponent(name, updateFunction) {
        this.components.set(name, updateFunction);
    }

    updateAllComponents() {
        this.components.forEach((updateFunction, name) => {
            try {
                updateFunction();
                this.log(`Component ${name} updated`);
            } catch (error) {
                console.warn(`Failed to update component ${name}:`, error);
            }
        });
    }

    updateChartsTheme(theme) {
        if (typeof Chart === 'undefined') return;

        const themeConfig = {
            light: {
                text: '#374151',
                grid: '#e5e7eb',
                background: 'transparent'
            },
            dark: {
                text: '#e5e7eb',
                grid: '#4b5563',
                background: 'transparent'
            }
        };

        const config = themeConfig[theme];

        Chart.helpers.each(Chart.instances, (instance) => {
            const { options } = instance.config;
            
            // Update colors for various chart elements
            if (options.plugins?.legend?.labels) {
                options.plugins.legend.labels.color = config.text;
            }
            
            if (options.plugins?.tooltip) {
                options.plugins.tooltip.backgroundColor = config.background;
                options.plugins.tooltip.titleColor = config.text;
                options.plugins.tooltip.bodyColor = config.text;
            }
            
            if (options.scales) {
                Object.keys(options.scales).forEach(scaleKey => {
                    const scale = options.scales[scaleKey];
                    if (scale.ticks) scale.ticks.color = config.text;
                    if (scale.grid) scale.grid.color = config.grid;
                    if (scale.border) scale.border.color = config.grid;
                });
            }
            
            instance.update('none');
        });
    }

    updateDataTablesTheme() {
        if (typeof $.fn.DataTable === 'undefined') return;
        
        $.fn.DataTable.tables({ visible: true, api: true }).every(function() {
            $(this.table().node()).trigger('draw.dt');
        });
    }

    updateFlatpickrTheme() {
        if (typeof flatpickr === 'undefined') return;
        
        document.querySelectorAll('.flatpickr-input').forEach(input => {
            const instance = input._flatpickr;
            if (instance) {
                instance.redraw();
                // Update theme class
                instance.calendarContainer.className = 
                    instance.calendarContainer.className.replace(/(^|\s)theme-\S+/g, '') + 
                    ` theme-${this.theme}`;
            }
        });
    }

    updateSelect2Theme() {
        if (typeof $.fn.select2 === 'undefined') return;
        
        $('select.select2-hidden-accessible').each(function() {
            const $select = $(this);
            const $container = $select.next('.select2-container');
            if ($container.length) {
                $container.attr('data-theme', this.theme);
            }
        });
    }

    // Utility Methods
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    checkThemeConsistency() {
        const storedTheme = localStorage.getItem(this.defaultOptions.storageKey);
        if (storedTheme && storedTheme !== this.theme) {
            this.log('Theme inconsistency detected, correcting...');
            this.theme = storedTheme;
            this.applyTheme(true);
        }
    }

    dispatchEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { 
            detail: { ...detail, theme: this.theme, timestamp: Date.now() }
        });
        window.dispatchEvent(event);
    }

    dispatchThemeChangeEvent(oldTheme) {
        this.dispatchEvent('themeChanged', { 
            oldTheme, 
            newTheme: this.theme 
        });
    }

    trackThemeChange(previousTheme) {
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'theme_toggle', {
                event_category: 'engagement',
                event_label: `${previousTheme}_to_${this.theme}`,
                value: this.theme === 'dark' ? 1 : 0
            });
        }

        // Custom analytics
        this.dispatchEvent('themeAnalytics', {
            previousTheme,
            newTheme: this.theme,
            source: 'toggle_button'
        });
    }

    log(...args) {
        if (this.defaultOptions.debug) {
            console.log('[DarkModeManager]', ...args);
        }
    }

    // Public API
    getCurrentTheme() {
        return this.theme;
    }

    setTheme(theme, persist = true) {
        if (['light', 'dark'].includes(theme)) {
            const oldTheme = this.theme;
            this.theme = theme;
            
            if (persist) {
                localStorage.setItem(this.defaultOptions.storageKey, theme);
                sessionStorage.setItem(this.defaultOptions.storageKey, theme);
            }
            
            this.applyTheme();
            this.log(`Theme changed from ${oldTheme} to ${theme}`);
        }
    }

    resetToSystem() {
        localStorage.removeItem(this.defaultOptions.storageKey);
        sessionStorage.removeItem(this.defaultOptions.storageKey);
        this.theme = this.getSystemTheme();
        this.applyTheme();
    }

    // Cleanup method
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        
        if (this.mediaQuery && this.handleSystemThemeChange) {
            this.mediaQuery.removeEventListener('change', this.handleSystemThemeChange);
        }
        
        if (this.handleKeydown) {
            document.removeEventListener('keydown', this.handleKeydown);
        }
        
        if (this.handleVisibilityChange) {
            document.removeEventListener('visibilitychange', this.handleVisibilityChange);
        }
        
        if (this.toggleButton) {
            this.toggleButton.removeEventListener('click', this.toggleTheme);
        }
        
        this.components.clear();
        this.log('DarkModeManager destroyed');
    }
}

// Advanced initialization with multiple fallbacks
function initializeDarkModeManager(options = {}) {
    // Wait for DOM to be ready
    const init = () => {
        try {
            // Check if already initialized
            if (window.darkModeManager) {
                console.warn('DarkModeManager already initialized');
                return window.darkModeManager;
            }

            // Check environment support
            if (typeof window === 'undefined' || typeof document === 'undefined') {
                throw new Error('Unsupported environment');
            }

            // Initialize with options
            window.darkModeManager = new DarkModeManager(options);
            
            // Export for module systems
            if (typeof module !== 'undefined' && module.exports) {
                module.exports = DarkModeManager;
            }
            
            // Export for ES6 modules
            if (typeof exports !== 'undefined') {
                exports.DarkModeManager = DarkModeManager;
            }

            console.log('DarkModeManager successfully initialized');
            return window.darkModeManager;
            
        } catch (error) {
            console.error('Failed to initialize DarkModeManager:', error);
            return null;
        }
    };

    // Initialize based on document ready state
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // Use setTimeout to ensure other scripts are loaded
        setTimeout(init, 100);
    }
}

// Auto-initialize with default options
const darkModeOptions = {
    enableSystemPreference: true,
    enableTransitions: true,
    enableKeyboardShortcut: true,
    debug: false
};

// Initialize automatically
initializeDarkModeManager(darkModeOptions);

// Export for global access and manual initialization
window.DarkModeManager = DarkModeManager;
window.initializeDarkModeManager = initializeDarkModeManager;

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { DarkModeManager, initializeDarkModeManager };
}