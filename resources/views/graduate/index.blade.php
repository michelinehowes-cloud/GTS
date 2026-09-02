@extends('layouts.app')

@section('title', 'البرامج التدريبية المتاحة')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'البرامج التدريبية', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-graduation-cap me-2"></i>البرامج والدورات التدريبية
            </h2>
            <div class="text-muted small mt-1">تصفح البرامج التدريبية وورش العمل المتاحة للالتحاق والتطوير المهني</div>
        </div>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($trainings->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($trainings as $training)
                @php
                    $app = \App\Models\TrainingApplication::where('user_id', auth()->id())->where('training_id', $training->id)->first();
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card-modern h-100 d-flex flex-column">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1">
                                @switch($training->type)
                                    @case('workshop') ورشة عمل @break
                                    @case('course') دورة @break
                                    @case('seminar') ندوة @break
                                    @case('internship') تدريب عملي @break
                                    @default {{ $training->type }}
                                @endswitch
                            </span>
                            @if($app)
                                @if($app->status === 'approved')
                                    <span class="badge bg-light text-success border border-success rounded-pill px-2 py-1 small">
                                        <i class="fas fa-check-circle me-1"></i> تم القبول
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="badge bg-light text-danger border border-danger rounded-pill px-2 py-1 small">
                                        <i class="fas fa-times-circle me-1"></i> تم الرفض
                                    </span>
                                @else
                                    <span class="badge bg-light text-warning border border-warning rounded-pill px-2 py-1 small">
                                        <i class="fas fa-clock me-1"></i> قيد المراجعة
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-light text-success border border-success rounded-pill px-2 py-1 small">
                                    نشط
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-4 flex-grow-1">
                            <h5 class="fw-bold text-dark mb-2">{{ $training->title }}</h5>
                            <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit($training->description, 130) }}
                            </p>

                            <div class="p-3 bg-light rounded-3 mb-3 small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> المكان:</span>
                                    <span class="fw-bold text-dark">{{ $training->location ?? 'جامعة طرابلس' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fas fa-clock text-info me-1"></i> المدة:</span>
                                    <span class="fw-bold text-dark">{{ $training->duration ?? 'غير محددة' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="fas fa-calendar-alt text-warning me-1"></i> البداية:</span>
                                    <span class="fw-bold text-dark">{{ $training->start_date ? \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') : 'قريباً' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center gap-2">
                            @if($app)
                                <a href="{{ route('graduate.trainings.show', $training->id) }}" class="btn btn-sm btn-outline-primary-modern w-100 py-2">
                                    <i class="fas fa-info-circle me-1"></i> متابعة حالة الطلب
                                </a>
                            @else
                                <form action="{{ route('graduate.trainings.apply', $training->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <input type="hidden" name="training_id" value="{{ $training->id }}">
                                    <button type="submit" class="btn btn-sm btn-primary-modern w-100">
                                        <i class="fas fa-paper-plane me-1"></i> التقديم للتدريب
                                    </button>
                                </form>
                                <a href="{{ route('graduate.trainings.show', $training->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye me-1"></i> التفاصيل
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-modern text-center py-5">
            <div class="card-body py-5">
                <i class="fas fa-graduation-cap display-3 text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">لا توجد تدريبات متاحة حالياً</h4>
                <p class="text-muted mb-0">يرجى مراجعة هذه الصفحة لاحقاً للاطلاع على التدريبات الجديدة</p>
            </div>
        </div>
    @endif
</div>
@endsection