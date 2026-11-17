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
                <i class="far fa-clock me-1 text-warning"></i> تاريخ الانتهاء: {{ $survey->end_date ? $survey->end_date->format('Y-m-d') : 'مستمر' }}
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
                            <strong>تعديل الإجابات:</strong> لقد أجبت على هذا الاستبيان مسبقاً. يمكنك تعديل إجاباتك أدناه وحفظ التعديلات{{ $survey->end_date ? ' قبل انتهاء مدة الاستبيان (' . $survey->end_date->format('Y-m-d') . ')' : '' }}.
                        </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('graduate.surveys.store', $survey->id) }}" method="POST">
                @csrf

                <div class="card-modern mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-list-check me-2"></i>أسئلة الاستبيان
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @include('partials.survey-questions', ['survey' => $survey, 'existingResponse' => $existingResponse ?? null])

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

