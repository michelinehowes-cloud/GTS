@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التدريب')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم منسق التدريب', 'active' => true],
        ]
    ])

    <!-- بطاقة الترحيب والترويسة -->
    <div class="card-modern mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px; font-size: 1.6rem;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h2 class="text-primary fw-bold mb-1">مرحباً بك، {{ auth()->user()->name }} 👋</h2>
                        <div class="text-muted small">
                            <i class="fas fa-tasks me-1 text-primary"></i>لوحة إدارة برامج التدريب والتأهيل ومتابعة حضور الخريجين
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#scannerModal" class="btn btn-warning-modern fw-bold">
                        <i class="fas fa-qrcode me-1"></i> الماسح الضوئي (QR)
                    </a>
                    <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-plus-circle me-1"></i> إضافة برنامج تدريبي
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'إجمالي برامج التدريب',
            'value' => $stats['totalTrainings'] ?? 0,
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary',
            'description' => 'جميع البرامج المسجلة'
        ])

        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'البرامج النشطة',
            'value' => $stats['activeTrainings'] ?? 0,
            'icon' => 'fas fa-play-circle',
            'color' => 'success',
            'description' => 'برامج قيد التنفيذ حالياً'
        ])

        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'المتدربون المقبولون',
            'value' => $stats['totalTrainees'] ?? 0,
            'icon' => 'fas fa-users',
            'color' => 'info',
            'description' => 'إجمالي الخريجين المؤكدين'
        ])

        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'طلبات معلقة جديدة',
            'value' => $stats['pendingApplications'] ?? 0,
            'icon' => 'fas fa-clock',
            'color' => 'warning',
            'description' => 'بانتظار المراجعة والقرار'
        ])
    </div>

    <!-- روابط وأزرار الوصول السريع -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-bolt me-2 text-warning"></i>إجراءات وروابط سريعة
            </h5>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-outline-primary-modern w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-graduation-cap fa-lg"></i>
                        <span class="fw-bold">إدارة برامج التدريب</span>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('training-coordinator.applications') }}" class="btn btn-outline-success-modern w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-clipboard-list fa-lg"></i>
                        <span class="fw-bold">إدارة طلبات التدريب</span>
                        @if(($stats['pendingApplications'] ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $stats['pendingApplications'] }}</span>
                        @endif
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#scannerModal" class="btn btn-outline-warning-modern w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-qrcode fa-lg"></i>
                        <span class="fw-bold">تسجيل الحضور اليومي</span>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('training-coordinator.calendar') }}" class="btn btn-outline-info-modern w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                        <span class="fw-bold">التقويم والجدول الزمني</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- البطاقات الرئيسية المزدوجة -->
    <div class="row mb-4">
        <!-- أحدث طلبات التدريب -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-file-alt me-2 text-warning"></i>أحدث طلبات التدريب
                    </h5>
                    <a href="{{ route('training-coordinator.applications') }}" class="btn btn-sm btn-outline-primary-modern">
                        عرض الكل <i class="fas fa-chevron-left ms-1 small"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    @if(isset($recentApplications) && $recentApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @foreach($recentApplications as $application)
                                    <tr>
                                        <td class="ps-3 py-3" style="width: 45px;">
                                            <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                {{ mb_substr($application->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('graduate.profile.public', $application->user->id ?? 0) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                {{ $application->user->name ?? 'غير محدد' }}
                                            </a>
                                            <small class="text-muted text-truncate d-block" style="max-width: 200px;">
                                                {{ $application->training->title ?? 'برنامج تدريبي' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if($application->status == 'pending')
                                                <span class="badge bg-light text-warning border border-warning rounded-pill px-2 py-1 small">
                                                    قيد المراجعة
                                                </span>
                                            @elseif($application->status == 'approved')
                                                <span class="badge bg-light text-success border border-success rounded-pill px-2 py-1 small">
                                                    مقبول
                                                </span>
                                            @else
                                                <span class="badge bg-light text-danger border border-danger rounded-pill px-2 py-1 small">
                                                    مرفوض
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <small class="text-muted d-block">
                                                {{ $application->applied_at ? \Carbon\Carbon::parse($application->applied_at)->diffForHumans() : $application->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted opacity-50 mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">لا توجد طلبات تدريب حديثة</h6>
                            <p class="text-muted small mb-0">ستظهر طلبات الخريجين الجديدة هنا مباشرة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أحدث برامج التدريب -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>أحدث برامج التدريب
                    </h5>
                    <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-sm btn-outline-primary-modern">
                        عرض الكل <i class="fas fa-chevron-left ms-1 small"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    @if(isset($recentTrainings) && $recentTrainings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @foreach($recentTrainings as $training)
                                    <tr>
                                        <td class="ps-3 py-3" style="width: 45px;">
                                            <div class="rounded-circle bg-light text-success fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                {{ $training->title }}
                                            </a>
                                            <small class="text-muted d-block">
                                                {{ $training->company->name ?? 'مكتب تدريب الخريجين' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if($training->status == 'active')
                                                <span class="badge bg-light text-success border border-success rounded-pill px-2 py-1 small">
                                                    نشط
                                                </span>
                                            @elseif($training->status == 'completed')
                                                <span class="badge bg-light text-info border border-info rounded-pill px-2 py-1 small">
                                                    مكتمل
                                                </span>
                                            @else
                                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                                                    متوقف
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                @if($training->status == 'active')
                                                    <a href="{{ route('training-coordinator.trainings.scanner', $training->id) }}" class="btn btn-outline-primary-modern" title="ماسح الحضور">
                                                        <i class="fas fa-qrcode"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('training-coordinator.trainings.attendance', $training->id) }}" class="btn btn-outline-warning-modern ms-1" title="سجل الحضور">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
                                                <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="btn btn-outline-secondary-modern ms-1" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted opacity-50 mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">لا توجد برامج تدريب مسجلة بعد</h6>
                            <p class="text-muted small mb-0">يمكنك البدء بإضافة برامج تدريبية جديدة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة اختيار البرنامج للماسح الضوئي -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-modern border-0 shadow">
            <div class="modal-header bg-white py-3 border-bottom">
                <h5 class="modal-title text-primary fw-bold" id="scannerModalLabel">
                    <i class="fas fa-qrcode me-2 text-warning"></i>اختر البرنامج لفتح الماسح الضوئي
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @php
                    $activeTrainingsForScanner = isset($recentTrainings) ? $recentTrainings->where('status', 'active') : collect();
                @endphp
                @if($activeTrainingsForScanner->count() > 0)
                    <p class="text-muted mb-3 small">حدد البرنامج التدريبي المطلوب لتسجيل الحضور اليومي للمتدربين:</p>
                    <div class="list-group list-group-flush gap-2">
                        @foreach($activeTrainingsForScanner as $training)
                            <a href="{{ route('training-coordinator.trainings.scanner', $training->id) }}" class="list-group-item list-group-item-action bg-light border rounded-3 p-3 text-decoration-none">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-graduation-cap me-2 text-primary"></i>{{ $training->title }}</h6>
                                    <span class="badge bg-primary rounded-pill px-2 py-1"><i class="fas fa-camera me-1"></i>مسح QR</span>
                                </div>
                                <small class="text-muted d-block mt-1"><i class="fas fa-building me-1"></i>الجهة: {{ $training->company->name ?? 'مكتب تدريب الخريجين' }}</small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6 class="fw-bold text-dark">لا توجد برامج تدريبية نشطة حالياً</h6>
                        <p class="text-muted small mt-2">يجب تفعيل البرنامج ليكون "نشط" لتتمكن من استخدام الماسح الضوئي لتسجيل الحضور اليومي.</p>
                        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-primary-modern mt-2">
                            <i class="fas fa-plus-circle me-1"></i> إضافة برنامج نشط
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection