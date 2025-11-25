@extends('layouts.app')

@section('title', $jobOpportunity->title)

@section('content')
    <div class="container-fluid px-4">
        <div class="mb-4">
            <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة إلى القائمة
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h3 class="m-0 font-weight-bold text-primary">{{ $jobOpportunity->title }}</h3>
                        @if($nomination)
                            <span
                                class="badge bg-{{ $nomination->status == 'pending' ? 'warning' : ($nomination->status == 'accepted' ? 'success' : 'info') }} fs-6">
                                {{ $nomination->status_text }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($jobOpportunity->company)
                            <div class="mb-4">
                                <h5><i class="fas fa-building text-primary"></i> الشركة</h5>
                                <p class="lead">{{ $jobOpportunity->company->name }}</p>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-map-marker-alt text-danger"></i> الموقع:</strong>
                                    {{ $jobOpportunity->location }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-clock text-info"></i> نوع الوظيفة:</strong>
                                    {{ $jobOpportunity->job_type == 'full_time' ? 'دوام كامل' : ($jobOpportunity->job_type == 'part_time' ? 'دوام جزئي' : ($jobOpportunity->job_type == 'contract' ? 'عقد' : 'تدريب')) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-calendar text-warning"></i> آخر موعد للتقديم:</strong>
                                    {{ $jobOpportunity->deadline->format('Y-m-d') }}</p>
                            </div>
                            @if($jobOpportunity->salary_range)
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-money-bill text-success"></i> الراتب:</strong>
                                        {{ $jobOpportunity->salary_range }}</p>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h5><i class="fas fa-align-left text-primary"></i> الوصف الوظيفي</h5>
                            <p class="text-justify">{!! nl2br(e($jobOpportunity->description)) !!}</p>
                        </div>

                        @if($jobOpportunity->requirements)
                            <div class="mb-4">
                                <h5><i class="fas fa-list-check text-primary"></i> المتطلبات</h5>
                                <p class="text-justify">{!! nl2br(e($jobOpportunity->requirements)) !!}</p>
                            </div>
                        @endif

                        @if($jobOpportunity->responsibilities)
                            <div class="mb-4">
                                <h5><i class="fas fa-tasks text-primary"></i> المسؤوليات</h5>
                                <p class="text-justify">{!! nl2br(e($jobOpportunity->responsibilities)) !!}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- حالة الترشيح -->
                @if($nomination)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-success text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-check-circle"></i> حالة طلبك
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>الحالة:</strong>
                                <span
                                    class="badge bg-{{ $nomination->status == 'pending' ? 'warning' : ($nomination->status == 'accepted' ? 'success' : 'info') }}">
                                    {{ $nomination->status_text }}
                                </span>
                            </div>

                            @if($nomination->nomination_type == 'self')
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    قدمت طلبك بنفسك
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-user-check"></i>
                                    تم ترشيحك من قبل مسؤول الإرشاد المهني
                                </div>
                            @endif

                            @if($nomination->interview_date)
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-calendar-check"></i> موعد المقابلة</h6>
                                    <p class="mb-1"><strong>التاريخ:</strong> {{ $nomination->interview_date->format('Y-m-d') }}</p>
                                    @if($nomination->interview_time)
                                        <p class="mb-1"><strong>الوقت:</strong> {{ $nomination->interview_time }}</p>
                                    @endif
                                    @if($nomination->interview_location)
                                        <p class="mb-0"><strong>المكان:</strong> {{ $nomination->interview_location }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($nomination->notes)
                                <div class="mt-3">
                                    <strong>ملاحظات:</strong>
                                    <p class="text-muted">{{ $nomination->notes }}</p>
                                </div>
                            @endif

                            @if($nomination->status == 'pending')
                                <form action="{{ route('graduate.my-applications.cancel', $nomination->id) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الترشيح؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-times"></i> إلغاء الترشيح
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- نموذج التقديم -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-paper-plane"></i> قدم الآن
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(!$graduateData)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    يجب إكمال بياناتك الشخصية أولاً قبل التقديم
                                </div>
                            @elseif($jobOpportunity->deadline < now())
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i>
                                    انتهى موعد التقديم لهذه الفرصة
                                </div>
                            @else
                                <form action="{{ route('graduate.job-opportunities.apply', $jobOpportunity->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">ملاحظات إضافية (اختياري)</label>
                                        <textarea name="notes" class="form-control" rows="4"
                                            placeholder="أضف أي ملاحظات أو معلومات إضافية..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane"></i> تقديم الطلب
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- معلومات إضافية -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> معلومات إضافية
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>تاريخ النشر:</strong><br>
                            {{ $jobOpportunity->created_at->format('Y-m-d') }}
                        </p>
                        <p class="mb-0">
                            <strong>الحالة:</strong><br>
                            <span class="badge bg-{{ $jobOpportunity->status == 'active' ? 'success' : 'secondary' }}">
                                {{ $jobOpportunity->status == 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: '{{ session('success') }}',
                confirmButtonText: 'حسناً'
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: '{{ $errors->first() }}',
                confirmButtonText: 'حسناً'
            });
        </script>
    @endif
@endsection