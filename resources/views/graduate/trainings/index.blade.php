@extends('layouts.app')

@section('title', 'البرامج التدريبية المتاحة')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="البرامج والدورات التدريبية"
        subtitle="تصفح البرامج التدريبية المعتمدة وورش العمل لتطوير مهاراتك وقدم عليها مباشرة"
        icon="fas fa-graduation-cap"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'البرامج التدريبية']
        ]"
        :badge="isset($trainings) && $trainings->count() > 0 ? (method_exists($trainings, 'total') ? $trainings->total() : $trainings->count()) . ' برنامج تدريبي' : null"
        badgeIcon="fas fa-certificate"
    >
        <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>فرص العمل</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($trainings->count() > 0)
        <div class="row g-3 g-md-4 mb-4">
            @foreach($trainings as $training)
                @php
                    $app = isset($myApplications) ? ($myApplications[$training->id] ?? null) : \App\Models\TrainingApplication::where('user_id', auth()->id())->where('training_id', $training->id)->first();
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden d-flex flex-column bg-white">
                        <!-- Top Badges -->
                        <div class="p-3 pb-2 d-flex justify-content-between align-items-center border-bottom">
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #eff6ff; color: #1d4ed8; font-size: 0.72rem;">
                                <i class="fas fa-graduation-cap me-1"></i>
                                @if($training->type == 'workshop') ورشة عمل
                                @elseif($training->type == 'course') دورة تدريبية
                                @elseif($training->type == 'seminar') ندوة
                                @elseif($training->type == 'internship') تدريب عملي
                                @else {{ $training->type }}
                                @endif
                            </span>
                            @if($app)
                                @if($app->status === 'approved')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold small">
                                        <i class="fas fa-check-circle me-1"></i> تم القبول
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-bold small">
                                        <i class="fas fa-times-circle me-1"></i> تم الرفض
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-15 text-dark rounded-pill px-3 py-1 fw-bold small">
                                        <i class="fas fa-clock me-1 text-warning"></i> قيد المراجعة
                                    </span>
                                @endif
                            @else
                                <span class="badge {{ $training->status == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-3 py-1 fw-bold small">
                                    {{ $training->status == 'active' ? 'متاح للتسجيل' : 'غير نشط' }}
                                </span>
                            @endif
                        </div>

                        <div class="card-body p-3 p-md-4 flex-grow-1 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-2 fs-6">{{ $training->title }}</h5>
                            <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $training->description ?: 'لا يوجد وصف إضافي متاح للبرنامج التدريبي.' }}
                            </p>

                            <!-- Modern Info Grid Chips -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm text-danger flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <small class="text-muted d-block" style="font-size: 0.68rem;">المكان</small>
                                            <span class="fw-bold text-dark small text-truncate d-block" style="font-size: 0.75rem;">{{ $training->location ?? 'جامعة طرابلس' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm text-info flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <small class="text-muted d-block" style="font-size: 0.68rem;">المدة</small>
                                            <span class="fw-bold text-dark small text-truncate d-block" style="font-size: 0.75rem;">{{ $training->duration ?? 'غير محددة' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 rounded-3 bg-light d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm text-warning flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.68rem;">تاريخ البدء</small>
                                            <span class="fw-bold text-dark small" style="font-size: 0.75rem;">{{ $training->start_date ? \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') : 'قريباً' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="mt-auto pt-3 border-top">
                                @if($app)
                                    <a href="{{ route('graduate.trainings.show', $training->id) }}" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-bold btn-sm d-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-info-circle"></i>
                                        <span>متابعة حالة الطلب</span>
                                        <i class="fas fa-arrow-left ms-auto small"></i>
                                    </a>
                                @else
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('graduate.trainings.apply', $training->id) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <input type="hidden" name="training_id" value="{{ $training->id }}">
                                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold btn-sm">
                                                <i class="fas fa-paper-plane me-1"></i> تقديم طلب
                                            </button>
                                        </form>
                                        <a href="{{ route('graduate.trainings.show', $training->id) }}" class="btn btn-light rounded-pill px-3 py-2 btn-sm text-muted">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 rounded-4 shadow-sm text-center py-5 bg-white">
            <div class="card-body py-5">
                <i class="fas fa-graduation-cap display-4 text-muted mb-3 opacity-50"></i>
                <h5 class="fw-bold text-dark mb-2">لا توجد تدريبات متاحة حالياً</h5>
                <p class="text-muted small mb-0">يرجى مراجعة هذه الصفحة لاحقاً للاطلاع على الدورات الجديدة فور إطلاقها</p>
            </div>
        </div>
    @endif
</div>
@endsection