@extends('layouts.app')

@section('title', 'إدارة الإرشاد المهني')

@section('page-title', 'إدارة الإرشاد المهني')

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
                                إجمالي الخريجين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalGraduates'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x" style="color: #1e3a8a;"></i>
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
                                خريجين موظفين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['employedGraduates'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x" style="color: #10b981;"></i>
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
                                ترشيحات قيد المراجعة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingNominations'] ?? 0 }}</div>
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
                                إجمالي الترشيحات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalNominations'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x" style="color: #0ea5e9;"></i>
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
                            <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>إضافة خريج جديد
                            </a>
                            <button type="button" class="btn btn-outline-primary btn-lg" id="showGraduates">
                                <i class="fas fa-users me-2"></i>عرض الخريجين
                            </button>
                            <button type="button" class="btn btn-outline-info btn-lg" id="showNominations">
                                <i class="fas fa-list-alt me-2"></i>عرض الترشيحات
                            </button>
                            <a href="{{ route('admin.career-guidance.nominations.create') }}" class="btn btn-info btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>إضافة ترشيح
                            </a>
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

    <!-- قسم إدارة الخريجين -->
    <div class="row" id="graduatesSection">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%); border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <h3 class="mb-0" style="color: #1e3a8a; font-weight: 800;">
                        <i class="fas fa-users me-2"></i>إدارة الخريجين
                    </h3>
                    <span class="badge fs-6" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">عدد الخريجين: {{ $stats['totalGraduates'] ?? 0 }}</span>
                </div>
                <div class="card-body">
                    @if(($recentGraduates ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>التخصص</th>
                                        <th>سنة التخرج</th>
                                        <th>حالة التوظيف</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentGraduates as $graduate)
                                    <tr class="graduate-row">
                                        <td class="fw-bold" style="color: #1e3a8a;">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong style="color: #1e3a8a;">{{ $graduate->name }}</strong>
                                            <br><small class="text-muted">{{ $graduate->email }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold" style="color: #374151;">{{ $graduate->major }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold" style="color: #374151;">{{ $graduate->graduation_year }}</span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: {{ $graduate->employment_status == 'employed' ? '#10b981' : ($graduate->employment_status == 'seeking_opportunities' ? '#f59e0b' : '#6b7280') }};">
                                                {{ $graduate->employment_status == 'employed' ? 'موظف' : ($graduate->employment_status == 'seeking_opportunities' ? 'باحث عن عمل' : 'غير موظف') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.career-guidance.graduates.show', $graduate->id) }}" class="btn btn-sm" style="background: #0ea5e9; color: white;" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.career-guidance.graduates.edit', $graduate->id) }}" class="btn btn-sm" style="background: #f59e0b; color: white;" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Add delete form if needed -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x mb-3" style="color: #9ca3af;"></i>
                            <h4 style="color: #6b7280;">لا توجد بيانات خريجين حتى الآن</h4>
                            <p class="mb-4" style="color: #9ca3af;">يمكنك البدء بإضافة أول خريج أو استيراد البيانات</p>
                            <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
                                <i class="fas fa-plus me-2"></i>إضافة خريج جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- قسم إدارة الترشيحات -->
    <div class="row mt-4" id="nominationsSection" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #fef3c7, #fef7cd); border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <h3 class="mb-0" style="color: #92400e; font-weight: 800;">
                        <i class="fas fa-list-alt me-2"></i>إدارة الترشيحات
                    </h3>
                    <div class="d-flex gap-2">
                        <span class="badge fs-6" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                            إجمالي الترشيحات: {{ $stats['totalNominations'] ?? 0 }}
                        </span>
                        <span class="badge fs-6" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #92400e;">
                            قيد المراجعة: {{ $stats['pendingNominations'] ?? 0 }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if(($recentNominations ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #92400e;">
                                    <tr>
                                        <th>#</th>
                                        <th>الخريج</th>
                                        <th>فرصة العمل</th>
                                        <th>تاريخ الترشيح</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentNominations as $nomination)
                                    <tr class="nomination-row">
                                        <td class="fw-bold" style="color: #92400e;">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong style="color: #374151;">{{ $nomination->graduate->name ?? 'غير محدد' }}</strong>
                                            <br><small class="text-muted">{{ $nomination->graduate->email ?? 'لا يوجد بريد' }}</small>
                                        </td>
                                        <td>
                                            <strong style="color: #1e3a8a;">{{ $nomination->jobOpportunity->title ?? 'غير محدد' }}</strong>
                                            <br><small class="text-muted">
                                                @if($nomination->jobOpportunity)
                                                    {{ $nomination->jobOpportunity->company->name ?? 'غير محدد' }}
                                                @else
                                                    شركة غير محددة
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-bold" style="color: #374151;">{{ $nomination->nominated_at ? $nomination->nominated_at->format('Y-m-d') : 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge fs-6" style="background: {{ $nomination->status == 'pending' ? '#f59e0b' : ($nomination->status == 'accepted' ? '#10b981' : '#6b7280') }};">
                                                {{ $nomination->status == 'pending' ? 'قيد المراجعة' : ($nomination->status == 'accepted' ? 'مقبول' : 'أخرى') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.career-guidance.nominations.show', $nomination->id) }}" class="btn btn-sm" style="background: #0ea5e9; color: white;" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- Add edit/update status forms if needed -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane fa-4x mb-3" style="color: #9ca3af;"></i>
                            <h4 style="color: #6b7280;">لا توجد ترشيحات حالياً</h4>
                            <p style="color: #9ca3af;">سيظهر هنا جميع ترشيحات الخريجين لفرص العمل</p>
                            <a href="{{ route('admin.career-guidance.nominations.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
                                <i class="fas fa-plus me-2"></i>إضافة ترشيح جديد
                            </a>
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
.graduate-row:hover,
.nomination-row:hover {
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
    const graduatesSection = document.getElementById('graduatesSection');
    const nominationsSection = document.getElementById('nominationsSection');
    const showGraduatesBtn = document.getElementById('showGraduates');
    const showNominationsBtn = document.getElementById('showNominations');

    // عرض الخريجين وإخفاء الترشيحات
    showGraduatesBtn.addEventListener('click', function() {
        graduatesSection.style.display = 'block';
        nominationsSection.style.display = 'none';
        showGraduatesBtn.classList.remove('btn-outline-primary');
        showGraduatesBtn.classList.add('btn-primary');
        showNominationsBtn.classList.remove('btn-primary');
        showNominationsBtn.classList.add('btn-outline-info');
    });

    // عرض الترشيحات وإخفاء الخريجين
    showNominationsBtn.addEventListener('click', function() {
        graduatesSection.style.display = 'none';
        nominationsSection.style.display = 'block';
        showNominationsBtn.classList.remove('btn-outline-info');
        showNominationsBtn.classList.add('btn-primary');
        showGraduatesBtn.classList.remove('btn-primary');
        showGraduatesBtn.classList.add('btn-outline-primary');
    });

    // في البداية، إظهار الخريجين فقط
    graduatesSection.style.display = 'block';
    nominationsSection.style.display = 'none';
});
</script>
@endsection
