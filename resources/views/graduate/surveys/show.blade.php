@extends('layouts.app')

@section('title', $survey->title)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'الاستبيانات المتاحة', 'url' => route('graduate.surveys.index')],
            ['label' => $survey->title, 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-poll me-2"></i>{{ $survey->title }}
            </h2>
            <div class="text-muted small mt-1">
                <i class="far fa-clock me-1 text-warning"></i> تاريخ الانتهاء: {{ $survey->end_date->format('Y-m-d') }}
            </div>
        </div>
        <a href="{{ route('graduate.surveys.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> العودة للاستبيانات
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card-modern mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-info-circle me-2"></i>تعليمات ونبذة عن الاستبيان
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-dark lh-lg mb-0">{{ $survey->description }}</p>

                    @if(isset($existingResponse) && $existingResponse)
                        <div class="alert alert-info border-0 rounded-3 mt-3 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>تعديل الإجابات:</strong> لقد أجبت على هذا الاستبيان مسبقاً. يمكنك تعديل إجاباتك أدناه وحفظ التعديلات قبل انتهاء مدة الاستبيان ({{ $survey->end_date->format('Y-m-d') }}).
                        </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('graduate.surveys.store', $survey->id) }}" method="POST">
                @csrf
                @php
                    $previousAnswers = isset($existingResponse) ? $existingResponse->answers : [];
                @endphp

                <div class="card-modern mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-list-check me-2"></i>أسئلة الاستبيان
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach($survey->questions as $index => $question)
                            <div class="mb-4 p-4 rounded-3 bg-light border">
                                <label class="form-label-modern fw-bold mb-3 d-flex align-items-center">
                                    <span class="badge bg-primary rounded-circle p-2 me-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="fs-6">{{ $question['question'] }}</span>
                                    @if(isset($question['required']) && $question['required'])
                                        <span class="text-danger ms-1">*</span>
                                    @endif
                                </label>

                                @if($question['type'] === 'text')
                                    <input type="text" name="answers[{{ $index }}]" class="form-control-modern bg-white" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        placeholder="اكتب إجابتك هنا..."
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'textarea')
                                    <textarea name="answers[{{ $index }}]" class="form-control-modern bg-white" rows="3"
                                        placeholder="اكتب إجابتك التفصيلية هنا..."
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>{{ $previousAnswers[$index] ?? '' }}</textarea>
                                
                                @elseif($question['type'] === 'radio')
                                    <div class="ps-2">
                                        @foreach($question['options'] as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="answers[{{ $index }}]" 
                                                    id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}"
                                                    {{ isset($previousAnswers[$index]) && $previousAnswers[$index] == $option ? 'checked' : '' }}
                                                    {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                                <label class="form-check-label text-dark" for="q{{ $index }}_opt{{ $loop->index }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                
                                @elseif($question['type'] === 'checkbox')
                                    <div class="ps-2">
                                        @foreach($question['options'] as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][]" 
                                                    id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}"
                                                    {{ isset($previousAnswers[$index]) && is_array($previousAnswers[$index]) && in_array($option, $previousAnswers[$index]) ? 'checked' : '' }}>
                                                <label class="form-check-label text-dark" for="q{{ $index }}_opt{{ $loop->index }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                @elseif($question['type'] === 'select')
                                    <select name="answers[{{ $index }}]" class="form-select-modern bg-white"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                        <option value="" {{ !isset($previousAnswers[$index]) ? 'selected' : '' }} disabled>اختر إجابة من القائمة...</option>
                                        @foreach($question['options'] as $option)
                                            <option value="{{ $option }}" {{ isset($previousAnswers[$index]) && $previousAnswers[$index] == $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                
                                @elseif($question['type'] === 'rating')
                                    @php
                                        $maxRating = $question['max_rating'] ?? 5;
                                        $currentRating = $previousAnswers[$index] ?? '';
                                    @endphp
                                    <div class="rating-stars bg-white p-3 rounded-3 border" data-question-index="{{ $index }}">
                                        <input type="hidden" name="answers[{{ $index }}]" id="rating_{{ $index }}" value="{{ $currentRating }}" 
                                            {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                        <div class="d-flex gap-2 align-items-center flex-wrap">
                                            @for($i = 1; $i <= $maxRating; $i++)
                                                <span class="star" data-rating="{{ $i }}" style="font-size: 2.2rem; cursor: pointer; color: {{ $currentRating && $i <= $currentRating ? '#ffc107' : '#ddd' }}; user-select: none;">
                                                    ★
                                                </span>
                                            @endfor
                                            <span class="rating-display ms-3 fw-bold {{ $currentRating ? 'text-warning' : 'text-muted' }}">
                                                {{ $currentRating ? $currentRating . ($currentRating == 1 ? ' نجمة' : ' نجوم') : 'لم يتم التقييم بعد' }}
                                            </span>
                                        </div>
                                    </div>
                                
                                @elseif($question['type'] === 'date')
                                    <input type="date" name="answers[{{ $index }}]" class="form-control-modern bg-white" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'email')
                                    <input type="email" name="answers[{{ $index }}]" class="form-control-modern bg-white" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        placeholder="example@domain.com"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'number')
                                    <input type="number" name="answers[{{ $index }}]" class="form-control-modern bg-white" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        placeholder="أدخل رقماً..."
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                @endif
                            </div>
                        @endforeach

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary-modern px-5 py-2">
                                <i class="fas fa-paper-plane me-2"></i> {{ isset($existingResponse) && $existingResponse ? 'حفظ وتحديث الإجابات' : 'إرسال الإجابات' }}
                            </button>
                            <a href="{{ route('graduate.surveys.index') }}" class="btn btn-outline-secondary px-4 py-2">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.star').on('click', function() {
        const $container = $(this).closest('.rating-stars');
        const rating = $(this).data('rating');
        
        $container.find('input[type="hidden"]').val(rating);
        
        $container.find('.star').each(function(index) {
            if (index < rating) {
                $(this).css('color', '#ffc107');
            } else {
                $(this).css('color', '#ddd');
            }
        });
        
        $container.find('.rating-display').text(`${rating} ${rating === 1 ? 'نجمة' : 'نجوم'}`).removeClass('text-muted').addClass('text-warning');
    });
    
    $('.star').on('mouseenter', function() {
        const $container = $(this).closest('.rating-stars');
        const rating = $(this).data('rating');
        
        $container.find('.star').each(function(index) {
            if (index < rating) {
                $(this).css('color', '#ffdb4d');
            } else {
                $(this).css('color', '#ddd');
            }
        });
    });
    
    $('.rating-stars').on('mouseleave', function() {
        const currentRating = $(this).find('input[type="hidden"]').val();
        
        $(this).find('.star').each(function(index) {
            if (currentRating && index < currentRating) {
                $(this).css('color', '#ffc107');
            } else {
                $(this).css('color', '#ddd');
            }
        });
    });
});
</script>
@endpush

