@extends('layouts.public')

@section('title', $survey->title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">{{ $survey->title }}</h2>
                    @if($survey->description)
                        <p class="mb-0 mt-2">{{ $survey->description }}</p>
                    @endif
                </div>

                <form action="{{ route('public.survey.store', $survey->slug) }}" method="POST" id="survey-form">
                    @csrf

                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- معلومات المشارك -->
                        <div class="participant-info mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user mr-2"></i>
                                معلومات المشارك
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="participant_name" class="form-label">
                                            الاسم الكامل <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('participant_name') is-invalid @enderror"
                                               id="participant_name" name="participant_name"
                                               value="{{ old('participant_name') }}" required>
                                        @error('participant_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="participant_email" class="form-label">
                                            البريد الإلكتروني <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('participant_email') is-invalid @enderror"
                                               id="participant_email" name="participant_email"
                                               value="{{ old('participant_email') }}" required>
                                        @error('participant_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أسئلة الاستبيان -->
                        <div class="survey-questions">
                            @foreach($survey->questions as $index => $question)
                                <div class="question-item mb-4 p-3 border rounded">
                                    <h6 class="question-title mb-3">
                                        <span class="question-number badge badge-primary mr-2">{{ $index + 1 }}</span>
                                        {{ $question['question'] }}
                                        @if($question['required'] ?? false)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </h6>

                                    <div class="question-input">
                                        @switch($question['type'])
                                            @case('text')
                                                <textarea class="form-control @error('responses.question_' . $index) is-invalid @enderror"
                                                          name="responses[question_{{ $index }}]" rows="3"
                                                          placeholder="اكتب إجابتك هنا..."
                                                          {{ ($question['required'] ?? false) ? 'required' : '' }}>
                                                    {{ old('responses.question_' . $index) }}
                                                </textarea>
                                                @break

                                            @case('radio')
                                                @if(isset($question['options']) && is_array($question['options']))
                                                    @foreach($question['options'] as $option)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio"
                                                                   name="responses[question_{{ $index }}]"
                                                                   id="q{{ $index }}_{{ $loop->index }}"
                                                                   value="{{ $option }}"
                                                                   {{ old('responses.question_' . $index) == $option ? 'checked' : '' }}
                                                                   {{ ($question['required'] ?? false) ? 'required' : '' }}>
                                                            <label class="form-check-label" for="q{{ $index }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @break

                                            @case('checkbox')
                                                @if(isset($question['options']) && is_array($question['options']))
                                                    @foreach($question['options'] as $option)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="responses[question_{{ $index }}][]"
                                                                   id="q{{ $index }}_{{ $loop->index }}"
                                                                   value="{{ $option }}"
                                                                   {{ in_array($option, old('responses.question_' . $index, [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="q{{ $index }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @break

                                            @case('select')
                                                <select class="form-control @error('responses.question_' . $index) is-invalid @enderror"
                                                        name="responses[question_{{ $index }}]"
                                                        {{ ($question['required'] ?? false) ? 'required' : '' }}>
                                                    <option value="">اختر إجابة</option>
                                                    @if(isset($question['options']) && is_array($question['options']))
                                                        @foreach($question['options'] as $option)
                                                            <option value="{{ $option }}"
                                                                    {{ old('responses.question_' . $index) == $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @break

                                            @case('rating')
                                                <div class="rating-stars">
                                                    @php
                                                        $maxRating = $question['max_rating'] ?? 5;
                                                        $currentRating = old('responses.question_' . $index, 0);
                                                    @endphp
                                                    <div class="star-rating">
                                                        @for($i = 1; $i <= $maxRating; $i++)
                                                            <input type="radio" id="star{{ $index }}_{{ $i }}" name="responses[question_{{ $index }}]" value="{{ $i }}"
                                                                   {{ $currentRating == $i ? 'checked' : '' }}
                                                                   {{ ($question['required'] ?? false) ? 'required' : '' }} />
                                                            <label for="star{{ $index }}_{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</label>
                                                        @endfor
                                                    </div>
                                                    <div class="rating-labels d-flex justify-content-between mt-2">
                                                        <small class="text-muted">سيء جداً</small>
                                                        <small class="text-muted">ممتاز</small>
                                                    </div>
                                                </div>
                                                @break


                                            @case('date')
                                                <input type="date" class="form-control @error('responses.question_' . $index) is-invalid @enderror"
                                                       name="responses[question_{{ $index }}]"
                                                       value="{{ old('responses.question_' . $index) }}"
                                                       {{ ($question['required'] ?? false) ? 'required' : '' }}>
                                                @break

                                            @case('email')
                                                <input type="email" class="form-control @error('responses.question_' . $index) is-invalid @enderror"
                                                       name="responses[question_{{ $index }}]"
                                                       placeholder="example@email.com"
                                                       value="{{ old('responses.question_' . $index) }}"
                                                       {{ ($question['required'] ?? false) ? 'required' : '' }}>
                                                @break

                                            @case('number')
                                                <input type="number" class="form-control @error('responses.question_' . $index) is-invalid @enderror"
                                                       name="responses[question_{{ $index }}]"
                                                       value="{{ old('responses.question_' . $index) }}"
                                                       {{ ($question['required'] ?? false) ? 'required' : '' }}>
                                                @break
                                        @endswitch

                                        @error('responses.question_' . $index)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer text-center py-4">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-paper-plane mr-2"></i>
                            إرسال الاستبيان
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.question-item {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff !important;
}

.question-title {
    color: #495057;
    font-weight: 600;
}

.question-number {
    font-size: 0.9em;
    padding: 0.4em 0.6em;
}

.rating-stars {
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating input[type="radio"]:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffc107;
}



.participant-info {
    background-color: #e9ecef;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.card-header {
    border-bottom: none;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // التحقق من صحة النموذج قبل الإرسال
    $('#survey-form').on('submit', function(e) {
        // يمكن إضافة تحقق إضافي هنا إذا لزم الأمر
        console.log('Submitting survey form...');
    });

    // تحسين تجربة المستخدم للأسئلة المطلوبة
    $('input[required], select[required], textarea[required]').on('blur', function() {
        if ($(this).val().trim() === '') {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endsection
