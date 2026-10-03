@extends('layouts.app')

@section('title', 'تقارير التغطية الإعلامية | وحدة الإعلام')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- Hero Header -->
    <x-page-hero
        title="تقارير التغطية والتوثيق الإعلامي"
        description="متابعة نسب إنجاز التغطية الصحفية، واستخراج وطباعة تقارير التوثيق الفنية للبرامج والمحطات التدريبية"
        icon="fas fa-file-invoice"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'تقارير التغطية']
        ]"
        secondaryBadge="{{ $stats['rate'] ?? 0 }}% نسبة التغطية"
        secondaryBadgeIcon="fas fa-chart-pie"
    >
        <a href="{{ route('media.reports.coverage.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>إضافة تقرير تغطية جديد</span>
        </a>
        <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-print fs-6"></i>
            <span>طباعة الملخص</span>
        </button>
        <a href="{{ route('media.dashboard') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-th-large fs-6"></i>
            <span>لوحة الميديا</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 d-print-none" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <span class="fw-bold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- صف بطاقات إحصائيات Bento المعتمدة للمنصة -->
    <div class="row g-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'إجمالي البرامج',
            'value' => $stats['total'] ?? 0,
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary',
            'description' => 'جميع التدريبات بالمنظومة'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'تمت التغطية',
            'value' => $stats['covered'] ?? 0,
            'icon' => 'fas fa-check-double',
            'color' => 'success',
            'badge' => ($stats['rate'] ?? 0) . '% إنجاز',
            'badgeClass' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            'description' => 'بيانات وتقارير معتمدة'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'بانتظار التغطية',
            'value' => $stats['pending'] ?? 0,
            'icon' => 'fas fa-hourglass-half',
            'color' => 'warning',
            'badge' => 'قيد المتابعة',
            'badgeClass' => 'bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25',
            'description' => 'تحت المتابعة أو جارية حالياً'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'التقارير الصحفية',
            'value' => $stats['total_reports'] ?? 0,
            'icon' => 'fas fa-feather-alt',
            'color' => 'info',
            'badge' => ($stats['with_links'] ?? 0) . ' بروابط سحابية',
            'badgeClass' => 'bg-info bg-opacity-10 text-primary border border-info border-opacity-25',
            'description' => 'بيانات صحفية وتقارير محررة'
        ])
    </div>

    <!-- بطاقة البحث والتصفية -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-filter text-primary"></i>
                <span>البحث وتصفية التقارير</span>
            </h6>
            <form method="GET" action="{{ route('media.reports.coverage') }}" class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-search text-primary me-1"></i> البحث عن برنامج أو شركة أو قاعة
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0"
                               placeholder="اسم البرنامج التدريبي، الشركة الشريكة، مكان الانعقاد..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-tag text-primary me-1"></i> حالة التغطية
                    </label>
                    <select name="coverage_status" class="form-select bg-light rounded-3">
                        <option value="">جميع الحالات</option>
                        <option value="covered" {{ request('coverage_status') === 'covered' ? 'selected' : '' }}>✅ تمت التغطية</option>
                        <option value="pending" {{ request('coverage_status') === 'pending' ? 'selected' : '' }}>⏳ بانتظار التغطية</option>
                        <option value="not_required" {{ request('coverage_status') === 'not_required' ? 'selected' : '' }}>⚪ غير مطلوبة</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="far fa-calendar-alt text-primary me-1"></i> السنة
                    </label>
                    <select name="year" class="form-select bg-light rounded-3">
                        <option value="">كل السنوات</option>
                        @for($y = date('Y') + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #1d4ed8;">
                        <i class="fas fa-search"></i>
                        <span>تصفية</span>
                    </button>
                    @if(request()->anyFilled(['search', 'coverage_status', 'year']))
                        <a href="{{ route('media.reports.coverage') }}" class="btn btn-outline-secondary py-2.5 px-3 rounded-3" title="إلغاء الفلاتر">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

@push('styles')
<style>
    .reports-table-card {
        transform: none !important;
        transition: box-shadow 0.2s ease !important;
    }
    .reports-table-card:hover {
        transform: none !important;
    }
    .reports-table tbody tr {
        transition: background-color 0.15s ease !important;
        transform: none !important;
    }
    .reports-table tbody tr:hover {
        background-color: #f8fafc !important;
        transform: none !important;
        box-shadow: none !important;
    }
</style>
@endpush

    <!-- جدول تقارير التدريبات -->
    <div class="card reports-table-card shadow-sm border-0 rounded-4 overflow-hidden mb-4 p-0" style="background: #ffffff; transform: none !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark fs-6">قائمة تقارير التغطية التدريبية</h6>
                    <small class="text-muted">متابعة صياغة التقارير الفنية، البيانات الصحفية، والتوثيق المعتمد</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-primary border rounded-pill px-3 py-1.5 fw-bold">
                    {{ method_exists($trainings, 'total') ? $trainings->total() : $trainings->count() }} برامج مسجلة
                </span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">
                    {{ $stats['rate'] ?? 0 }}% نسبة الإنجاز
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table reports-table table-hover align-middle mb-0">
                <thead class="bg-light" style="font-size: 0.82rem; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 45px;">#</th>
                        <th class="py-3">البرنامج التدريبي</th>
                        <th class="py-3">الجهة / المدرب</th>
                        <th class="py-3 text-nowrap">الفترة الزمنية</th>
                        <th class="py-3 text-center text-nowrap">التوثيق والروابط</th>
                        <th class="py-3 text-center text-nowrap">حالة التغطية</th>
                        <th class="pe-4 py-3 text-center text-nowrap" style="min-width: 220px;">الإجراءات والتقرير الصحفي</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($trainings as $training)
                        <tr>
                            <td class="ps-4 font-monospace text-muted small">
                                {{ $loop->iteration + (method_exists($trainings, 'currentPage') ? ($trainings->currentPage() - 1) * $trainings->perPage() : 0) }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; 
                                        @if($training->type === 'workshop') background: rgba(5,150,105,0.1); color:#059669;
                                        @elseif($training->type === 'seminar') background: rgba(217,119,6,0.1); color:#d97706;
                                        @elseif($training->type === 'internship') background: rgba(29,78,216,0.1); color:#1d4ed8;
                                        @else background: rgba(13,56,130,0.1); color:#0d3882;
                                        @endif">
                                        <i class="{{ $training->type === 'workshop' ? 'fas fa-tools' : ($training->type === 'seminar' ? 'fas fa-bullhorn' : ($training->type === 'internship' ? 'fas fa-laptop-code' : 'fas fa-graduation-cap')) }}" style="font-size: 0.95rem;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('media.reports.coverage.show', $training) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                {{ $training->title }}
                                            </a>
                                            @if($training->media_press_release || $training->media_coverage_summary)
                                                <span class="badge rounded-pill px-2 py-0.5 fw-bold" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.68rem;" title="تمت كتابة التقرير والبيان الصحفي">
                                                    <i class="fas fa-feather-alt me-0.5"></i>محرر صحفياً
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-flex align-items-center gap-2 mt-1" style="font-size: 0.74rem;">
                                            <span><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $training->location ?: 'جامعة طرابلس' }}</span>
                                            <span>•</span>
                                            @php
                                                $typeMap = [
                                                    'course' => ['label' => 'دورة تدريبية', 'bg' => '#eff6ff', 'color' => '#1d4ed8'],
                                                    'workshop' => ['label' => 'ورشة عمل', 'bg' => '#ecfdf5', 'color' => '#059669'],
                                                    'seminar' => ['label' => 'ندوة علمية', 'bg' => '#fef3c7', 'color' => '#d97706'],
                                                    'internship' => ['label' => 'تدريب عملي', 'bg' => '#f5f3ff', 'color' => '#7c3aed'],
                                                ];
                                                $tc = $typeMap[$training->type] ?? ['label' => $training->type_arabic ?? 'تدريب', 'bg' => '#f1f5f9', 'color' => '#475569'];
                                            @endphp
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: {{ $tc['bg'] }}; color: {{ $tc['color'] }}; font-size: 0.7rem; font-weight: 600;">
                                                {{ $tc['label'] }}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($training->company)
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.74rem;">
                                        <i class="fas fa-building me-1"></i>{{ $training->company->name }}
                                    </span>
                                @elseif($training->coordinator)
                                    <span class="text-dark small fw-semibold">
                                        <i class="fas fa-user-tie text-secondary me-1"></i>{{ $training->coordinator->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">مكتب تدريب الخريجين</span>
                                @endif
                            </td>
                            <td class="text-nowrap" style="white-space: nowrap;">
                                <div class="d-flex flex-column small">
                                    <div class="text-nowrap font-monospace text-dark fw-bold">
                                        <i class="far fa-calendar-alt text-primary ms-1"></i>{{ $training->start_date ? $training->start_date->format('Y-m-d') : '--' }}
                                    </div>
                                    <div class="text-nowrap font-monospace text-muted mt-0.5" style="font-size: 0.73rem;">
                                        <i class="far fa-calendar-check text-secondary ms-1"></i>إلى: {{ $training->end_date ? $training->end_date->format('Y-m-d') : '--' }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-nowrap" style="white-space: nowrap;">
                                @php
                                    $hasWritten = !empty($training->media_press_release) || !empty($training->media_coverage_summary);
                                    $hasLinks = !empty($training->media_coverage_links);
                                @endphp
                                @if($hasWritten || $hasLinks)
                                    <div class="d-inline-flex align-items-center gap-1 flex-wrap justify-content-center">
                                        @if($hasWritten)
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.72rem;" title="بيان صحفي وموجز مكتوب">
                                                <i class="fas fa-file-alt me-1"></i>بيان صحفي
                                            </span>
                                        @endif
                                        @if($hasLinks)
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.72rem;" title="روابط تخزين سحابية">
                                                <i class="fas fa-cloud me-1"></i>روابط سحابية
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1 fw-normal" style="background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; font-size: 0.72rem;">قيد الإعداد</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap" style="white-space: nowrap;">
                                @if($training->media_coverage_status === 'covered')
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.74rem;">
                                        <i class="fas fa-check-circle me-1"></i>تمت التغطية
                                    </span>
                                @elseif($training->media_coverage_status === 'pending' || is_null($training->media_coverage_status))
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; font-size: 0.74rem;">
                                        <i class="fas fa-hourglass-half me-1"></i>بانتظار التغطية
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.74rem;">
                                        <i class="fas fa-minus-circle me-1"></i>غير مطلوبة
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-center text-nowrap" style="white-space: nowrap;">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                                    <a href="{{ route('media.reports.coverage.show', $training) }}"
                                       class="btn btn-sm btn-primary-modern py-1.5 px-3 rounded-pill text-nowrap d-inline-flex align-items-center gap-1.5 shadow-sm text-decoration-none"
                                       style="font-size: 0.78rem;"
                                       title="معاينة التقرير الرسمي المعتمد">
                                        <i class="fas fa-eye"></i>
                                        <span>عرض</span>
                                    </a>
                                    @if($hasWritten)
                                        <a href="{{ route('media.reports.coverage.edit', $training) }}"
                                           class="btn btn-sm btn-outline-warning text-dark fw-bold py-1.5 px-3 rounded-pill text-nowrap d-inline-flex align-items-center gap-1.5 shadow-sm text-decoration-none"
                                           style="font-size: 0.78rem;"
                                           title="تعديل وصياغة التقرير والبيان الصحفي">
                                            <i class="fas fa-edit"></i>
                                            <span>تعديل التقرير</span>
                                        </a>
                                    @else
                                        <a href="{{ route('media.reports.coverage.edit', $training) }}"
                                           class="btn btn-sm btn-warning text-dark fw-bold py-1.5 px-3 rounded-pill text-nowrap d-inline-flex align-items-center gap-1.5 shadow-sm text-decoration-none"
                                           style="font-size: 0.78rem;"
                                           title="إضافة وصياغة التقرير والبيان الصحفي">
                                            <i class="fas fa-plus-circle"></i>
                                            <span>إضافة تقرير</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted opacity-50 mb-3"></i>
                                    <h6 class="fw-bold text-dark">لا توجد تقارير تغطية مطابقة للبحث</h6>
                                    <p class="small text-muted mb-0">يمكنك تعديل خيارات التصفية أو البحث عن برنامج تدريبي آخر.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($trainings, 'hasPages') && $trainings->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-center">
                {{ $trainings->appends(request()->query())->links() }}
            </div>
        @endif
</div>
@endsection
