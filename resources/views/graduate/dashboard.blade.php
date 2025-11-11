@extends('layouts.app')

@section('title', 'لوحة تحكم الخريج')

@section('content')
<div class="container-fluid">
    <!-- رسائل التنبيه -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- بطاقات الإحصائيات -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                التدريبات المتاحة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTrainings }}</div>
                            <div class="mt-2">
                                <a href="{{ route('graduate.trainings') }}" class="text-decoration-none small">
                                    عرض جميع التدريبات <i class="fas fa-arrow-left ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="card-icon bg-gradient-primary">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                طلباتي
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $myApplications }}</div>
                            <div class="mt-2">
                                <span class="text-muted small">إجمالي الطلبات المقدمة</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="card-icon bg-gradient-success">
                                <i class="fas fa-paper-plane fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                قيد المراجعة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingApplications }}</div>
                            <div class="mt-2">
                                <span class="text-muted small">بانتظار الموافقة</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="card-icon bg-gradient-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                مقبولة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedApplications }}</div>
                            <div class="mt-2">
                                <span class="text-muted small">طلبات تمت الموافقة عليها</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="card-icon bg-gradient-info">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="row">
        <!-- التدريبات الموصى بها -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star me-2 text-warning"></i>
                        التدريبات الموصى بها
                    </h6>
                    <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recommendedTrainings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recommendedTrainings as $training)
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="training-icon bg-light rounded p-2">
                                            <i class="fas fa-graduation-cap text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $training->title }}</h6>
                                        <p class="text-muted small mb-2">{{ Str::limit($training->description, 70) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $training->start_date }}
                                                </small>
                                                <small class="text-muted ms-3">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $training->seats }} مقاعد
                                                </small>
                                            </div>
                                            <span class="badge bg-primary">
                                                @if($training->type == 'workshop') ورشة عمل
                                                @elseif($training->type == 'course') دورة
                                                @elseif($training->type == 'seminar') ندوة
                                                @elseif($training->type == 'internship') تدريب عملي
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد تدريبات موصى بها حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- طلباتي الأخيرة -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2 text-info"></i>
                        طلباتي الأخيرة
                    </h6>
                    <span class="badge bg-primary">{{ $myApplications }}</span>
                </div>
                <div class="card-body">
                    @if($myRecentApplications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($myRecentApplications as $application)
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $application->training->title }}</h6>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-{{ $application->status == 'approved' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }} me-2">
                                                @if($application->status == 'pending') قيد المراجعة
                                                @elseif($application->status == 'approved') مقبول
                                                @elseif($application->status == 'rejected') مرفوض
                                                @endif
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $application->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chevron-left text-muted"></i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد طلبات سابقة</p>
                            <a href="{{ route('graduate.trainings') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-paper-plane me-2"></i>تقديم طلب جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-rocket me-2"></i>
                        الوصول السريع
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 text-center mb-3">
                            <a href="{{ route('graduate.trainings') }}" class="quick-link-card">
                                <div class="quick-link-icon bg-primary">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h6 class="mt-2 mb-1">التدريبات المتاحة</h6>
                                <p class="text-muted small">استعرض جميع التدريبات</p>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 text-center mb-3">
                            <a href="#" class="quick-link-card">
                                <div class="quick-link-icon bg-success">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h6 class="mt-2 mb-1">طلباتي</h6>
                                <p class="text-muted small">إدارة طلبات التدريب</p>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 text-center mb-3">
                            <a href="#" class="quick-link-card">
                                <div class="quick-link-icon bg-info">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <h6 class="mt-2 mb-1">الملف الشخصي</h6>
                                <p class="text-muted small">تحديث المعلومات</p>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 text-center mb-3">
                            <a href="#" class="quick-link-card">
                                <div class="quick-link-icon bg-warning">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <h6 class="mt-2 mb-1">المساعدة</h6>
                                <p class="text-muted small">الدعم والمساعدة</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-right: 4px solid var(--university-gold);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.bg-gradient-primary { background: linear-gradient(135deg, var(--university-blue), #3b82f6); }
.bg-gradient-success { background: linear-gradient(135deg, #10b981, #059669); }
.bg-gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.bg-gradient-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }

.training-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-link-card {
    display: block;
    text-decoration: none;
    color: inherit;
    padding: 20px 10px;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-link-card:hover {
    text-decoration: none;
    color: inherit;
    background: #f8fafc;
    border-color: var(--university-blue);
    transform: translateY(-3px);
}

.quick-link-icon {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto;
    font-size: 1.5rem;
}

.list-group-item {
    transition: background-color 0.3s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>
@endsection