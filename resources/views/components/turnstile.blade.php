@props([
    'theme' => 'light',
    'size' => 'flexible',
    'action' => null,
])

@if(config('security.turnstile.enabled', true))
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

        // دالة نجاح التحقق من كلاودفير
        window["onTurnstileSuccess_{{ $funcSuffix }}"] = function(token) {
            isVerified = true;
            const msgBox = document.getElementById(msgId);
            if (msgBox) msgBox.innerHTML = '';

            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            if (form) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (submitBtn.dataset.originalHtml) {
                        submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                    }
                }

                // إذا كان المستخدم قد ضغط على الدخول بانتظار التحقق، نكمل الإرسال فوراً
                if (pendingSubmit) {
                    pendingSubmit = false;
                    form.submit();
                }
            }
        };

        // دالة انتهاء صلاحية التحقق
        window["onTurnstileExpired_{{ $funcSuffix }}"] = function() {
            isVerified = false;
            const msgBox = document.getElementById(msgId);
            if (msgBox) {
                msgBox.innerHTML = '<div class="text-warning small mt-1 fw-bold text-center"><i class="fas fa-clock me-1"></i> انتهت صلاحية التحقق الأمني، يرجى إعادة النقر على الكاشف.</div>';
            }
        };

        // دالة حدوث خطأ
        window["onTurnstileError_{{ $funcSuffix }}"] = function() {
            isVerified = false;
            const msgBox = document.getElementById(msgId);
            if (msgBox) {
                msgBox.innerHTML = '<div class="text-danger small mt-1 fw-bold text-center"><i class="fas fa-exclamation-circle me-1"></i> تعذر الاتصال بكاشف الروبوتات، يرجى التحقق من اتصال الإنترنت.</div>';
            }
        };

        // منع إرسال النموذج قبل اكتمال التحقق من كلاودفير
        function bindFormValidation() {
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            if (!form) return;

            form.addEventListener('submit', function(e) {
                // إذا كانت هناك حقول مطلوبة لم تُملأ بعد، نسمح للمتصفح بإظهار تنبيه ملء الحقول أولاً
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }

                // التأكد من وجود التوكن
                const tokenInput = form.querySelector('[name="cf-turnstile-response"]');
                const hasToken = isVerified || (tokenInput && tokenInput.value && tokenInput.value.trim().length > 10);

                if (!hasToken) {
                    e.preventDefault();
                    e.stopPropagation();
                    pendingSubmit = true;

                    const msgBox = document.getElementById(msgId);
                    if (msgBox) {
                        msgBox.innerHTML = '<div class="text-warning small mt-1 fw-bold text-center p-1 rounded bg-warning bg-opacity-10"><i class="fas fa-spinner fa-spin me-1"></i> يرجى الانتظار حتى يكتمل التحقق الأمني (Cloudflare)...</div>';
                    }

                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        if (!submitBtn.dataset.originalHtml) {
                            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                        }
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري التحقق...';
                        submitBtn.disabled = true;
                    }

                    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            }, true);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindFormValidation);
        } else {
            bindFormValidation();
        }
    })();
    </script>
@endif
