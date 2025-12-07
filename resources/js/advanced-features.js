// Advanced Features: Breadcrumbs, Validation, Input Masks, Search, etc.

document.addEventListener('DOMContentLoaded', function () {
    console.log('Advanced features loading...');

    // 1. Enhanced Breadcrumbs
    initBreadcrumbs();

    // 2. Auto-save for Forms
    initAutoSave();

    // 3. Smart Validation
    initSmartValidation();

    // 4. Input Masks
    initInputMasks();

    // 5. Search Enhancements
    initSearchEnhancements();

    // 6. Biometric Authentication
    initBiometricAuth();

    // 7. Session Management
    initSessionManagement();

    // 8. Dark Mode Enhancements
    initDarkMode();

    // 9. Interactive Charts
    initInteractiveCharts();

    // 10. Error Boundaries & Retry
    initErrorHandling();

    console.log('Advanced features initialized!');
});

// ==================== 1. BREADCRUMBS ====================
function initBreadcrumbs() {
    const breadcrumbContainer = document.querySelector('.breadcrumb-container');
    if (!breadcrumbContainer) {
        // إنشاء breadcrumbs تلقائياً
        createAutoBreadcrumbs();
    }
}

function createAutoBreadcrumbs() {
    const path = window.location.pathname;
    const segments = path.split('/').filter(s => s);

    if (segments.length === 0) return;

    const breadcrumbHTML = `
        <nav aria-label="breadcrumb" class="breadcrumb-container mb-3">
            <ol class="breadcrumb bg-light p-3 rounded shadow-sm">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>الرئيسية
                    </a>
                </li>
                ${segments.map((segment, index) => {
        const url = '/' + segments.slice(0, index + 1).join('/');
        const label = formatBreadcrumbLabel(segment);
        const isLast = index === segments.length - 1;

        return isLast
            ? `<li class="breadcrumb-item active" aria-current="page">${label}</li>`
            : `<li class="breadcrumb-item"><a href="${url}" class="text-decoration-none">${label}</a></li>`;
    }).join('')}
            </ol>
        </nav>
    `;

    const mainContent = document.querySelector('.main-content, .content-area-padding');
    if (mainContent && mainContent.firstChild) {
        mainContent.insertAdjacentHTML('afterbegin', breadcrumbHTML);
    }
}

function formatBreadcrumbLabel(segment) {
    const labels = {
        'admin': 'لوحة الإدارة',
        'graduate': 'الخريج',
        'dashboard': 'لوحة التحكم',
        'trainings': 'التدريبات',
        'job-opportunities': 'فرص العمل',
        'companies': 'الشركات',
        'nominations': 'الترشيحات',
        'reports': 'التقارير',
        'profile': 'الملف الشخصي',
        'notifications': 'الإشعارات',
        'create': 'إضافة جديد',
        'edit': 'تعديل',
    };

    return labels[segment] || segment.replace(/-/g, ' ');
}

// ==================== 2. AUTO-SAVE ====================
function initAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');

    forms.forEach(form => {
        const formId = form.id || 'form-' + Math.random().toString(36).substr(2, 9);
        const storageKey = 'autosave-' + formId;

        // استرجاع البيانات المحفوظة
        const savedData = localStorage.getItem(storageKey);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(name => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input && !input.value) {
                        input.value = data[name];
                    }
                });

                // إظهار رسالة
                showAutoSaveNotice(form);
            } catch (e) {
                console.error('Error loading autosave:', e);
            }
        }

        // حفظ تلقائي عند التغيير
        let saveTimeout;
        form.addEventListener('input', function (e) {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                localStorage.setItem(storageKey, JSON.stringify(data));
                showAutoSaveIndicator(form);
            }, 1000);
        });

        // مسح البيانات عند الإرسال الناجح
        form.addEventListener('submit', function () {
            setTimeout(() => {
                localStorage.removeItem(storageKey);
            }, 2000);
        });
    });
}

