@extends('layouts.app')

@section('title', 'البرامج التدريبية المتاحة')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="البرامج والدورات التدريبية"
        subtitle="تصفح البرامج التدريبية المعتمدة وورش العمل لتطوير مهاراتك وقدم عليها مباشرة"
        icon="fas fa-graduation-cap"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'البرامج التدريبية']
        ]"
        :badge="isset($trainings) && $trainings->count() > 0 ? (method_exists($trainings, 'total') ? $trainings->total() : $trainings->count()) . ' برنامج تدريبي' : null"
        badgeIcon="fas fa-certificate"
    >
        <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>فرص العمل</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($trainings->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($trainings as $training)
                @php
                    $app = isset($myApplications) ? ($myApplications[$training->id] ?? null) : \App\Models\TrainingApplication::where('user_id', auth()->id())->where('training_id', $training->id)->first();
                    $typeLabel = match($training->type) {
                        'workshop' => 'ورشة عمل تدريبية',
                        'course' => 'دورة تدريبية متخصصة',
                        'seminar' => 'ندوة علمية',
                        'internship' => 'تدريب عملي ميداني',
                        default => 'برنامج تدريبي معتمد',
                    };
                    $durDigits = preg_replace('/[^0-9]/', '', $training->duration);
                    $durIsHours = Str::contains(strtolower($training->duration), 'hour') || Str::contains($training->duration, 'ساعة');
                @endphp
                <div class="col-12 col-md-6 col-lg-4 d-flex">
                    <div class="uiverse-train-card">
                        <div class="title-card">
                            <p>
                                <i class="fas fa-certificate text-warning"></i>
                                <span>{{ $typeLabel }}</span>
                            </p>
                            @if($app)
                                @if($app->status === 'approved')
                                    <span class="badge rounded-pill bg-success text-white px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-check-circle me-1"></i> تم القبول
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="badge rounded-pill bg-danger text-white px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-times-circle me-1"></i> تم الرفض
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-warning text-dark px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-clock me-1"></i> قيد المراجعة
                                    </span>
                                @endif
                            @else
                                <span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.72rem;">
                                    <i class="fas fa-bolt me-1"></i> متاح للتسجيل
                                </span>
                            @endif
                        </div>

                        <div class="card-content">
                            <span class="card-category">
                                <i class="fas fa-graduation-cap text-warning"></i>
                                <span>تأهيل الكفاءات • جامعة طرابلس</span>
                            </span>
                            <h5 class="card-title-main" title="{{ $training->title }}">{{ $training->title }}</h5>
                            <div class="plain">
                                <span>{{ $durDigits ?: ($training->duration ?? '30') }}</span>
                                <span>{{ $durIsHours ? 'ساعة تدريبية معتمدة' : ($durDigits ? 'أيام تدريبية مكثفة' : 'برنامج تدريبي متكامل') }}</span>
                            </div>
                            <p class="description">
                                {{ Str::limit(strip_tags($training->description ?: 'برنامج تدريبي معتمد لتطوير المهارات التخصصية وسد الفجوة مع متطلبات سوق العمل.'), 115) }}
                            </p>
                            <div class="card-meta-pill">
                                <span><i class="fas fa-calendar-alt text-warning me-1"></i>
                                    {{ $training->start_date ? \Carbon\Carbon::parse($training->start_date)->format('Y/m/d') : 'قريباً' }}</span>
                                <span><i class="fas fa-map-marker-alt text-info me-1"></i>
                                    {{ $training->location ?? 'جامعة طرابلس' }}</span>
                            </div>

                            @if($app)
                                <a href="{{ route('graduate.trainings.show', $training->id) }}" class="card-btn">
                                    <span>متابعة حالة الطلب</span>
                                    <i class="fas fa-arrow-left ms-1 small"></i>
                                </a>
                            @else
                                <div class="d-flex gap-2 w-100">
                                    <form action="{{ route('graduate.trainings.apply', $training->id) }}" method="POST" class="flex-grow-1 m-0">
                                        @csrf
                                        <input type="hidden" name="training_id" value="{{ $training->id }}">
                                        <button type="submit" class="card-btn">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            <span>تقديم طلب</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('graduate.trainings.show', $training->id) }}" class="card-btn card-btn-icon" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 rounded-4 shadow-sm text-center py-5 bg-white">
            <div class="card-body py-5">
                <i class="fas fa-graduation-cap display-4 text-muted mb-3 opacity-50"></i>
                <h5 class="fw-bold text-dark mb-2">لا توجد تدريبات متاحة حالياً</h5>
                <p class="text-muted small mb-0">يرجى مراجعة هذه الصفحة لاحقاً للاطلاع على الدورات الجديدة فور إطلاقها</p>
            </div>
        </div>
    @endif
