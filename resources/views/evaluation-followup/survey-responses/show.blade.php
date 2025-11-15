@extends('layouts.app')

@section('title', 'عرض رد الاستبيان')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye mr-2"></i>
                        رد الاستبيان: {{ $response->survey->title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('evaluation-followup.survey-responses.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> العودة للقائمة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- معلومات المستجيب -->
                            <div class="mb-4">
                                <h5>معلومات المستجيب</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>الاسم:</strong>
                                                @if($response->user)
                                                    {{ $response->user->name }}
                                                @else
                                                    <span class="text-muted">مستخدم مجهول</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <strong>البريد الإلكتروني:</strong>
                                                @if($response->user)
                                                    {{ $response->user->email }}
                                                @else
                                                    <span class="text-muted">غير متوفر</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>الدور:</strong>
                                                @if($response->user)
                                                    <span class="badge badge-primary">{{ ucfirst($response->user->role) }}</span>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <strong>تاريخ الرد:</strong>
                                                {{ $response->created_at->format('Y-m-d H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ردود الاستبيان -->
                            <div class="mb-4">
                                <h5>ردود الاستبيان</h5>
                                @if($response->answers && count($response->answers) > 0)
                                    <div class="responses-list">
                                        @foreach($response->answers as $index => $answer)
                                            <div class="response-item border rounded p-3 mb-3">
                                                <div class="question mb-2">
                                                    <strong>{{ $index + 1 }}. {{ $answer['question'] }}</strong>
                                                </div>
                                                <div class="answer">
                                                    @if(is_array($answer['answer']))
                                                        <ul class="list-unstyled">
                                                            @foreach($answer['answer'] as $item)
                                                                <li><i class="fas fa-check-circle text-success mr-2"></i> {{ $item }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <div class="alert alert-light">
                                                            {{ $answer['answer'] ?: 'لم يتم الإجابة' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد ردود لهذا الاستبيان</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- معلومات الاستبيان -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-poll mr-2"></i>
                                        معلومات الاستبيان
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>عنوان الاستبيان:</strong>
                                        <p>{{ $response->survey->title }}</p>
                                    </div>

                                    @if($response->survey->description)
                                        <div class="mb-3">
                                            <strong>الوصف:</strong>
                                            <p class="text-muted">{{ $response->survey->description }}</p>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <strong>الجمهور المستهدف:</strong>
                                        <span class="badge badge-info">
                                            @switch($response->survey->target_audience)
                                                @case('graduates')
                                                    الخريجين
                                                    @break
                                                @case('companies')
                                                    الشركات
                                                    @break
                                                @case('training_coordinators')
                                                    منسقي التدريب
                                                    @break
                                                @case('all')
                                                    الكل
                                                    @break
                                            @endswitch
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>فترة الاستبيان:</strong>
                                        <p class="mb-1">
                                            من: {{ $response->survey->start_date->format('Y-m-d') }}
                                        </p>
                                        <p class="mb-0">
                                            إلى: {{ $response->survey->end_date->format('Y-m-d') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- إحصائيات -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar mr-2"></i>
                                        إحصائيات
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>إجمالي الأسئلة:</strong>
                                        <h4 class="text-primary">{{ count($response->survey->questions ?? []) }}</h4>
                                    </div>

                                    <div class="mb-3">
                                        <strong>الأسئلة المجاب عليها:</strong>
                                        <h4 class="text-success">{{ count($response->answers ?? []) }}</h4>
                                    </div>

                                    <div class="mb-3">
                                        <strong>معدل الإكمال:</strong>
                                        @php
                                            $totalQuestions = count($response->survey->questions ?? []);
                                            $answeredQuestions = count($response->answers ?? []);
                                            $completionRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                                        @endphp
                                        <div class="progress mt-2">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $completionRate }}%">
                                                {{ $completionRate }}%
                                            </div>
                                        </div>
                                    </div>
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
