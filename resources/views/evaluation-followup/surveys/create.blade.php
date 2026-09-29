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
                            <!-- 💡 شريط قوالب الاستبيانات الجاهزة -->
                            <div class="card border-0 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #a7f3d0 !important;">
                                <div class="card-body p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 1.2rem;">
                                            <i class="fas fa-magic"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">وفر وقتك عبر قوالب الاستبيانات الجاهزة</h6>
                                            <small class="text-muted">اختر قالباً معداً مسبقاً (تقييم التدريب، رضا أرباب العمل، معارض التوظيف، وغيرها) لتعبئة البيانات والأسئلة تلقائياً!</small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success fw-bold px-3.5 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#templateSelectorModal">
                                            <i class="fas fa-layer-group"></i>
                                            <span>تصفح واختيار قالب جاهز</span>
                                        </button>
                                        <a href="{{ route('evaluation-followup.surveys.templates.index') }}" target="_blank" class="btn btn-outline-success fw-bold px-3 py-2 rounded-pill d-flex align-items-center gap-1.5" title="عرض كافة القوالب في صفحة منفصلة">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>مكتبة القوالب</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

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

                <!-- حفظ كقالب اختياري -->
                <div class="card bg-light border-dashed rounded-3 p-3 my-3">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="save_as_template" name="save_as_template" value="1" onchange="toggleSaveTemplateOptions(this)">
                        <label class="form-check-label fw-bold text-dark" for="save_as_template" style="cursor: pointer;">
                            <i class="fas fa-bookmark text-primary ms-1"></i> حفظ هذا الاستبيان أيضاً كقالب جديد للاستخدام المستقبلي
                        </label>
                    </div>
                    <div id="save_template_options" class="row g-2 mt-1" style="display: none;">
                        <div class="col-md-7">
                            <label class="form-label small text-muted mb-1">اسم القالب الجديد</label>
                            <input type="text" class="form-control form-control-sm" name="template_title" id="template_title" placeholder="أدخل اسماً للقالب الجديد...">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small text-muted mb-1">تصنيف القالب</label>
                            <select class="form-select form-select-sm" name="template_category">
                                <option value="training">التدريب وورش العمل</option>
                                <option value="employment">التوظيف والشراكات</option>
                                <option value="events">المعارض والفعاليات</option>
                                <option value="general">قالب عام</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
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

        <!-- Modal اختيار قالب جاهز -->
        <div class="modal fade" id="templateSelectorModal" tabindex="-1" aria-labelledby="templateSelectorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-light border-bottom py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 bg-success text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fas fa-magic"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0">اختيار قالب استبيان جاهز</h5>
                                <small class="text-muted">تطبيق القالب يملأ العنوان، الوصف، ويولد كافة الأسئلة والخيارات فوراً</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- التبويبات -->
                        <ul class="nav nav-pills mb-4 gap-2" id="templateTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active rounded-pill px-3 py-2 fw-bold" id="tab-all-btn" data-bs-toggle="pill" data-bs-target="#tab-all" type="button">
                                    كافة القوالب ({{ count($templates ?? []) }})
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill px-3 py-2 fw-bold" id="tab-training-btn" data-bs-toggle="pill" data-bs-target="#tab-training" type="button">
                                    <i class="fas fa-graduation-cap me-1 text-success"></i> التدريب وورش العمل
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill px-3 py-2 fw-bold" id="tab-employment-btn" data-bs-toggle="pill" data-bs-target="#tab-employment" type="button">
                                    <i class="fas fa-briefcase me-1 text-primary"></i> التوظيف والشراكات
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill px-3 py-2 fw-bold" id="tab-events-btn" data-bs-toggle="pill" data-bs-target="#tab-events" type="button">
                                    <i class="fas fa-calendar-star me-1 text-warning"></i> المعارض والفعاليات
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill px-3 py-2 fw-bold" id="tab-custom-btn" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button">
                                    <i class="fas fa-bookmark me-1 text-purple"></i> قوالبي المحفوظة
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="templateTabsContent">
                            @php
                                $tabCategories = [
                                    'tab-all' => $templates ?? collect(),
                                    'tab-training' => ($templates ?? collect())->where('category', 'training'),
                                    'tab-employment' => ($templates ?? collect())->where('category', 'employment'),
                                    'tab-events' => ($templates ?? collect())->where('category', 'events'),
                                    'tab-custom' => ($templates ?? collect())->where('is_system', false),
                                ];
                            @endphp

                            @foreach($tabCategories as $tabId => $tList)
                            <div class="tab-pane fade {{ $tabId === 'tab-all' ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
                                <div class="row g-3">
                                    @forelse($tList as $t)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border rounded-3 p-3 shadow-sm hover-shadow d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-{{ $t->category === 'training' ? 'success' : ($t->category === 'employment' ? 'primary' : 'warning') }}-subtle text-dark border">
                                                        {{ $t->category_label }}
                                                    </span>
                                                    <span class="badge bg-light text-muted border">
                                                        {{ count($t->questions ?? []) }} أسئلة
                                                    </span>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $t->title }}</h6>
                                                <p class="text-muted small mb-2" style="font-size: 0.8rem; line-height: 1.4;">
                                                    {{ Str::limit($t->description, 90) }}
                                                </p>
                                            </div>
                                            <div class="pt-2 border-top d-flex gap-2 align-items-center mt-2">
                                                <button type="button" class="btn btn-sm btn-success rounded-pill flex-grow-1 fw-bold" onclick='applyTemplate(@json($t))'>
                                                    <i class="fas fa-check-circle me-1"></i> تطبيق القالب
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12 py-4 text-center text-muted">
                                        لا توجد قوالب في هذا القسم حالياً.
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSaveTemplateOptions(checkbox) {
            const optionsDiv = document.getElementById('save_template_options');
            if (checkbox.checked) {
                optionsDiv.style.display = 'flex';
                const titleInput = document.getElementById('template_title');
                if (!titleInput.value) {
                    titleInput.value = document.getElementById('title').value || '';
                }
            } else {
                optionsDiv.style.display = 'none';
            }
        }

        window.applyTemplate = function(template) {
            if (!template) return;
            if (confirm(`هل ترغب في تطبيق قالب «${template.title}»؟ سيتم استبدال الأسئلة الحالية بأسئلة هذا القالب.`)) {
                $('#title').val(template.title);
                $('#description').val(template.description || '');
                if (template.target_audience) {
                    $('#target_audience').val(template.target_audience);
                }
                
                // مسح الأسئلة الحالية
                $('#questions-container').empty();
                window.resetQuestionCount();
                
                // إضافة أسئلة القالب
                if (template.questions && Array.isArray(template.questions)) {
                    template.questions.forEach(function(q) {
                        window.doAddQuestion(q);
                    });
                }
                
                // إغلاق المودال
                const modalEl = document.getElementById('templateSelectorModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
        };

        $(document).ready(function () {
            console.log('create.blade.php script: Document ready. Initializing question logic.');
            let questionCount = 0;

            window.resetQuestionCount = function() {
                questionCount = 0;
            };

            window.doAddQuestion = function(qData) {
                addQuestion(qData);
            };

            // إضافة سؤال جديد
            $('#add-question-btn').on('click', function (e) {
                e.preventDefault();
                try {
                    addQuestion();
                } catch (error) {
                    console.error('Error adding question:', error);
                }
            });

            @if(isset($selectedTemplate) && $selectedTemplate)
                // إذا تم تمرير قالب مسبقاً عبر الرابط (?template_id=X)
                const initialTemplate = @json($selectedTemplate);
                $('#title').val(initialTemplate.title);
                $('#description').val(initialTemplate.description || '');
                if (initialTemplate.target_audience) {
                    $('#target_audience').val(initialTemplate.target_audience);
                }
                if (initialTemplate.questions && Array.isArray(initialTemplate.questions)) {
                    initialTemplate.questions.forEach(function(q) {
                        addQuestion(q);
                    });
                }
            @else
                // إضافة سؤال أول عند تحميل الصفحة إذا لم يتم تمرير قالب
                if ($('.question-item').length === 0) {
                    try {
                        addQuestion();
                    } catch (error) {
                        console.error('Error adding initial question:', error);
                    }
                }
            @endif

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