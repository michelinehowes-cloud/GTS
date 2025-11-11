@extends('layouts.app')

@section('title', 'التقارير والإحصائيات')

@section('page-title', 'التقارير والإحصائيات')

@section('content')
<!-- إحصائيات سريعة -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">إجمالي المستخدمين</h5>
                        <h3 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h3>
                        <small class="text-success">+{{ $stats['recent_users'] ?? 0 }} جديد</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">الشركات المسجلة</h5>
                        <h3 class="mb-0">{{ $stats['total_companies'] ?? 0 }}</h3>
                        <small class="text-success">نشطة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">برامج التدريب</h5>
                        <h3 class="mb-0">{{ $stats['total_trainings'] ?? 0 }}</h3>
                        <small class="text-success">نشطة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">مستخدمين جدد</h5>
                        <h3 class="mb-0">{{ $stats['recent_users'] ?? 0 }}</h3>
                        <small class="text-success">آخر 30 يوم</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- توزيع المستخدمين حسب الدور -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>توزيع المستخدمين حسب الدور
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="stat-item">
                            <h4 class="text-danger">{{ $usersByRole['admin'] ?? 0 }}</h4>
                            <small class="text-muted">مدير النظام</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-item">
                            <h4 class="text-warning">{{ $usersByRole['training_coordinator'] ?? 0 }}</h4>
                            <small class="text-muted">منسق تدريب</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-item">
                            <h4 class="text-primary">{{ $usersByRole['graduate'] ?? 0 }}</h4>
                            <small class="text-muted">خريجين</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-item">
                            <h4 class="text-success">{{ $usersByRole['company'] ?? 0 }}</h4>
                            <small class="text-muted">شركات</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.users') ?? '#' }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-users me-2"></i>إدارة المستخدمين
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.companies') ?? '#' }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-building me-2"></i>إدارة الشركات
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.trainings') ?? '#' }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-graduation-cap me-2"></i>إدارة التدريبات
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.applications.index') ?? '#' }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-clipboard-list me-2"></i>طلبات التدريب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- أحدث المستخدمين -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>أحدث المستخدمين
                </h5>
                <a href="{{ route('admin.users') ?? '#' }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentUsers as $user)
                    <div class="list-group-item d-flex align-items-center">
                        <div class="user-avatar-sm me-3" style="width: 35px; height: 35px; background: #1e3a8a; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge 
                                @if($user->role == 'admin') bg-danger
                                @elseif($user->role == 'training_coordinator') bg-warning
                                @elseif($user->role == 'company') bg-info
                                @else bg-success @endif">
                                @if($user->role == 'admin') مدير
                                @elseif($user->role == 'training_coordinator') منسق تدريب
                                @elseif($user->role == 'company') شركة
                                @else خريج @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">لا توجد مستخدمين حديثين</p>
                @endif
            </div>
        </div>
    </div>

    <!-- أحدث الشركات -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>أحدث الشركات
                </h5>
                <a href="{{ route('admin.companies') ?? '#' }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @if($recentCompanies->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentCompanies as $company)
                    <div class="list-group-item">
                        <h6 class="mb-1">{{ $company->name }}</h6>
                        <small class="text-muted">{{ $company->industry ?? 'غير محدد' }} • {{ $company->created_at->format('Y-m-d') }}</small>
                        <div class="mt-1">
                            <span class="badge {{ $company->is_approved ? 'bg-success' : 'bg-warning' }}">
                                {{ $company->is_approved ? 'موافق عليها' : 'قيد المراجعة' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">لا توجد شركات حديثة</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid #e9ecef;
}
.stat-card {
    border-right: 4px solid #d4af37;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafe 100%);
}
.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #1e3a8a, #1e40af);
}
</style>
@endsection