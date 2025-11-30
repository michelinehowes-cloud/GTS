@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-primary-blue">
                    <i class="fas fa-poll me-2"></i> الاستبيانات المتاحة
                </h2>
                <p class="text-muted">شاركنا رأيك وساعدنا في تحسين خدماتنا. الاستبيانات أدناه مخصصة لك بناءً على نشاطك.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($surveys->count() > 0)
            <div class="row">
                @foreach($surveys as $survey)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3 hover-card">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge 
                                                @if($survey->type == 'general') bg-secondary 
                                                @elseif($survey->type == 'training') bg-info 
                                                @elseif($survey->type == 'job_opportunity') bg-success 
                                                @else bg-primary @endif">
                                        @if($survey->type == 'general') عام
                                        @elseif($survey->type == 'training') تدريب
                                        @elseif($survey->type == 'job_opportunity') فرصة عمل
                                        @else نشاط @endif
                                    </span>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i> ينتهي في {{ $survey->end_date->format('Y-m-d') }}
                                    </small>
                                </div>

                                <h5 class="card-title fw-bold mb-3">{{ $survey->title }}</h5>
                                <p class="card-text text-muted flex-grow-1">{{ Str::limit($survey->description, 100) }}</p>

                                @if($survey->type == 'training' && $survey->training)
                                    <div class="mb-3 p-2 bg-light rounded small">
                                        <i class="fas fa-chalkboard-teacher text-info me-1"></i>
                                        مرتبط بـ: <strong>{{ $survey->training->title }}</strong>
                                    </div>
                                @elseif($survey->type == 'job_opportunity' && $survey->jobOpportunity)
                                    <div class="mb-3 p-2 bg-light rounded small">
                                        <i class="fas fa-briefcase text-success me-1"></i>
                                        مرتبط بـ: <strong>{{ $survey->jobOpportunity->title }}</strong>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    @if(in_array($survey->id, $answeredSurveyIds))
                                        <button class="btn btn-outline-success w-100" disabled>
                                            <i class="fas fa-check-circle me-2"></i> تم الإجابة
                                        </button>
                                    @else
                                        <a href="{{ route('graduate.surveys.show', $survey->id) }}" class="btn btn-primary w-100">
                                            <i class="fas fa-edit me-2"></i> بدء الاستبيان
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Surveys" class="mb-4"
                    style="max-width: 200px; opacity: 0.5;">
                <h4 class="text-muted">لا توجد استبيانات متاحة حالياً</h4>
                <p class="text-muted">يرجى التحقق لاحقاً.</p>
            </div>
        @endif
    </div>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection