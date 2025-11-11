@extends('layouts.app')

@section('title', 'تفاصيل الترشيح - ' . $nomination->graduate->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تفاصيل الترشيح</h3>
                    <a href="{{ route('admin.career-guidance.nominations') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- معلومات الخريج -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>معلومات الخريج</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>الاسم:</strong> {{ $nomination->graduate->name }}</p>
                                    <p><strong>البريد الإلكتروني:</strong> {{ $nomination->graduate->email }}</p>
                                    <p><strong>التخصص:</strong> {{ $nomination->graduate->major }}</p>
                                    <p><strong>سنة التخرج:</strong> {{ $nomination->graduate->graduation_year }}</p>
                                    <p><strong>حالة التوظيف:</strong> 
                                        <span class="badge bg-{{ $nomination->graduate->employment_status == 'employed' ? 'success' : 'warning' }}">
                                            {{ $nomination->graduate->employment_status == 'employed' ? 'موظف' : 'باحث عن عمل' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات الفرصة -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>معلومات الفرصة</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>العنوان:</strong> {{ $nomination->jobOpportunity->title }}</p>
                                    <p><strong>الشركة:</strong> {{ $nomination->jobOpportunity->company->name }}</p>
                                    <p><strong>النوع:</strong> 
                                        @if($nomination->jobOpportunity->type == 'job')
                                            وظيفة
                                        @elseif($nomination->jobOpportunity->type == 'training')
                                            تدريب
                                        @else
                                            تدريب عملي
                                        @endif
                                    </p>
                                    <p><strong>المكان:</strong> {{ $nomination->jobOpportunity->location }}</p>
                                    <p><strong>آخر موعد للتقديم:</strong> {{ $nomination->jobOpportunity->application_deadline->format('Y-m-d') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات الترشيح -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات الترشيح</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>حالة الترشيح:</strong>
                                        <span class="badge bg-{{ $nomination->status == 'accepted' ? 'success' : ($nomination->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $nomination->status_text }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>تاريخ الترشيح:</strong> {{ $nomination->nominated_at->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>تم الترشيح بواسطة:</strong> {{ $nomination->nominator->name }}</p>
                                </div>
                                <div class="col-12">
                                    <p><strong>أسباب الترشيح:</strong></p>
                                    <p class="bg-light p-3 rounded">{{ $nomination->matching_reasons }}</p>
                                </div>
                                @if($nomination->nomination_notes)
                                <div class="col-12">
                                    <p><strong>ملاحظات إضافية:</strong></p>
                                    <p class="bg-light p-3 rounded">{{ $nomination->nomination_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- معلومات المقابلة (إذا كانت مجدولة) -->
                    @if($nomination->interview_date)
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>معلومات المقابلة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>تاريخ المقابلة:</strong> {{ $nomination->interview_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>وقت المقابلة:</strong> {{ $nomination->interview_time }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>مكان المقابلة:</strong> {{ $nomination->interview_location }}</p>
                                </div>
                                @if($nomination->interview_notes)
                                <div class="col-12">
                                    <p><strong>ملاحظات المقابلة:</strong></p>
                                    <p class="bg-light p-3 rounded">{{ $nomination->interview_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
