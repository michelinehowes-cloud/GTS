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

    <!-- بطاقة دعوة لمعرض التوظيف (إن وجد) -->
    @php
        $upcomingFair = \App\Models\JobFair::where('status', 'published')
                            ->where('registration_open', true)
                            ->where('event_date', '>=', now()->startOfDay())
                            ->orderBy('event_date', 'asc')
                            ->first();
        
        $isRegistered = false;
        if ($upcomingFair) {
            $isRegistered = \App\Models\JobFairRegistration::where('user_id', auth()->id())
                            ->where('job_fair_id', $upcomingFair->id)
                            ->exists();
        }
    @endphp

    @if($upcomingFair && !$isRegistered)
        <div class="card-modern mb-4 p-4 border-start border-4 border-primary">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1 fw-bold mb-2">
                        <i class="fas fa-star me-1"></i> حدث قادم
                    </span>
                    <h4 class="fw-bold text-dark mb-2">{{ $upcomingFair->title }}</h4>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <div><i class="fas fa-calendar-alt me-1 text-primary"></i> {{ \Carbon\Carbon::parse($upcomingFair->event_date)->format('Y-m-d') }}</div>
                        <div><i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $upcomingFair->location }}</div>
                    </div>
                </div>
                <div class="text-md-end">
                    <a href="{{ route('job-fair.public') }}" class="btn btn-primary-modern px-4 py-2">
                        سجّل الآن في المعرض <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                    <div class="text-muted small mt-1">حضور المعرض يتيح لك التواصل المباشر مع الشركات</div>
                </div>
            </div>
        </div>
    @endif

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
                <div class="col-md-6 col-lg-4">
                    <div class="card-modern h-100 d-flex flex-column">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-start">
                            <h5 class="fw-bold text-dark mb-0 lh-base" style="font-size: 1.05rem;">{{ $job->title }}</h5>
                            @if(in_array($job->id, $myNominations))
                                <span class="badge bg-light text-success border border-success rounded-pill px-2 py-1 small flex-shrink-0 ms-2">
                                    <i class="fas fa-check me-1"></i> مُقدَّم
                                </span>
                            @endif
                        </div>
                        
                        <div class="card-body p-4 flex-grow-1">
                            @if($job->company)
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    <span class="fw-bold text-dark">{{ $job->company->name }}</span>
                                </div>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $job->location ?? 'غير محدد' }}
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                                    <i class="fas fa-clock text-info me-1"></i>
                                    @if($job->contract_type == 'full_time')
                                        دوام كامل
                                    @elseif($job->contract_type == 'part_time')
                                        دوام جزئي
                                    @elseif($job->contract_type == 'contract')
                                        عقد
                                    @else
                                        عمل حر
                                    @endif
                                </span>
                            </div>

                            <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit($job->description, 130) }}
                            </p>
                        </div>

                        <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar-times text-danger me-1"></i>
                                ينتهي: {{ $job->application_deadline->format('Y-m-d') }}
                            </small>
                            <a href="{{ route('graduate.job-opportunities.show', $job->id) }}" class="btn btn-sm btn-outline-primary-modern">
                                <i class="fas fa-eye me-1"></i> عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
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
@endsection