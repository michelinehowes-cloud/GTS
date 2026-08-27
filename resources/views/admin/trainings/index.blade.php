@extends('layouts.app')

@section('title', 'إدارة برامج التدريب وطلبات الالتحاق')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة التدريب', 'active' => true],
        ]
    ])

    <!-- قسم الإحصائيات -->
    <div class="bento-grid mb-4">
        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">إجمالي البرامج</h3>
                <div class="bento-card-icon bento-icon-primary">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $trainings->count() }}</div>
            <div class="bento-desc mt-2">كافة البرامج التدريبية المضافة للنظام</div>
        </div>

        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">البرامج النشطة</h3>
                <div class="bento-card-icon bento-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-1">
                <div class="bento-stat">{{ $trainings->where('status', 'active')->count() }}</div>
                <div class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 mb-2">
                    نشط <i class="fas fa-bolt ms-1"></i>
                </div>
            </div>
            <div class="bento-desc mt-2">برامج متاحة حالياً للتسجيل</div>
        </div>

        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">طلبات قيد المراجعة</h3>
                <div class="bento-card-icon bento-icon-warning">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-1">
                <div class="bento-stat">{{ $applications->where('status', 'pending')->count() }}</div>
                <div class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 mb-2" style="color: #d97706 !important;">
                    بانتظار الموافقة <i class="fas fa-clock ms-1"></i>
                </div>
            </div>
            <div class="bento-desc mt-2">طلبات التحاق تحتاج لمعالجة</div>
        </div>

        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">إجمالي الطلبات</h3>
                <div class="bento-card-icon bento-icon-primary" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $applications->count() }}</div>
            <div class="bento-desc mt-2">جميع طلبات الالتحاق المسجلة</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 bg-success-subtle text-success-emphasis mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fs-4 me-2"></i>
                <div class="fw-bold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- المحتوى الرئيسي (تبويبات) -->
    <div class="bento-card p-0">
        <div class="border-bottom p-0">
            <div class="d-flex justify-content-between align-items-center p-3 pb-0 flex-wrap gap-3">
                <ul class="nav nav-tabs border-bottom-0" id="trainingTabs" role="tablist" style="margin-bottom: -1px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-secondary" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs" type="button" role="tab" style="border-radius: 12px 12px 0 0; background-color: var(--bento-surface); color: var(--bento-text) !important;">
                            <i class="fas fa-graduation-cap me-2"></i>برامج التدريب
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-secondary" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab" style="border-radius: 12px 12px 0 0;">
                            <i class="fas fa-file-alt me-2"></i>طلبات الالتحاق 
                            @if($applications->where('status', 'pending')->count() > 0)
                                <span class="badge bg-danger rounded-pill ms-2">{{ $applications->where('status', 'pending')->count() }}</span>
                            @endif
                        </button>
                    </li>
                </ul>
                <div class="pb-2">
                    <a href="{{ route('admin.trainings.create') }}" class="btn-bento btn-sm text-decoration-none d-inline-block">
                        <i class="fas fa-plus me-1"></i> إضافة برنامج جديد
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content p-4" id="trainingTabsContent">
                
                <!-- تبويب برامج التدريب -->
                <div class="tab-pane fade show active" id="programs" role="tabpanel" tabindex="0">
                    @if($trainings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold">#</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">البرنامج</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الشركة</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">النوع</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">المدة</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">البداية</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الحالة</th>
                                        <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @foreach($trainings as $training)
                                    <tr>
                                        <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $training->title }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;">
                                                <i class="fas fa-users text-primary me-1"></i> {{ $training->seats }} مقعد
                                            </div>
                                        </td>
                                        <td>
                                            @if($training->company)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-building text-secondary"></i>
                                                    </div>
                                                    <span>{{ $training->company->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($training->type)
                                                @case('workshop') <span class="badge bg-info-subtle text-info fw-normal px-2 py-1"><i class="fas fa-tools me-1"></i>ورشة عمل</span> @break
                                                @case('course') <span class="badge bg-primary-subtle text-primary fw-normal px-2 py-1"><i class="fas fa-book-reader me-1"></i>دورة</span> @break
                                                @case('seminar') <span class="badge bg-secondary-subtle text-secondary fw-normal px-2 py-1"><i class="fas fa-chalkboard-teacher me-1"></i>ندوة</span> @break
                                                @case('internship') <span class="badge bg-success-subtle text-success fw-normal px-2 py-1"><i class="fas fa-user-md me-1"></i>تدريب عملي</span> @break
                                                @default <span class="badge bg-light text-dark border">{{ $training->type }}</span>
                                            @endswitch
                                        </td>
                                        <td><span class="fw-medium text-dark">{{ $training->duration }} أيام</span></td>
                                        <td><span class="text-muted">{{ $training->start_date }}</span></td>
                                        <td>
                                            @if($training->status == 'active')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">نشط</span>
                                            @elseif($training->status == 'inactive')
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">غير نشط</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">مكتمل</span>
                                            @endif
                                        </td>
                                        <td class="px-4 text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.trainings.show', $training->id) }}" class="btn btn-sm btn-outline-info-modern" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-sm btn-outline-warning-modern" data-bs-toggle="tooltip" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger-modern rounded-start-0" onclick="return confirm('هل أنت متأكد من حذف هذا البرنامج؟')" data-bs-toggle="tooltip" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-graduation-cap fa-3x text-muted opacity-50"></i>
                                </div>
                            </div>
                            <h5 class="text-muted mb-3">لا توجد برامج تدريبية حالياً</h5>
                            <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary-modern">
                                <i class="fas fa-plus me-2"></i>إضافة أول برنامج
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب طلبات الالتحاق -->
                <div class="tab-pane fade" id="applications" role="tabpanel" tabindex="0">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold">#</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الخريج</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">البرنامج المطلوب</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">تاريخ التقديم</th>
                                        <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الحالة</th>
                                        <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @foreach($applications as $application)
                                    <tr>
                                        <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                                    <span class="fw-bold">{{ substr($application->user->name ?? 'U', 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $application->user->name ?? 'غير محدد' }}</div>
                                                    <div class="small text-muted">{{ $application->user->email ?? 'لا يوجد بريد' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($application->training)
                                                <div class="fw-bold text-dark">{{ $application->training->title }}</div>
                                                <div class="small text-muted">
                                                    @switch($application->training->type)
                                                        @case('workshop') ورشة عمل @break
                                                        @case('course') دورة @break
                                                        @case('seminar') ندوة @break
                                                        @case('internship') تدريب عملي @break
                                                        @default {{ $application->training->type }}
                                                    @endswitch
                                                </div>
                                            @else
                                                <span class="text-danger small"><i class="fas fa-exclamation-circle"></i> غير متوفر</span>
                                            @endif
                                        </td>
                                        <td><span class="text-muted">{{ $application->applied_at ? $application->applied_at->format('Y-m-d') : '--' }}</span></td>
                                        <td>
                                            @if($application->status == 'pending')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">قيد المراجعة</span>
                                            @elseif($application->status == 'approved')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">مقبول</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">مرفوض</span>
                                            @endif
                                        </td>
                                        <td class="px-4 text-end">
                                            @if($application->status == 'pending')
                                                <div class="btn-group">
                                                    <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success-modern rounded-end-0" data-bs-toggle="tooltip" title="قبول">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger-modern rounded-start-0" data-bs-toggle="tooltip" title="رفض">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <form action="{{ route('admin.applications.pending', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary-modern" data-bs-toggle="tooltip" title="إعادة للمراجعة">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-inbox fa-3x text-muted opacity-50"></i>
                                </div>
                            </div>
                            <h5 class="text-muted mb-3">لا توجد طلبات التحاق حتى الآن</h5>
                            <p class="text-muted small">ستظهر هنا جميع الطلبات المقدمة من الخريجين.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل الـ Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection