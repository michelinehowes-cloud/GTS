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
        window["onTurnstileError_{{ $funcSuffix }}"] = function() {
            isVerified = false;
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            restoreSubmitBtn(form, '<div class="text-danger small mt-1 fw-bold text-center"><i class="fas fa-exclamation-circle me-1"></i> تعذر الاتصال بكاشف الروبوتات، انقر مجدداً للدخول.</div>');
        };

        // منع إرسال النموذج قبل اكتمال التحقق من كلاودفير مع مهلة أمان قصوى
        function bindFormValidation() {
            const wrapper = document.getElementById(wrapId);
            const form = wrapper ? wrapper.closest('form') : null;
            if (!form) return;

            form.addEventListener('submit', function(e) {
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }

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

                    // مهلة أمان قصوى (3.5 ثانية فقط): لمنع تجميد النموذج للمدير
                    if (timeoutTimer) clearTimeout(timeoutTimer);
                    timeoutTimer = setTimeout(function() {
                        if (pendingSubmit && !isVerified) {
                            let bypassInput = form.querySelector('[name="cf-turnstile-response"]');
                            if (!bypassInput) {
                                bypassInput = document.createElement('input');
                                bypassInput.type = 'hidden';
                                bypassInput.name = 'cf-turnstile-response';
                                form.appendChild(bypassInput);
                            }
                            bypassInput.value = 'BYPASS_TIMEOUT';
                            restoreSubmitBtn(form, '');
                            form.submit();
                        }
                    }, 3500);

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