function showAutoSaveNotice(form) {
    const notice = document.createElement('div');
    notice.className = 'alert alert-info alert-dismissible fade show';
    notice.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        تم استرجاع البيانات المحفوظة تلقائياً
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    form.insertAdjacentElement('beforebegin', notice);
    setTimeout(() => notice.remove(), 5000);
}

function showAutoSaveIndicator(form) {
    let indicator = form.querySelector('.autosave-indicator');
    if (!indicator) {
        indicator = document.createElement('small');
        indicator.className = 'autosave-indicator text-success';
        indicator.innerHTML = '<i class="fas fa-check-circle me-1"></i>تم الحفظ تلقائياً';
        form.insertAdjacentElement('afterbegin', indicator);
    }
    indicator.style.opacity = '1';
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 2000);
}

// ==================== 3. SMART VALIDATION ====================
function initSmartValidation() {
    const inputs = document.querySelectorAll('input[required], textarea[required], select[required]');

    inputs.forEach(input => {
        // التحقق الفوري
        input.addEventListener('blur', function () {
            validateInput(this);
        });

        input.addEventListener('input', function () {
            if (this.classList.contains('is-invalid')) {
                validateInput(this);
            }
        });
    });
}

function validateInput(input) {
    const value = input.value.trim();
    const type = input.type;
    const name = input.name;

    let isValid = true;
    let message = '';

    // التحقق من الحقول المطلوبة
    if (input.hasAttribute('required') && !value) {
        isValid = false;
        message = 'هذا الحقل مطلوب';
    }

    // التحقق من البريد الإلكتروني
    else if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'يرجى إدخال بريد إلكتروني صحيح';
        }
    }

    // التحقق من رقم الهاتف
    else if (name.includes('phone') && value) {
        const phoneRegex = /^(05|5)[0-9]{8}$/;
        if (!phoneRegex.test(value.replace(/\s/g, ''))) {
            isValid = false;
            message = 'يرجى إدخال رقم جوال صحيح (مثال: 0512345678)';
        }
    }

    // التحقق من الرقم الوطني
    else if (name.includes('national_id') && value) {
        if (value.length !== 10 || !/^\d+$/.test(value)) {
            isValid = false;
            message = 'الرقم الوطني يجب أن يكون 10 أرقام';
        }
    }

    // التحقق من كلمة المرور
    else if (type === 'password' && value && name === 'password') {
        if (value.length < 8) {
            isValid = false;
            message = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        }
    }

    // التحقق من تأكيد كلمة المرور
    else if (name === 'password_confirmation' && value) {
        const password = document.querySelector('input[name="password"]');
        if (password && value !== password.value) {
            isValid = false;
            message = 'كلمة المرور غير متطابقة';
        }
    }

    // عرض النتيجة
    if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        removeErrorMessage(input);
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        showErrorMessage(input, message);
    }

    return isValid;
}

function showErrorMessage(input, message) {
    removeErrorMessage(input);
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback d-block';
    feedback.textContent = message;
    input.parentNode.appendChild(feedback);
}

function removeErrorMessage(input) {
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
}

// ==================== 4. INPUT MASKS ====================
function initInputMasks() {
    // رقم الجوال
    document.querySelectorAll('input[name*="phone"]').forEach(input => {
        input.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.substr(0, 10);

            if (value.length >= 3) {
                value = value.substr(0, 3) + ' ' + value.substr(3);
            }
            if (value.length >= 7) {
                value = value.substr(0, 7) + ' ' + value.substr(7);
            }

            e.target.value = value;
        });
    });

    // الرقم الوطني
    document.querySelectorAll('input[name*="national_id"]').forEach(input => {
        input.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.substr(0, 10);
            e.target.value = value;
        });
    });

    // التاريخ (إذا لم يكن input type="date")
    document.querySelectorAll('input[data-mask="date"]').forEach(input => {
        input.placeholder = 'YYYY-MM-DD';
        input.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 4) {
                value = value.substr(0, 4) + '-' + value.substr(4);
            }
            if (value.length >= 7) {
                value = value.substr(0, 7) + '-' + value.substr(7, 2);
            }
            e.target.value = value.substr(0, 10);
        });
    });
}

// سأكمل باقي الدوال في الرسالة التالية...
