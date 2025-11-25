@extends('layouts.app')

@section('title', 'فرص العمل المتاحة')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-briefcase text-primary"></i>
            فرص العمل المتاحة
        </h1>
    </div>

    <!-- البحث والتصفية -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('graduate.job-opportunities.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-control" placeholder="ابحث عن وظيفة..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع الوظيفة</label>
                    <select name="contract_type" class="form-select">
                        <option value="">الكل</option>
                        <option value="full_time" {{ request('contract_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                        <option value="part_time" {{ request('contract_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                        <option value="contract" {{ request('contract_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                        <option value="freelance" {{ request('contract_type') == 'freelance' ? 'selected' : '' }}>عمل حر</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الموقع</label>
                    <input type="text" name="location" class="form-control" placeholder="المدينة..." value="{{ request('location') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($jobOpportunities->count() > 0)
        <div class="row">
            @foreach($jobOpportunities as $job)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">{{ $job->title }}</h5>
                                @if(in_array($job->id, $myNominations))
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> مقدم
                                    </span>
                                @endif
                            </div>
                            
                            @if($job->company)
                                <p class="text-muted mb-2">
                                    <i class="fas fa-building"></i>
                                    {{ $job->company->name }}
                                </p>
                            @endif

                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $job->location }}
                            </p>

                            <p class="text-muted mb-2">
                                <i class="fas fa-clock"></i>
                                @if($job->contract_type == 'full_time')
                                    دوام كامل
                                @elseif($job->contract_type == 'part_time')
                                    دوام جزئي
                                @elseif($job->contract_type == 'contract')
                                    عقد
                                @else
                                    عمل حر
                                @endif
                            </p>

                            <p class="card-text text-truncate-3">
                                {{ Str::limit($job->description, 150) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    ينتهي: {{ $job->application_deadline->format('Y-m-d') }}
                                </small>
                                <a href="{{ route('graduate.job-opportunities.show', $job->id) }}" class="btn btn-sm btn-primary">
                                    عرض التفاصيل
                                </a>
                            </div>
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
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>لا توجد فرص عمل متاحة حالياً</h4>
            <p>تحقق لاحقاً للحصول على فرص جديدة</p>
        </div>
    @endif
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.text-truncate-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection