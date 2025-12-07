/**
 * Enhanced UI Features
 * وظائف تفاعلية محسنة لتحسين تجربة المستخدم
 */

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function () {
    console.log('Enhanced UI Features initialized');

    // Initialize all features
    initFormValidation();
    initSearchSuggestions();
    initConfirmationDialogs();
    initTooltips();
    initAutoSave();
    initLoadingStates();
    initTableEnhancements();
    initFilterPanel();
});

// ==================== FORM VALIDATION ====================
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            // Real-time validation
            input.addEventListener('blur', function () {
                validateField(this);
            });

            input.addEventListener('input', function () {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });

        // Form submission
        form.addEventListener('submit', function (e) {
            let isValid = true;

            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();

                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    let isValid = true;
    let message = '';

    // Check if required
    if (required && !value) {
        isValid = false;
        message = 'هذا الحقل مطلوب';
    }

    // Email validation
    if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'يرجى إدخال بريد إلكتروني صحيح';
        }
    }

    // Phone validation (Libyan format)
    if (field.name === 'phone' && value) {
        const phoneRegex = /^(09[0-9]{8}|21809[0-9]{8})$/;
        if (!phoneRegex.test(value.replace(/[\s-]/g, ''))) {
            isValid = false;
            message = 'يرجى إدخال رقم هاتف صحيح';
        }
    }

    // National ID validation (Libyan format)
    if (field.name === 'national_id' && value) {
        const nationalIdRegex = /^[0-9]{12}$/;
        if (!nationalIdRegex.test(value)) {
            isValid = false;
            message = 'يرجى إدخال رقم وطني صحيح (12 رقم)';
        }
    }

    // Password validation
    if (type === 'password' && value && field.name === 'password') {
        if (value.length < 8) {
            isValid = false;
            message = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        }
    }

    // Password confirmation
    if (field.name === 'password_confirmation' && value) {
        const password = document.querySelector('input[name="password"]');
        if (password && value !== password.value) {
            isValid = false;
            message = 'كلمتا المرور غير متطابقتين';
        }
    }

    // Update field state
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.style.display = 'none';
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        let feedback = field.parentElement.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentElement.appendChild(feedback);
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    return isValid;
}

// ==================== SEARCH SUGGESTIONS ====================
function initSearchSuggestions() {
    const searchInputs = document.querySelectorAll('.search-with-suggestions');

    searchInputs.forEach(input => {
        let suggestionsContainer = input.parentElement.querySelector('.search-suggestions-modern');

        if (!suggestionsContainer) {
            suggestionsContainer = document.createElement('div');
            suggestionsContainer.className = 'search-suggestions-modern';
            suggestionsContainer.style.display = 'none';
            input.parentElement.appendChild(suggestionsContainer);
        }

        let debounceTimer;

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsContainer.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetchSuggestions(query, suggestionsContainer, input);
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    });
}

function fetchSuggestions(query, container, input) {
    // This would typically fetch from an API
    // For now, we'll show a simple example
    const suggestions = [
        { icon: 'fas fa-search', text: query },
        { icon: 'fas fa-history', text: 'بحث سابق 1' },
        { icon: 'fas fa-history', text: 'بحث سابق 2' }
    ];

    container.innerHTML = suggestions.map(item => `
        <div class="suggestion-item-modern">
            <i class="${item.icon}"></i>
            <span>${item.text}</span>
        </div>
    `).join('');

    container.style.display = 'block';

    // Add click handlers
    container.querySelectorAll('.suggestion-item-modern').forEach((item, index) => {
        item.addEventListener('click', function () {
            input.value = suggestions[index].text;
            container.style.display = 'none';
            input.form?.submit();
        });
    });
}

// ==================== CONFIRMATION DIALOGS ====================
function initConfirmationDialogs() {
    const confirmButtons = document.querySelectorAll('[data-confirm]');

    confirmButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm');
            const type = this.getAttribute('data-confirm-type') || 'warning';
            const action = this.getAttribute('href') || this.getAttribute('data-action');

            showConfirmationDialog(message, type, () => {
                if (this.tagName === 'A') {
                    window.location.href = action;
                } else if (this.tagName === 'BUTTON' && this.form) {
                    this.form.submit();
                }
            });
        });
    });
}

