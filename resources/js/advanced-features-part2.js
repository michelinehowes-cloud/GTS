// Advanced Features Part 2: Search, Security, Dark Mode, Charts, Error Handling

// ==================== 5. SEARCH ENHANCEMENTS ====================
function initSearchEnhancements() {
    const searchInputs = document.querySelectorAll('input[type="search"], input[data-search]');

    searchInputs.forEach(input => {
        // Search Suggestions
        initSearchSuggestions(input);

        // Recent Searches
        initRecentSearches(input);
    });
}

function initSearchSuggestions(input) {
    let suggestionsBox;

    input.addEventListener('input', function () {
        const query = this.value.trim();

        if (query.length < 2) {
            hideSuggestions();
            return;
        }

        // هنا يمكن إضافة AJAX للحصول على الاقتراحات من السيرفر
        const suggestions = getLocalSuggestions(query);

        if (suggestions.length > 0) {
            showSuggestions(input, suggestions);
        }
    });

    function showSuggestions(input, suggestions) {
        hideSuggestions();

        suggestionsBox = document.createElement('div');
        suggestionsBox.className = 'search-suggestions';
        suggestionsBox.innerHTML = suggestions.map(s => `
            <div class="suggestion-item" data-value="${s}">
                <i class="fas fa-search me-2"></i>${s}
            </div>
        `).join('');

        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(suggestionsBox);

        // النقر على اقتراح
        suggestionsBox.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function () {
                input.value = this.dataset.value;
                hideSuggestions();
                input.form?.submit();
            });
        });
    }

    function hideSuggestions() {
        if (suggestionsBox) {
            suggestionsBox.remove();
            suggestionsBox = null;
        }
    }

    // إخفاء عند النقر خارج الصندوق
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && suggestionsBox && !suggestionsBox.contains(e.target)) {
            hideSuggestions();
        }
    });
}

function getLocalSuggestions(query) {
    // يمكن استبدال هذا بـ AJAX call للسيرفر
    const commonSearches = [
        'تدريب تقني',
        'وظيفة محاسب',
        'فرصة تدريب',
        'شركة تقنية',
        'خريج جديد'
    ];

    return commonSearches.filter(s => s.includes(query));
}

function initRecentSearches(input) {
    const storageKey = 'recent-searches';

    // عرض البحوث الأخيرة عند التركيز
    input.addEventListener('focus', function () {
        if (!this.value) {
            const recent = getRecentSearches();
            if (recent.length > 0) {
                showRecentSearches(this, recent);
            }
        }
    });

    // حفظ البحث عند الإرسال
    input.form?.addEventListener('submit', function () {
        const query = input.value.trim();
        if (query) {
            saveRecentSearch(query);
        }
    });
}

function getRecentSearches() {
    const stored = localStorage.getItem('recent-searches');
    return stored ? JSON.parse(stored) : [];
}

function saveRecentSearch(query) {
    let recent = getRecentSearches();
    recent = recent.filter(s => s !== query);
    recent.unshift(query);
    recent = recent.slice(0, 5); // آخر 5 عمليات بحث
    localStorage.setItem('recent-searches', JSON.stringify(recent));
}

function showRecentSearches(input, searches) {
    const box = document.createElement('div');
    box.className = 'search-suggestions';
    box.innerHTML = `
        <div class="suggestion-header">عمليات البحث الأخيرة</div>
        ${searches.map(s => `
            <div class="suggestion-item" data-value="${s}">
                <i class="fas fa-history me-2"></i>${s}
            </div>
        `).join('')}
    `;

    input.parentNode.appendChild(box);

    box.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('click', function () {
            input.value = this.dataset.value;
            box.remove();
        });
    });
}

// ==================== 6. BIOMETRIC AUTHENTICATION ====================
function initBiometricAuth() {
    const isNativeApp = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();

    if (!isNativeApp) return;

    // التحقق من توفر البصمة
    if (window.Capacitor.Plugins && window.Capacitor.Plugins.BiometricAuth) {
        const BiometricAuth = window.Capacitor.Plugins.BiometricAuth;

        // إضافة زر البصمة في صفحة تسجيل الدخول
        const loginForm = document.querySelector('form[action*="login"]');
        if (loginForm) {
            addBiometricButton(loginForm, BiometricAuth);
        }
    }
}

function addBiometricButton(form, BiometricAuth) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn btn-outline-primary w-100 mt-2';
    button.innerHTML = '<i class="fas fa-fingerprint me-2"></i>تسجيل الدخول بالبصمة';

    button.addEventListener('click', async function () {
        try {
            const result = await BiometricAuth.verify({
                reason: 'تسجيل الدخول إلى النظام',
                title: 'المصادقة البيومترية'
            });

            if (result.verified) {
                // استرجاع بيانات الاعتماد المحفوظة
                const credentials = await getStoredCredentials();
                if (credentials) {
                    form.querySelector('[name="email"]').value = credentials.email;
                    form.querySelector('[name="password"]').value = credentials.password;
                    form.submit();
                }
            }
        } catch (error) {
            console.error('Biometric auth error:', error);
            window.showError('فشلت المصادقة البيومترية');
        }
    });

    form.appendChild(button);
}

async function getStoredCredentials() {
    if (window.Capacitor.Plugins.Storage) {
        const { value } = await window.Capacitor.Plugins.Storage.get({ key: 'biometric-credentials' });
        return value ? JSON.parse(value) : null;
    }
    return null;
}