</div>

<style>
/* 🌟 بطاقات التدريب بنمط Uiverse المتناسقة تماماً مع صفحة الترحيب 🌟 */
.uiverse-train-card {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg,
            #0d3882 0%,
            #1565c0 30%,
            #1e88e5 55%,
            #f59e0b 85%,
            #d97706 100%);
    padding: 3.5px;
    border-radius: 30px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 14px 34px -8px rgba(11, 31, 58, 0.18), 0 0 16px rgba(245, 158, 11, 0.12);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.uiverse-train-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 22px 48px -10px rgba(13, 56, 130, 0.28), 0 0 28px rgba(245, 158, 11, 0.35);
}

.uiverse-train-card .title-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px 10px;
    color: #ffffff;
}

.uiverse-train-card .title-card p {
    margin: 0;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 8px rgba(13, 56, 130, 0.8);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.uiverse-train-card .title-card svg {
    color: #fbbf24;
    filter: drop-shadow(0 0 6px rgba(251, 191, 36, 0.8));
}

.uiverse-train-card .card-content {
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, #091e3e 0%, #06152c 100%);
    border-radius: 27px;
    color: #94a3b8;
    font-size: 0.85rem;
    padding: 22px 20px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 14px;
    position: relative;
    flex-grow: 1;
}

.uiverse-train-card .card-content .card-category {
    font-size: 0.76rem;
    font-weight: 800;
    color: #38bdf8;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.3px;
}

.uiverse-train-card .card-content .card-title-main {
    font-size: 1.12rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.45;
    margin: 0;
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.uiverse-train-card .card-content .plain {
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin: 0;
    padding-bottom: 4px;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.12);
}

.uiverse-train-card .card-content .plain :nth-child(1) {
    font-size: 1.85rem;
    font-weight: 900;
    color: #ffffff;
    font-family: monospace;
    line-height: 1;
    text-shadow: 0 2px 10px rgba(245, 158, 11, 0.35);
}

.uiverse-train-card .card-content .plain :nth-child(2) {
    font-size: 0.84rem;
    font-weight: 700;
    color: #fbbf24;
}

.uiverse-train-card .card-content .description {
    font-size: 0.84rem;
    color: #94a3b8;
    line-height: 1.6;
    margin: 0;
    flex-grow: 1;
    min-height: 42px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.uiverse-train-card .card-meta-pill {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.78rem;
    color: #cbd5e1;
}

.uiverse-train-card .card-btn {
    background: rgba(245, 158, 11, 0.12) !important;
    border: 2px solid #f59e0b !important;
    color: #ffffff !important;
    font-weight: 800;
    font-size: 0.88rem;
    padding: 0.65rem 1.2rem;
    border-radius: 50px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    transition: all 0.28s ease;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2), inset 0 0 10px rgba(245, 158, 11, 0.08);
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    text-decoration: none;
    cursor: pointer;
}

.uiverse-train-card .card-btn i {
    color: #fbbf24 !important;
    transition: all 0.28s ease;
}

.uiverse-train-card .card-btn:hover {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    border-color: #f59e0b !important;
    color: #0b1f3a !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.45);
    text-shadow: none;
}

.uiverse-train-card .card-btn:hover i {
    color: #0b1f3a !important;
}

.uiverse-train-card .card-btn-icon {
    width: 44px;
    padding: 0.65rem 0;
    flex-shrink: 0;
}
</style>
@endsection