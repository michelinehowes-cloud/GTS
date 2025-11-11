@extends('layouts.app')

@section('title', 'إدارة برامج التدريب')

@section('content')
<div class="container-fluid">
    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2" style="border-right: 4px solid #1e3a8a !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي برامج التدريب</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $trainings->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x" style="color: #1e3a8a;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2" style="border-right: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                البرامج النشطة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $trainings->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x" style="color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2" style="border-right: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                طلبات قيد المراجعة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $applications->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2" style="border-right: 4px solid #0ea5e9 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إجمالي الطلبات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $applications->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x" style="color: #0ea5e9;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أزرار سريعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus-circle me-2"></i>إضافة برنامج تدريب جديد
                            </a>
                            <button type="button" class="btn btn-outline-primary btn-lg" id="showTrainings">
                                <i class="fas fa-graduation-cap me-2"></i>عرض البرامج التدريبية
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-lg" id="showApplications">
                                <i class="fas fa-users me-2"></i>عرض طلبات التدريب
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- قسم برامج التدريب -->
    <div class="row" id="trainingsSection">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%); border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <h3 class="mb-0" style="color: #1e3a8a; font-weight: 800;">
                        <i class="fas fa-graduation-cap me-2"></i>برامج التدريب
                    </h3>
                    <span class="badge fs-6" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">عدد البرامج: {{ $trainings->count() }}</span>
                </div>
                <div class="card-body">
                    @if($trainings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم البرنامج</th>
                                        <th>الشركة</th>
                                        <th>النوع</th>
                                        <th>المدة</th>
                                        <th>تاريخ البدء</th>
                                        <th>المقاعد</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trainings as $training)
                                    <tr class="training-row">
                                        <td class="fw-bold" style="color: #1e3a8a;">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong style="color: #1e3a8a;">{{ $training->title }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold" style="color: #374151;">{{ $training->company->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            @switch($training->type)
                                                @case('workshop') <span class="badge" style="background: #0ea5e9;">ورشة عمل</span> @break
                                                @case('course') <span class="badge" style="background: #1e3a8a;">دورة</span> @break
                                                @case('seminar') <span class="badge" style="background: #6b7280;">ندوة</span> @break
                                                @case('internship') <span class="badge" style="background: #10b981;">تدريب عملي</span> @break
                                                @default <span class="badge" style="background: #f59e0b;">{{ $training->type }}</span>
                                            @endswitch
                                        </td>
                                        <td style="color: #374151;">{{ $training->duration }}</td>
                                        <td>
                                            <span class="fw-bold" style="color: #374151;">{{ $training->start_date }}</span>
                                        </td>
                                        <td>
                                            <span class="badge fs-6" style="background: #374151;">{{ $training->seats }}</span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: {{ $training->status == 'active' ? '#10b981' : ($training->status == 'inactive' ? '#f59e0b' : '#6b7280') }};">
                                                {{ $training->status == 'active' ? 'نشط' : ($training->status == 'inactive' ? 'متوقف' : 'مكتمل') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.trainings.show', $training->id) }}" class="btn btn-sm" style="background: #0ea5e9; color: white;" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-sm" style="background: #f59e0b; color: white;" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white;" onclick="return confirm('هل أنت متأكد من حذف هذا البرنامج؟')" title="حذف">
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
                            <i class="fas fa-graduation-cap fa-4x mb-3" style="color: #9ca3af;"></i>
                            <h4 style="color: #6b7280;">لا توجد برامج تدريب حتى الآن</h4>
                            <p class="mb-4" style="color: #9ca3af;">يمكنك البدء بإضافة أول برنامج تدريب</p>
                            <a href="{{ route('admin.trainings.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
                                <i class="fas fa-plus me-2"></i>إضافة أول برنامج تدريب
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- قسم طلبات التدريب -->
    <div class="row mt-4" id="applicationsSection" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #fef3c7, #fef7cd); border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <h3 class="mb-0" style="color: #92400e; font-weight: 800;">
                        <i class="fas fa-users me-2"></i>طلبات التدريب
                    </h3>
                    <div class="d-flex gap-2">
                        <span class="badge fs-6" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                            إجمالي الطلبات: {{ $applications->count() }}
                        </span>
                        <span class="badge fs-6" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #92400e;">
                            قيد المراجعة: {{ $applications->where('status', 'pending')->count() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #92400e;">
                                    <tr>
                                        <th>#</th>
                                        <th>الخريج</th>
                                        <th>برنامج التدريب</th>
                                        <th>تاريخ التقديم</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                    <tr class="application-row">
                                        <td class="fw-bold" style="color: #92400e;">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong style="color: #374151;">{{ $application->user->name ?? 'غير محدد' }}</strong>
                                            <br><small class="text-muted">{{ $application->user->email ?? 'لا يوجد بريد' }}</small>
                                        </td>
                                        <td>
                                            <strong style="color: #1e3a8a;">{{ $application->training->title ?? 'غير محدد' }}</strong>
                                            <br><small class="text-muted">
                                                @if($application->training)
                                                    @switch($application->training->type)
                                                        @case('workshop') ورشة عمل @break
                                                        @case('course') دورة @break
                                                        @case('seminar') ندوة @break
                                                        @case('internship') تدريب عملي @break
                                                        @default {{ $application->training->type }}
                                                    @endswitch
                                                @else
                                                    نوع غير محدد
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-bold" style="color: #374151;">{{ $application->applied_at ? $application->applied_at->format('Y-m-d') : 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            @if($application->status == 'pending')
                                                <span class="badge fs-6" style="background: #f59e0b; color: #92400e;">قيد المراجعة</span>
                                            @elseif($application->status == 'approved')
                                                <span class="badge fs-6" style="background: #10b981; color: white;">مقبول</span>
                                            @else
                                                <span class="badge fs-6" style="background: #ef4444; color: white;">مرفوض</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($application->status == 'pending')
                                                <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm" style="background: #10b981; color: white;" title="موافقة">
                                                        <i class="fas fa-check"></i> قبول
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white;" title="رفض">
                                                        <i class="fas fa-times"></i> رفض
                                                    </button>
                                                </form>
                                                @endif

                                                @if($application->status != 'pending')
                                                <form action="{{ route('admin.applications.pending', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm" style="background: #f59e0b; color: #92400e;" title="إعادة للمراجعة">
                                                        <i class="fas fa-redo"></i> إعادة
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x mb-3" style="color: #9ca3af;"></i>
                            <h4 style="color: #6b7280;">لا توجد طلبات تدريب حالياً</h4>
                            <p style="color: #9ca3af;">سيظهر هنا جميع طلبات الخريجين لبرامج التدريب</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --university-blue: #1e3a8a;
    --university-gold: #d4af37;
    --primary-blue: #1e3a8a;
    --primary-dark: #1e40af;
    --primary-medium: #3b82f6;
    --primary-light: #60a5fa;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #0ea5e9;
}

.stat-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-right: 4px solid !important;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card {
    border-radius: 18px;
    border-right: 4px solid var(--university-gold);
}

.card-header {
    border-radius: 18px 18px 0 0 !important;
}

/* تأثير Hover سلس بدون اهتزاز */
.training-row:hover,
.application-row:hover {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.03), rgba(59, 130, 246, 0.03)) !important;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
    transition: all 0.2s ease-in-out;
}

/* إزالة الخطوط من الجدول لجعل المظهر أنظف */
.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table td, .table th {
    border: none;
    padding: 12px 15px;
}

.table tbody tr {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.table tbody tr:last-child {
    border-bottom: none;
}

.btn-group .btn {
    border-radius: 8px;
    margin: 2px;
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trainingsSection = document.getElementById('trainingsSection');
    const applicationsSection = document.getElementById('applicationsSection');
    const showTrainingsBtn = document.getElementById('showTrainings');
    const showApplicationsBtn = document.getElementById('showApplications');

    // عرض التدريبات وإخفاء الطلبات
    showTrainingsBtn.addEventListener('click', function() {
        trainingsSection.style.display = 'block';
        applicationsSection.style.display = 'none';
        showTrainingsBtn.classList.remove('btn-outline-primary');
        showTrainingsBtn.classList.add('btn-primary');
        showApplicationsBtn.classList.remove('btn-primary');
        showApplicationsBtn.classList.add('btn-outline-warning');
    });

    // عرض الطلبات وإخفاء التدريبات
    showApplicationsBtn.addEventListener('click', function() {
        trainingsSection.style.display = 'none';
        applicationsSection.style.display = 'block';
        showApplicationsBtn.classList.remove('btn-outline-warning');
        showApplicationsBtn.classList.add('btn-primary');
        showTrainingsBtn.classList.remove('btn-primary');
        showTrainingsBtn.classList.add('btn-outline-primary');
    });

    // في البداية، إظهار التدريبات فقط
    trainingsSection.style.display = 'block';
    applicationsSection.style.display = 'none';
});
</script>
@endsection