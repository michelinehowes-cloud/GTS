@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التدريب')
@section('page-title', 'لوحة تحكم منسق التدريب')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'لوحة تحكم منسق التدريب', 'active' => true],
            ]
        ])

        <div class="row mb-4">
            <div class="col-12">
                <div class="bento-card" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9)); border-left: 4px solid var(--bento-primary);">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 70px; height: 70px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px;">مرحباً بك، {{ auth()->user()->name }} 👋</h2>
                            <p class="mb-0 text-white-50 fs-5"><i class="fas fa-tasks me-2"></i>منسق التدريب والبرامج التأهيلية</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة (Bento Grid) -->
        <div class="bento-grid-large mb-4">
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">إجمالي برامج التدريب</h3>
                    <div class="bento-card-icon bento-icon-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="bento-stat">{{ $stats['totalTrainings'] ?? 0 }}</div>
                <div class="bento-desc">جميع البرامج المسجلة تحت إشرافك</div>
            </div>

            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">البرامج النشطة</h3>
                    <div class="bento-card-icon bento-icon-success">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
                <div class="bento-stat">{{ $stats['activeTrainings'] ?? 0 }}</div>
                <div class="bento-desc">برامج قيد التنفيذ حالياً</div>
            </div>

            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">المتدربين المسجلين</h3>
                    <div class="bento-card-icon bento-icon-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="bento-stat">{{ $stats['totalTrainees'] ?? 0 }}</div>
                <div class="bento-desc">إجمالي الخريجين في برامجك</div>
            </div>

            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">طلبات جديدة</h3>
                    <div class="bento-card-icon bento-icon-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="bento-stat">{{ $stats['pendingApplications'] ?? 0 }}</div>
                <div class="bento-desc">طلبات التحاق بانتظار المراجعة</div>
            </div>
        </div>

        <!-- محتوى إضافي -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="bento-card h-100">
                    <div class="bento-card-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between align-items-center">
                        <h3 class="bento-card-title text-white fs-5 m-0">
                            <i class="fas fa-file-alt me-2 text-warning"></i>أحدث طلبات التدريب
                        </h3>
                        <a href="{{ route('training-coordinator.applications') }}" class="btn-bento-outline btn-sm py-1 px-3">عرض الكل</a>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($recentApplications) && $recentApplications->count() > 0)
                            <ul class="list-group list-group-flush bg-transparent">
                                @foreach($recentApplications as $application)
                                    <li class="list-group-item bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-white">{{ $application->user->name }}</h6>
                                            <small class="text-white-50">{{ $application->applied_at ? \Carbon\Carbon::parse($application->applied_at)->diffForHumans() : '' }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-white-50 small">{{ $application->training->title }}</p>
                                            <span class="bento-badge bento-badge-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }}">
                                                {{ $application->status == 'pending' ? 'قيد المراجعة' : ($application->status == 'approved' ? 'مقبول' : 'مرفوض') }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <p class="text-white-50 mb-0">لا توجد طلبات تدريب حديثة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="bento-card h-100">
                    <div class="bento-card-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between align-items-center">
                        <h3 class="bento-card-title text-white fs-5 m-0">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>أحدث برامج التدريب
                        </h3>
                        <a href="{{ route('training-coordinator.trainings') }}" class="btn-bento-outline btn-sm py-1 px-3">عرض الكل</a>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($recentTrainings) && $recentTrainings->count() > 0)
                            <ul class="list-group list-group-flush bg-transparent">
                                @foreach($recentTrainings as $training)
                                    <li class="list-group-item bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-white">{{ $training->title }}</h6>
                                            <small class="text-white-50">{{ $training->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 text-white-50 small">{{ $training->company->name ?? 'بدون شركة' }}</p>
                                            <span class="bento-badge bento-badge-{{ $training->status == 'active' ? 'success' : ($training->status == 'inactive' ? 'warning' : 'info') }}">
                                                {{ $training->status == 'active' ? 'نشط' : ($training->status == 'inactive' ? 'متوقف' : 'مكتمل') }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <p class="text-white-50 mb-0">لا توجد برامج تدريب حديثة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقات سريعة للوصول -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bento-card">
                    <div class="bento-card-header border-bottom border-secondary pb-3 mb-4">
                        <h3 class="bento-card-title text-white fs-4">
                            <i class="fas fa-bolt me-2 text-gold"></i>إجراءات سريعة
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="{{ route('training-coordinator.trainings') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none">
                                    <i class="fas fa-graduation-cap me-2 text-primary"></i>برامج التدريب
                                </a>
                            </div>
                            
                            <div class="col-md-4">
                                <a href="{{ route('training-coordinator.applications') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none">
                                    <i class="fas fa-users me-2 text-success"></i>طلبات التدريب
                                </a>
                            </div>
                            
                            <div class="col-md-4">
                                <a href="{{ route('training-coordinator.trainings.create') }}" class="btn-bento w-100 d-block text-center text-decoration-none" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <i class="fas fa-plus-circle me-2"></i>إضافة برنامج جديد
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection