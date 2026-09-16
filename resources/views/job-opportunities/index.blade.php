@extends('layouts.app')

@section('title', 'إدارة فرص العمل والتدريب')

@push('styles')
<style>
    .job-mobile-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    .job-mobile-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }
    .sticky-actions-col {
        position: sticky;
        left: 0;
        background: #ffffff;
        z-index: 5;
        box-shadow: -3px 0 8px rgba(0, 0, 0, 0.04);
    }
    thead th.sticky-actions-col {
        background: #f8fafc !important;
        z-index: 6;
    }
    tr:hover td.sticky-actions-col {
        background: #f1f5f9;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="فرص العمل والتدريب"
        subtitle="{{ ($isCompany ?? false) ? 'إدارة واستعراض فرص العمل والتدريب الخاصة بشركتكم ومتابعة المتقدمين والمرشحين' : 'إدارة واستعراض جميع فرص العمل والبرامج التدريبية المتاحة للخريجين والشركات الشريكة' }}"
        icon="fas fa-briefcase"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'فرص العمل والتدريب']
        ]"
        badge="{{ ($isCompany ?? false) ? ($userCompany->name ?? 'لوحة إدارة الفرص') : 'إدارة التوظيف والفرص' }}"
    >
        <a href="{{ route('job-opportunities.create') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة فرصة جديدة</span>
        </a>
        @if(!($isCompany ?? false))
        <button class="btn btn-light bg-white text-success fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import"></i>
            <span>استيراد</span>
        </button>
        @endif
    </x-page-hero>

    <!-- بطاقات الإحصائيات (2x3 على الموبايل و6 على الديسكتوب) -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => ($isCompany ?? false) ? 'إجمالي فرَصنا' : 'إجمالي الفرص',
            'value' => $stats['total'] ?? (method_exists($opportunities, 'total') ? $opportunities->total() : $opportunities->count()),
            'icon' => 'fas fa-briefcase',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'مفتوحة للتقديم',
            'value' => $stats['open'] ?? $opportunities->where('status', 'open')->count(),
            'icon' => 'fas fa-door-open',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'وظائف شاغرة',
            'value' => $stats['jobs'] ?? $opportunities->where('type', 'job')->count(),
            'icon' => 'fas fa-user-tie',
            'color' => 'info'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'تدريب مهني (شركات)',
            'value' => $stats['trainings'] ?? $opportunities->where('type', 'training')->count(),
            'icon' => 'fas fa-graduation-cap',
            'color' => 'warning'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'تدريب تعاوني',
            'value' => $stats['internships'] ?? $opportunities->where('type', 'internship')->count(),
            'icon' => 'fas fa-laptop-code',
            'color' => 'secondary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => ($isCompany ?? false) ? 'إجمالي الترشيحات' : 'الترشيحات',
            'value' => ($isCompany ?? false) ? ($stats['nominations'] ?? $opportunities->sum('nominations_count')) : $opportunities->sum('nominations_count'),
            'icon' => 'fas fa-users',
            'color' => 'danger'
        ])
    </div>

    <!-- فلترة وتصفية البيانات -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 text-primary fw-bold fs-6">
                <i class="fas fa-filter me-2"></i>فلاتر وتصفية الفرص
            </h6>
            @if(request()->hasAny(['search', 'type', 'status', 'company_id']))
                <a href="{{ route('job-opportunities.index') }}" class="btn btn-sm btn-link text-danger p-0 text-decoration-none">
                    <i class="fas fa-times-circle me-1"></i>مسح الفلاتر
                </a>
            @endif
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('job-opportunities.index') }}" class="row g-2 g-md-3 align-items-end">
                <div class="{{ ($isCompany ?? false) ? 'col-12 col-md-4' : 'col-12 col-md-3' }}">
                    <label for="search" class="form-label-modern small fw-bold">بحث بالاسم أو المكان</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control form-control-sm border-start-0" placeholder="ابحث بعنوان الفرصة أو المكان..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="{{ ($isCompany ?? false) ? 'col-6 col-md-3' : 'col-6 col-md-2' }}">
                    <label for="type" class="form-label-modern small fw-bold">نوع الفرصة</label>
                    <select name="type" id="type" class="form-select form-select-sm">
                        <option value="">جميع الأنواع</option>
                        <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>وظيفة شاغرة</option>
                        <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>تدريب مهني بشركات</option>
                        <option value="internship" {{ request('type') == 'internship' ? 'selected' : '' }}>تدريب تعاوني</option>
                    </select>
                </div>

                <div class="{{ ($isCompany ?? false) ? 'col-6 col-md-2' : 'col-6 col-md-2' }}">
                    <label for="status" class="form-label-modern small fw-bold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الاعتماد</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    </select>
                </div>

                @if(!($isCompany ?? false))
                <div class="col-12 col-md-3">
                    <label for="company_id" class="form-label-modern small fw-bold">الشركة</label>
                    <select name="company_id" id="company_id" class="form-select form-select-sm">
                        <option value="">جميع الشركات</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="{{ ($isCompany ?? false) ? 'col-12 col-md-3' : 'col-12 col-md-2' }} d-flex gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-sm flex-grow-1">
                        <i class="fas fa-search me-1"></i>بحث
                    </button>
                    <a href="{{ route('job-opportunities.index') }}" class="btn btn-outline-secondary btn-sm" title="إعادة تعيين">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- تنبيه للفرص المعلقة بانتظار اعتماد المسؤولين --}}
    @if(in_array(auth()->user()->role, ['admin', 'partnership_officer', 'career_guidance_officer']) && ($stats['pending'] ?? 0) > 0)
        <div class="alert alert-warning border-warning border-opacity-25 d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 rounded-4 mb-4 shadow-sm gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-20 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-dark">يوجد {{ $stats['pending'] }} فرص عمل جديدة بانتظار المراجعة والاعتماد</h6>
                    <small class="text-secondary">قامت الشركات الشريكة بإرسال هذه الفرص وتتطلب اعتمادكم قبل أن تصبح مرئية ومتاحة للتقديم للخريجين.</small>
                </div>
            </div>
            <a href="{{ route('job-opportunities.index', ['status' => 'pending']) }}" class="btn btn-warning text-dark fw-bold btn-sm px-3 py-2 rounded-pill shadow-sm text-nowrap align-self-start align-self-md-center">
                <i class="fas fa-check-double me-1"></i>استعراض الفرص المعلقة ({{ $stats['pending'] }})
            </a>
        </div>
    @endif

    {{-- تنبيه خاص للشركة لمعرفة حالة الفرص الخاصة بها --}}
    @if(($isCompany ?? false) && (($stats['pending'] ?? 0) > 0 || ($stats['rejected'] ?? 0) > 0))
        <div class="alert alert-info border-info border-opacity-25 p-3 rounded-4 mb-4 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-20 text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold text-dark">حالة الفرص الوظيفية والتدريبية الخاصة بشركتكم</h6>
                    <p class="mb-0 text-secondary small">
                        @if(($stats['pending'] ?? 0) > 0)
                            <span class="badge bg-warning text-dark me-1"><i class="fas fa-clock me-1"></i>{{ $stats['pending'] }} بانتظار الاعتماد</span>
                            تخضع الفرص الجديدة لمراجعة إدارة المنظومة قبل نشرها للخريجين.
                        @endif
                        @if(($stats['rejected'] ?? 0) > 0)
                            <span class="badge bg-danger text-white ms-2 me-1"><i class="fas fa-times-circle me-1"></i>{{ $stats['rejected'] }} مرفوضة بحاجة لتعديل</span>
                            يمكنكم النقر على تعديل لمعرفة ملاحظات الإدارة وتحديث الفرصة.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- قائمة وجدول البيانات -->
    <div class="card-modern shadow-sm border-0">
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($opportunities->count() > 0)
                {{-- 🖥️ عرض سطح المكتب: جدول متجاوب حديث مع عمود إجراءات ثابت --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr class="text-secondary fw-bold" style="font-size: 0.82rem;">
                                <th class="py-3 px-3 text-center text-nowrap" style="width: 45px;">#</th>
                                <th class="py-3 text-nowrap" style="min-width: 200px;">الفرصة</th>
                                @if(!($isCompany ?? false))
                                    <th class="py-3 text-nowrap" style="min-width: 170px;">الشركة</th>
                                @endif
                                <th class="py-3 text-center text-nowrap" style="width: 120px;">النوع</th>
                                <th class="py-3 text-nowrap" style="width: 110px;">المكان</th>
                                <th class="py-3 text-nowrap" style="width: 130px;">التواريخ</th>
                                <th class="py-3 text-center text-nowrap" style="width: 70px;">المقاعد</th>
                                <th class="py-3 text-center text-nowrap" style="width: 85px;">الترشيحات</th>
                                <th class="py-3 text-center text-nowrap" style="width: 100px;">الحالة</th>
                                <th class="py-3 text-center text-nowrap sticky-actions-col" style="width: 160px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opportunities as $opportunity)
                            <tr>
                                <td class="px-3 text-center text-muted fw-bold" style="font-size: 0.85rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <a href="{{ route('job-opportunities.show', $opportunity) }}" class="fw-bold text-dark text-decoration-none d-block mb-1 hover-primary fs-6">
                                            {{ $opportunity->title }}
                                        </a>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            @if($opportunity->salary)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold" style="font-size: 0.72rem;">
                                                    <i class="fas fa-money-bill-wave me-1"></i>{{ number_format($opportunity->salary) }} د.ل
                                                </span>
                                            @endif
                                            @if($opportunity->contract_type)
                                                @php
                                                    $contracts = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'freelance' => 'عمل حر'];
                                                @endphp
                                                <span class="badge bg-light text-secondary rounded-pill border" style="font-size: 0.7rem;">
                                                    {{ $contracts[$opportunity->contract_type] ?? $opportunity->contract_type }}
                                                </span>
                                            @endif
                                            @if($opportunity->status === 'rejected' && $opportunity->rejection_reason)
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" style="font-size: 0.7rem;" title="{{ $opportunity->rejection_reason }}">
                                                    <i class="fas fa-info-circle me-1"></i>سبب الرفض: {{ Str::limit($opportunity->rejection_reason, 30) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                @if(!($isCompany ?? false))
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.3;">{{ $opportunity->company->name ?? 'غير محدد' }}</div>
                                            @if(!empty($opportunity->company->industry))
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $opportunity->company->industry }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                @endif

                                <td class="text-center text-nowrap">
                                    @php
                                        $typeLabels = ['job' => 'وظيفة شاغرة', 'training' => 'تدريب مهني بشركات', 'internship' => 'تدريب تعاوني'];
                                    @endphp
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;
                                        @if($opportunity->type === 'job') background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;
                                        @elseif($opportunity->type === 'training') background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;
                                        @else background:#fef3c7; color:#d97706; border:1px solid #fde68a;
                                        @endif">
                                        <i class="{{ $opportunity->type === 'job' ? 'fas fa-briefcase' : ($opportunity->type === 'training' ? 'fas fa-graduation-cap' : 'fas fa-laptop-code') }} me-1"></i>{{ $typeLabels[$opportunity->type] ?? $opportunity->type }}
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <span class="text-dark small d-inline-flex align-items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt text-danger" style="font-size: 0.8rem;"></i>
                                        <span class="fw-semibold">{{ $opportunity->location }}</span>
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                        <span class="text-nowrap text-secondary font-monospace d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-play-circle text-success" style="font-size: 0.7rem;"></i>
                                            <span>{{ $opportunity->start_date ? $opportunity->start_date->format('Y-m-d') : '--' }}</span>
                                        </span>
                                        @if($opportunity->end_date)
                                        <span class="text-nowrap text-muted font-monospace d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-flag-checkered text-danger" style="font-size: 0.7rem;"></i>
                                            <span>{{ $opportunity->end_date->format('Y-m-d') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center text-nowrap">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #f1f5f9; color: #334155; font-size: 0.78rem;" title="عدد المقاعد المتاحة">
                                        {{ $opportunity->seats }}
                                    </span>
                                </td>

                                <td class="text-center text-nowrap">
                                    <a href="{{ route('job-opportunities.nominations', $opportunity) }}" class="text-decoration-none" title="عرض المرشحين">
                                        <span class="badge rounded-pill px-2.5 py-1 fw-bold {{ $opportunity->nominations_count > 0 ? 'bg-primary text-white' : 'bg-light text-muted border' }}" style="font-size: 0.78rem;">
                                            <i class="fas fa-users me-1"></i>{{ $opportunity->nominations_count }}
                                        </span>
                                    </a>
                                </td>

                                <td class="text-center text-nowrap">
                                    @php
                                        $statusLabels = [
                                            'new' => 'جديدة', 
                                            'open' => 'مفتوحة', 
                                            'closed' => 'مغلقة', 
                                            'completed' => 'مكتملة',
                                            'pending' => 'بانتظار الاعتماد',
                                            'rejected' => 'مرفوضة'
                                        ];
                                    @endphp
                                    @if($opportunity->status === 'pending')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;" title="بانتظار المراجعة والاعتماد من قبل الإدارة">
                                            <i class="fas fa-clock me-1"></i>بانتظار الاعتماد
                                        </span>
                                    @elseif($opportunity->status === 'rejected')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;" title="{{ $opportunity->rejection_reason ? 'السبب: ' . $opportunity->rejection_reason : 'مرفوضة' }}">
                                            <i class="fas fa-times-circle me-1"></i>مرفوضة
                                        </span>
                                    @elseif($opportunity->status === 'open')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                                            <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>مفتوحة
                                        </span>
                                    @elseif($opportunity->status === 'new')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc;">
                                            <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>جديدة
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                                            <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>{{ $statusLabels[$opportunity->status] ?? $opportunity->status }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center text-nowrap sticky-actions-col">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if(in_array(auth()->user()->role, ['admin', 'partnership_officer', 'career_guidance_officer']) && $opportunity->status === 'pending')
                                            {{-- زر الاعتماد السريع للمسؤولين --}}
                                            <form action="{{ route('job-opportunities.approve', $opportunity) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-2 px-2 py-1 shadow-none text-white fw-bold" data-bs-toggle="tooltip" title="اعتماد ونشر الفرصة للخريجين" onclick="return confirm('هل أنت متأكد من اعتماد فرصة العمل ونشرها للخريجين؟')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            {{-- زر الرفض مع سبب --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 shadow-none" data-bs-toggle="modal" data-bs-target="#rejectJobModal" data-action="{{ route('job-opportunities.reject', $opportunity) }}" data-title="{{ $opportunity->title }}" data-company="{{ $opportunity->company->name ?? '' }}" title="رفض الفرصة مع ذكر السبب">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        {{-- عرض التفاصيل --}}
                                        <a href="{{ route('job-opportunities.show', $opportunity) }}" class="btn btn-sm btn-light border text-primary rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- استعراض المرشحين --}}
                                        <a href="{{ route('job-opportunities.nominations', $opportunity) }}" class="btn btn-sm btn-light border text-info rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="المرشحون">
                                            <i class="fas fa-user-check"></i>
                                        </a>

                                        {{-- تعديل --}}
                                        <a href="{{ route('job-opportunities.edit', $opportunity) }}" class="btn btn-sm btn-light border text-warning rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- حذف --}}
                                        <form action="{{ route('job-opportunities.destroy', $opportunity) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-2 px-2 py-1 shadow-none" onclick="return confirm('هل أنت متأكد من حذف هذه الفرصة؟')" data-bs-toggle="tooltip" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📱 عرض الموبايل: بطاقات لمسية متكاملة --}}
                <div class="d-md-none p-3">
                    @foreach($opportunities as $opportunity)
                    <div class="job-mobile-card">
                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1 fs-6">{{ $opportunity->title }}</h6>
                                @if(!($isCompany ?? false))
                                <div class="text-muted small">
                                    <i class="fas fa-building me-1 text-primary"></i>{{ $opportunity->company->name ?? 'غير محدد' }}
                                </div>
                                @endif
                            </div>
                            @if($opportunity->status === 'pending')
                                <span class="badge rounded-pill bg-warning text-dark fw-bold" style="font-size: 0.72rem;">
                                    <i class="fas fa-clock me-1"></i>بانتظار الاعتماد
                                </span>
                            @elseif($opportunity->status === 'rejected')
                                <span class="badge rounded-pill bg-danger text-white fw-bold" style="font-size: 0.72rem;">
                                    <i class="fas fa-times-circle me-1"></i>مرفوضة
                                </span>
                            @elseif($opportunity->status === 'open')
                                <span class="badge rounded-pill bg-success text-white" style="font-size: 0.72rem;">
                                    مفتوحة
                                </span>
                            @elseif($opportunity->status === 'new')
                                <span class="badge rounded-pill bg-info text-white" style="font-size: 0.72rem;">
                                    جديدة
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary text-white" style="font-size: 0.72rem;">
                                    {{ $statusLabels[$opportunity->status] ?? $opportunity->status }}
                                </span>
                            @endif
                        </div>

                        @if($opportunity->status === 'rejected' && $opportunity->rejection_reason)
                            <div class="alert alert-danger py-1.5 px-2.5 small rounded-3 my-2 mb-2">
                                <i class="fas fa-exclamation-triangle me-1"></i><strong>سبب الرفض:</strong> {{ $opportunity->rejection_reason }}
                            </div>
                        @endif

                        <div class="d-flex flex-wrap gap-2 my-2">
                            <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">
                                <i class="fas fa-tag me-1"></i>{{ $typeLabels[$opportunity->type] ?? $opportunity->type }}
                            </span>
                            @if($opportunity->location)
                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $opportunity->location }}
                            </span>
                            @endif
                            @if($opportunity->salary)
                            <span class="badge bg-light text-success border" style="font-size: 0.72rem;">
                                <i class="fas fa-money-bill me-1"></i>{{ number_format($opportunity->salary) }} د.ل
                            </span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center my-2 py-2 border-top border-bottom border-light small text-muted">
                            <div><i class="fas fa-chair me-1 text-primary"></i> المقاعد: <strong>{{ $opportunity->seats }}</strong></div>
                            <div>
                                <a href="{{ route('job-opportunities.nominations', $opportunity) }}" class="text-decoration-none">
                                    <i class="fas fa-users me-1 text-info"></i> المرشحين: <strong class="badge bg-primary rounded-pill">{{ $opportunity->nominations_count }}</strong>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-end gap-1.5 mt-2 pt-1">
                            @if(in_array(auth()->user()->role, ['admin', 'partnership_officer', 'career_guidance_officer']) && $opportunity->status === 'pending')
                                <form action="{{ route('job-opportunities.approve', $opportunity) }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 text-white fw-bold" onclick="return confirm('هل أنت متأكد من اعتماد ونشر الفرصة؟')">
                                        <i class="fas fa-check me-1"></i>اعتماد
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#rejectJobModal" data-action="{{ route('job-opportunities.reject', $opportunity) }}" data-title="{{ $opportunity->title }}" data-company="{{ $opportunity->company->name ?? '' }}">
                                    <i class="fas fa-times me-1"></i>رفض
                                </button>
                            @endif
                            <a href="{{ route('job-opportunities.show', $opportunity) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5">
                                <i class="fas fa-eye me-1"></i>عرض
                            </a>
                            <a href="{{ route('job-opportunities.nominations', $opportunity) }}" class="btn btn-sm btn-outline-info rounded-pill px-2.5">
                                <i class="fas fa-users me-1"></i>المرشحون
                            </a>
                            <a href="{{ route('job-opportunities.edit', $opportunity) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2.5">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                            <form action="{{ route('job-opportunities.destroy', $opportunity) }}" method="POST" class="d-inline m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- الترقيم والتصفح -->
                @if(method_exists($opportunities, 'hasPages') && $opportunities->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $opportunities->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <div class="d-inline-flex p-3 rounded-circle bg-light text-muted">
                            <i class="fas fa-briefcase fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <h5 class="text-dark fw-bold mb-2">لا توجد فرص عمل أو تدريب مسجلة</h5>
                    <p class="text-muted small mb-3">لم يتم العثور على أي فرص وظيفية تطابق معايير البحث الحالية.</p>
                    <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary-modern btn-sm px-3 py-2">
                        <i class="fas fa-plus-circle me-1"></i>إضافة أول فرصة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal رفض فرصة العمل -->
<div class="modal fade" id="rejectJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold">
                    <i class="fas fa-times-circle me-2"></i>رفض نشر فرصة العمل
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectJobForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">
                        سيتم إشعار الشركة بسبب الرفض حتى تتمكن من تصحيح واستيفاء المطلوب وإعادة إرسال الفرصة للمراجعة.
                    </p>
                    <div class="alert alert-light border rounded-3 p-3 mb-3">
                        <div class="fw-bold text-dark mb-1" id="modalJobTitle">--</div>
                        <small class="text-muted" id="modalJobCompany">--</small>
                    </div>
                    <div class="mb-3">
                        <label for="modalRejectionReason" class="form-label fw-bold text-dark small">سبب الرفض والملاحظات <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="modalRejectionReason" rows="4" class="form-control rounded-3" placeholder="اكتب سبب عدم قبول الفرصة والتوجيهات للشركة لتعديلها..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i>تأكيد الرفض وإشعار الشركة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal الاستيراد -->
@if(!($isCompany ?? false))
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-import me-2 text-success"></i>استيراد فرص عمل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('job-opportunities.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="company_id_import" class="form-label">اختر الشركة</label>
                        <select name="company_id" id="company_id_import" class="form-select" required>
                            <option value="">اختر الشركة...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">اختر ملف CSV</label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".csv,.txt" required>
                        <small class="text-muted">الملفات المدعومة: CSV فقط بحجم أقصى 5 ميجابايت.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-upload me-1"></i>استيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rejectModal = document.getElementById('rejectJobModal');
    if (rejectModal) {
        rejectModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const action = button.getAttribute('data-action');
            const title = button.getAttribute('data-title');
            const company = button.getAttribute('data-company');

            const form = document.getElementById('rejectJobForm');
            if (form && action) {
                form.setAttribute('action', action);
            }

            const titleEl = document.getElementById('modalJobTitle');
            if (titleEl) titleEl.textContent = title || '--';

            const compEl = document.getElementById('modalJobCompany');
            if (compEl) compEl.textContent = company ? ('الشركة: ' + company) : '';

            const reasonEl = document.getElementById('modalRejectionReason');
            if (reasonEl) reasonEl.value = '';
        });
    }
});
</script>
@endpush
@endsection