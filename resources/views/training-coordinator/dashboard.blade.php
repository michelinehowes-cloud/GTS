@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التدريب')
@section('page-title', 'لوحة تحكم منسق التدريب')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'لوحة تحكم منسق التدريب', 'active' => true],
            ]
        ])

        <!-- إحصائيات سريعة -->
        <div class="row mb-4">
            @include('components.stat-card', [
                'title' => 'إجمالي برامج التدريب',
                'value' => $stats['totalTrainings'],
                'icon' => 'fas fa-calendar-alt',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])

            @include('components.stat-card', [
                'title' => 'البرامج النشطة',
                'value' => $stats['activeTrainings'],
                'icon' => 'fas fa-play-circle',
                'color' => 'success',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])

            @include('components.stat-card', [
                'title' => 'البرامج المتوقفة',
                'value' => $stats['inactiveTrainings'],
                'icon' => 'fas fa-pause-circle',
                'color' => 'warning',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])

            @include('components.stat-card', [
                'title' => 'المتدربين المسجلين',
                'value' => $stats['totalTrainees'],
                'icon' => 'fas fa-users',
                'color' => 'info',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])
        </div>

        <!-- صف إضافي للإحصائيات -->
        <div class="row mb-4">
            @include('components.stat-card', [
                'title' => 'إجمالي الطلبات',
                'value' => $stats['totalApplications'],
                'icon' => 'fas fa-clipboard-list',
                'color' => 'primary', // Changed from secondary for better visibility
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])

            @include('components.stat-card', [
                'title' => 'طلبات قيد المراجعة',
                'value' => $stats['pendingApplications'],
                'icon' => 'fas fa-clock',
                'color' => 'warning',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])

            @include('components.stat-card', [
                'title' => 'طلبات مقبولة',
                'value' => $stats['approvedApplications'],
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])

            @include('components.stat-card', [
                'title' => 'طلبات مرفوضة',
                'value' => $stats['rejectedApplications'],
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
                'col' => 'col-xl-3 col-md-6 mb-4'
            ])
        </div>

        <!-- محتوى إضافي -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card-modern h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-alt me-2"></i>أحدث طلبات التدريب
                        </h6>
                        <a href="{{ route('training-coordinator.applications') }}"
                            class="btn btn-sm btn-outline-primary-modern">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        @if($recentApplications->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($recentApplications as $application)
                                    <li class="list-group-item border-0 px-0">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold">{{ $application->user->name }}</h6>
                                            <small class="text-muted">{{ $application->applied_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-muted small">{{ $application->training->title }}</p>
                                            <span class="badge bg-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }}">
                                                {{ $application->status == 'pending' ? 'قيد المراجعة' : ($application->status == 'approved' ? 'مقبول' : 'مرفوض') }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">لا توجد طلبات تدريب حديثة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card-modern h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-graduation-cap me-2"></i>أحدث برامج التدريب
                        </h6>
                        <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-sm btn-outline-primary-modern">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        @if($recentTrainings->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($recentTrainings as $training)
                                    <li class="list-group-item border-0 px-0">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold">{{ $training->title }}</h6>
                                            <small class="text-muted">{{ $training->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-muted small">{{ $training->company->name ?? 'بدون شركة' }}</p>
                                            <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'inactive' ? 'warning' : 'info') }}">
                                                {{ $training->status == 'active' ? 'نشط' : ($training->status == 'inactive' ? 'متوقف' : 'مكتمل') }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">لا توجد برامج تدريب حديثة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقات سريعة للوصول -->
        <div class="row mb-4">
            <div class="col-md-4 mb-4">
                <div class="card-modern bg-primary text-white h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-graduation-cap fa-3x text-white-50"></i>
                        </div>
                        <h5 class="mb-2">برامج التدريب</h5>
                        <p class="text-white-50 mb-4">إدارة جميع برامج التدريب</p>
                        <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-light w-100 rounded-pill text-primary fw-bold">الدخول</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card-modern bg-success text-white h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-white-50"></i>
                        </div>
                        <h5 class="mb-2">طلبات التدريب</h5>
                        <p class="text-white-50 mb-4">مراجعة طلبات المتدربين</p>
                        <a href="{{ route('training-coordinator.applications') }}" class="btn btn-light w-100 rounded-pill text-success fw-bold">الدخول</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card-modern bg-warning text-white h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-plus-circle fa-3x text-white-50"></i>
                        </div>
                        <h5 class="mb-2">إضافة برنامج</h5>
                        <p class="text-white-50 mb-4">إضافة برنامج تدريب جديد</p>
                        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-light w-100 rounded-pill text-warning fw-bold">الدخول</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection