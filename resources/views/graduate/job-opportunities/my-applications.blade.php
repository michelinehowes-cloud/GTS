@extends('layouts.app')

@section('title', 'ترشيحاتي ومقابلاتي')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-list text-primary"></i>
                ترشيحاتي ومقابلاتي
            </h1>
            <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-primary">
                <i class="fas fa-search"></i> تصفح فرص العمل
            </a>
        </div>

        @if(!$graduateData)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                لم يتم العثور على بياناتك في النظام. يرجى التواصل مع مسؤول الإرشاد المهني.
            </div>
        @elseif($nominations->count() > 0)
            <div class="row">
                @foreach($nominations as $nomination)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div
                                class="card-header bg-{{ $nomination->status == 'pending' ? 'warning' : ($nomination->status == 'accepted' ? 'success' : ($nomination->status == 'interview_scheduled' ? 'info' : 'secondary')) }} text-white">
                                <h6 class="mb-0">
                                    <i
                                        class="fas fa-{{ $nomination->status == 'pending' ? 'clock' : ($nomination->status == 'accepted' ? 'check-circle' : ($nomination->status == 'interview_scheduled' ? 'calendar-check' : 'info-circle')) }}"></i>
                                    {{ $nomination->status_text }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $nomination->jobOpportunity->title }}</h5>

                                @if($nomination->jobOpportunity->company)
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-building"></i>
                                        {{ $nomination->jobOpportunity->company->name }}
                                    </p>
                                @endif

                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $nomination->jobOpportunity->location }}
                                </p>

                                <div class="mb-3">
                                    @if($nomination->nomination_type == 'self')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user"></i> ترشيح ذاتي
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-user-check"></i> ترشيح من المسؤول
                                        </span>
                                    @endif
                                </div>

                                <!-- معلومات المقابلة -->
                                @if($nomination->interview_date)
                                    <div class="alert alert-warning mb-3">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-calendar-check"></i> موعد المقابلة
                                        </h6>
                                        <hr>
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
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <strong>ملاحظات:</strong><br>
                                            {{ Str::limit($nomination->notes, 100) }}
                                        </small>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        {{ $nomination->created_at->diffForHumans() }}
                                    </small>
                                    <a href="{{ route('graduate.job-opportunities.show', $nomination->job_opportunity_id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>لا توجد ترشيحات حتى الآن</h4>
                    <p class="text-muted">ابدأ بتصفح فرص العمل المتاحة وقدم طلبك</p>
                    <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-primary">
                        <i class="fas fa-search"></i> تصفح فرص العمل
                    </a>
                </div>
            </div>
        @endif

        <!-- إحصائيات سريعة -->
        @if($graduateData && $nominations->count() > 0)
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="mb-0">{{ $nominations->where('status', 'pending')->count() }}</h2>
                                <small>قيد المراجعة</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="mb-0">{{ $nominations->where('status', 'interview_scheduled')->count() }}</h2>
                                <small>مقابلات مجدولة</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="mb-0">{{ $nominations->where('status', 'accepted')->count() }}</h2>
                                <small>مقبول</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white shadow">
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="mb-0">{{ $nominations->whereIn('status', ['rejected', 'withdrawn'])->count() }}</h2>
                                <small>مرفوض/ملغي</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
@endsection