// ==================== 7. SESSION MANAGEMENT ====================
function initSessionManagement() {
    let lastActivity = Date.now();
    const timeout = 30 * 60 * 1000; // 30 دقيقة

    // تحديث آخر نشاط
    ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, () => {
            lastActivity = Date.now();
        });
    });

    // فحص الجلسة كل دقيقة
    setInterval(() => {
        const inactive = Date.now() - lastActivity;

        if (inactive > timeout) {
            handleSessionTimeout();
        } else if (inactive > timeout - 5 * 60 * 1000) {
            // تحذير قبل 5 دقائق
            showSessionWarning();
        }
    }, 60000);
}

function handleSessionTimeout() {
    if (confirm('انتهت جلستك بسبب عدم النشاط. هل تريد تسجيل الدخول مرة أخرى؟')) {
        window.location.href = '/login';
    } else {
        // تسجيل خروج
        document.querySelector('form[action*="logout"]')?.submit();
    }
}

function showSessionWarning() {
    const warning = document.createElement('div');
    warning.className = 'alert alert-warning alert-dismissible fade show position-fixed';
    warning.style.cssText = 'top: 80px; right: 20px; z-index: 9999;';
    warning.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ستنتهي جلستك قريباً بسبب عدم النشاط
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(warning);
    setTimeout(() => warning.remove(), 10000);
}

// ==================== 8. DARK MODE ENHANCEMENTS ====================
function initDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const savedMode = localStorage.getItem('darkMode');

    // تطبيق الوضع المحفوظ
    if (savedMode === 'dark') {
        document.body.classList.add('dark-mode');
    }

    // Auto Switch حسب الوقت
    const hour = new Date().getHours();
    if (!savedMode && (hour < 6 || hour > 18)) {
        document.body.classList.add('dark-mode');
    }

    // Toggle
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark ? 'dark' : 'light');
        });
    }
}

// ==================== 9. INTERACTIVE CHARTS ====================
function initInteractiveCharts() {
    // تحسين الرسوم البيانية الموجودة
    if (typeof Chart !== 'undefined') {
        Chart.defaults.plugins.tooltip.enabled = true;
        Chart.defaults.plugins.legend.onClick = function (e, legendItem, legend) {
            const index = legendItem.datasetIndex;
            const chart = legend.chart;
            const meta = chart.getDatasetMeta(index);
            meta.hidden = !meta.hidden;
            chart.update();
        };
    }
}

// ==================== 10. ERROR HANDLING ====================
function initErrorHandling() {
    // Error Boundary
    window.addEventListener('error', function (event) {
        console.error('Global error:', event.error);
        handleError(event.error);
    });

    // Unhandled Promise Rejection
    window.addEventListener('unhandledrejection', function (event) {
        console.error('Unhandled rejection:', event.reason);
        handleError(event.reason);
    });

    // Retry Mechanism for fetch
    window.fetchWithRetry = async function (url, options = {}, retries = 3) {
        for (let i = 0; i < retries; i++) {
            try {
                const response = await fetch(url, options);
                if (response.ok) return response;

                if (i === retries - 1) throw new Error('Max retries reached');
            } catch (error) {
                if (i === retries - 1) throw error;
                await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
            }
        }
    };
}

function handleError(error) {
    const errorMessage = error.message || 'حدث خطأ غير متوقع';

    // عدم إظهار أخطاء معينة
    if (errorMessage.includes('ResizeObserver') || errorMessage.includes('Script error')) {
        return;
    }

    // إظهار رسالة خطأ للمستخدم
    if (window.showError) {
        window.showError(errorMessage);
    }

    // يمكن إرسال الخطأ للسيرفر للتسجيل
    logErrorToServer(error);
}

function logErrorToServer(error) {
    // إرسال الخطأ للسيرفر
    if (navigator.onLine) {
        fetch('/api/log-error', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({
                message: error.message,
                stack: error.stack,
                url: window.location.href,
                userAgent: navigator.userAgent
            })
        }).catch(() => {
            // فشل في إرسال الخطأ - لا بأس
        });
    }
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * عرض رسالة خطأ تحت عنصر الإدخال
 */
function showErrorMessage(input, message) {
    removeErrorMessage(input);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block';
    errorDiv.dataset.errorFor = input.id || input.name || 'unknown';
    errorDiv.textContent = message;
    input.classList.add('is-invalid');
    input.parentNode.appendChild(errorDiv);
}

/**
 * إزالة رسالة الخطأ من عنصر الإدخال
 */
function removeErrorMessage(input) {
    input.classList.remove('is-invalid');
    const key = input.id || input.name || 'unknown';
    const existing = input.parentNode.querySelector(`[data-error-for="${key}"]`);
    if (existing) existing.remove();
}

/**
 * التحقق من صحة إدخال واحد
 */
function validateInputLocal(input) {
    const value = input.value.trim();
    if (input.hasAttribute('required') && !value) {
        showErrorMessage(input, 'هذا الحقل مطلوب');
        return false;
    }
    removeErrorMessage(input);
    input.classList.add('is-valid');
    return true;
}

// تصدير الدوال للاستخدام العام
window.advancedFeatures = {
    validateInput:    validateInputLocal,
    showErrorMessage: showErrorMessage,
    removeErrorMessage: removeErrorMessage,
    getRecentSearches: getRecentSearches,
    saveRecentSearch:  saveRecentSearch,
    handleError:       handleError
};
