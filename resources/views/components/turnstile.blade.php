@props([
    'theme' => 'light',
    'size' => 'flexible',
    'action' => null,
])

@if(config('security.turnstile.enabled', false) && !empty(config('security.turnstile.site_key')) && !empty(config('security.turnstile.secret_key')))
    @php
        $siteKey = config('security.turnstile.site_key');
        $funcSuffix = 'ts_' . substr(md5($action . '_' . uniqid()), 0, 8);
    @endphp

    <div class="turnstile-wrapper my-2.5 d-flex flex-column align-items-center justify-content-center" id="wrap-{{ $funcSuffix }}">
        <div id="widget-{{ $funcSuffix }}"
             class="cf-turnstile" 
             data-sitekey="{{ $siteKey }}" 
             data-theme="{{ $theme }}" 
             data-size="{{ $size }}"
             data-callback="onTurnstileSuccess_{{ $funcSuffix }}"
             data-expired-callback="onTurnstileExpired_{{ $funcSuffix }}"
             data-error-callback="onTurnstileError_{{ $funcSuffix }}"
             @if($action) data-action="{{ $action }}" @endif>
        </div>
        <div id="turnstile-msg-{{ $funcSuffix }}"></div>
        @error('cf-turnstile-response')
            <div class="text-danger small mt-1 fw-bold text-center">
                <i class="fas fa-shield-alt me-1"></i>{{ $message }}
            </div>
        @enderror
        @error('security')
            <div class="text-danger small mt-1 fw-bold text-center">
                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>

    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce

    <script>
    (function() {
        const wrapId = "wrap-{{ $funcSuffix }}";
        const msgId = "turnstile-msg-{{ $funcSuffix }}";
        let isVerified = false;
        let pendingSubmit = false;
        let timeoutTimer = null;

        function restoreSubmitBtn(form, msgHtml) {
            pendingSubmit = false;
            if (timeoutTimer) {
                clearTimeout(timeoutTimer);
                timeoutTimer = null;
            }
            if (form) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (submitBtn.dataset.originalHtml) {
                        submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                    }
                }
            }
            const msgBox = document.getElementById(msgId);
            if (msgBox && typeof msgHtml === 'string') {
                msgBox.innerHTML = msgHtml;
            }
        }

        // دالة نجاح التحقق من كلاودفير
        window["onTurnstileSuccess_{{ $funcSuffix }}"] = function(token) {
            isVerified = true;
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            restoreSubmitBtn(form, '');

            // إذا كان المستخدم قد ضغط على الدخول بانتظار التحقق، نكمل الإرسال فوراً
            if (form && pendingSubmit) {
                pendingSubmit = false;
                form.submit();
            }
        };

        // دالة انتهاء صلاحية التحقق
        window["onTurnstileExpired_{{ $funcSuffix }}"] = function() {
            isVerified = false;
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            restoreSubmitBtn(form, '<div class="text-warning small mt-1 fw-bold text-center"><i class="fas fa-clock me-1"></i> انتهت صلاحية التحقق الأمني، يرجى إعادة النقر على الكاشف.</div>');
        };

        // دالة حدوث خطأ في الاتصال بكلاودفير
        window["onTurnstileError_{{ $funcSuffix }}"] = function(errorCode) {
            isVerified = false;
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            let errorText = 'تعذر الاتصال بكاشف الروبوتات.';
            if (errorCode == '110200' || errorCode == '110600' || String(errorCode).includes('domain')) {
                errorText = 'تنبيه: يجب إضافة هذا النطاق (' + window.location.hostname + ') في لوحة تحكم Cloudflare Turnstile ضمن قائمة Domains المسموح بها.';
            } else {
                errorText = 'فشل التحقق من كاشف الروبوتات (Cloudflare). يرجى التأكد من اتصال الإنترنت أو النقر على الكاشف لإعادة المحاولة.';
            }
            restoreSubmitBtn(form, `<div class="text-danger small mt-2 p-2 rounded bg-danger bg-opacity-10 fw-bold text-center"><i class="fas fa-exclamation-triangle me-1"></i> ${errorText}</div>`);
        };

        // منع إرسال النموذج نهائياً قبل اكتمال التحقق من كلاودفير
        function bindFormValidation() {
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            if (!form) return;

            form.addEventListener('submit', function(e) {
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }

                const tokenInput = form.querySelector('[name="cf-turnstile-response"]');
                const hasToken = isVerified || (tokenInput && tokenInput.value && tokenInput.value.trim().length > 10 && !tokenInput.value.startsWith('BYPASS_'));

                if (!hasToken) {
                    e.preventDefault();
                    e.stopPropagation();
                    restoreSubmitBtn(form, '<div class="text-danger small mt-2 p-2 rounded bg-danger bg-opacity-10 fw-bold text-center"><i class="fas fa-shield-alt me-1"></i> يرجى النقر على كاشف الروبوتات (Cloudflare) وإكمال التحقق الأمني أولاً.</div>');
                    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            }, true);
        }

        // دعم إعادة الرسم عند فتح المودال في بوتستراب
        document.addEventListener('shown.bs.modal', function(e) {
            const widget = document.getElementById("widget-{{ $funcSuffix }}");
            if (widget && window.turnstile && typeof window.turnstile.render === 'function') {
                if (!widget.hasChildNodes()) {
                    try {
                        window.turnstile.render(widget);
                    } catch (err) {}
                }
            }
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindFormValidation);
        } else {
            bindFormValidation();
        }
    })();
    </script>
@endif
