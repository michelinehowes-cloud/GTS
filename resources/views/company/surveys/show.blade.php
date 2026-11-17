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
                    $surveyQuestionsCount = is_array($survey->questions)
                        ? count($survey->questions)
                        : (is_string($survey->questions) ? count(json_decode($survey->questions, true) ?? []) : 0);
                @endphp

                <div class="card-modern mb-4 rounded-4 border-0 shadow-sm bg-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                            <i class="fas fa-list-check"></i>
                            <span>أسئلة الاستبيان ({{ $surveyQuestionsCount }})</span>
                        </h5>
                    </div>

                    @include('partials.survey-questions', ['survey' => $survey, 'existingResponse' => $existingResponse ?? null])

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
