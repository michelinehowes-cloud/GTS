@extends('layouts.app')

@section('title', 'إضافة تقييم شامل')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-gradient text-white"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i> نموذج التقييم الشامل</h5>
            </div>

            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('evaluation-followup.evaluations.store') }}" method="POST">
                    @csrf

                    <!-- المعلومات الأساسية -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-info-circle"></i> المعلومات الأساسية
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع التقييم <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="type" name="type" required>
                                        <option value="">-- اختر --</option>
                                        <option value="training">📚 تقييم تدريب</option>
                                        <option value="employment">💼 تقييم توظيف</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="training-wrapper">
                                    <label class="form-label fw-bold">التدريب <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" name="training_id" id="training_id" required>
                                        <option value="">-- اختر --</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}" data-start="{{ $training->start_date }}"
                                                data-end="{{ $training->end_date }}">{{ $training->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ التقييم <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg" name="evaluation_date"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة</label>
                                    <select class="form-select form-select-lg" name="status">
                                        <option value="draft">مسودة</option>
                                        <option value="completed" selected>مكتمل</option>
                                        <option value="reviewed">تم المراجعة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييمات الأيام (للتدريبات متعددة الأيام) -->
                    <div id="day-evaluations-section" style="display: none;">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-calendar-day"></i> تقييمات الأيام
                            </div>
                            <div class="card-body">
                                <div id="day-evaluations-container">
                                    <!-- سيتم إضافة تقييمات الأيام هنا ديناميكياً -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييمات المدربين -->
                    <div id="trainer-evaluations-section" style="display: none;">
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <i class="fas fa-chalkboard-teacher"></i> تقييمات المدربين
                            </div>
                            <div class="card-body">
                                <div id="trainer-evaluations-container">
                                    <!-- سيتم إضافة تقييمات المدربين هنا ديناميكياً -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييم التدريب -->
                    <div id="training-section" style="display: none;">

                        <!-- التجهيزات -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white"><i class="fas fa-building"></i> تقييم التجهيزات
                                والمرافق</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $facilities = [
                                            'room_quality' => 'جودة القاعة التدريبية',
                                            'equipment' => 'التجهيزات والأدوات',
                                            'comfort' => 'الراحة والإضاءة',
                                            'cleanliness' => 'النظافة والترتيب'
                                        ];
                                    @endphp
                                    @foreach($facilities as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="facilities[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3">⭐⭐⭐ جيد</option>
                                                <option value="2">⭐⭐ مقبول</option>
                                                <option value="1">⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- المحتوى -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white"><i class="fas fa-book-open"></i> تقييم المحتوى
                                التدريبي</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $content = [
                                            'relevance' => 'ملاءمة المحتوى للأهداف',
                                            'quality' => 'جودة المواد التدريبية',
                                            'organization' => 'تنظيم المحتوى',
                                            'practical' => 'التطبيقات العملية',
                                            'updated' => 'حداثة المعلومات'
                                        ];
                                    @endphp
                                    @foreach($content as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="content[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3">⭐⭐⭐ جيد</option>
                                                <option value="2">⭐⭐ مقبول</option>
                                                <option value="1">⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- تقييمات المدربين -->
                        <div id="trainer-evaluations-section" style="display: none;">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <i class="fas fa-chalkboard-teacher"></i> تقييمات المدربين
                                </div>
                                <div class="card-body">
                                    <button type="button" id="add-trainer-btn"
                                        class="btn btn-sm btn-outline-primary mb-3"><i class="fas fa-plus"></i> إضافة مدرب
                                        للتقييم</button>
                                    <div id="trainer-evaluations-container">
                                        <!-- Dynamic trainer rows will be added here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- التنظيم -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white"><i class="fas fa-tasks"></i> تقييم التنظيم
                                والإدارة</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $organization = [
                                            'scheduling' => 'الجدول الزمني',
                                            'coordination' => 'التنسيق والتنظيم',
                                            'support' => 'الدعم الإداري',
                                            'communication_admin' => 'التواصل الإداري'
                                        ];
                                    @endphp
                                    @foreach($organization as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="organization[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3">⭐⭐⭐ جيد</option>
                                                <option value="2">⭐⭐ مقبول</option>
                                                <option value="1">⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- الأثر -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white"><i class="fas fa-chart-line"></i> تقييم الأثر
                                والاستفادة</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $impact = [
                                            'skills_gained' => 'المهارات المكتسبة',
                                            'knowledge_gained' => 'المعرفة المكتسبة',
                                            'practical_application' => 'إمكانية التطبيق',
                                            'career_impact' => 'الأثر المهني',
                                            'overall_satisfaction' => 'الرضا العام'
                                        ];
                                    @endphp
                                    @foreach($impact as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="impact[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3">⭐⭐⭐ جيد</option>
                                                <option value="2">⭐⭐ مقبول</option>
                                                <option value="1">⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييم التوظيف -->
                    <div id="employment-section" style="display: none;">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white"><i class="fas fa-building"></i> تقييم بيئة العمل
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $employment = [
                                            'workplace_quality' => 'جودة مكان العمل',
                                            'tools_equipment' => 'الأدوات والمعدات',
                                            'safety' => 'الأمان والسلامة',
                                            'work_culture' => 'ثقافة العمل',
                                            'supervisor_support' => 'دعم المشرف',
                                            'guidance' => 'التوجيه والإرشاد'
                                        ];
                                    @endphp
                                    @foreach($employment as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="employment[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3">⭐⭐⭐ جيد</option>
                                                <option value="2">⭐⭐ مقبول</option>
                                                <option value="1">⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التعليقات -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white"><i class="fas fa-comment"></i> التعليقات والتوصيات</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نقاط القوة</label>
                                    <textarea class="form-control" name="strengths" rows="3"
                                        placeholder="ما هي نقاط القوة؟"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نقاط الضعف</label>
                                    <textarea class="form-control" name="weaknesses" rows="3"
                                        placeholder="ما هي نقاط الضعف؟"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التعليقات</label>
                                    <textarea class="form-control" name="comments" rows="3"
                                        placeholder="تعليقات إضافية"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التوصيات</label>
                                    <textarea class="form-control" name="recommendations" rows="3"
                                        placeholder="توصياتك للتحسين"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="status" value="completed">

                    <!-- الأزرار -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('evaluation-followup.evaluations.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-right"></i> العودة
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle"></i> حفظ التقييم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template for Day Evaluation -->
    <template id="day-evaluation-template">
        <div class="card mb-3 border-light day-evaluation-instance">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">اختر تاريخ اليوم</label>
                        <input type="date" name="daily_evaluations[__INDEX__][date]" class="form-control form-control-sm"
                            required>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">تقييم محاور اليوم</h6>
                <div class="row">
                    @php
                        $dayContent = [
                            'clarity' => 'وضوح المحتوى',
                            'relevance' => 'الارتباط بالأهداف',
                            'engagement' => 'التفاعل والمشاركة'
                        ];
                    @endphp
                    @foreach($dayContent as $key => $label)
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">{{ $label }}</label>
                            <select name="daily_evaluations[__INDEX__][content][{{ $key }}]" class="form-select form-select-sm">
                                <option value="">-- اختر --</option>
                                <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                <option value="3">⭐⭐⭐ جيد</option>
                                <option value="2">⭐⭐ مقبول</option>
                                <option value="1">⭐ ضعيف</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const trainingSelect = document.getElementById('training_id');
            const trainingWrapper = document.getElementById('training-wrapper');
            const trainingSection = document.getElementById('training-section');
            const employmentSection = document.getElementById('employment-section');
            const trainerSection = document.getElementById('trainer-evaluations-section');
            const daySection = document.getElementById('day-evaluations-section');

            // Toggle sections based on Type
            function toggleSections() {
                if (!typeSelect) return;
                const type = typeSelect.value;

                // Reset all
                if (trainingSection) trainingSection.style.display = 'none';
                if (employmentSection) employmentSection.style.display = 'none';
                if (daySection) daySection.style.display = 'none';
                if (trainerSection) trainerSection.style.display = 'none';

                if (trainingWrapper) trainingWrapper.style.display = 'none';
                if (trainingSelect) trainingSelect.required = false;

                if (type === 'training') {
                    if (trainingWrapper) trainingWrapper.style.display = 'block';
                    if (trainingSelect) trainingSelect.required = true;

                    if (trainingSection) trainingSection.style.display = 'block';
                    // Trainer and Day sections depend on specific training selection
                    if (trainingSelect && trainingSelect.value) {
                        // Trigger change to re-show if training is already selected
                        trainingSelect.dispatchEvent(new Event('change'));
                    }
                } else if (type === 'employment') {
                    if (employmentSection) employmentSection.style.display = 'block';
                }
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', toggleSections);
                toggleSections(); // Init on load
            }

            if (trainingSelect) {
                trainingSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const type = typeSelect ? typeSelect.value : '';

                    if (!selectedOption.value || type !== 'training') {
                        if (daySection) daySection.style.display = 'none';
                        if (trainerSection) trainerSection.style.display = 'none';
                        return;
                    }

                    const startDateStr = selectedOption.getAttribute('data-start');
                    const endDateStr = selectedOption.getAttribute('data-end');

                    // Show Trainer Section (Always visible for training)
                    if (trainerSection) {
                        trainerSection.style.display = 'block';
                        // Initialize first trainer row if empty
                        const container = document.getElementById('trainer-evaluations-container');
                        if (container && container.children.length === 0 && typeof renderTrainerRow === 'function') {
                            renderTrainerRow();
                        }
                    }

                    // Handle Days Evaluation
                    const dayContainer = document.getElementById('day-evaluations-container');
                    const dayTemplate = document.getElementById('day-evaluation-template');

                    if (dayContainer) dayContainer.innerHTML = ''; // Clear previous

                    if (startDateStr && endDateStr && daySection) {
                        const start = new Date(startDateStr);
                        const end = new Date(endDateStr);
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays > 0) { // Show if there is at least one day
                            daySection.style.display = 'block';
                            addDayEvaluation(0, start, end); // Add initial day

                            // Add button logic
                            if (!document.getElementById('add-day-btn')) {
                                const addDayBtn = document.createElement('button');
                                addDayBtn.id = 'add-day-btn';
                                addDayBtn.type = 'button';
                                addDayBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
                                addDayBtn.innerHTML = '<i class="fas fa-plus"></i> إضافة يوم آخر للتقييم';
                                addDayBtn.onclick = function () {
                                    const newIndex = dayContainer.children.length;
                                    addDayEvaluation(newIndex, start, end);
                                };
                                dayContainer.parentNode.appendChild(addDayBtn);
                            }

                        } else {
                            daySection.style.display = 'none';
                            const addBtn = document.getElementById('add-day-btn');
                            if (addBtn) addBtn.remove();
                        }
                    } else if (daySection) {
                        daySection.style.display = 'none';
                    }
                });
            }

            // Helper to add day evaluation
            function addDayEvaluation(index, minDate, maxDate) {
                const dayTemplate = document.getElementById('day-evaluation-template');
                const dayContainer = document.getElementById('day-evaluations-container');
                if (!dayTemplate || !dayContainer) return;

                const templateContent = dayTemplate.innerHTML.replace(/__INDEX__/g, index);
                const newDayEl = document.createElement('div');
                newDayEl.innerHTML = templateContent;

                const dateInput = newDayEl.querySelector('input[type="date"]');
                if (dateInput) {
                    dateInput.min = minDate.toISOString().split('T')[0];
                    dateInput.max = maxDate.toISOString().split('T')[0];
                }

                dayContainer.appendChild(newDayEl);
            }

            // Trainer Evaluation Logic
            const trainerContainer = document.getElementById('trainer-evaluations-container');
            const addTrainerBtn = document.getElementById('add-trainer-btn');

            window.renderTrainerRow = function () {
                if (!trainerContainer) return;

                const index = Date.now();
                // Note: We use the blade directive inside JS string, which works because it's in a blade file
                const trainersOptions = `
                            <option value="">-- اختر المدرب --</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                            @endforeach
                        `;

                const rowHtml = `
                            <div class="row mb-3 border-bottom pb-3" id="trainer-row-${index}">
                                <div class="col-md-4">
                                    <label class="form-label">المدرب</label>
                                    <select name="instructors[${index}][id]" class="form-select" required>
                                        ${trainersOptions}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">التقييم العام</label>
                                        <select name="instructors[${index}][rating]" class="form-select" required>
                                        <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                                        <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                                        <option value="3">⭐⭐⭐ جيد</option>
                                        <option value="2">⭐⭐ مقبول</option>
                                        <option value="1">⭐ ضعيف</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                        <label class="form-label">ملاحظات</label>
                                        <input type="text" name="instructors[${index}][comments]" class="form-control" placeholder="ملاحظات حول المدرب">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeTrainerRow('${index}')"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        `;
                trainerContainer.insertAdjacentHTML('beforeend', rowHtml);
            }

            window.removeTrainerRow = function (index) {
                const row = document.getElementById(`trainer-row-${index}`);
                if (row) row.remove();
            }

            if (addTrainerBtn) {
                addTrainerBtn.addEventListener('click', function () {
                    renderTrainerRow();
                });
            }
        });
    </script>
@endsection