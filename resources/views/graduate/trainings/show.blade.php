@extends('layouts.app')

@section('title', 'تفاصيل التدريب - ' . $training->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تفاصيل برنامج التدريب</h3>
                    <a href="{{ route('graduate.trainings') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-primary">{{ $training->title }}</h4>
                            <p class="text-muted">{{ $training->description }}</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-calendar me-2 text-warning"></i>معلومات البرنامج</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>النوع:</strong> 
                                            @switch($training->type)
                                                @case('workshop') ورشة عمل @break
                                                @case('course') دورة @break
                                                @case('seminar') ندوة @break
                                                @case('internship') تدريب عملي @break
                                            @endswitch
                                        </li>
                                        <li><strong>المدة:</strong> {{ $training->duration }}</li>
                                        <li><strong>تاريخ البدء:</strong> {{ $training->start_date }}</li>
                                        <li><strong>تاريخ الانتهاء:</strong> {{ $training->end_date }}</li>
                                        <li><strong>المكان:</strong> {{ $training->location }}</li>
                                        <li><strong>المقاعد المتاحة:</strong> {{ $training->seats }}</li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="fas fa-building me-2 text-info"></i>معلومات الجهة المنظمة</h6>
                                    @if($training->company)
                                        <ul class="list-unstyled">
                                            <li><strong>الشركة:</strong> {{ $training->company->name }}</li>
                                            <li><strong>المجال:</strong> {{ $training->company->industry }}</li>
                                            <li><strong>الموقع:</strong> 
                                                @if($training->company->website)
                                                    <a href="{{ $training->company->website }}" target="_blank">{{ $training->company->website }}</a>
                                                @else
                                                    غير متوفر
                                                @endif
                                            </li>
                                        </ul>
                                    @else
                                        <p class="text-muted">غير محدد</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">التسجيل في البرنامج</h5>
                                    <p class="text-muted">يمكنك التقديم لهذا البرنامج إذا كنت مهتماً</p>
                                    
                                    @php
                                        $application = \App\Models\TrainingApplication::where('user_id', auth()->id())
                                            ->where('training_id', $training->id)
                                            ->first();
                                    @endphp
                                    
                                    @if($application)
                                        @if($application->status === 'approved')
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <strong>تم قبول طلبك!</strong><br>
                                                <small>تهانينا! تم قبولك في برنامج التدريب. يمكنك البدء في التدريب.</small>
                                            </div>
                                        @elseif($application->status === 'rejected')
                                            <div class="alert alert-danger">
                                                <i class="fas fa-times-circle me-2"></i>
                                                <strong>تم رفض طلبك</strong><br>
                                                <small>نأسف لإبلاغك بأنه تم رفض طلبك لهذا البرنامج. يمكنك التقديم على برامج أخرى.</small>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>لقد قدمت طلباً لهذا البرنامج</strong><br>
                                                <small>طلبك قيد المراجعة من قبل المسؤول</small>
                                            </div>
                                        @endif
                                    @else
                                        <form action="{{ route('graduate.trainings.apply', $training->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                                <i class="fas fa-paper-plane me-2"></i>
                                                تقديم طلب التسجيل
                                            </button>
                                        </form>
                                        
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                سيتم مراجعة طلبك من قبل المسؤول
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
@endsection