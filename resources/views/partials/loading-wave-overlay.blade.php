<!-- ══════════════════════════════════════════════════════════
     مكون شاشة المعالجة التفاعلية العامة (Global Loading Wave Overlay)
     تصميم أنيق بموجة متحركة بهوية المنصة (كحلي ملكي، أزرق سماوي، ذهبي عنبري)
     ══════════════════════════════════════════════════════════ -->
<style>
.global-processing-overlay {
    position: fixed;
    inset: 0;
    background: rgba(3, 14, 33, 0.78);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 109999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    pointer-events: none;
}

.global-processing-overlay.is-active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.global-processing-box {
    background: linear-gradient(160deg, rgba(8, 28, 58, 0.96) 0%, rgba(3, 14, 30, 0.98) 100%);
    border: 1.5px solid rgba(56, 189, 248, 0.45);
    border-radius: 24px;
    padding: 32px 38px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 35px rgba(13, 56, 130, 0.4);
    text-align: center;
    min-width: 290px;
    max-width: 420px;
    position: relative;
    color: #ffffff;
    font-family: 'Tajawal', sans-serif;
    transform: scale(0.95);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.global-processing-overlay.is-active .global-processing-box {
    transform: scale(1);
}

/* 🌟 موجة الأشرطة المتحركة الرئيسية 🌟 */
.loading-wave {
    width: 220px;
    height: 65px;
    display: flex;
    justify-content: center;
    align-items: flex-end;
    margin: 0 auto 16px auto;
}

.loading-bar {
    width: 14px;
    height: 10px;
    margin: 0 5px;
    border-radius: 6px;
    animation: loading-wave-animation 1s ease-in-out infinite;
    transition: all 0.3s ease;
}

.loading-bar:nth-child(1) {
    background: linear-gradient(180deg, #38bdf8, #0284c7);
    box-shadow: 0 0 14px rgba(56, 189, 248, 0.65);
    animation-delay: 0s;
}

.loading-bar:nth-child(2) {
    background: linear-gradient(180deg, #f59e0b, #d97706);
    box-shadow: 0 0 14px rgba(245, 158, 11, 0.65);
    animation-delay: 0.12s;
}

.loading-bar:nth-child(3) {
    background: linear-gradient(180deg, #60a5fa, #0d3882);
    box-shadow: 0 0 14px rgba(96, 165, 250, 0.65);
    animation-delay: 0.24s;
}

.loading-bar:nth-child(4) {
    background: linear-gradient(180deg, #fbbf24, #f59e0b);
    box-shadow: 0 0 14px rgba(251, 191, 36, 0.65);
    animation-delay: 0.36s;
}

@keyframes loading-wave-animation {
    0%, 100% {
        height: 10px;
        opacity: 0.55;
    }
    50% {
        height: 48px;
        opacity: 1;
    }
}

.loading-wave-text {
    font-size: 1rem;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    margin-bottom: 6px;
    letter-spacing: 0.2px;
}

.loading-wave-subtext {
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.72);
    font-weight: 500;
    line-height: 1.5;
}

/* 🌟 موجة مصغرة داخل الأزرار (Mini Inline Wave For Buttons) 🌟 */
.loading-wave-sm {
    width: auto;
    height: 16px;
    display: inline-flex;
    align-items: flex-end;
    gap: 3px;
    vertical-align: middle;
}

.loading-wave-sm .loading-bar {
    width: 3.5px;
    height: 4px;
    margin: 0;
    border-radius: 2px;
    animation: loading-wave-sm-animation 0.9s ease-in-out infinite;
}

.loading-wave-sm .loading-bar:nth-child(1) { animation-delay: 0s; background: #38bdf8; box-shadow: 0 0 6px rgba(56, 189, 248, 0.6); }
.loading-wave-sm .loading-bar:nth-child(2) { animation-delay: 0.12s; background: #f59e0b; box-shadow: 0 0 6px rgba(245, 158, 11, 0.6); }
.loading-wave-sm .loading-bar:nth-child(3) { animation-delay: 0.24s; background: #60a5fa; box-shadow: 0 0 6px rgba(96, 165, 250, 0.6); }
.loading-wave-sm .loading-bar:nth-child(4) { animation-delay: 0.36s; background: #fbbf24; box-shadow: 0 0 6px rgba(251, 191, 36, 0.6); }

@keyframes loading-wave-sm-animation {
    0%, 100% { height: 4px; opacity: 0.6; }
    50% { height: 14px; opacity: 1; }
}
</style>

<!-- شاشة المعالجة التفاعلية العالمية -->
<div class="global-processing-overlay" id="globalProcessingOverlay" aria-hidden="true">
    <div class="global-processing-box">
        <div class="loading-wave">
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
        </div>
        <div class="loading-wave-text" id="globalProcessingText">
            <i class="fas fa-circle-notch fa-spin text-warning small me-2"></i>
            <span>جاري معالجة الطلب...</span>
        </div>
        <div class="loading-wave-subtext" id="globalProcessingSubtext">
            يرجى الانتظار لحظات، يجري التواصل الآمن وحفظ البيانات...
        </div>
    </div>
</div>

<script>
(function () {
    // دوال عامة متاحة لجميع صفحات وسكربتات المنصة
    window.showLoadingWave = function(text, subtext) {
        const overlay = document.getElementById('globalProcessingOverlay') || document.getElementById('cyberProcessingOverlay');
        const textEl = document.getElementById('globalProcessingText') || document.getElementById('cyberProcessingText');
        const subtextEl = document.getElementById('globalProcessingSubtext') || document.getElementById('cyberProcessingSubtext');
        
        if (text && textEl) {
            const s = textEl.querySelector('span');
            if (s) {
                s.textContent = text;
            } else {
                textEl.textContent = text;
            }
        }
        if (subtext && subtextEl) {
            subtextEl.textContent = subtext;
        }
        if (overlay) {
            overlay.classList.add('is-active');
            overlay.setAttribute('aria-hidden', 'false');
        }
    };

    window.hideLoadingWave = function() {
        const overlay = document.getElementById('globalProcessingOverlay') || document.getElementById('cyberProcessingOverlay');
        if (overlay) {
            overlay.classList.remove('is-active');
            overlay.setAttribute('aria-hidden', 'true');
        }
    };

    // إخفاء فوري واستعادة الأزرار عند رجوع المستخدم للخلف أو للأمام بالمتصفح
    window.addEventListener('pageshow', function() {
        window.hideLoadingWave();
        document.querySelectorAll('.btn-wave-loading').forEach(function(btn) {
            btn.disabled = false;
            if (btn.dataset.originalHtml) {
                btn.innerHTML = btn.dataset.originalHtml;
                delete btn.dataset.originalHtml;
            }
            btn.classList.remove('btn-wave-loading');
        });
    });

    // الاستماع العام لكافة نماذج وإجراءات المنصة
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // تجاهل النماذج المستثناة برمجياً أو نماذج التصفية البسيطة (GET) غير الحساسة
        if (form.hasAttribute('data-no-loading') || form.classList.contains('no-loading')) return;
        
        // التحقق من صحة الحقول (HTML5 validation)
        if (form.checkValidity && !form.checkValidity()) return;

        const method = (form.method || 'GET').toUpperCase();
        const isPost = method === 'POST';
        const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.btn-submit') || (document.activeElement && form.contains(document.activeElement) ? document.activeElement : null);

        // وضع الأيقونة المصغرة داخل زر الإرسال
        if (submitBtn && submitBtn.tagName === 'BUTTON' && !submitBtn.classList.contains('btn-wave-loading')) {
            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            submitBtn.classList.add('btn-wave-loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-wave-sm me-2"><span class="loading-bar"></span><span class="loading-bar"></span><span class="loading-bar"></span><span class="loading-bar"></span></span><span>جاري المعالجة...</span>';
        }

        // إظهار شاشة الموجة الكبيرة إذا كان طلب POST (حفظ، تقديم، حذف، تعديل)
        if (isPost || form.hasAttribute('data-wave-loading')) {
            const customText = form.getAttribute('data-loading-text') || 'جاري معالجة وتأكيد الطلب...';
            const customSubtext = form.getAttribute('data-loading-subtext') || 'يرجى الانتظار لحظات، يجري الربط الآمن وحفظ البيانات...';
            window.showLoadingWave(customText, customSubtext);
        }

        // مهلة أمان قصوى 12 ثانية لحماية المستخدم في حال حدوث خطأ شبكة أو تنزيل ملف
        setTimeout(function() {
            window.hideLoadingWave();
            if (submitBtn && submitBtn.dataset.originalHtml) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                delete submitBtn.dataset.originalHtml;
                submitBtn.classList.remove('btn-wave-loading');
            }
        }, 12000);
    }, true);

    // مراقبة أي زر أو رابط يحمل سمة data-loading-wave
    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-loading-wave], .btn-loading-wave');
        if (trigger && !trigger.disabled) {
            const text = trigger.getAttribute('data-loading-text') || 'جاري تنفيذ الإجراء...';
            const subtext = trigger.getAttribute('data-loading-subtext') || 'يرجى الانتظار لحظات...';
            window.showLoadingWave(text, subtext);
            setTimeout(window.hideLoadingWave, 10000);
        }
    });
})();
</script>
