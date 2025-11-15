@extends('layouts.app')

@section('title', 'تعديل الاستبيان')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل الاستبيان: {{ $survey->title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('evaluation-followup.surveys.show', $survey) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="{{ route('evaluation-followup.surveys.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>

                <form action="{{ route('evaluation-followup.surveys.update', $survey) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
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
                                            <div class="question-item border rounded p-3 mb-3" data-question-id="{{ $index + 1 }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>السؤال {{ $index + 1 }}</label>
                                                            <input type="text" class="form-control"
                                                                   name="questions[{{ $index + 1 }}][question]"
                                                                   value="{{ $question['question'] }}"
                                                                   placeholder="أدخل نص السؤال" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>نوع السؤال</label>
                                                            <select class="form-control question-type"
                                                                    name="questions[{{ $index + 1 }}][type]" required>
                                                                <option value="text" {{ $question['type'] == 'text' ? 'selected' : '' }}>نص حر</option>
                                                                <option value="radio" {{ $question['type'] == 'radio' ? 'selected' : '' }}>اختيار واحد</option>
                                                                <option value="checkbox" {{ $question['type'] == 'checkbox' ? 'selected' : '' }}>اختيار متعدد</option>
                                                                <option value="select" {{ $question['type'] == 'select' ? 'selected' : '' }}>قائمة منسدلة</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>مطلوب</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="required_{{ $index + 1 }}"
                                                                       name="questions[{{ $index + 1 }}][required]"
                                                                       value="1" {{ isset($question['required']) && $question['required'] ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="required_{{ $index + 1 }}"></label>
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
                                                        <label>الخيارات (كل خيار في سطر منفصل)</label>
                                                        <textarea class="form-control" name="questions[{{ $index + 1 }}][options]" rows="3"
                                                                  placeholder="الخيار الأول&#10;الخيار الثاني&#10;الخيار الثالث">{{ isset($question['options']) ? implode("\n", $question['options']) : '' }}</textarea>
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

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                        <a href="{{ route('evaluation-followup.surveys.show', $survey) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                            <label>السؤال ${questionId}</label>
                            <input type="text" class="form-control" name="questions[${questionId}][question]"
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
                        <label>الخيارات (كل خيار في سطر منفصل)</label>
                        <textarea class="form-control" name="questions[${questionId}][options]" rows="3"
                                  placeholder="الخيار الأول\nالخيار الثاني\nالخيار الثالث"></textarea>
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

        if (['radio', 'checkbox', 'select'].includes(type)) {
            $optionsContainer.show();
        } else {
            $optionsContainer.hide();
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

    function updateQuestionNumbers() {
        $('.question-item').each(function(index) {
            const newNumber = index + 1;
            $(this).find('label:first').text(`السؤال ${newNumber}`);
            $(this).attr('data-question-id', newNumber);
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
