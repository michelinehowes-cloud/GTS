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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="فرص العمل والتدريب"
        subtitle="إدارة واستعراض جميع فرص العمل والبرامج التدريبية المتاحة للخريجين والشركات الشريكة"
        icon="fas fa-briefcase"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'فرص العمل والتدريب']
        ]"
        badge="إدارة التوظيف والفرص"
    >
        <a href="{{ route('job-opportunities.create') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة فرصة جديدة</span>
        </a>
        <button class="btn btn-light bg-white text-success fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import"></i>
            <span>استيراد</span>
        </button>
    </x-page-hero>

    <!-- بطاقات الإحصائيات (2x3 على الموبايل و6 على الديسكتوب) -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'إجمالي الفرص',
            'value' => $stats['total'] ?? (method_exists($opportunities, 'total') ? $opportunities->total() : $opportunities->count()),
            'icon' => 'fas fa-briefcase',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl-2',
            'title' => 'مفتوحة',
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
            'title' => 'الترشيحات',
            'value' => $opportunities->sum('nominations_count'),
            'icon' => 'fas fa-users',
            'color' => 'danger'
        ])
    </div>

    <!-- فلترة وتصفية البيانات -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 text-primary fw-bold fs-6">
                <i class="fas fa-filter me-2"></i>فلاتر البحث والفرز
            </h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 g-md-3">
                <div class="col-6 col-md-3">
                    <label for="type" class="form-label-modern small fw-bold">نوع الفرصة</label>
                    <select name="type" id="type" class="form-select form-select-sm">
                        <option value="">جميع الأنواع</option>
                        <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>وظيفة شاغرة</option>
                        <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>تدريب مهني بشركات</option>
                        <option value="internship" {{ request('type') == 'internship' ? 'selected' : '' }}>تدريب تعاوني</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label for="status" class="form-label-modern small fw-bold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">جميع الحالات</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    </select>
                </div>
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
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-sm flex-grow-1">
                        <i class="fas fa-search me-1"></i>بحث
                    </button>
                    <a href="{{ route('job-opportunities.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                        <i class="fas fa-redo me-1"></i>إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- قائمة وجدول البيانات -->
    <div class="card-modern">
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($opportunities->count() > 0)
                {{-- 🖥️ عرض سطح المكتب: جدول متجاوب --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0" style="min-width: 1050px;">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr class="text-secondary fw-bold" style="font-size: 0.83rem;">
                                <th class="py-3 px-3 text-center text-nowrap" style="width: 50px;">#</th>
                                <th class="py-3 text-nowrap" style="min-width: 220px;">الفرصة</th>
                                <th class="py-3 text-nowrap" style="min-width: 180px;">الشركة</th>
                                <th class="py-3 text-center text-nowrap" style="width: 110px;">النوع</th>
                                <th class="py-3 text-nowrap" style="width: 120px;">المكان</th>
                                <th class="py-3 text-nowrap" style="width: 150px;">التواريخ</th>
                                <th class="py-3 text-center text-nowrap" style="width: 70px;">المقاعد</th>
                                <th class="py-3 text-center text-nowrap" style="width: 80px;">الترشيحات</th>
                                <th class="py-3 text-center text-nowrap" style="width: 95px;">الحالة</th>
                                <th class="py-3 text-center text-nowrap" style="width: 130px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opportunities as $opportunity)
                            <tr>
                                <td class="px-3 text-center text-muted fw-bold" style="font-size: 0.85rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark fs-6">{{ $opportunity->title }}</h6>
                                        <p class="text-muted small mb-1 text-truncate" style="max-width: 280px; font-size: 0.78rem;">
                                            {{ Str::limit($opportunity->description, 70) }}
                                        </p>
                                        @if($opportunity->salary)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-money-bill-wave me-1"></i>{{ number_format($opportunity->salary) }} د.ل
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.86rem; line-height: 1.3;">{{ $opportunity->company->name ?? 'غير محدد' }}</div>
                                            @if(!empty($opportunity->company->industry))
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $opportunity->company->industry }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
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
                                    <div class="d-flex flex-column gap-1" style="font-size: 0.78rem;">
                                        <span class="text-nowrap text-secondary font-monospace d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-play-circle text-success" style="font-size: 0.75rem;"></i>
                                            <span>{{ $opportunity->start_date ? $opportunity->start_date->format('Y-m-d') : '--' }}</span>
                                        </span>
                                        @if($opportunity->end_date)
                                        <span class="text-nowrap text-muted font-monospace d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-flag-checkered text-danger" style="font-size: 0.75rem;"></i>
                                            <span>{{ $opportunity->end_date->format('Y-m-d') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #f1f5f9; color: #334155; font-size: 0.8rem;">
                                        {{ $opportunity->seats }}
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #eef2ff; color: #4f46e5; font-size: 0.8rem;">
                                        {{ $opportunity->nominations_count }}
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    @php
                                        $statusLabels = ['new' => 'جديدة', 'open' => 'مفتوحة', 'closed' => 'مغلقة', 'completed' => 'مكتملة'];
                                    @endphp
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;
                                        @if($opportunity->status === 'open') background:#dcfce7; color:#15803d; border:1px solid #86efac;
                                        @elseif($opportunity->status === 'new') background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc;
                                        @else background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;
                                        @endif">
                                        <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>{{ $statusLabels[$opportunity->status] ?? $opportunity->status }}
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <a href="{{ route('job-opportunities.show', $opportunity) }}" class="btn btn-sm btn-light border text-primary rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('job-opportunities.edit', $opportunity) }}" class="btn btn-sm btn-light border text-warning rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
                                <div class="text-muted small">
                                    <i class="fas fa-building me-1 text-primary"></i>{{ $opportunity->company->name ?? 'غير محدد' }}
                                </div>
                            </div>
                            <span class="badge rounded-pill
                                @if($opportunity->status === 'open') bg-success text-white
                                @elseif($opportunity->status === 'new') bg-info text-white
                                @else bg-secondary text-white
                                @endif" style="font-size: 0.72rem;">
                                {{ $statusLabels[$opportunity->status] ?? $opportunity->status }}
                            </span>
                        </div>

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
                            <div><i class="fas fa-users me-1 text-info"></i> الترشيحات: <strong>{{ $opportunity->nominations_count }}</strong></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('job-opportunities.show', $opportunity) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-eye me-1"></i>عرض
                            </a>
                            <a href="{{ route('job-opportunities.edit', $opportunity) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                <i class="fas fa-edit me-1"></i>تعديل
                            </a>
                            <form action="{{ route('job-opportunities.destroy', $opportunity) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
                    <i class="fas fa-briefcase fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 class="text-muted mb-3">لا توجد فرص عمل أو تدريب مسجلة</h5>
                    <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>إضافة أول فرصة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal الاستيراد -->
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
                        <label for="file" class="form-label">اختر ملف Excel أو CSV</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required>
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
@endsection