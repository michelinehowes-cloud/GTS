@extends('layouts.app')

@section('title', 'عرض الاستبيان')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-poll mr-2"></i>
                        {{ $survey->title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('evaluation-followup.surveys.edit', $survey) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('evaluation-followup.surveys.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5>معلومات الاستبيان</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">العنوان:</th>
                                        <td>{{ $survey->title }}</td>
                                    </tr>
                                    @if($survey->description)
                                    <tr>
                                        <th>الوصف:</th>
                                        <td>{{ $survey->description }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>الجمهور المستهدف:</th>
                                        <td>
                                            <span class="badge badge-info">
                                                @switch($survey->target_audience)
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
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ البداية:</th>
                                        <td>{{ $survey->start_date->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ النهاية:</th>
                                        <td>{{ $survey->end_date->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>الحالة:</th>
                                        <td>
                                            @if($survey->isActive())
                                                <span class="badge badge-success">نشط</span>
                                            @elseif($survey->is_active)
                                                <span class="badge badge-warning">مجدول</span>
                                            @else
                                                <span class="badge badge-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>عدد الردود:</th>
                                        <td>
                                            <span class="badge badge-primary">{{ $survey->responses_count ?? 0 }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mb-4">
                                <h5>أسئلة الاستبيان</h5>
                                @if($survey->questions && count($survey->questions) > 0)
                                    <div class="questions-list">
                                        @foreach($survey->questions as $index => $question)
                                            <div class="question-item border rounded p-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-2">
                                                            {{ $index + 1 }}. {{ $question['question'] }}
                                                            @if(isset($question['required']) && $question['required'])
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </h6>

                                                        <div class="question-type mb-2">
                                                            <small class="text-muted">
                                                                نوع السؤال:
                                                                @switch($question['type'])
                                                                    @case('text')
                                                                        نص حر
                                                                        @break
                                                                    @case('radio')
                                                                        اختيار واحد
                                                                        @break
                                                                    @case('checkbox')
                                                                        اختيار متعدد
                                                                        @break
                                                                    @case('select')
                                                                        قائمة منسدلة
                                                                        @break
                                                                @endswitch
                                                            </small>
                                                        </div>

                                                        @if(in_array($question['type'], ['radio', 'checkbox', 'select']) && isset($question['options']))
                                                            <div class="question-options">
                                                                <strong>الخيارات:</strong>
                                                                <ul class="list-unstyled ml-3">
                                                                    @foreach($question['options'] as $option)
                                                                        <li><i class="fas fa-circle text-primary mr-2" style="font-size: 8px;"></i> {{ $option }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد أسئلة في هذا الاستبيان</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar mr-2"></i>
                                        إحصائيات الاستبيان
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>معدل الإكمال:</strong>
                                        <div class="progress mt-2">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $survey->responses_count > 0 ? 100 : 0 }}%">
                                                {{ $survey->responses_count > 0 ? '100%' : '0%' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong>عدد الردود:</strong>
                                        <h4 class="text-primary">{{ $survey->responses_count ?? 0 }}</h4>
                                    </div>

                                    <div class="mb-3">
                                        <strong>الفترة المتبقية:</strong>
                                        @if($survey->isActive())
                                            <span class="text-success">
                                                {{ $survey->end_date->diffInDays(now()) }} يوم
                                            </span>
                                        @elseif($survey->start_date->isFuture())
                                            <span class="text-warning">
                                                يبدأ في {{ $survey->start_date->diffInDays(now()) }} يوم
                                            </span>
                                        @else
                                            <span class="text-danger">انتهى</span>
                                        @endif
                                    </div>

                                    <hr>

                                    <div class="text-center">
                                        <a href="{{ route('evaluation-followup.survey-responses.index') }}?survey_id={{ $survey->id }}"
                                           class="btn btn-info btn-block">
                                            <i class="fas fa-list"></i> عرض الردود
                                        </a>
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