function showConfirmationDialog(message, type, onConfirm) {
    const dialog = document.createElement('div');
    dialog.className = 'confirmation-dialog';
    dialog.innerHTML = `
        <div class="confirmation-content">
            <div class="confirmation-icon ${type}">
                <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'question-circle'}"></i>
            </div>
            <h3 class="confirmation-title">تأكيد العملية</h3>
            <p class="confirmation-message">${message}</p>
            <div class="confirmation-actions">
                <button class="btn btn-outline-modern flex-fill" onclick="this.closest('.confirmation-dialog').remove()">
                    إلغاء
                </button>
                <button class="btn btn-${type === 'danger' ? 'danger' : 'primary'}-modern flex-fill" id="confirmAction">
                    تأكيد
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(dialog);

    dialog.querySelector('#confirmAction').addEventListener('click', function () {
        onConfirm();
        dialog.remove();
    });

    // Close on backdrop click
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) {
            dialog.remove();
        }
    });
}

// ==================== TOOLTIPS ====================
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        const text = element.getAttribute('data-tooltip');
        const wrapper = document.createElement('span');
        wrapper.className = 'tooltip-modern';

        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);

        const tooltip = document.createElement('span');
        tooltip.className = 'tooltip-text';
        tooltip.textContent = text;
        wrapper.appendChild(tooltip);
    });
}

// ==================== AUTO-SAVE ====================
function initAutoSave() {
    const autoSaveForms = document.querySelectorAll('[data-autosave]');

    autoSaveForms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        let saveTimer;

        // Create indicator
        const indicator = document.createElement('div');
        indicator.className = 'autosave-indicator';
        indicator.style.display = 'none';
        form.insertBefore(indicator, form.firstChild);

        inputs.forEach(input => {
            input.addEventListener('input', function () {
                clearTimeout(saveTimer);
                indicator.textContent = 'جاري الحفظ...';
                indicator.style.display = 'inline-block';

                saveTimer = setTimeout(() => {
                    saveFormData(form);
                    indicator.textContent = '✓ تم الحفظ تلقائياً';
                    setTimeout(() => {
                        indicator.style.display = 'none';
                    }, 2000);
                }, 2000);
            });
        });
    });
}

function saveFormData(form) {
    const formData = new FormData(form);
    const data = {};

    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    // Save to localStorage
    const formId = form.getAttribute('id') || 'autosave-form';
    localStorage.setItem(`autosave_${formId}`, JSON.stringify(data));
}

// ==================== LOADING STATES ====================
function initLoadingStates() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.classList.contains('no-loading')) {
                submitButton.classList.add('btn-loading');
                submitButton.disabled = true;

                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';

                // Restore after 10 seconds (fallback)
                setTimeout(() => {
                    submitButton.classList.remove('btn-loading');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }, 10000);
            }
        });
    });
}

// ==================== TABLE ENHANCEMENTS ====================
function initTableEnhancements() {
    const tables = document.querySelectorAll('.table-sortable');

    tables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sortable]');

        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.innerHTML += ' <i class="fas fa-sort ms-2"></i>';

            header.addEventListener('click', function () {
                const column = this.getAttribute('data-sortable');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const isAscending = this.classList.contains('sort-asc');

                // Remove sort classes from all headers
                headers.forEach(h => {
                    h.classList.remove('sort-asc', 'sort-desc');
                    h.querySelector('i').className = 'fas fa-sort ms-2';
                });

                // Sort rows
                rows.sort((a, b) => {
                    const aValue = a.querySelector(`td:nth-child(${this.cellIndex + 1})`).textContent;
                    const bValue = b.querySelector(`td:nth-child(${this.cellIndex + 1})`).textContent;

                    if (isAscending) {
                        return bValue.localeCompare(aValue, 'ar');
                    } else {
                        return aValue.localeCompare(bValue, 'ar');
                    }
                });

                // Update table
                rows.forEach(row => tbody.appendChild(row));

                // Update header
                this.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
                this.querySelector('i').className = `fas fa-sort-${isAscending ? 'down' : 'up'} ms-2`;
            });
        });
    });
}

// ==================== FILTER PANEL ====================
function initFilterPanel() {
    const filterChips = document.querySelectorAll('.filter-chip');

    filterChips.forEach(chip => {
        chip.addEventListener('click', function () {
            this.classList.toggle('active');

            // Trigger filter update
            const panel = this.closest('.filters-panel-modern');
            if (panel) {
                updateFilters(panel);
            }
        });
    });
}

function updateFilters(panel) {
    const activeFilters = panel.querySelectorAll('.filter-chip.active');
    const filters = {};

    activeFilters.forEach(chip => {
        const group = chip.closest('.filter-group').getAttribute('data-filter-group');
        const value = chip.getAttribute('data-filter-value');

        if (!filters[group]) {
            filters[group] = [];
        }
        filters[group].push(value);
    });

    console.log('Active filters:', filters);
    // Here you would typically send this to the server or filter client-side
}

// ==================== UTILITY FUNCTIONS ====================

// Show loading overlay
function showLoading(message = 'جاري التحميل...') {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loadingOverlay';
    overlay.innerHTML = `
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="text-white mt-3">${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

// Hide loading overlay
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert-modern alert-${type}-modern`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.left = '20px';
    toast.style.zIndex = '10000';
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        <div class="alert-modern-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i>
        </div>
        <div class="alert-modern-content">
            <div class="alert-modern-message">${message}</div>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutLeft 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Export functions for global use
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showToast = showToast;
window.showConfirmationDialog = showConfirmationDialog;
