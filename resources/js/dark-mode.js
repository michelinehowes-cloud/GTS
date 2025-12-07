// Simple Dark Mode Toggle
(function() {
    'use strict';
    
    // Get stored theme or default to light
    function getStoredTheme() {
        return localStorage.getItem('theme') || 'light';
    }
    
    // Apply theme to document
    function applyTheme(theme) {
        const root = document.documentElement;
        
        console.log('Applying theme:', theme);
        
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
            root.classList.add('dark-theme');
            root.classList.remove('light-theme');
        } else {
            root.removeAttribute('data-theme');
            root.classList.add('light-theme');
            root.classList.remove('dark-theme');
        }
        
        // Update button icon
        updateButtonIcon(theme);
        
        // Save to localStorage
        localStorage.setItem('theme', theme);
    }
    
    // Update button icon based on theme
    function updateButtonIcon(theme) {
        const button = document.getElementById('darkModeToggle');
        if (!button) {
            console.warn('Dark mode button not found');
            return;
        }
        
        const icon = button.querySelector('i');
        if (!icon) {
            console.warn('Icon element not found in button');
            return;
        }
        
        console.log('Updating icon for theme:', theme);
        
        if (theme === 'dark') {
            // In dark mode, show sun icon
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            button.setAttribute('title', 'التبديل إلى الوضع الفاتح');
        } else {
            // In light mode, show moon icon
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            button.setAttribute('title', 'التبديل إلى الوضع المظلم');
        }
        
        // Add animation class
        button.classList.add('changing');
        setTimeout(() => button.classList.remove('changing'), 500);
    }
    
    // Toggle theme
    function toggleTheme() {
        const currentTheme = getStoredTheme();
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        console.log('Toggling theme from', currentTheme, 'to', newTheme);
        applyTheme(newTheme);
    }
    
    // Initialize on DOM ready
    function init() {
        console.log('Initializing dark mode...');
        
        // Apply stored theme
        const theme = getStoredTheme();
        applyTheme(theme);
        
        // Bind click event to button
        const button = document.getElementById('darkModeToggle');
        if (button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Button clicked!');
                toggleTheme();
            });
            console.log('Dark mode toggle initialized successfully');
        } else {
            console.warn('Dark mode toggle button not found in DOM');
        }
        
        // Keyboard shortcut: Alt+D or Cmd+D
        document.addEventListener('keydown', function(e) {
            if ((e.altKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                toggleTheme();
            }
        });
    }
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Export for global access
    window.toggleDarkMode = toggleTheme;
    
})();
