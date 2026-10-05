@extends('layouts.app')

@section('title', 'إعدادات المساعد الذكي والـ API')
@section('page-title', 'إعدادات المساعد الذكي')

@section('content')
<div class="container-fluid pb-5">

    <!-- الشريط العلوي المعتمد -->
    <x-page-hero
        title="إعدادات المساعد الذكي ومفاتيح الـ API"
        subtitle="إدارة وتكوين مفاتيح API لنماذج الذكاء الاصطناعي (Google Gemini و Groq LLaMA) وفحص الاتصال الحي بالنماذج"
        icon="fas fa-robot"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم المدير', 'url' => route('admin.dashboard')],
            ['label' => 'إعدادات المساعد الذكي']
        ]"
        secondaryBadge="إعدادات المنظومة"
        secondaryBadgeIcon="fas fa-sliders-h"
    >
        <a href="{{ route('admin.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right fs-6"></i>
            <span>العودة للوحة التحكم</span>
        </a>
    </x-page-hero>

    <!-- بطاقات المؤشرات العلوية (Bento Stats) -->
    <div class="row g-3 mb-4">
        <!-- الحالة التشغيلية الحالية -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-light opacity-75 small fw-bold">المزود النشط حالياً</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.1); width: 36px; height: 36px;">
                        <i class="fas fa-brain text-warning"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h4 class="fw-bold mb-0 text-white">
                        @if($provider === 'auto')
                            تلقائي (Auto)
                        @elseif($provider === 'gemini')
                            Google Gemini
                        @elseif($provider === 'groq')
                            Groq LLaMA
                        @else
                            المحرك المحلي
                        @endif
                    </h4>
                    <span class="badge {{ $stats['gemini_configured'] || $stats['groq_configured'] ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill" style="font-size: 0.7rem;">
                        {{ $stats['gemini_configured'] || $stats['groq_configured'] ? 'مفعل بـ API' : 'محلي فقط' }}
                    </span>
                </div>
                <small class="text-light opacity-75 mt-2 d-block">
                    @if($stats['gemini_configured'] && $stats['groq_configured'])
                        كلا المفتاحين متاحين مع خاصية التبديل التلقائي
                    @elseif($stats['gemini_configured'])
                        متصل بمحرك Google Gemini Flash
                    @elseif($stats['groq_configured'])
                        متصل بمحرك Groq LLaMA 3.3
                    @else
                        يعمل بالنظام الذكي الداخلي لحين إضافة مفتاح
                    @endif
                </small>
            </div>
        </div>

        <!-- Google Gemini Status -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">Google Gemini API</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fab fa-google"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">{{ $geminiModel }}</h5>
                    @if($stats['gemini_configured'])
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill" style="font-size: 0.72rem;">
                            <i class="fas fa-check-circle me-1"></i>جاهز
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill" style="font-size: 0.72rem;">غير مدخل</span>
                    @endif
                </div>
                <small class="text-muted mt-2 d-block">
                    {{ $stats['gemini_configured'] ? 'المفتاح محفوظ ويدعم استدعاء الدوال' : 'أدخل المفتاح بالأسفل لتفعيل نماذج Gemini' }}
                </small>
            </div>
        </div>

        <!-- Groq LLaMA Status -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">Groq Cloud (LLaMA 3.3)</span>
                    <div class="rounded-circle bg-warning bg-opacity-15 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-bolt"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark">{{ Str::limit($groqModel, 18) }}</h5>
                    @if($stats['groq_configured'])
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill" style="font-size: 0.72rem;">
                            <i class="fas fa-check-circle me-1"></i>جاهز
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill" style="font-size: 0.72rem;">غير مدخل</span>
                    @endif
                </div>
                <small class="text-muted mt-2 d-block">
                    {{ $stats['groq_configured'] ? 'استجابة فائقة السرعة (~300 رمز/ثانية)' : 'أدخل المفتاح بالأسفل لتفعيل نماذج LLaMA' }}
                </small>
            </div>
        </div>

        <!-- إحصائيات التفاعل -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">الأدوات والعمليات المؤكدة</span>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-3">
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['tools_count'] }} <span class="fs-6 fw-normal text-muted">أداة</span></h4>
                    <span class="text-muted small">| {{ $stats['actions_confirmed'] }} عملية معتمدة</span>
                </div>
                <small class="text-muted mt-2 d-block">
                    إجمالي محادثات النظام: {{ number_format($stats['total_chats']) }} رسالة
                </small>
            </div>
        </div>
    </div>

    <!-- نموذج حفظ الإعدادات الرئيسي -->
    <form action="{{ route('admin.settings.ai.update') }}" method="POST" id="aiSettingsForm">
        @csrf

        <div class="row g-4">
            <!-- العمود الأيمن (الإعدادات الرئيسية والمفاتيح) -->
            <div class="col-12 col-lg-8">

                <!-- 1. مزود الخدمة الافتراضي -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                1
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">مزود الذكاء الاصطناعي الأساسي (AI Provider)</h5>
                        </div>
                        <p class="text-muted small mt-1 mb-0 pe-4">
                            حدد كيف يتعامل النظام مع طلبات المستخدمين والمحادثة الذكية
                        </p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- خيار تلقائي -->
                            <div class="col-12 col-md-6">
                                <label class="provider-card p-3 border rounded-3 d-flex align-items-start gap-3 w-100 cursor-pointer h-100 position-relative {{ $provider === 'auto' ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}">
                                    <input type="radio" name="ai_provider" value="auto" class="form-check-input mt-1" {{ $provider === 'auto' ? 'checked' : '' }}>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-dark">تلقائي ذكي (Auto)</strong>
                                            <span class="badge bg-primary rounded-pill" style="font-size: 0.68rem;">موصى به</span>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            يبدأ بـ Groq لسرعته، ثم Gemini، مع التحول التلقائي للمحرك الداخلي عند انقطاع الإنترنت أو نفاد الرصيد.
                                        </small>
                                    </div>
                                </label>
                            </div>

                            <!-- خيار Gemini -->
                            <div class="col-12 col-md-6">
                                <label class="provider-card p-3 border rounded-3 d-flex align-items-start gap-3 w-100 cursor-pointer h-100 position-relative {{ $provider === 'gemini' ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}">
                                    <input type="radio" name="ai_provider" value="gemini" class="form-check-input mt-1" {{ $provider === 'gemini' ? 'checked' : '' }}>
                                    <div>
                                        <strong class="text-dark">Google Gemini فقط</strong>
                                        <small class="text-muted d-block mt-1">
                                            الاعتماد الدائم والمباشر على Google AI Studio عبر مفتاح Gemini المخصص.
                                        </small>
                                    </div>
                                </label>
                            </div>

                            <!-- خيار Groq -->
                            <div class="col-12 col-md-6">
                                <label class="provider-card p-3 border rounded-3 d-flex align-items-start gap-3 w-100 cursor-pointer h-100 position-relative {{ $provider === 'groq' ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}">
                                    <input type="radio" name="ai_provider" value="groq" class="form-check-input mt-1" {{ $provider === 'groq' ? 'checked' : '' }}>
                                    <div>
                                        <strong class="text-dark">Groq Cloud (LLaMA) فقط</strong>
                                        <small class="text-muted d-block mt-1">
                                            الاعتماد الدائم على خوادم Groq الفائقة السرعة ونموذج Meta LLaMA 3.3.
                                        </small>
                                    </div>
                                </label>
                            </div>

                            <!-- خيار محلي -->
                            <div class="col-12 col-md-6">
                                <label class="provider-card p-3 border rounded-3 d-flex align-items-start gap-3 w-100 cursor-pointer h-100 position-relative {{ $provider === 'local' ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}">
                                    <input type="radio" name="ai_provider" value="local" class="form-check-input mt-1" {{ $provider === 'local' ? 'checked' : '' }}>
                                    <div>
                                        <strong class="text-dark">المحرك الداخلي المحلي (Offline)</strong>
                                        <small class="text-muted d-block mt-1">
                                            تنفيذ الإجراءات والاستعلامات بناءً على محرك القواعد الداخلي دون استدعاء أي سيرفرات خارجية.
                                        </small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. بطاقة Google Gemini API Key -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                2
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">مفتاح Google Gemini API</h5>
                                <small class="text-muted">يدعم استدعاء الدوال الحية (Function Calling) والتفاعل مع قاعدة بيانات المنظومة</small>
                            </div>
                        </div>
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-bold">
                            <i class="fas fa-external-link-alt me-1"></i> الحصول على مفتاح مجاني
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-key text-warning me-1"></i> مفتاح الـ API (API Key)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fab fa-google text-muted"></i>
                                </span>
                                <input type="password" 
                                       id="gemini_api_key" 
                                       name="gemini_api_key" 
                                       class="form-control font-monospace border-start-0 border-end-0" 
                                       placeholder="{{ $stats['gemini_configured'] ? 'المفتاح محفوظ حالياً: ' . $maskedGeminiKey : 'أدخل مفتاح Google Gemini هنا (يبدأ بـ AIza أو AQ...)' }}" 
                                       value="">
                                <button type="button" class="btn btn-light border border-start-0" onclick="togglePasswordVisibility('gemini_api_key', this)" title="إظهار/إخفاء">
                                    <i class="fas fa-eye text-muted"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success px-3 fw-bold" id="btnTestGemini" onclick="testConnection('gemini')">
                                    <i class="fas fa-plug me-1"></i> فحص الاتصال
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                <small class="text-muted">
                                    @if($stats['gemini_configured'])
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>المفتاح مسجل ونشط: <code>{{ $maskedGeminiKey }}</code></span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>لا يوجد مفتاح مسجل لـ Gemini حالياً</span>
                                    @endif
                                </small>
                                @if($stats['gemini_configured'])
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="clear_gemini_key" value="1" id="clear_gemini_key">
                                        <label class="form-check-label text-danger small" for="clear_gemini_key">
                                            حذف المفتاح المحفوظ
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <!-- منطقة عرض نتائج الفحص الحي لـ Gemini -->
                            <div id="geminiTestFeedback" class="mt-2 d-none"></div>
                        </div>

                        <!-- اختيار نموذج Gemini -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-microchip text-primary me-1"></i> نموذج Gemini المعتمد (Model)
                            </label>
                            <select name="gemini_model" id="gemini_model" class="form-select">
                                <option value="gemini-3.6-flash" {{ $geminiModel === 'gemini-3.6-flash' ? 'selected' : '' }}>
                                    gemini-3.6-flash (موصى به للمفاتيح الحديثة AQ - فائق السرعة مع استدعاء الأدوات)
                                </option>
                                <option value="gemini-2.5-pro" {{ $geminiModel === 'gemini-2.5-pro' ? 'selected' : '' }}>
                                    gemini-2.5-pro (الأكثر ذكاءً وتحليلاً للمهام الأكاديمية المعقدة)
                                </option>
                                <option value="gemini-2.5-flash" {{ $geminiModel === 'gemini-2.5-flash' ? 'selected' : '' }}>
                                    gemini-2.5-flash (توليد سريع واقتصادي)
                                </option>
                                <option value="gemini-1.5-flash" {{ $geminiModel === 'gemini-1.5-flash' ? 'selected' : '' }}>
                                    gemini-1.5-flash (الجيل السابق)
                                </option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                ملاحظة: تم ضبط <code>gemini-3.6-flash</code> كخيار افتراضي لأنه يدعم المفاتيح الجديدة بنسبة نجاح 100%.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 3. بطاقة Groq Cloud API Key -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                3
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">مفتاح Groq Cloud API (LLaMA 3.3)</h5>
                                <small class="text-muted">أسرع محرك ذكاء اصطناعي استدلالي في العالم (Meta LLaMA 3.3 70B)</small>
                            </div>
                        </div>
                        <a href="https://console.groq.com/keys" target="_blank" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 py-1 fw-bold">
                            <i class="fas fa-external-link-alt me-1"></i> الحصول على مفتاح Groq مجاني
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-key text-warning me-1"></i> مفتاح Groq API Key
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-bolt text-warning"></i>
                                </span>
                                <input type="password" 
                                       id="groq_api_key" 
                                       name="groq_api_key" 
                                       class="form-control font-monospace border-start-0 border-end-0" 
                                       placeholder="{{ $stats['groq_configured'] ? 'المفتاح محفوظ حالياً: ' . $maskedGroqKey : 'أدخل مفتاح Groq هنا (يبدأ بـ gsk_...)' }}" 
                                       value="">
                                <button type="button" class="btn btn-light border border-start-0" onclick="togglePasswordVisibility('groq_api_key', this)" title="إظهار/إخفاء">
                                    <i class="fas fa-eye text-muted"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success px-3 fw-bold" id="btnTestGroq" onclick="testConnection('groq')">
                                    <i class="fas fa-plug me-1"></i> فحص الاتصال
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                <small class="text-muted">
                                    @if($stats['groq_configured'])
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>المفتاح مسجل ونشط: <code>{{ $maskedGroqKey }}</code></span>
                                    @else
                                        <span class="text-muted"><i class="fas fa-info-circle me-1"></i>لم يتم تسجيل مفتاح لـ Groq بعد (اختياري)</span>
                                    @endif
                                </small>
                                @if($stats['groq_configured'])
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="clear_groq_key" value="1" id="clear_groq_key">
                                        <label class="form-check-label text-danger small" for="clear_groq_key">
                                            حذف المفتاح المحفوظ
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <!-- منطقة عرض نتائج الفحص الحي لـ Groq -->
                            <div id="groqTestFeedback" class="mt-2 d-none"></div>
                        </div>

                        <!-- اختيار نموذج Groq -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-microchip text-warning me-1"></i> نموذج الذكاء الاصطناعي المعتمد في Groq
                            </label>
                            <select name="groq_model" id="groq_model" class="form-select">
                                <option value="llama-3.3-70b-versatile" {{ $groqModel === 'llama-3.3-70b-versatile' ? 'selected' : '' }}>
                                    llama-3.3-70b-versatile (Meta LLaMA 3.3 70B - متعدد القدرات)
                                </option>
                                <option value="llama-3.1-8b-instant" {{ $groqModel === 'llama-3.1-8b-instant' ? 'selected' : '' }}>
                                    llama-3.1-8b-instant (Meta LLaMA 3.1 8B - فائق السرعة والخفة)
                                </option>
                                <option value="openai/gpt-oss-120b" {{ $groqModel === 'openai/gpt-oss-120b' ? 'selected' : '' }}>
                                    openai/gpt-oss-120b (MoE عالي التفكير والاستدلال)
                                </option>
                                <option value="openai/gpt-oss-20b" {{ $groqModel === 'openai/gpt-oss-20b' ? 'selected' : '' }}>
                                    openai/gpt-oss-20b (سريع وذكي جداً في المهام المتعددة)
                                </option>
                                <option value="mixtral-8x7b-32768" {{ $groqModel === 'mixtral-8x7b-32768' ? 'selected' : '' }}>
                                    mixtral-8x7b-32768 (سياق ضخم 32k)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 4. بطاقة كاشف الروبوتات والحماية (Cloudflare Turnstile) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                4
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">كاشف الروبوتات الذكي (Cloudflare Turnstile)</h5>
                                <small class="text-muted">حماية ذكية بدون كابتشا معقدة لتأمين تسجيل الدخول والنماذج العامة</small>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="turnstile_enabled" name="turnstile_enabled" value="1" {{ $turnstileEnabled ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.3em;">
                            <label class="form-check-label fw-bold text-dark small ms-1" for="turnstile_enabled">
                                {{ $turnstileEnabled ? 'مفعل' : 'معطل' }}
                            </label>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Site Key -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-globe text-primary me-1"></i> مفتاح الموقع العام (Site Key)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-key text-muted"></i>
                                </span>
                                <input type="text"
                                       id="turnstile_site_key"
                                       name="turnstile_site_key"
                                       class="form-control font-monospace border-start-0"
                                       placeholder="أدخل مفتاح الموقع (Site Key)"
                                       value="{{ $turnstileSiteKey }}">
                            </div>
                            <small class="text-muted mt-1 d-block">
                                المفتاح العام للموقع المستخدم في صفحات تسجيل الدخول والتسجيل.
                            </small>
                        </div>

                        <!-- Secret Key -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-user-secret text-danger me-1"></i> المفتاح السري (Secret Key)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password"
                                       id="turnstile_secret_key"
                                       name="turnstile_secret_key"
                                       class="form-control font-monospace border-start-0 border-end-0"
                                       placeholder="{{ $stats['turnstile_configured'] ? 'المفتاح السري محفوظ حالياً: ' . $maskedTurnstileSecretKey : 'أدخل المفتاح السري هنا' }}"
                                       value="">
                                <button type="button" class="btn btn-light border border-start-0" onclick="togglePasswordVisibility('turnstile_secret_key', this)" title="إظهار/إخفاء">
                                    <i class="fas fa-eye text-muted"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info px-3 fw-bold" id="btnTestTurnstile" onclick="testConnection('turnstile')">
                                    <i class="fas fa-shield-alt me-1"></i> فحص المفتاح
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                <small class="text-muted">
                                    @if($stats['turnstile_configured'])
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>المفتاح السري مسجل ومحمي: <code>{{ $maskedTurnstileSecretKey }}</code></span>
                                    @else
                                        <span class="text-muted"><i class="fas fa-info-circle me-1"></i>لم يتم تسجيل المفتاح السري الحقيقي بعد</span>
                                    @endif
                                </small>
                            </div>
                            <!-- منطقة عرض نتائج فحص Cloudflare -->
                            <div id="turnstileTestFeedback" class="mt-2 d-none"></div>
                        </div>
                    </div>
                </div>

                <!-- زر الحفظ النهائي -->
                <div class="d-flex align-items-center justify-content-between p-3 bg-white border-0 rounded-4 shadow-sm">
                    <div class="text-muted small">
                        <i class="fas fa-shield-alt text-primary me-1"></i>
                        يتم حفظ وتشفير المفاتيح مباشرة في ملف البيئة الداخلي <code>.env</code>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold d-flex align-items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i>
                        <span>حفظ الإعدادات وتطبيق التغييرات</span>
                    </button>
                </div>
            </div>

            <!-- العمود الأيسر (معايير التوليد وقائمة الأدوات الذكية) -->
            <div class="col-12 col-lg-4">

                <!-- معايير التوليد المتقدمة -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-sliders-h text-primary me-2"></i>معايير التوليد والتحكم
                        </h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <!-- Temperature -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">درجة الدقة / الإبداع (Temperature)</label>
                                <span class="badge bg-light text-dark border font-monospace" id="tempValueDisplay">{{ $temperature }}</span>
                            </div>
                            <input type="range" class="form-range" min="0" max="1" step="0.05" id="ai_temperature" name="ai_temperature" value="{{ $temperature }}" oninput="document.getElementById('tempValueDisplay').innerText = this.value">
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                <span>0.0 (دقة صارمة وأرقام محددة)</span>
                                <span>1.0 (إبداعي)</span>
                            </div>
                        </div>

                        <!-- Max Tokens -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">أقصى حد لطول الرد (Max Output Tokens)</label>
                            <select name="ai_max_tokens" class="form-select form-select-sm">
                                <option value="1024" {{ $maxTokens == 1024 ? 'selected' : '' }}>1024 رمز (~750 كلمة)</option>
                                <option value="2048" {{ $maxTokens == 2048 ? 'selected' : '' }}>2048 رمز (~1500 كلمة - موصى به)</option>
                                <option value="4096" {{ $maxTokens == 4096 ? 'selected' : '' }}>4096 رمز (~3000 كلمة للتقارير الطويلة)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- الأدوات البرمجية المفعلة (Capabilities) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-magic text-warning me-2"></i>صلاحيات وأدوات الوكيل (Tools)
                        </h6>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5">
                            {{ $stats['tools_count'] }} أداة نشطة
                        </span>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <p class="text-muted small mb-3">
                            يمتلك المساعد الذكي صلاحية تنفيذ الإجراءات التالية بأمر مباشر من المستخدم بعد تأكيد المدير:
                        </p>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2 text-dark small">
                                <i class="fas fa-user-graduate text-primary"></i>
                                <span>البحث المتقدم في الخريجين والسير الذاتية</span>
                            </div>
                            <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2 text-dark small">
                                <i class="fas fa-briefcase text-success"></i>
                                <span>صياغة ونشر وإدارة فرص العمل</span>
                            </div>
                            <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2 text-dark small">
                                <i class="fas fa-handshake text-warning"></i>
                                <span>ترشيح الخريجين الفردي والجماعي للشركات</span>
                            </div>
                            <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2 text-dark small">
                                <i class="fas fa-chalkboard-teacher text-info"></i>
                                <span>إدارة وقبول طلبات الالتحاق بالتدريب</span>
                            </div>
                            <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2 text-dark small">
                                <i class="fas fa-newspaper text-danger"></i>
                                <span>صياغة الأخبار والبيانات الصحفية كوحدة إعلام</span>
                            </div>
                            <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2 text-dark small">
                                <i class="fas fa-chart-line text-secondary"></i>
                                <span>توليد التقارير التنفيذية ومؤشرات الأداء</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إرشادات سريعة للحصول على المفاتيح -->
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);">
                    <h6 class="fw-bold mb-2 text-white">
                        <i class="fas fa-lightbulb text-warning me-2"></i>كيف تحصل على مفتاح API؟
                    </h6>
                    <ol class="small ps-3 mb-0 text-white text-opacity-90" style="line-height: 1.7;">
                        <li>ادخل إلى <strong>Google AI Studio</strong> بحساب Google العادي الخاص بك.</li>
                        <li>اضغط على <strong>Create API Key</strong> وانسخ المفتاح الذي يبدأ بـ <code>AQ...</code> أو <code>AIza...</code>.</li>
                        <li>ألصق المفتاح في الحقل أعلاه واضغط على <strong>فحص الاتصال</strong> للتأكد من جاهزيته.</li>
                        <li>الخدمة مجانية تماماً للمشاريع التعليمية والجامعية!</li>
                    </ol>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- كود جافاسكريبت التفاعلي لفحص الاتصال الحي وإظهار/إخفاء المفاتيح -->
<script>
    // إظهار أو إخفاء نص كلمة المرور / المفتاح
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // فحص الاتصال الحي بنموذج الذكاء الاصطناعي أو Cloudflare
    function testConnection(provider) {
        let btn, feedback, keyInput, modelSelect;

        if (provider === 'gemini') {
            btn = document.getElementById('btnTestGemini');
            feedback = document.getElementById('geminiTestFeedback');
            keyInput = document.getElementById('gemini_api_key');
            modelSelect = document.getElementById('gemini_model');
        } else if (provider === 'groq') {
            btn = document.getElementById('btnTestGroq');
            feedback = document.getElementById('groqTestFeedback');
            keyInput = document.getElementById('groq_api_key');
            modelSelect = document.getElementById('groq_model');
        } else if (provider === 'turnstile') {
            btn = document.getElementById('btnTestTurnstile');
            feedback = document.getElementById('turnstileTestFeedback');
            keyInput = document.getElementById('turnstile_secret_key');
            modelSelect = { value: 'turnstile' };
        }

        const originalBtnHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> جارِ الاتصال...';

        feedback.classList.remove('d-none');
        feedback.innerHTML = `
            <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-0">
                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                <span>جارِ إرسال طلب تجريبي للتحقق من الاتصال والمفتاح...</span>
            </div>
        `;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('admin.settings.ai.test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                provider: provider,
                api_key: keyInput.value,
                model: modelSelect.value
            })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;

            if (result.body.success) {
                // تحديث النموذج المختار تلقائياً إذا اقترح الخادم نموذجاً متاحاً في الحساب
                if (result.body.suggested_model && modelSelect.value !== result.body.suggested_model) {
                    let exists = false;
                    for (let i = 0; i < modelSelect.options.length; i++) {
                        if (modelSelect.options[i].value === result.body.suggested_model) {
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        let opt = document.createElement('option');
                        opt.value = result.body.suggested_model;
                        opt.textContent = result.body.suggested_model + ' (متوفر ونشط في حسابك)';
                        modelSelect.appendChild(opt);
                    }
                    modelSelect.value = result.body.suggested_model;
                }

                feedback.innerHTML = `
                    <div class="alert alert-success py-2.5 px-3 small d-flex align-items-start gap-2 mb-0 rounded-3 shadow-sm border border-success border-opacity-25">
                        <i class="fas fa-check-circle text-success fs-5 mt-0.5"></i>
                        <div>
                            <strong class="d-block text-success fs-6">${result.body.message}</strong>
                            <div class="text-muted mt-1">
                                <span>المزود: <strong>${result.body.provider}</strong></span> | 
                                <span>النموذج النشط: <strong class="badge bg-success bg-opacity-10 text-success border border-success">${result.body.model}</strong></span> | 
                                <span>زمن الاستجابة: <strong>${result.body.latency_ms} ms</strong></span>
                            </div>
                            ${keyInput.value ? `
                                <div class="mt-2 p-2 bg-light rounded border border-warning border-opacity-50 text-dark small">
                                    <i class="fas fa-hand-point-down text-warning me-1"></i>
                                    <strong>تنبيه هام:</strong> تم اختبار المفتاح بنجاح! لحفظه بشكل دائم في المنظومة، يرجى النزول لأسفل والضغط على زر <strong>"حفظ الإعدادات وتطبيق التغييرات"</strong>.
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                feedback.innerHTML = `
                    <div class="alert alert-danger py-2.5 px-3 small d-flex align-items-start gap-2 mb-0 rounded-3 shadow-sm border border-danger border-opacity-25">
                        <i class="fas fa-times-circle text-danger fs-5 mt-0.5"></i>
                        <div>
                            <strong class="d-block text-danger fs-6">${result.body.message || 'فشل الاتصال بالمزود.'}</strong>
                            <small class="text-muted mt-1 d-block">يرجى التأكد من كتابة المفتاح بصورة صحيحة وعدم وجود مسافات فارغة قبله أو بعده.</small>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
            feedback.innerHTML = `
                <div class="alert alert-danger py-2.5 px-3 small d-flex align-items-center gap-2 mb-0">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <span>حدث خطأ في الاتصال بالخادم: ${error.message}</span>
                </div>
            `;
        });
    }

    // تأثير اختياري على بطاقات المزود
    document.querySelectorAll('.provider-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.provider-card').forEach(c => {
                c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
                c.classList.add('bg-light');
            });
            if (this.checked) {
                const parent = this.closest('.provider-card');
                parent.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
                parent.classList.remove('bg-light');
            }
        });
    });
</script>
@endsection
