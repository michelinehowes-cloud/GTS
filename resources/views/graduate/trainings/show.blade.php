@extends('layouts.app')

@section('title', 'تفاصيل التدريب - ' . $training->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل برنامج التدريب</h6>
                    <a href="{{ route('graduate.trainings') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="text-primary mb-3">{{ $training->title }}</h3>
                            <div class="mb-3">
                                <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }} me-2">
                                    @if($training->status == 'active') نشط
                                    @elseif($training->status == 'completed') مكتمل
                                    @else غير نشط
                                    @endif
                                </span>
                                <span class="badge bg-primary me-2">{{ $training->category ?? 'عام' }}</span>
                                <span class="badge bg-info text-dark">
                                    @switch($training->type)
                                        @case('workshop') ورشة عمل @break
                                        @case('course') دورة @break
                                        @case('seminar') ندوة @break
                                        @case('internship') تدريب عملي @break
                                        @default {{ $training->type }}
                                    @endswitch
                                </span>
                            </div>

                            <div class="card bg-light mb-4 border-0">
                                <div class="card-body">
                                    <p class="lead mb-0">{{ $training->description }}</p>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-calendar-alt me-2 text-warning"></i>تفاصيل التوقيت والمكان</h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">المدة:</strong> {{ $training->duration }}</li>
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">تاريخ البدء:</strong> {{ \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') }}</li>
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">تاريخ الانتهاء:</strong> {{ \Carbon\Carbon::parse($training->end_date)->format('Y-m-d') }}</li>
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">المكان:</strong> {{ $training->location }}</li>
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">المقاعد المتاحة:</strong> {{ $training->seats }}</li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-building me-2 text-info"></i>معلومات إضافية</h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">الشركة المنظمة:</strong> {{ $training->company->name ?? 'غير محدد' }}</li>
                                        @if($training->company && $training->company->industry)
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">المجال:</strong> {{ $training->company->industry }}</li>
                                        @endif
                                        @if($training->company && $training->company->website)
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">الموقع الإلكتروني:</strong> <a href="{{ $training->company->website }}" target="_blank">{{ $training->company->website }}</a></li>
                                        @endif
                                        <li class="list-group-item bg-transparent px-0"><strong class="text-muted">اسم المدرب:</strong> {{ $training->instructor_name ?? 'غير محدد' }}</li>
                                    </ul>
                                </div>
                            </div>

                            @if($training->objectives)
                            <div class="mt-4">
                                <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-bullseye me-2 text-danger"></i>أهداف البرنامج</h6>
                                <div class="p-3 bg-white border rounded">
                                    {{ $training->objectives }}
                                </div>
                            </div>
                            @endif

                            @if($training->requirements)
                            <div class="mt-4">
                                <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-tasks me-2 text-success"></i>متطلبات البرنامج</h6>
                                <div class="p-3 bg-white border rounded">
                                    {{ $training->requirements }}
                                </div>
                            </div>
                            @endif

                            @if($training->instructor_qualifications)
                            <div class="mt-4">
                                <h6 class="text-dark font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-user-graduate me-2 text-primary"></i>مؤهلات المدرب</h6>
                                <div class="p-3 bg-white border rounded">
                                    {{ $training->instructor_qualifications }}
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 bg-light sticky-top" style="top: 20px; z-index: 1;">
                                <div class="card-body text-center p-4">
                                    <h5 class="text-primary font-weight-bold mb-3">التسجيل في البرنامج</h5>
                                    <p class="text-muted mb-4">هل أنت مهتم بهذا البرنامج التدريبي؟ قدم طلبك الآن.</p>
                                    
                                    @php
                                        $application = \App\Models\TrainingApplication::where('user_id', auth()->id())
                                            ->where('training_id', $training->id)
                                            ->first();
                                    @endphp
                                    
                                    @if($application)
                                        @if($application->status === 'approved')
                                            <div class="alert alert-success border-0 shadow-sm">
                                                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                                <strong>تم قبول طلبك!</strong><br>
                                                <small>تهانينا! يمكنك البدء في التدريب.</small>
                                            </div>
                                        @elseif($application->status === 'rejected')
                                            <div class="alert alert-danger border-0 shadow-sm">
                                                <i class="fas fa-times-circle fa-2x mb-2"></i><br>
                                                <strong>تم رفض طلبك</strong><br>
                                                <small>نأسف، تم رفض طلبك لهذا البرنامج.</small>
                                            </div>
                                        @else
                                            <div class="alert alert-info border-0 shadow-sm">
                                                <i class="fas fa-clock fa-2x mb-2"></i><br>
                                                <strong>طلبك قيد المراجعة</strong><br>
                                                <small>سيتم إشعارك عند تحديث الحالة.</small>
                                            </div>
                                        @endif
                                    @else
                                        @if($training->status == 'active')
                                            <form action="{{ route('graduate.trainings.apply', $training->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm hover-scale">
                                                    <i class="fas fa-paper-plane me-2"></i>
                                                    تقديم طلب التسجيل
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-secondary border-0 shadow-sm">
                                                <i class="fas fa-lock fa-2x mb-2"></i><br>
                                                <strong>التسجيل مغلق</strong><br>
                                                <small>هذا البرنامج غير متاح للتسجيل حالياً.</small>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-3 text-start">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-info-circle me-1"></i>
                                                تأكد من تحديث ملفك الشخصي قبل التقديم.
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-check-circle me-1"></i>
                                                سيتم مراجعة طلبك من قبل المسؤول.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.02);
    }
</style>
@endsection