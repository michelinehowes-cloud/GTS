@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white p-4 rounded-top-3">
                    <h3 class="mb-2 fw-bold">{{ $survey->title }}</h3>
                    <p class="mb-0 opacity-75">{{ $survey->description }}</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('graduate.surveys.store', $survey->id) }}" method="POST">
                        @csrf
                        
                        @foreach($survey->questions as $index => $question)
                            <div class="mb-4 p-3 border rounded bg-light">
                                <label class="form-label fw-bold mb-3">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    {{ $question['question'] }}
                                    @if(isset($question['required']) && $question['required'])
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if($question['type'] === 'text')
                                    <input type="text" name="answers[{{ $index }}]" class="form-control" 
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'textarea')
                                    <textarea name="answers[{{ $index }}]" class="form-control" rows="3"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}></textarea>
                                
                                @elseif($question['type'] === 'radio')
                                    @foreach($question['options'] as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="answers[{{ $index }}]" 
                                                id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}"
                                                {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                            <label class="form-check-label" for="q{{ $index }}_opt{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                
                                @elseif($question['type'] === 'checkbox')
                                    @foreach($question['options'] as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][]" 
                                                id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}">
                                            <label class="form-check-label" for="q{{ $index }}_opt{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach

                                @elseif($question['type'] === 'select')
                                    <select name="answers[{{ $index }}]" class="form-select"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                        <option value="" selected disabled>اختر إجابة...</option>
                                        @foreach($question['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                
                                @elseif($question['type'] === 'rating')
                                    @php
                                        $maxRating = $question['max_rating'] ?? 5;
                                    @endphp
                                    <div class="rating-stars" data-question-index="{{ $index }}">
                                        <input type="hidden" name="answers[{{ $index }}]" id="rating_{{ $index }}" value="" 
                                            {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                        <div class="d-flex gap-2 align-items-center">
                                            @for($i = 1; $i <= $maxRating; $i++)
                                                <span class="star" data-rating="{{ $i }}" style="font-size: 2rem; cursor: pointer; color: #ddd;">
                                                    ★
                                                </span>
                                            @endfor
                                            <span class="rating-display ms-3 fw-bold text-muted">لم يتم التقييم</span>
                                        </div>
                                    </div>
                                
                                @elseif($question['type'] === 'date')
                                    <input type="date" name="answers[{{ $index }}]" class="form-control" 
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'email')
                                    <input type="email" name="answers[{{ $index }}]" class="form-control" 
                                        placeholder="example@email.com"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'number')
                                    <input type="number" name="answers[{{ $index }}]" class="form-control" 
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                @endif
                            </div>
                        @endforeach

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> إرسال الإجابات
                            </button>
                            <a href="{{ route('graduate.surveys.index') }}" class="btn btn-outline-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // معالجة النقر على النجوم
    $('.star').on('click', function() {
        const $container = $(this).closest('.rating-stars');
        const questionIndex = $container.data('question-index');
        const rating = $(this).data('rating');
        
        // تحديث القيمة المخفية
        $container.find('input[type="hidden"]').val(rating);
        
        // تحديث مظهر النجوم
        $container.find('.star').each(function(index) {
            if (index < rating) {
                $(this).css('color', '#ffc107'); // ذهبي للنجوم المحددة
            } else {
                $(this).css('color', '#ddd'); // رمادي للنجوم غير المحددة
            }
        });
        
        // تحديث النص المعروض
        $container.find('.rating-display').text(`${rating} ${rating === 1 ? 'نجمة' : 'نجوم'}`).removeClass('text-muted').addClass('text-warning');
    });
    
    // تأثير hover على النجوم
    $('.star').on('mouseenter', function() {
        const $container = $(this).closest('.rating-stars');
        const rating = $(this).data('rating');
        
        $container.find('.star').each(function(index) {
            if (index < rating) {
                $(this).css('color', '#ffdb4d'); // لون أفتح عند التحويم
            } else {
                $(this).css('color', '#ddd');
            }
        });
    });
    
    // إعادة تعيين اللون عند مغادرة المؤشر
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
@endsection
