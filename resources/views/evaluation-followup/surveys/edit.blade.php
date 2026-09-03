@extends('layouts.app')

@section('title', 'تعديل الاستبيان')

@section('content')
<div class="container-fluid py-4">
    <x-bento-form title="تعديل الاستبيان: {{ $survey->title }}" subtitle="تعديل وتحديث محاور وأسئلة الاستبيان" icon="fa-poll-h" :backRoute="route('evaluation-followup.surveys.index')">
        <form action="{{ route('evaluation-followup.surveys.update', $survey) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">عنوان الاستبيان <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $survey->title) }}"
                                           placeholder="أدخل عنوان الاستبيان" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">وصف الاستبيان</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="وصف مختصر للاستبيان">{{ old('description', $survey->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="target_audience">الجمهور المستهدف <span class="text-danger">*</span></label>
                                    <select class="form-control @error('target_audience') is-invalid @enderror"
                                            id="target_audience" name="target_audience" required>
                                        <option value="">اختر الجمهور المستهدف</option>
                                        <option value="graduates" {{ old('target_audience', $survey->target_audience) == 'graduates' ? 'selected' : '' }}>
                                            الخريجين
                                        </option>
                                        <option value="companies" {{ old('target_audience', $survey->target_audience) == 'companies' ? 'selected' : '' }}>
                                            الشركات
                                        </option>
                                        <option value="training_coordinators" {{ old('target_audience', $survey->target_audience) == 'training_coordinators' ? 'selected' : '' }}>
                                            منسقي التدريب
                                        </option>
                                        <option value="all" {{ old('target_audience', $survey->target_audience) == 'all' ? 'selected' : '' }}>
                                            الكل
                                        </option>
                                    </select>
                                    @error('target_audience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="start_date">تاريخ البداية <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                           id="start_date" name="start_date"
                                           value="{{ old('start_date', $survey->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="end_date">تاريخ النهاية <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                           id="end_date" name="end_date"
                                           value="{{ old('end_date', $survey->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active"
                                               name="is_active" value="1" {{ old('is_active', $survey->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            نشط (سيظهر للمستخدمين)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- قسم الأسئلة -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-question-circle mr-2"></i>
                                    أسئلة الاستبيان
                                </h5>

                                <div id="questions-container">
                                    @if($survey->questions && count($survey->questions) > 0)
                                        @foreach($survey->questions as $index => $question)
                                            <div class="question-item border rounded p-3 mb-3" data-question-id="{{ $index }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="question-label">السؤال {{ $index + 1 }}</label>
                                                            <input type="text" class="form-control question-text"
                                                                   name="questions[{{ $index }}][question]"
                                                                   value="{{ $question['question'] }}"
                                                                   placeholder="أدخل نص السؤال" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>نوع السؤال</label>
                                                            <select class="form-control question-type"
                                                                    name="questions[{{ $index }}][type]" required>
                                                                <option value="text" {{ $question['type'] == 'text' ? 'selected' : '' }}>نص حر</option>
                                                                <option value="radio" {{ $question['type'] == 'radio' ? 'selected' : '' }}>اختيار واحد</option>
                                                                <option value="checkbox" {{ $question['type'] == 'checkbox' ? 'selected' : '' }}>اختيار متعدد</option>
                                                                <option value="select" {{ $question['type'] == 'select' ? 'selected' : '' }}>قائمة منسدلة</option>
                                                                <option value="rating" {{ $question['type'] == 'rating' ? 'selected' : '' }}>تقييم (نجوم)</option>
                                                                <option value="date" {{ $question['type'] == 'date' ? 'selected' : '' }}>تاريخ</option>
                                                                <option value="email" {{ $question['type'] == 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                                                                <option value="number" {{ $question['type'] == 'number' ? 'selected' : '' }}>رقم</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>مطلوب</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="required_{{ $index }}"
                                                                       name="questions[{{ $index }}][required]"
                                                                       value="1" {{ isset($question['required']) && $question['required'] ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="required_{{ $index }}"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-sm remove-question mt-4">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="options-container" style="{{ in_array($question['type'], ['radio', 'checkbox', 'select']) ? '' : 'display: none;' }}">
                                                    <div class="form-group">
                                                        <label>الخيارات</label>
                                                        <div class="options-list">
                                                            @if(isset($question['options']) && is_array($question['options']))
                                                                @foreach($question['options'] as $optIndex => $option)
                                                                    <div class="input-group mb-2">
                                                                        <input type="text" class="form-control" 
                                                                               name="questions[{{ $index }}][options][{{ $optIndex }}]" 
                                                                               value="{{ $option }}" 
                                                                               placeholder="الخيار {{ $optIndex + 1 }}">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-danger remove-option">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="input-group mb-2">
                                                                    <input type="text" class="form-control" 
                                                                           name="questions[{{ $index }}][options][0]" 
                                                                           placeholder="الخيار 1">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-danger remove-option">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                                        </div>
                                                                        <button type="button" class="btn btn-outline-primary btn-sm add-option">
                                                                            <i class="fas fa-plus"></i> إضافة خيار
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="rating-container" style="{{ $question['type'] == 'rating' ? '' : 'display: none;' }}">
                                                                    <div class="form-group">
                                                                        <label>عدد النجوم</label>
                                                                        <select class="form-control" name="questions[{{ $index }}][max_rating]">
                                                                            <option value="5" {{ (isset($question['max_rating']) && $question['max_rating'] == 5) || !isset($question['max_rating']) ? 'selected' : '' }}>5 نجوم</option>
                                                                            <option value="10" {{ isset($question['max_rating']) && $question['max_rating'] == 10 ? 'selected' : '' }}>10 نجوم</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-question-btn">
                                    <i class="fas fa-plus"></i> إضافة سؤال
                                </button>
                            </div>
                        </div>
                    </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('evaluation-followup.surveys.show', $survey) }}" class="btn btn-secondary px-4 py-2 rounded-pill">
                        <i class="fas fa-times me-2"></i> إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-save me-2"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
    </x-bento-form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let questionCount = {{ $survey->questions ? count($survey->questions) : 0 }};

    // إضافة سؤال جديد
    $('#add-question-btn').on('click', function() {
        questionCount++;
        addQuestion(questionCount);
    });

    // إضافة سؤال أول إذا لم تكن هناك أسئلة
    if (questionCount === 0) {
        questionCount++;
        addQuestion(questionCount);
    }

    function addQuestion(questionId) {
        const questionHtml = `
            <div class="question-item border rounded p-3 mb-3" data-question-id="${questionId}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="question-label">السؤال ${questionId + 1}</label>
                            <input type="text" class="form-control question-text" name="questions[${questionId}][question]"
                                   placeholder="أدخل نص السؤال" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>نوع السؤال</label>
                            <select class="form-control question-type" name="questions[${questionId}][type]" required>
                                <option value="text">نص حر</option>
                                <option value="radio">اختيار واحد</option>
                                <option value="checkbox">اختيار متعدد</option>
                                <option value="select">قائمة منسدلة</option>
                                <option value="rating">تقييم (نجوم)</option>
                                <option value="date">تاريخ</option>
                                <option value="email">بريد إلكتروني</option>
                                <option value="number">رقم</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>مطلوب</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="required_${questionId}"
                                       name="questions[${questionId}][required]" value="1" checked>
                                <label class="custom-control-label" for="required_${questionId}"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-question mt-4">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="options-container" style="display: none;">
                    <div class="form-group">
                        <label>الخيارات</label>
                        <div class="options-list">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="questions[${questionId}][options][0]" placeholder="الخيار 1">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-danger remove-option">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm add-option">
                            <i class="fas fa-plus"></i> إضافة خيار
                        </button>
                    </div>
                </div>
                <div class="rating-container" style="display: none;">
                    <div class="form-group">
                        <label>عدد النجوم</label>
                        <select class="form-control" name="questions[${questionId}][max_rating]">
                            <option value="5" selected>5 نجوم</option>
                            <option value="10">10 نجوم</option>
                        </select>
                    </div>
                </div>
            </div>
        `;

        $('#questions-container').append(questionHtml);
    }

    // تغيير نوع السؤال
    $(document).on('change', '.question-type', function() {
        const $container = $(this).closest('.question-item');
        const type = $(this).val();
        const $optionsContainer = $container.find('.options-container');
        const $ratingContainer = $container.find('.rating-container');

        if (['radio', 'checkbox', 'select'].includes(type)) {
            $optionsContainer.show();
            $ratingContainer.hide();
        } else if (type === 'rating') {
            $optionsContainer.hide();
            $ratingContainer.show();
        } else {
            $optionsContainer.hide();
            $ratingContainer.hide();
        }
    });

    // إضافة خيار جديد
    $(document).on('click', '.add-option', function() {
        const $optionsList = $(this).closest('.form-group').find('.options-list');
        const questionId = $(this).closest('.question-item').data('question-id');
        const optionCount = $optionsList.find('.input-group').length;

        const optionHtml = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="questions[${questionId}][options][${optionCount}]" placeholder="الخيار ${optionCount + 1}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        $optionsList.append(optionHtml);
    });

    // حذف خيار
    $(document).on('click', '.remove-option', function() {
        const $optionsList = $(this).closest('.options-list');
        if ($optionsList.find('.input-group').length > 1) {
            $(this).closest('.input-group').remove();
            // إعادة ترقيم الخيارات
            updateOptionNumbers($optionsList);
        } else {
            alert('يجب أن يحتوي السؤال على خيار واحد على الأقل');
        }
    });

    // حذف سؤال
    $(document).on('click', '.remove-question', function() {
        if ($('.question-item').length > 1) {
            $(this).closest('.question-item').remove();
            updateQuestionNumbers();
        } else {
            alert('يجب أن يحتوي الاستبيان على سؤال واحد على الأقل');
        }
    });

    // إعادة ترقيم الخيارات
    function updateOptionNumbers($optionsList) {
        const questionId = $optionsList.closest('.question-item').data('question-id');
        $optionsList.find('.input-group').each(function(index) {
            $(this).find('input').attr('name', `questions[${questionId}][options][${index}]`);
            $(this).find('input').attr('placeholder', `الخيار ${index + 1}`);
        });
    }

    function updateQuestionNumbers() {
        $('.question-item').each(function(index) {
            const displayNumber = index + 1;
            const arrayIndex = index;
            
            // Update data attribute
            $(this).attr('data-question-id', arrayIndex);
            
            // Update display label
            $(this).find('.question-label').text(`السؤال ${displayNumber}`);
            
            // Update all field names
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    // Don't update option names here, they'll be updated separately
                    if (!name.includes('[options]')) {
                        const newName = name.replace(/questions\[\d+\]/, `questions[${arrayIndex}]`);
                        $(this).attr('name', newName);
                    }
                }
                
                // Update required checkbox id
                const id = $(this).attr('id');
                if (id && id.startsWith('required_')) {
                    $(this).attr('id', `required_${arrayIndex}`);
                    $(this).siblings('label').attr('for', `required_${arrayIndex}`);
                }
            });
            
            // Update options for this question
            const $optionsList = $(this).find('.options-list');
            if ($optionsList.length) {
                $optionsList.find('.input-group').each(function(optIndex) {
                    $(this).find('input').attr('name', `questions[${arrayIndex}][options][${optIndex}]`);
                    $(this).find('input').attr('placeholder', `الخيار ${optIndex + 1}`);
                });
            }
        });
        questionCount = $('.question-item').length;
    }

    // التحقق من التواريخ
    $('#start_date, #end_date').on('change', function() {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());

        if (startDate && endDate && startDate >= endDate) {
            alert('تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
            $('#end_date').val('');
        }
    });
});
</script>
@endsection
