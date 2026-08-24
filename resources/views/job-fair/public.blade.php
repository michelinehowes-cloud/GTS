@extends('layouts.app')

@section('title', 'معرض التوظيف 2026 - جامعة طرابلس')

@push('styles')
<style>
    :root {
        --fair-gold:    #F59E0B;
        --fair-dark:    #0A1628;
        --fair-blue:    #1E3A5F;
        --fair-accent:  #3B82F6;
        --fair-green:   #10B981;
    }

    /* ===== Hero Section ===== */
    .fair-hero {
        background: linear-gradient(135deg, #0A1628 0%, #1E3A5F 50%, #0A2647 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .fair-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.15) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 20%, rgba(245,158,11,0.1) 0%, transparent 50%);
        pointer-events: none;
    }
    .floating-particles {
        position: absolute; inset: 0; overflow: hidden; pointer-events: none;
    }
    .particle {
        position: absolute;
        width: 4px; height: 4px;
        background: rgba(245,158,11,0.6);
        border-radius: 50%;
        animation: float-up linear infinite;
    }
    @keyframes float-up {
        0%   { transform: translateY(100vh) scale(0); opacity: 0; }
        10%  { opacity: 1; }
        90%  { opacity: 1; }
        100% { transform: translateY(-10vh) scale(1.5); opacity: 0; }
    }

    .fair-badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--fair-gold), #F97316);
        color: #fff;
        padding: 6px 20px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(245,158,11,0.4);
    }

    .fair-title {
        font-size: clamp(2.5rem, 6vw, 5rem);
        font-weight: 900;
        background: linear-gradient(135deg, #fff 30%, var(--fair-gold) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
        margin-bottom: 1rem;
    }
    .fair-subtitle {
        color: rgba(255,255,255,0.75);
        font-size: 1.2rem;
        margin-bottom: 2.5rem;
    }

    /* ===== Countdown ===== */
    .countdown-wrapper {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 2.5rem;
    }
    .countdown-item {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        text-align: center;
        min-width: 90px;
        transition: transform 0.3s;
    }
    .countdown-item:hover { transform: translateY(-4px); }
    .countdown-number {
        font-size: 2.8rem;
        font-weight: 900;
        color: var(--fair-gold);
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .countdown-label {
        color: rgba(255,255,255,0.6);
        font-size: 0.75rem;
        margin-top: 4px;
        font-weight: 500;
    }

    /* ===== Stats Bar ===== */
    .stats-bar {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
        padding: 1.2rem 2rem;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }
    .stat-item { text-align: center; }
    .stat-number {
        font-size: 2rem;
        font-weight: 900;
        color: var(--fair-gold);
    }
    .stat-label {
        color: rgba(255,255,255,0.65);
        font-size: 0.85rem;
    }

    /* ===== Registration Button ===== */
    .btn-register {
        background: linear-gradient(135deg, var(--fair-gold), #F97316);
        color: #fff;
        border: none;
        padding: 16px 48px;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 8px 30px rgba(245,158,11,0.4);
        text-decoration: none;
        display: inline-block;
    }
    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(245,158,11,0.6);
        color: #fff;
    }
    .btn-ticket {
        background: linear-gradient(135deg, var(--fair-green), #059669);
        color: #fff;
        border: none;
        padding: 14px 40px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        box-shadow: 0 6px 20px rgba(16,185,129,0.4);
    }
    .btn-ticket:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(16,185,129,0.6);
        color: #fff;
    }

    /* ===== Details Section ===== */
    .fair-details {
        background: #f8fafc;
        padding: 80px 0;
    }
    .detail-card {
        background: #fff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #e8f0fe;
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .detail-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }
    .detail-icon {
        width: 60px; height: 60px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 1rem;
    }

    /* ===== Companies Section ===== */
    .companies-section {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        padding: 80px 0;
    }
    .company-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid #e8f0fe;
        transition: all 0.3s;
    }
    .company-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-4px);
        border-color: var(--fair-accent);
    }
    .company-logo {
        width: 60px; height: 60px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e8f0fe, #dbeafe);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .booth-badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--fair-blue), var(--fair-accent));
        color: #fff;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    /* ===== Section Headers ===== */
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .section-header h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--fair-dark);
        margin-bottom: 0.5rem;
    }
    .section-header p {
        color: #64748b;
        font-size: 1.05rem;
    }
    .section-header .section-line {
        width: 60px; height: 4px;
        background: linear-gradient(90deg, var(--fair-gold), var(--fair-accent));
        border-radius: 2px;
        margin: 1rem auto 0;
    }

    /* ===== Registration Modal ===== */
    .modal-header-custom {
        background: linear-gradient(135deg, var(--fair-dark), var(--fair-blue));
        color: #fff;
        border-radius: 16px 16px 0 0;
    }

    /* ===== Alerts ===== */
    .registered-banner {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: 2px solid #10B981;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* No Fair Banner */
    .no-fair-banner {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0A1628 0%, #1E3A5F 100%);
        color: white;
        text-align: center;
    }
</style>
@endpush

@section('content')

@if($fair)
<!-- ===== HERO SECTION ===== -->
<section class="fair-hero">
    <!-- Particles -->
    <div class="floating-particles" id="particles"></div>

    <div class="container text-center text-white position-relative" style="z-index:2">
        <div class="fair-badge">
            <i class="fas fa-star ms-2"></i>
            معرض التوظيف السنوي
        </div>

        <h1 class="fair-title">{{ $fair->title }}</h1>

        @if($fair->subtitle)
            <p class="fair-subtitle">{{ $fair->subtitle }}</p>
        @endif

        <!-- Event Info -->
        <div class="d-flex justify-content-center gap-4 mb-4 flex-wrap" style="color: rgba(255,255,255,0.8)">
            <span><i class="fas fa-calendar-alt me-2" style="color: var(--fair-gold)"></i>{{ $fair->event_date->format('d/m/Y') }}</span>
            @if($fair->start_time)
            <span><i class="fas fa-clock me-2" style="color: var(--fair-gold)"></i>{{ $fair->start_time }} - {{ $fair->end_time }}</span>
            @endif
            <span><i class="fas fa-map-marker-alt me-2" style="color: var(--fair-gold)"></i>{{ $fair->location }}</span>
        </div>

        <!-- Countdown -->
        @if($fair->is_upcoming)
        <div class="countdown-wrapper" id="countdown-wrapper">
            <div class="countdown-item">
                <div class="countdown-number" id="cd-days">00</div>
                <div class="countdown-label">يوم</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="cd-hours">00</div>
                <div class="countdown-label">ساعة</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="cd-minutes">00</div>
                <div class="countdown-label">دقيقة</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number" id="cd-seconds">00</div>
                <div class="countdown-label">ثانية</div>
            </div>
        </div>
        @else
        <div class="mb-4">
            <span class="badge fs-5 py-2 px-4" style="background: rgba(16,185,129,0.3); border: 2px solid #10B981; color: #10B981">
                <i class="fas fa-circle-dot me-2"></i>المعرض يجري الآن
            </span>
        </div>
        @endif

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_registered'] ?? 0 }}</div>
                <div class="stat-label">خريج مسجل</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_companies'] ?? 0 }}</div>
                <div class="stat-label">شركة مشاركة</div>
            </div>
            @if(isset($stats['total_attended']) && $stats['total_attended'] > 0)
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_attended'] }}</div>
                <div class="stat-label">حاضر</div>
            </div>
            @endif
        </div>

        <!-- CTA Buttons -->
        @auth
            @if($myRegistration)
            <div class="registered-banner d-inline-flex text-dark" style="border-radius:50px; padding: 12px 28px">
                <i class="fas fa-check-circle fa-lg" style="color: #10B981"></i>
                <span class="fw-bold">أنت مسجل! رقم تسجيلك: <span style="color:#059669">{{ $myRegistration->registration_number }}</span></span>
                <a href="{{ route('job-fair.my-ticket', $myRegistration->id) }}" class="btn-ticket me-3">
                    <i class="fas fa-qrcode me-2"></i>عرض بطاقتي
                </a>
            </div>
            @elseif($fair->can_register)
            <button class="btn-register" data-bs-toggle="modal" data-bs-target="#registerModal">
                <i class="fas fa-user-plus ms-2"></i>سجّل الآن في المعرض
            </button>
            @else
            <span class="btn-register" style="opacity:0.5; cursor:not-allowed">
                <i class="fas fa-lock ms-2"></i>التسجيل مغلق
            </span>
            @endif
        @else
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('login') }}" class="btn-register">
                <i class="fas fa-sign-in-alt ms-2"></i>سجّل الدخول للتسجيل
            </a>
            <a href="{{ route('graduate.register') }}" class="btn-ticket">
                <i class="fas fa-user-plus ms-2"></i>تسجيل كخريج جديد
            </a>
        </div>
        @endauth

        <!-- Scroll Down -->
        <div class="mt-5" style="color: rgba(255,255,255,0.4)">
            <i class="fas fa-chevron-down fa-bounce"></i>
        </div>
    </div>
