@extends('layouts.app')

@section('title', $survey->title . ' — استبيان الشركات')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- Breadcrumbs -->
    <x-page-hero
        :title="$survey->title"
        :subtitle="$survey->description ?: 'استبيان تقييم واستطلاع رأي الشركاء وأصحاب الأعمال'"
        icon="fas fa-poll"
        :breadcrumbs="[
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'الاستبيانات', 'url' => route('company.surveys.index')],
            ['label' => $survey->title]
        ]"
        :badge="$survey->end_date ? 'ينتهي: ' . $survey->end_date->format('Y-m-d') : 'متاح باستمرار'"
        badgeIcon="fas fa-clock"
    >
        <a href="{{ route('company.surveys.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right fs-6"></i>
            <span>العودة للاستبيانات</span>
        </a>
    </x-page-hero>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
            <div class="card-modern mb-4 rounded-4 border-0 shadow-sm bg-white p-4">
                <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: rgba(13, 56, 130, 0.08); color: #0d3882;">
                        <i class="fas fa-info-circle fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">تعليمات ونبذة عن الاستبيان</h6>
                        <small class="text-muted">ملاحظات وإرشادات مهمة قبل الإرسال</small>
                    </div>
                </div>

                <p class="text-dark lh-lg mb-0 mt-2" style="font-size: 0.95rem;">
                    {{ $survey->description ?: 'يسر إدارة الجامعة ومكتب تدريب الخريجين استطلاع رأيكم وتقدير ملاحظاتكم لتطوير برامج الشراكة ومعارض التوظيف ورفع كفاءة مخرجات التدريب.' }}
                </p>

                @if(isset($existingResponse) && $existingResponse)
                    <div class="alert alert-info border-0 rounded-3 mt-3 mb-0 d-flex align-items-center gap-2" style="background: #e0f2fe; color: #0369a1;">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div>
                            <strong>تم تقديم إجابات سابقة:</strong> 
                            لقد قامت شركتكم بتعبئة هذا الاستبيان مسبقاً في {{ $existingResponse->submitted_at ? $existingResponse->submitted_at->format('Y-m-d H:i') : '' }}. يمكنك مراجعة وتعديل الإجابات أدناه.
                        </div>
                    </div>
                @endif
            </div>

            <form action="{{ route('company.surveys.store', $survey->id) }}" method="POST">
                @csrf
                @php
                    $previousAnswers = isset($existingResponse) ? $existingResponse->answers : [];
                @endphp

                <div class="card-modern mb-4 rounded-4 border-0 shadow-sm bg-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                            <i class="fas fa-list-check"></i>
                            <span>أسئلة الاستبيان ({{ count($survey->questions ?? []) }})</span>
                        </h5>
                    </div>

                    <div class="survey-questions-list">
                        @foreach($survey->questions ?? [] as $index => $question)
                            <div class="mb-4 p-3.5 p-md-4 rounded-4 bg-light border">
                                <label class="fw-bold mb-3 d-flex align-items-start gap-2">
                                    <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background: #0d3882; color: #fff; font-size: 0.82rem;">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="fs-6 text-dark lh-base pt-0.5">
                                        {{ $question['question'] ?? 'سؤال بدون نص' }}
                                        @if(isset($question['required']) && $question['required'])
                                            <span class="text-danger ms-1">*</span>
                                        @endif
                                    </span>
                                </label>

                                @if(($question['type'] ?? '') === 'text')
                                    <input type="text" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        placeholder="اكتب إجابتك هنا..."
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif(($question['type'] ?? '') === 'textarea')
                                    <textarea name="answers[{{ $index }}]" class="form-control bg-white rounded-3 p-3" rows="3"
                                        placeholder="اكتب ملاحظاتك وتفاصيل إجابتك هنا..."
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>{{ $previousAnswers[$index] ?? '' }}</textarea>
                                
                                @elseif(($question['type'] ?? '') === 'radio')
                                    <div class="ps-2">
                                        @foreach($question['options'] ?? [] as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="answers[{{ $index }}]" 
                                                    id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}"
                                                    {{ isset($previousAnswers[$index]) && $previousAnswers[$index] == $option ? 'checked' : '' }}
                                                    {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                                <label class="form-check-label text-dark fw-semibold" for="q{{ $index }}_opt{{ $loop->index }}" style="font-size: 0.92rem;">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                
                                @elseif(($question['type'] ?? '') === 'checkbox')
                                    <div class="ps-2">
                                        @foreach($question['options'] ?? [] as $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][]" 
                                                    id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}"
                                                    {{ isset($previousAnswers[$index]) && is_array($previousAnswers[$index]) && in_array($option, $previousAnswers[$index]) ? 'checked' : '' }}>
                                                <label class="form-check-label text-dark fw-semibold" for="q{{ $index }}_opt{{ $loop->index }}" style="font-size: 0.92rem;">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                @elseif(($question['type'] ?? '') === 'select')
                                    <select name="answers[{{ $index }}]" class="form-select bg-white rounded-3 py-2 px-3"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                        <option value="" {{ !isset($previousAnswers[$index]) ? 'selected' : '' }} disabled>اختر إجابة من القائمة...</option>
                                        @foreach($question['options'] ?? [] as $option)
                                            <option value="{{ $option }}" {{ isset($previousAnswers[$index]) && $previousAnswers[$index] == $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                
                                @elseif(($question['type'] ?? '') === 'rating')
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
                                
                                @elseif(($question['type'] ?? '') === 'date')
                                    <input type="date" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif(($question['type'] ?? '') === 'email')
                                    <input type="email" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        placeholder="company@domain.ly"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif(($question['type'] ?? '') === 'number')
                                    <input type="number" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3" 
                                        value="{{ $previousAnswers[$index] ?? '' }}"
                                        placeholder="أدخل رقماً..."
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4 py-2.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>{{ isset($existingResponse) && $existingResponse ? 'حفظ وتحديث الإجابات' : 'إرسال الإجابات رسمياً' }}</span>
                        </button>
                        <a href="{{ route('company.surveys.index') }}" class="btn btn-light border fw-bold px-4 py-2.5 rounded-3">
                            إلغاء والعودة
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // نجوم التقييم التفاعلية
    document.querySelectorAll('.rating-stars').forEach(function (container) {
        const qIndex = container.getAttribute('data-question-index');
        const hiddenInput = document.getElementById('rating_' + qIndex);
        const stars = container.querySelectorAll('.star');
        const display = container.querySelector('.rating-display');

        stars.forEach(function (star) {
            star.addEventListener('mouseenter', function () {
                const rating = parseInt(this.getAttribute('data-rating'));
                highlightStars(stars, rating);
            });

            star.addEventListener('mouseleave', function () {
                const currentVal = parseInt(hiddenInput.value) || 0;
                highlightStars(stars, currentVal);
            });

            star.addEventListener('click', function () {
                const rating = parseInt(this.getAttribute('data-rating'));
                hiddenInput.value = rating;
                highlightStars(stars, rating);
                if (display) {
                    display.textContent = rating + (rating === 1 ? ' نجمة' : ' نجوم');
                    display.className = 'rating-display ms-3 fw-bold text-warning';
                }
            });
        });
    });

    function highlightStars(stars, rating) {
        stars.forEach(function (s) {
            const r = parseInt(s.getAttribute('data-rating'));
            s.style.color = (r <= rating) ? '#ffc107' : '#ddd';
        });
    }
});
</script>
@endsection
