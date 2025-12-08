@extends('layouts.app')

@section('title', 'تفاصيل البرنامج التدريبي')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة التدريب', 'url' => route('admin.trainings')],
            ['label' => Str::limit($training->title, 30), 'active' => true],
        ]
    ])

    <div class="row g-4 mb-4">
        <!-- Header Card -->
        <div class="col-12">
            <div class="card-modern">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="fas fa-graduation-cap me-2 text-secondary"></i>
                            {{ $training->title }}
                        </h4>
                        <div class="d-flex align-items-center gap-3 text-muted">
                            <small><i class="fas fa-building me-1"></i> {{ $training->company->name ?? 'غير محدد' }}</small>
                            <span class="text-light">|</span>
                            <small>
                                @if($training->status == 'active')
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> نشط</span>
                                @elseif($training->status == 'completed')
                                    <span class="text-info"><i class="fas fa-check-double me-1"></i> مكتمل</span>
                                @else
                                    <span class="text-muted"><i class="fas fa-pause-circle me-1"></i> غير نشط</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-warning-modern text-dark">
                            <i class="fas fa-edit me-2"></i>تعديل
                        </a>
                        <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger-modern" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                <i class="fas fa-trash me-2"></i>حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-3">
            @include('components.stat-card', [
                'title' => 'التاريخ',
                'value' => \Carbon\Carbon::parse($training->start_date)->format('Y-m-d'),
                'icon' => 'fas fa-calendar-alt',
                'color' => 'primary',
                'description' => 'تاريخ البداية'
            ])
        </div>
        <div class="col-md-3">
            @include('components.stat-card', [
                'title' => 'المدة',
                'value' => $training->duration,
                'icon' => 'fas fa-clock',
                'color' => 'success',
                'description' => 'مدة البرنامج (أيام)'
            ])
        </div>
        <div class="col-md-3">
            @include('components.stat-card', [
                'title' => 'المقاعد',
                'value' => $training->seats,
                'icon' => 'fas fa-users',
                'color' => 'info',
                'description' => 'إجمالي المقاعد'
            ])
        </div>
        <div class="col-md-3">
            @include('components.stat-card', [
                'title' => 'النوع',
                'value' => ucfirst($training->type),
                'icon' => 'fas fa-tag',
                'color' => 'warning',
                'description' => 'تصنيف البرنامج'
            ])
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card-modern mb-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fas fa-file-alt me-2 text-primary"></i> تفاصيل المحتوى
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-2">وصف البرنامج</h6>
                        <p class="text-muted bg-light p-3 rounded" style="line-height: 1.8;">{{ $training->description }}</p>
                    </div>

                    @if($training->objectives)
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-2">الأهداف</h6>
                        <div class="text-muted bg-light p-3 rounded" style="line-height: 1.8;">{{ $training->objectives }}</div>
                    </div>
                    @endif

                    @if($training->requirements)
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-2">متطلبات الالتحاق</h6>
                        <div class="text-muted bg-light p-3 rounded" style="line-height: 1.8;">{{ $training->requirements }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card-modern mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fas fa-info-circle me-2 text-info"></i> معلومات إضافية
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted"><i class="fas fa-layer-group me-2 w-20"></i>الفئة</span>
                            <span class="fw-bold text-dark">{{ $training->category ?? '--' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted"><i class="fas fa-map-marker-alt me-2 w-20"></i>المكان</span>
                            <span class="fw-bold text-dark">{{ $training->location }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted"><i class="fas fa-user-tie me-2 w-20"></i>المدرب</span>
                            <span class="fw-bold text-dark">{{ $training->instructor_name ?? '--' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted"><i class="fas fa-graduation-cap me-2 w-20"></i>المدرب (مؤهلات)</span>
                            <span class="fw-bold text-dark text-end ps-4 small">{{ Str::limit($training->instructor_qualifications ?? '--', 20) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted"><i class="fas fa-user-cog me-2 w-20"></i>المنسق</span>
                            <span class="fw-bold text-dark">{{ $training->coordinator->name ?? '--' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted"><i class="fas fa-calendar-check me-2 w-20"></i>تاريخ الانتهاء</span>
                            <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($training->end_date)->format('Y-m-d') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Meta Info -->
            <div class="card-modern bg-light border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">تاريخ الإنشاء:</small>
                        <small class="fw-bold">{{ $training->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">آخر تحديث:</small>
                        <small class="fw-bold">{{ $training->updated_at->format('Y-m-d H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection