// UI Enhancements for Better UX
// Safe and non-breaking improvements

document.addEventListener('DOMContentLoaded', function () {
    console.log('UI Enhancements loaded...');

    // 1. Haptic Feedback (للتطبيق فقط)
    const isNativeApp = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();

    if (isNativeApp && window.Capacitor.Plugins && window.Capacitor.Plugins.Haptics) {
        const Haptics = window.Capacitor.Plugins.Haptics;

        // إضافة haptic feedback للأزرار
        document.querySelectorAll('button, .btn, a.nav-link').forEach(element => {
            element.addEventListener('click', function () {
                Haptics.impact({ style: 'light' }).catch(err => console.log('Haptics not available'));
            });
        });

        // haptic أقوى للإجراءات المهمة
        document.querySelectorAll('.btn-primary, .btn-success, .btn-danger').forEach(element => {
            element.addEventListener('click', function () {
                Haptics.impact({ style: 'medium' }).catch(err => console.log('Haptics not available'));
            });
        });
    }

    // 2. Skeleton Loaders - إضافة تلقائية
    function showSkeleton(container) {
        const skeleton = `
            <div class="skeleton-loader">
                <div class="skeleton-item"></div>
                <div class="skeleton-item"></div>
                <div class="skeleton-item"></div>
            </div>
        `;
        container.innerHTML = skeleton;
    }

    // 3. Pull to Refresh (للتطبيق فقط)
    if (isNativeApp) {
        let startY = 0;
        let currentY = 0;
        let pulling = false;
        const threshold = 80;

        const refreshIndicator = document.createElement('div');
        refreshIndicator.className = 'pull-to-refresh-indicator';
        refreshIndicator.innerHTML = '<i class="fas fa-sync-alt"></i> اسحب للتحديث';
        document.body.insertBefore(refreshIndicator, document.body.firstChild);

        document.addEventListener('touchstart', function (e) {
            if (window.scrollY === 0) {
                startY = e.touches[0].pageY;
                pulling = true;
            }
        });

        document.addEventListener('touchmove', function (e) {
            if (!pulling) return;

            currentY = e.touches[0].pageY;
            const distance = currentY - startY;

            if (distance > 0 && distance < threshold * 2) {
                refreshIndicator.style.transform = `translateY(${distance}px)`;
                refreshIndicator.style.opacity = Math.min(distance / threshold, 1);
            }
        });

        document.addEventListener('touchend', function (e) {
            if (!pulling) return;

            const distance = currentY - startY;

            if (distance > threshold) {
                refreshIndicator.classList.add('refreshing');
                refreshIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> جاري التحديث...';

                // تحديث الصفحة
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                refreshIndicator.style.transform = 'translateY(0)';
                refreshIndicator.style.opacity = '0';
            }

            pulling = false;
        });
    }

    // 4. Smooth Scroll للروابط الداخلية
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // 5. Loading States للنماذج
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';

                // إعادة التفعيل بعد 10 ثوانٍ كحد أقصى (في حالة فشل الإرسال)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });
    });

    // 6. Auto-hide Success Messages
    setTimeout(() => {
        document.querySelectorAll('.alert-success').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // 7. Image Lazy Loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // 8. Prevent Double Click على الأزرار
    document.querySelectorAll('button, .btn').forEach(button => {
        let clicked = false;
        button.addEventListener('click', function () {
            if (clicked) return;
            clicked = true;
            setTimeout(() => clicked = false, 1000);
        });
    });

    // 9. Enhanced Error Messages
    window.showError = function (message, duration = 5000) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        errorDiv.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.style.opacity = '0';
            setTimeout(() => errorDiv.remove(), 300);
        }, duration);
    };

    // 10. Enhanced Success Messages
    window.showSuccess = function (message, duration = 3000) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        successDiv.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(successDiv);

        setTimeout(() => {
            successDiv.style.opacity = '0';
            setTimeout(() => successDiv.remove(), 300);
        }, duration);
    };

    console.log('UI Enhancements initialized successfully!');
});