</section>

<!-- ===== DETAILS SECTION ===== -->
<section class="fair-details">
    <div class="container">
        <div class="section-header">
            <h2>عن المعرض</h2>
            <p>كل ما تحتاج معرفته عن معرض التوظيف 2026</p>
            <div class="section-line"></div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="detail-card">
                    <div class="detail-icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe)">
                        <i class="fas fa-calendar-check" style="color: #2563EB"></i>
                    </div>
                    <h5 class="fw-bold">التاريخ والوقت</h5>
                    <p class="text-muted mb-0">{{ $fair->event_date->format('l، d MMMM Y') }}</p>
                    @if($fair->start_time)
                    <p class="text-muted mb-0">من {{ $fair->start_time }} حتى {{ $fair->end_time }}</p>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-card">
                    <div class="detail-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a)">
                        <i class="fas fa-map-marker-alt" style="color: #D97706"></i>
                    </div>
                    <h5 class="fw-bold">المكان</h5>
                    <p class="text-muted mb-0">{{ $fair->location }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-card">
                    <div class="detail-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0)">
                        <i class="fas fa-users" style="color: #059669"></i>
                    </div>
                    <h5 class="fw-bold">التسجيل</h5>
                    @if($fair->can_register)
                    <p class="text-success mb-0 fw-bold"><i class="fas fa-circle me-1" style="font-size:0.6rem"></i>التسجيل مفتوح</p>
                    @if($fair->registration_deadline)
                    <small class="text-muted">ينتهي: {{ $fair->registration_deadline->format('d/m/Y') }}</small>
                    @endif
                    @else
                    <p class="text-danger mb-0 fw-bold"><i class="fas fa-times-circle me-1"></i>التسجيل مغلق</p>
                    @endif
                </div>
            </div>
        </div>

        @if($fair->description)
        <div class="p-4 rounded-4 mb-4" style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border-right: 4px solid #0284C7">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2" style="color: #0284C7"></i>تفاصيل المعرض</h5>
            <p class="mb-0 text-secondary" style="line-height: 1.8">{{ $fair->description }}</p>
        </div>
        @endif
    </div>
</section>

<!-- ===== COMPANIES SECTION ===== -->
@if($companies->count() > 0)
<section class="companies-section">
    <div class="container">
        <div class="section-header">
            <h2>الشركات المشاركة</h2>
            <p>{{ $companies->count() }} شركة ستشارك في معرض هذا العام</p>
            <div class="section-line"></div>
        </div>

        <div class="row g-3">
            @foreach($companies as $fc)
            <div class="col-md-6 col-lg-4">
                <div class="company-card">
                    <div class="company-logo">🏢</div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="fw-bold mb-1">{{ $fc->company->name }}</h6>
                            @if($fc->booth_number)
                            <span class="booth-badge">جناح {{ $fc->booth_number }}</span>
                            @endif
                        </div>
                        @if($fc->available_positions)
                        <small class="text-muted">
                            <i class="fas fa-briefcase me-1 text-primary"></i>{{ $fc->available_positions }} وظيفة متاحة
                        </small>
                        @endif
                        @if($fc->participating_sectors)
                        <div>
                            <small class="text-muted">{{ Str::limit($fc->participating_sectors, 50) }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ===== REGISTRATION MODAL ===== -->
@auth
@if(!$myRegistration && $fair->can_register)
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
            <div class="modal-header modal-header-custom border-0 p-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-user-plus me-2" style="color: var(--fair-gold)"></i>
                    التسجيل في معرض التوظيف 2026
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('job-fair.register', $fair->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        ستحصل على بطاقة دخول إلكترونية مع رمز QR فريد بعد التسجيل.
                    </div>

                    <!-- بيانات الخريج (للعرض فقط) -->
                    <div class="p-3 rounded-3 mb-3" style="background: #f8fafc">
                        <div class="row g-2 text-sm">
                            <div class="col-6">
                                <small class="text-muted d-block">الاسم</small>
                                <strong>{{ auth()->user()->name }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">التخصص</small>
                                <strong>{{ auth()->user()->major ?? 'غير محدد' }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">الكلية</small>
                                <strong>{{ auth()->user()->faculty ?? 'غير محدد' }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">سنة التخرج</small>
                                <strong>{{ auth()->user()->graduation_year ?? 'غير محدد' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">القطاعات التي تهمك <small class="text-muted">(اختياري)</small></label>
                        <textarea name="interests" class="form-control rounded-3" rows="2"
                            placeholder="مثال: تكنولوجيا المعلومات، هندسة، صحة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn-register py-2 px-4">
                        <i class="fas fa-check ms-2"></i>تأكيد التسجيل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endauth

@else
<!-- No Fair -->
<div class="no-fair-banner">
    <div>
        <div style="font-size: 5rem; margin-bottom: 1rem">🎪</div>
        <h2 class="fw-bold mb-3">لا يوجد معرض منشور حالياً</h2>
        <p style="color: rgba(255,255,255,0.6)">تابع الإعلانات للاطلاع على موعد معرض التوظيف القادم</p>
        <a href="{{ route('home') }}" class="btn-register mt-3">
            <i class="fas fa-home ms-2"></i>الصفحة الرئيسية
        </a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// ===== Countdown Timer =====
@if($fair && $fair->is_upcoming)
(function() {
    const eventDate = new Date("{{ $fair->event_date->format('Y-m-d') }}T{{ $fair->start_time ?? '09:00' }}:00");

    function updateCountdown() {
        const now = new Date();
        const diff = eventDate - now;

        if (diff <= 0) {
            document.getElementById('countdown-wrapper')?.remove();
            return;
        }

        const days    = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours   = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
        const minutes = Math.floor((diff % (1000*60*60)) / (1000*60));
        const seconds = Math.floor((diff % (1000*60)) / 1000);

        document.getElementById('cd-days').textContent    = String(days).padStart(2, '0');
        document.getElementById('cd-hours').textContent   = String(hours).padStart(2, '0');
        document.getElementById('cd-minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('cd-seconds').textContent = String(seconds).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();
@endif

// ===== Floating Particles =====
(function() {
    const container = document.getElementById('particles');
    if (!container) return;

    for (let i = 0; i < 20; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            animation-duration: ${8 + Math.random() * 12}s;
            animation-delay: ${-Math.random() * 15}s;
            width: ${2 + Math.random() * 4}px;
            height: ${2 + Math.random() * 4}px;
            opacity: ${0.3 + Math.random() * 0.5};
        `;
        container.appendChild(p);
    }
})();
</script>
@endpush
