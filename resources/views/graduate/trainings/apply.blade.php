@extends('layouts.app')

@section('title', 'التدريبات المتاحة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>التدريبات المتاحة للالتحاق</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($trainings->count() > 0)
                    <div class="row">
                        @foreach($trainings as $training)
                        <div class="col-md-6 mb-4">
                            <div class="card training-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $training->title }}</h5>
                                    <p class="card-text">{{ $training->description }}</p>
                                    
                                    <div class="training-details mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i> {{ $training->duration }}
                                        </small>
                                        <small class="text-muted mx-2">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $training->location }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i> {{ $training->seats }} مقاعد
                                        </small>
                                    </div>

                                    <div class="training-details mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i> يبدأ: {{ $training->start_date->format('Y-m-d') }}
                                        </small>
                                        <small class="text-muted mx-2">
                                            <i class="fas fa-calendar-check me-1"></i> ينتهي: {{ $training->end_date->format('Y-m-d') }}
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-primary">
                                            @switch($training->type)
                                                @case('workshop') ورشة عمل @break
                                                @case('course') دورة @break
                                                @case('seminar') ندوة @break
                                                @case('internship') تدريب عملي @break
                                            @endswitch
                                        </span>
                                        <span class="badge bg-success">نشط</span>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <!-- استخدم Form مباشرة -->
                                        <form action="{{ route('graduate.trainings.apply') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="training_id" value="{{ $training->id }}">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-paper-plane me-1"></i>التقديم للتدريب
                                            </button>
                                        </form>
                                        <button class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-info-circle me-1"></i>تفاصيل أكثر
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد تدريبات متاحة حالياً</h5>
                        <p class="text-muted">يرجى مراجعة هذه الصفحة لاحقاً للاطلاع على التدريبات الجديدة</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.training-card {
    transition: transform 0.3s ease;
    border-right: 4px solid #1e3a8a;
    height: 100%;
}
.training-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endsection