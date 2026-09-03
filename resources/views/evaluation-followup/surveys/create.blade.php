@extends('layouts.app')

@section('title', 'إضافة استبيان جديد')

@section('content')
    <div class="container-fluid py-4">
        <x-bento-form title="إضافة استبيان جديد" subtitle="تصميم وإعداد محاور وأسئلة الاستبيان للمتابعة والتقييم" icon="fa-poll-h" :backRoute="route('evaluation-followup.surveys.index')">
            <form action="{{ route('evaluation-followup.surveys.store') }}" method="POST">
                @csrf
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
                                            id="title" name="title" value="{{ old('title') }}"
                                            placeholder="أدخل عنوان الاستبيان" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="description">وصف الاستبيان</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                            id="description" name="description" rows="3"
                                            placeholder="وصف مختصر للاستبيان">{{ old('description', '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="target_audience">الجمهور المستهدف <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control @error('target_audience') is-invalid @enderror"
                                            id="target_audience" name="target_audience" required>
                                            <option value="">اختر الجمهور المستهدف</option>
                                            <option value="graduates" {{ old('target_audience') == 'graduates' ? 'selected' : '' }}>
                                                الخريجين
                                            </option>
                                            <option value="companies" {{ old('target_audience') == 'companies' ? 'selected' : '' }}>
                                                الشركات
                                            </option>
                                            <option value="training_coordinators" {{ old('target_audience') == 'training_coordinators' ? 'selected' : '' }}>
                                                منسقي التدريب
                                            </option>
                                            <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>
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
                                            id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="end_date">تاريخ النهاية <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                            id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active"
                                                name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
                                        <!-- سيتم إضافة الأسئلة هنا ديناميكياً -->
                                    </div>

                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-question-btn">
                                        <i class="fas fa-plus"></i> إضافة سؤال
                                    </button>
                                </div>
                            </div>

                            <!-- معاينة الاستبيان -->
                            <div class="modal fade" id="preview-modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">معاينة الاستبيان</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="preview-content">
                                            <!-- سيتم تحميل محتوى المعاينة هنا -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('evaluation-followup.surveys.index') }}" class="btn btn-secondary px-4 py-2 rounded-pill">
                        <i class="fas fa-times me-2"></i> إلغاء
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-info text-white px-4 py-2 rounded-pill shadow-sm" id="preview-survey-btn">
                            <i class="fas fa-eye me-2"></i> معاينة
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-save me-2"></i> حفظ الاستبيان
                        </button>
                    </div>
                </div>
            </form>
        </x-bento-form>
    </div>
@endsection

@section('scripts')
    <script>

        $(document).ready(function () {
            console.log('create.blade.php script: Document ready. Initializing question logic.');
            let questionCount = 0;

            // إضافة سؤال جديد
            $('#add-question-btn').on('click', function (e) {
                e.preventDefault();
                console.log('Add question button clicked');
                try {
                    addQuestion();
                    console.log('Question added successfully. Current questionCount:', questionCount);
                } catch (error) {
                    console.error('Error adding question:', error);
                }
            });

            // إضافة سؤال أول عند تحميل الصفحة
            if ($('.question-item').length === 0) {
                console.log('No questions found, adding initial question.');
                try {
                    addQuestion();
                } catch (error) {
                    console.error('Error adding initial question:', error);
                }
            }

            function addQuestion(questionData = null) {
                questionCount++;
                console.log('Inside addQuestion. New questionCount:', questionCount);
                const questionId = questionData ? questionData.id || questionCount : questionCount;

                const questionHtml = `
                <div class="question-item card mb-3" data-question-id="${questionId}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="question-number badge badge-primary mr-2">${questionId}</span>
                            <i class="fas fa-grip-vertical text-muted mr-2 handle" style="cursor: move;"></i>
                            <span class="question-label">السؤال ${questionId}</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-question" title="حذف السؤال">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control question-text" name="questions[${questionId}][question]"
                                           placeholder="أدخل نص السؤال" value="${questionData ? questionData.question || '' : ''}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control question-type" name="questions[${questionId}][type]" required>
                                        <option value="text" ${questionData && questionData.type === 'text' ? 'selected' : ''}>نص حر</option>
                                        <option value="radio" ${questionData && questionData.type === 'radio' ? 'selected' : ''}>اختيار واحد</option>
                                        <option value="checkbox" ${questionData && questionData.type === 'checkbox' ? 'selected' : ''}>اختيار متعدد</option>
                                        <option value="select" ${questionData && questionData.type === 'select' ? 'selected' : ''}>قائمة منسدلة</option>
                                        <option value="rating" ${questionData && questionData.type === 'rating' ? 'selected' : ''}>تقييم (نجوم)</option>
                                        <option value="date" ${questionData && questionData.type === 'date' ? 'selected' : ''}>تاريخ</option>
                                        <option value="email" ${questionData && questionData.type === 'email' ? 'selected' : ''}>بريد إلكتروني</option>
                                        <option value="number" ${questionData && questionData.type === 'number' ? 'selected' : ''}>رقم</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="required_${questionId}"
                                               name="questions[${questionId}][required]" value="1" ${questionData && questionData.required ? 'checked' : 'checked'}>
                                        <label class="custom-control-label" for="required_${questionId}">
                                            إجباري
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="questions[${questionId}][description]"
                                           placeholder="وصف إضافي (اختياري)" value="${questionData ? questionData.description || '' : ''}">
                                </div>
                            </div>
                        </div>

                        <div class="options-container" style="display: ${questionData && ['radio', 'checkbox', 'select'].includes(questionData.type) ? 'block' : 'none'};">
                            <div class="form-group">
                                <label>الخيارات</label>
                                <div class="options-list">
                                    ${questionData && questionData.options ? questionData.options.map((option, index) => `
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="questions[${questionId}][options][${index}]" value="${option}" placeholder="الخيار ${index + 1}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger remove-option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('') : `
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="questions[${questionId}][options][0]" placeholder="الخيار 1">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger remove-option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `}
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm add-option">
                                    <i class="fas fa-plus"></i> إضافة خيار
                                </button>
                            </div>
                        </div>

                        <div class="rating-container" style="display: ${questionData && questionData.type === 'rating' ? 'block' : 'none'};">
                            <div class="form-group">
                                <label>عدد النجوم</label>
                                <select class="form-control" name="questions[${questionId}][max_rating]">
                                    <option value="5" ${questionData && questionData.max_rating == 5 ? 'selected' : 'selected'}>5 نجوم</option>
                                    <option value="10" ${questionData && questionData.max_rating == 10 ? 'selected' : ''}>10 نجوم</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                const $questionsContainer = $('#questions-container');
                if ($questionsContainer.length === 0) {
                    console.error('Error: #questions-container not found in the DOM.');
                    return;
                }
                $questionsContainer.append(questionHtml);
                updateQuestionNumbers();
                console.log('Question HTML appended to container.');
            }

            // تغيير نوع السؤال
            $(document).on('change', '.question-type', function () {
                const $container = $(this).closest('.question-item');
                const type = $(this).val();
                const $optionsContainer = $container.find('.options-container');
                const $ratingContainer = $container.find('.rating-container');

                if (['radio', 'checkbox', 'select'].includes(type)) {
                    $optionsContainer.show();
                    $ratingContainer.hide();
                } else {
                    $optionsContainer.hide();
                    $ratingContainer.hide();
                }
            });

            // إضافة خيار جديد
            $(document).on('click', '.add-option', function () {
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
            $(document).on('click', '.remove-option', function () {
                const $optionsList = $(this).closest('.options-list');
                if ($optionsList.find('.input-group').length > 1) {
                    $(this).closest('.input-group').remove();
                }
            });

            // حذف سؤال
            $(document).on('click', '.remove-question', function () {
                if ($('.question-item').length > 1) {
                    $(this).closest('.question-item').remove();
                    updateQuestionNumbers();
                } else {
                    alert('يجب أن يحتوي الاستبيان على سؤال واحد على الأقل');
                }
            });

            // معاينة الاستبيان
            $('#preview-survey-btn').on('click', function () {
                const questions = collectQuestionsForPreview();
                showPreviewModal(questions);
            });

            function collectQuestionsForPreview() {
                const questions = [];
                $('.question-item').each(function (index) {
                    const $item = $(this);
                    const type = $item.find('.question-type').val();
                    const question = $item.find('.question-text').val() || `السؤال ${index + 1}`;
                    const required = $item.find('input[name*="[required]"]').is(':checked');

                    let options = [];
                    if (['radio', 'checkbox', 'select'].includes(type)) {
                        $item.find('input[name*="[options]"]').each(function () {
                            const val = $(this).val().trim();
                            if (val) options.push(val);
                        });
                    }

                    const maxRating = type === 'rating' ? $item.find('select[name*="[max_rating]"]').val() : null;

                    questions.push({
                        question,
                        type,
                        required,
                        options,
                        max_rating: maxRating
                    });
                });
                return questions;
            }


            function showPreviewModal(questions) {
                let modalHtml = `
                <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">معاينة الاستبيان</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
            `;

                questions.forEach((q, index) => {
                    modalHtml += `
                    <div class="question-preview mb-4 p-3 border rounded">
                        <h6>${index + 1}. ${q.question} ${q.required ? '<span class="text-danger">*</span>' : ''}</h6>
                `;

                    switch (q.type) {
                        case 'text':
                            modalHtml += '<textarea class="form-control" rows="3" placeholder="إجابة نصية" disabled></textarea>';
                            break;
                        case 'radio':
                        case 'checkbox':
                            q.options.forEach(option => {
                                modalHtml += `
                                <div class="form-check">
                                    <input class="form-check-input" type="${q.type}" disabled>
                                    <label class="form-check-label">${option}</label>
                                </div>
                            `;
                            });
                            break;
                        case 'select':
                            modalHtml += '<select class="form-control" disabled><option>اختر إجابة</option>';
                            q.options.forEach(option => {
                                modalHtml += `<option>${option}</option>`;
                            });
                            modalHtml += '</select>';
                            break;
                        case 'rating':
                            for (let i = 1; i <= q.max_rating; i++) {
                                modalHtml += `<span class="mr-1">⭐</span>`;
                            }
                            break;
                        case 'date':
                            modalHtml += '<input type="date" class="form-control" disabled>';
                            break;
                        case 'email':
                            modalHtml += '<input type="email" class="form-control" placeholder="example@email.com" disabled>';
                            break;
                        case 'number':
                            modalHtml += '<input type="number" class="form-control" disabled>';
                            break;
                    }

                    modalHtml += '</div>';
                });

                modalHtml += `
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                $('body').append(modalHtml);
                $('#previewModal').modal('show');
                $('#previewModal').on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }


            // تحديث أرقام الأسئلة
            function updateQuestionNumbers() {
                $('.question-item').each(function (index) {
                    const displayNumber = index + 1;  // للعرض: 1, 2, 3...
                    const arrayIndex = index;  // لأسماء الحقول: 0, 1, 2...

                    $(this).attr('data-question-id', displayNumber);
                    $(this).find('.question-number').text(displayNumber);
                    $(this).find('.question-label').text(`السؤال ${displayNumber}`);

                    // تحديث أسماء الحقول باستخدام arrayIndex
                    $(this).find('input, select, textarea').each(function () {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/questions\[\d+\]/, `questions[${arrayIndex}]`);
                            $(this).attr('name', newName);

                            // تحديث id للـ checkbox required
                            if ($(this).attr('id') && $(this).attr('id').startsWith('required_')) {
                                $(this).attr('id', `required_${arrayIndex}`);
                                $(this).siblings('label').attr('for', `required_${arrayIndex}`);
                            }
                        }
                    });
                });
                questionCount = $('.question-item').length;
            }

            // إعادة ترتيب الأسئلة باستخدام Sortable
            // if (typeof Sortable !== 'undefined') { // Temporarily commented out
            //     new Sortable(document.getElementById('questions-container'), {
            //         handle: '.handle',
            //         animation: 150,
            //         onEnd: function() {
            //             updateQuestionNumbers();
            //         }
            //     });
            // }

            // التحقق من التواريخ
            $('#start_date, #end_date').on('change', function () {
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