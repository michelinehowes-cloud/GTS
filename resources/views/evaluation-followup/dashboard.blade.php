@extends('layouts.app')

@section('title', 'لوحة تحكم التقييم والمتابعة')

@section('page-title', 'لوحة تحكم التقييم والمتابعة')

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">إجمالي المستخدمين</h5>
                        <h3 class="mb-0">{{ $usersCount ?? 0 }}</h3>
                        <small class="text-muted">عدد المستخدمين المسجلين</small>
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
                        <h5 class="card-title text-primary">إجمالي الشركات</h5>
                        <h3 class="mb-0">{{ $companiesCount ?? 0 }}</h3>
                        <small class="text-muted">عدد الشركات المسجلة</small>
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
                        <h3 class="mb-0">{{ $trainingsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي برامج التدريب</small>
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
                        <h5 class="card-title text-primary">طلبات التدريب</h5>
                        <h3 class="mb-0">{{ $applicationsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي طلبات التدريب</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">طلبات قيد الانتظار</h5>
                        <h3 class="mb-0">{{ $pendingApplicationsCount ?? 0 }}</h3>
                        <small class="text-muted">طلبات التدريب المعلقة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-hourglass-half"></i>
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
                        <h5 class="card-title text-primary">الخريجون</h5>
                        <h3 class="mb-0">{{ $graduatesCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي بيانات الخريجين</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
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
                        <h5 class="card-title text-primary">وثائق الشراكة</h5>
                        <h3 class="mb-0">{{ $partnershipDocumentsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي وثائق الشراكة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-handshake"></i>
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
                        <h5 class="card-title text-primary">فرص العمل</h5>
                        <h3 class="mb-0">{{ $jobOpportunitiesCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي فرص العمل</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>توزيع المستخدمين حسب الدور
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($usersByRole as $role => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __($role) }}
                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>أحدث المستخدمين
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($recentUsers as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $user->name }} ({{ $user->role }})
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </li>
                    @empty
                        <li class="list-group-item">لا يوجد مستخدمون حديثون.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>أحدث الشركات
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($recentCompanies as $company)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $company->name }}
                            <small class="text-muted">{{ $company->created_at->diffForHumans() }}</small>
                        </li>
                    @empty
                        <li class="list-group-item">لا توجد شركات حديثة.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
