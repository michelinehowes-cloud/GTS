@extends('layouts.app')

@section('title', 'فرص العمل والتدريب المتاحة')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="فرص العمل والتدريب المتاحة"
        subtitle="تصفح الشواغر والفرص الوظيفية المتاحة من الشركات الشريكة وقدم عليها مباشرة"
        icon="fas fa-briefcase"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'فرص العمل المتاحة']
        ]"
        :badge="isset($jobOpportunities) && $jobOpportunities->count() > 0 ? (method_exists($jobOpportunities, 'total') ? $jobOpportunities->total() : $jobOpportunities->count()) . ' فرصة شاغرة' : null"
        badgeIcon="fas fa-search"
    >
        <a href="{{ route('graduate.my-applications') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-clipboard-list fs-6"></i>
            <span>متابعة ترشيحاتي</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    <!-- البحث والتصفية -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-filter me-2"></i>تصفية والبحث في الوظائف
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('graduate.job-opportunities.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-modern">البحث بالكلمة المفتاحية</label>
                    <input type="text" name="search" class="form-control-modern" placeholder="ابحث عن مسمى وظيفي أو مهارة..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern">نوع الفرصة</label>
                    <select name="contract_type" class="form-select-modern">
                        <option value="">جميع الأنواع</option>
                        <option value="full_time" {{ request('contract_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                        <option value="part_time" {{ request('contract_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                        <option value="contract" {{ request('contract_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                        <option value="freelance" {{ request('contract_type') == 'freelance' ? 'selected' : '' }}>عمل حر</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern">المدينة أو الموقع</label>
                    <input type="text" name="location" class="form-control-modern" placeholder="طرابلس، بنغازي..." value="{{ request('location') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-modern w-100 py-2">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($jobOpportunities->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($jobOpportunities as $job)
                @php
                    $isNominated = in_array($job->id, $myNominations);
                    $contractLabel = match($job->contract_type) {
                        'full_time' => 'دوام كامل',
                        'part_time' => 'دوام جزئي',
                        'contract' => 'عقد عمل',
                        'freelance' => 'عمل حر',
                        default => 'فرصة توظيف',
                    };
                    $companyName = $job->company->name ?? 'مؤسسة شريكة';
                @endphp
                <div class="col-12 col-md-6 col-lg-4 d-flex">
                    <div class="uiverse-train-card">
                        <div class="title-card">
                            <p title="{{ $companyName }}">
                                <i class="fas fa-building text-warning"></i>
                                <span>{{ Str::limit($companyName, 24) }}</span>
                            </p>
                            @if($isNominated)
                                <span class="badge rounded-pill bg-success text-white px-2.5 py-1" style="font-size: 0.72rem;">
                                    <i class="fas fa-check-circle me-1"></i> مُقدَّم
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.72rem;">
                                    <i class="fas fa-bullseye me-1"></i> تقديم متاح
                                </span>
                            @endif
                        </div>

                        <div class="card-content">
                            <span class="card-category">
                                <i class="fas fa-briefcase text-warning"></i>
                                <span>{{ $job->jobFair ? Str::limit($job->jobFair->title, 26) : 'فرصة وظيفية معتمدة' }}</span>
                            </span>
                            <h5 class="card-title-main" title="{{ $job->title }}">{{ $job->title }}</h5>
                            <div class="plain">
                                <span class="badge-contract">
                                    <i class="fas fa-clock text-primary me-1"></i> {{ $contractLabel }}
                                </span>
                                @if($job->seats)
                                    <span class="badge-seats">
                                        <i class="fas fa-users text-success me-1"></i> {{ $job->seats }} {{ $job->seats > 2 && $job->seats < 11 ? 'شواغر' : 'شاغر' }}
                                    </span>
                                @else
                                    <span class="badge-source">
                                        <i class="fas {{ $job->jobFair ? 'fa-handshake text-warning' : 'fa-check-circle text-info' }} me-1"></i>
                                        {{ $job->jobFair ? 'ملتقى التوظيف' : 'شراكات سوق العمل' }}
                                    </span>
                                @endif
                            </div>
                            <p class="description">
                                {{ Str::limit(strip_tags($job->description ?: 'فرصة عمل وتدريب وظيفي مميزة مقدمة من شركاء مكتب تدريب الخريجين.'), 115) }}
                            </p>
                            <div class="card-meta-pill">
                                <span><i class="fas fa-map-marker-alt text-info me-1"></i>
                                    {{ $job->location ?? 'طرابلس' }}</span>
                                <span><i class="fas fa-calendar-times text-warning me-1"></i>
                                    ينتهي: {{ $job->application_deadline ? \Carbon\Carbon::parse($job->application_deadline)->format('Y/m/d') : 'مستمر' }}</span>
                            </div>

                            <a href="{{ route('graduate.job-opportunities.show', $job->id) }}" class="card-btn">
                                <span>{{ $isNominated ? 'متابعة حالة ترشيحي' : 'تفاصيل الفرصة والتقديم' }}</span>
                                <i class="fas fa-arrow-left ms-1 small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $jobOpportunities->links() }}
        </div>
    @else
        <div class="card-modern text-center py-5">
            <div class="card-body py-5">
                <i class="fas fa-briefcase display-3 text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">لا توجد فرص عمل متاحة حالياً</h4>
                <p class="text-muted mb-0">تحقق لاحقاً أو قم بتعديل معايير البحث للحصول على نتائج أكثر</p>
            </div>
        </div>
    @endif
</div>

<style>
/* 🌟 بطاقات فرص العمل بنمط Uiverse المتناسقة مع الهوية الداخلية الفاتحة للمنصة 🌟 */
.uiverse-train-card {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg,
            #0d3882 0%,
            #1565c0 30%,
            #1e88e5 60%,
            #f59e0b 90%,
            #d97706 100%);
    padding: 3.5px;
    border-radius: 28px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px -6px rgba(13, 56, 130, 0.15), 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.uiverse-train-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px -8px rgba(13, 56, 130, 0.25), 0 8px 20px rgba(245, 158, 11, 0.2);
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
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    text-shadow: 0 2px 6px rgba(13, 56, 130, 0.6);
    display: inline-flex;
    align-items: center;
    gap: 7px;
}

.uiverse-train-card .title-card svg {
    color: #fbbf24;
    filter: drop-shadow(0 0 6px rgba(251, 191, 36, 0.8));
}

.uiverse-train-card .card-content {
    width: 100%;
    height: 100%;
    background: #ffffff;
    border-radius: 25px;
    color: #475569;
    font-size: 0.88rem;
    padding: 22px 20px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 14px;
    position: relative;
    flex-grow: 1;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.02);
}

.uiverse-train-card .card-content .card-category {
    font-size: 0.78rem;
    font-weight: 800;
    color: #0d3882;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: fit-content;
}

.uiverse-train-card .card-content .card-title-main {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
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
    align-items: center;
    gap: 8px;
    margin: 0;
    padding-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
    flex-wrap: wrap;
}

.uiverse-train-card .card-content .plain .badge-contract,
.uiverse-train-card .card-content .plain :nth-child(1) {
    font-size: 0.86rem;
    font-weight: 800;
    color: #0d3882;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    font-family: 'Tajawal', sans-serif;
    line-height: 1.3;
}

.uiverse-train-card .card-content .plain .badge-source,
.uiverse-train-card .card-content .plain :nth-child(2) {
    font-size: 0.82rem;
    font-weight: 700;
    color: #b45309;
    background: #fef3c7;
    border: 1px solid #fde68a;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    font-family: 'Tajawal', sans-serif;
    line-height: 1.3;
}

.uiverse-train-card .card-content .plain .badge-seats {
    font-size: 0.82rem;
    font-weight: 700;
    color: #065f46;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    font-family: 'Tajawal', sans-serif;
    line-height: 1.3;
}

.uiverse-train-card .card-content .description {
    font-size: 0.86rem;
    color: #64748b;
    line-height: 1.65;
    margin: 0;
    flex-grow: 1;
    min-height: 42px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.uiverse-train-card .card-meta-pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #334155;
    font-weight: 600;
}

.uiverse-train-card .card-btn {
    background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%) !important;
    border: 1.5px solid #0d3882 !important;
    color: #ffffff !important;
    font-weight: 800;
    font-size: 0.9rem;
    padding: 0.68rem 1.3rem;
    border-radius: 50px;
    transition: all 0.28s ease;
    box-shadow: 0 4px 14px rgba(13, 56, 130, 0.25);
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
    box-shadow: 0 8px 22px rgba(245, 158, 11, 0.4);
}

.uiverse-train-card .card-btn:hover i {
    color: #0b1f3a !important;
}

.uiverse-train-card .card-btn-icon {
    width: 44px;
    padding: 0.68rem 0;
    flex-shrink: 0;
}

/* دعم الوضع الليلي للمنصة إن تم تفعيله */
[data-theme="dark"] .uiverse-train-card .card-content,
.dark-mode .uiverse-train-card .card-content {
    background: #0f172a;
    color: #cbd5e1;
}
[data-theme="dark"] .uiverse-train-card .card-title-main,
.dark-mode .uiverse-train-card .card-title-main {
    color: #ffffff;
}
[data-theme="dark"] .uiverse-train-card .plain .badge-contract,
.dark-mode .uiverse-train-card .plain .badge-contract,
[data-theme="dark"] .uiverse-train-card .plain :nth-child(1),
.dark-mode .uiverse-train-card .plain :nth-child(1) {
    color: #93c5fd;
    background: rgba(30, 58, 138, 0.4);
    border-color: rgba(147, 197, 253, 0.3);
}
[data-theme="dark"] .uiverse-train-card .plain .badge-source,
.dark-mode .uiverse-train-card .plain .badge-source,
[data-theme="dark"] .uiverse-train-card .plain :nth-child(2),
.dark-mode .uiverse-train-card .plain :nth-child(2) {
    color: #fcd34d;
    background: rgba(180, 83, 9, 0.25);
    border-color: rgba(251, 191, 36, 0.3);
}
[data-theme="dark"] .uiverse-train-card .plain .badge-seats,
.dark-mode .uiverse-train-card .plain .badge-seats {
    color: #6ee7b7;
    background: rgba(6, 95, 70, 0.3);
    border-color: rgba(110, 231, 183, 0.3);
}
[data-theme="dark"] .uiverse-train-card .card-meta-pill,
.dark-mode .uiverse-train-card .card-meta-pill {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
}
</style>
@endsection