@extends('layouts.app')

@section('title', 'نموذج تقييم التدريبات - قسم التقييم والمتابعة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card with Tripoli University Branding -->
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                        <i class="fas fa-university text-primary fs-3"></i>
                    </div>
                    <div>
                        <div class="badge bg-warning text-dark mb-1 fw-bold">جامعة طرابلس - مكتب تدريب الخريجين</div>
                        <h4 class="mb-0 fw-bold text-white">قسم التقييم والمتابعة | نموذج تقييم التدريبات</h4>
                    </div>
                </div>
                <div>
                    <a href="{{ route('evaluation-followup.evaluations.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> العودة لسجل التقييمات
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-4 bg-light">
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0 mb-4">
                    <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> يرجى تصحيح الأخطاء التالية:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('evaluation-followup.evaluations.store') }}" method="POST" id="evaluation-form">
                @csrf

                <!-- الخطوة 1: اختيار التدريب والنوع -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-sliders-h me-2"></i> تحديد البرنامج التدريبي ونوع التقييم
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">نوع التقييم <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg border-2" id="type" name="type" required>
                                    <option value="training" selected>📚 تقييم تدريب (النموذج الرسمي المعتمد)</option>
                                    <option value="employment">💼 تقييم بيئة التوظيف والعمل</option>
                                </select>
                            </div>

                            <div class="col-md-5" id="training-select-wrapper">
                                <label class="form-label fw-bold">البرنامج التدريبي المستهدف <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg border-2" name="training_id" id="training_id" required>
                                    <option value="">-- اختر البرنامج التدريبي للتقييم --</option>
                                    @foreach($trainings as $training)
                                        @php
                                            $trainerName = $training->trainer ? $training->trainer->name : ($training->instructor_name ?? 'غير محدد');
                                            $dept = $training->category ?? ($training->coordinator ? $training->coordinator->name : 'مكتب تدريب الخريجين');
                                            $startStr = $training->start_date ? $training->start_date->format('Y-m-d') : '';
                                            $endStr = $training->end_date ? $training->end_date->format('Y-m-d') : $startStr;
                                            $beneficiaries = $training->applications_count > 0 ? $training->applications_count : ($training->seats ?? 25);
                                            $location = $training->location ?? 'جامعة طرابلس - القاعة المركزية';
                                        @endphp
                                        <option value="{{ $training->id }}"
                                            data-title="{{ $training->title }}"
                                            data-trainer="{{ $trainerName }}"
                                            data-trainer-id="{{ $training->trainer_id ?? '' }}"
                                            data-dept="{{ $dept }}"
                                            data-start="{{ $startStr }}"
                                            data-end="{{ $endStr }}"
                                            data-beneficiaries="{{ $beneficiaries }}"
                                            data-location="{{ $location }}"
                                            {{ old('training_id') == $training->id ? 'selected' : '' }}>
                                            {{ $training->title }} ({{ $trainerName }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">تاريخ التقييم <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-lg border-2" name="evaluation_date"
                                    value="{{ old('evaluation_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة البيانات المسترجعة تلقائياً للتدريب (وفق المتطلبات الرسمية) -->
                <div id="training-auto-data-card" class="card border-0 shadow-sm mb-4" style="display: none; background: #ffffff;">
                    <div class="card-header bg-primary bg-opacity-10 text-primary py-3 d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-6">
                            <i class="fas fa-check-circle me-1 text-success"></i> بيانات البرنامج المسترجعة تلقائياً من المنصة
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                            مسترجعة آلياً دون الحاجة لإعادة إدخالها
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-graduation-cap me-1"></i> اسم البرنامج التدريبي</small>
                                    <span class="fw-bold text-dark fs-6" id="meta-training-title">-</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-chalkboard-teacher me-1"></i> المدرب / المحاضر</small>
                                    <span class="fw-bold text-dark fs-6" id="meta-trainer-name">-</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-building me-1"></i> القسم المنفذ</small>
                                    <span class="fw-bold text-dark fs-6" id="meta-department">-</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-calendar-alt me-1"></i> الفترة الزمنية</small>
                                    <span class="fw-bold text-dark fs-6" id="meta-period">-</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-users me-1"></i> عدد المستفيدين</small>
                                    <span class="fw-bold text-primary fs-6" id="meta-beneficiaries">-</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-light rounded border">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-map-marker-alt me-1"></i> مكان التنفيذ</small>
                                    <span class="fw-bold text-dark fs-6" id="meta-location">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نموذج جامعة طرابلس الرسمي: أولاً: تقييم محتوى وتنفيذ الجلسة التدريبية -->
                <div id="official-training-evaluation-section">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header text-white py-3 d-flex justify-content-between align-items-center"
                            style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-tasks me-2"></i> أولاً: تقييم محتوى وتنفيذ الجلسة التدريبية (التقدير من 5)
                            </h5>
                            <span class="badge bg-white text-primary fw-bold px-3 py-2 fs-6 shadow-sm" id="session-score-badge">
                                نسبة إجمالي الجلسة: 100% (5.0 / 5)
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">#</th>
                                            <th>معيار التقييم</th>
                                            <th style="width: 320px;" class="text-center">التقدير (من 5)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sessionCriteria = [
                                                'clarity_of_goals' => '1. وضوح أهداف البرنامج التدريبي',
                                                'session_sequence' => '2. تنظيم وتتابع محاور الجلسة',
                                                'presentation_attractiveness' => '3. جاذبية العرض وأساليب التقديم',
                                                'trainee_interaction' => '4. تفاعل المتدربين أثناء النشاط',
                                                'diversity_of_tools' => '5. تنوع الوسائل التدريبية المستخدمة',
                                                'achieving_outcomes' => '6. مدى تحقيق مخرجات التدريب المستهدفة',
                                                'time_commitment' => '7. مدى التزام التدريب بالوقت المحدد',
                                                'pre_post_assessment' => '8. وجود قياس قبلي/ بعدي للجلسة (إن وُجد)',
                                                'general_training_rating' => '9. التقييم العام للتدريب',
                                            ];
                                            $index = 1;
                                        @endphp

                                        @foreach($sessionCriteria as $key => $label)
                                            <tr>
                                                <td class="text-center fw-bold text-muted">{{ $index++ }}</td>
                                                <td class="fw-semibold text-dark">{{ $label }}</td>
                                                <td>
                                                    <select name="session_criteria[{{ $key }}]" class="form-select border-primary-subtle session-criterion-select" required>
                                                        <option value="5" selected>⭐⭐⭐⭐⭐ ممتاز (5/5)</option>
                                                        <option value="4">⭐⭐⭐⭐ جيد جداً (4/5)</option>
                                                        <option value="3">⭐⭐⭐ جيد (3/5)</option>
                                                        <option value="2">⭐⭐ مقبول (2/5)</option>
                                                        <option value="1">⭐ ضعيف (1/5)</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- بند رقم 10: نسبة إجمالي تقييم التدريب -->
                                        <tr class="table-primary bg-opacity-25 fw-bold">
                                            <td class="text-center text-primary fs-5">10</td>
                                            <td class="text-primary fs-6">نسبة إجمالي تقييم التدريب (المحسوبة آلياً)</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 12px;">
                                                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="session-progress" style="width: 100%"></div>
                                                    </div>
                                                    <span class="badge bg-primary text-white fs-6" id="session-percentage">100%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- نموذج جامعة طرابلس الرسمي: ثانياً: تقييم أداء المدرب -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header text-white py-3 d-flex justify-content-between align-items-center"
                            style="background: linear-gradient(135deg, #198754 0%, #157347 100%);">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-chalkboard-teacher me-2"></i> ثانياً: تقييم أداء المدرب (التقدير من 5)
                            </h5>
                            <span class="badge bg-white text-success fw-bold px-3 py-2 fs-6 shadow-sm" id="trainer-score-badge">
                                نسبة إجمالي المدرب: 100% (5.0 / 5)
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">#</th>
                                            <th>معيار تقييم المدرب</th>
                                            <th style="width: 320px;" class="text-center">التقدير (من 5)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $trainerCriteria = [
                                                'trainer_punctuality' => '1. الحضور والانضباط في الوقت',
                                                'trainer_clarity' => '2. وضوح الشرح والأسلوب',
                                                'trainer_management' => '3. القدرة على إدارة المتدربين وتحفيزهم',
                                                'trainer_interaction' => '4. التفاعل مع الأسئلة والمداخلات',
                                                'trainer_content_adherence' => '5. الالتزام بالمحتوى المتفق عليه',
                                                'trainer_professionalism' => '6. المهنية في التعامل',
                                                'trainer_methods' => '7. توظيف أساليب تدريب مناسبة',
                                            ];
                                            $tIndex = 1;
                                        @endphp

                                        @foreach($trainerCriteria as $tKey => $tLabel)
                                            <tr>
                                                <td class="text-center fw-bold text-muted">{{ $tIndex++ }}</td>
                                                <td class="fw-semibold text-dark">{{ $tLabel }}</td>
                                                <td>
                                                    <select name="trainer_criteria[{{ $tKey }}]" class="form-select border-success-subtle trainer-criterion-select" required>
                                                        <option value="5" selected>⭐⭐⭐⭐⭐ ممتاز (5/5)</option>
                                                        <option value="4">⭐⭐⭐⭐ جيد جداً (4/5)</option>
                                                        <option value="3">⭐⭐⭐ جيد (3/5)</option>
                                                        <option value="2">⭐⭐ مقبول (2/5)</option>
                                                        <option value="1">⭐ ضعيف (1/5)</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- بند رقم 8: نسبة إجمالي تقييم المدرب -->
                                        <tr class="table-success bg-opacity-25 fw-bold">
                                            <td class="text-center text-success fs-5">8</td>
                                            <td class="text-success fs-6">نسبة إجمالي تقييم المدرب (المحسوبة آلياً)</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 12px;">
                                                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="trainer-progress" style="width: 100%"></div>
                                                    </div>
                                                    <span class="badge bg-success text-white fs-6" id="trainer-percentage">100%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- نموذج جامعة طرابلس الرسمي: ثالثاً: ملاحظات قسم التقييم والمتابعة واعتماد المقيم -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-clipboard-check me-2"></i> ثالثاً: ملاحظات قسم التقييم والمتابعة وبيانات الاعتماد
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="fas fa-comment-dots text-primary me-1"></i> ملاحظات قسم التقييم والمتابعة:
                                </label>
                                <textarea name="comments" class="form-control border-2" rows="4"
                                    placeholder="أدخل أي ملاحظات أو توصيات خاصة بالجلسة التدريبية أو أداء المدرب أو بيئة التدريب..."></textarea>
                            </div>

                            <div class="row g-3 p-3 bg-light rounded border">
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-user-check me-1"></i> اسم مقيم التدريب</small>
                                    <span class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</span>
                                    <small class="text-primary d-block font-monospace">({{ auth()->user()->email }})</small>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-calendar-day me-1"></i> تاريخ الاعتماد والتقييم</small>
                                    <span class="fw-bold text-dark fs-6">{{ date('Y / m / d') }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-signature me-1"></i> حالة التوقيع والاعتماد</small>
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-shield-alt me-1"></i> معتمد إلكترونياً باسم المقيم
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قسم التوظيف (يظهر فقط في حال اختيار تقييم توظيف) -->
                <div id="employment-section" style="display: none;">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-briefcase me-2"></i> تقييم بيئة التوظيف والفرص</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @php
                                    $employment = [
                                        'workplace_quality' => 'جودة بيئة العمل وملاءمتها',
                                        'tools_equipment' => 'توفر الأدوات والتقنيات اللازمة',
                                        'safety' => 'معايير السلامة والأمان المهني',
                                        'work_culture' => 'ثقافة بيئة العمل والتعاون',
                                        'supervisor_support' => 'مستوى الدعم والإشراف المباشر',
                                        'guidance' => 'التوجيه والإرشاد والتطوير'
                                    ];
                                @endphp
                                @foreach($employment as $key => $label)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">{{ $label }}</label>
                                        <select name="employment[{{ $key }}]" class="form-select border-2">
                                            <option value="5" selected>⭐⭐⭐⭐⭐ ممتاز (5/5)</option>
                                            <option value="4">⭐⭐⭐⭐ جيد جداً (4/5)</option>
                                            <option value="3">⭐⭐⭐ جيد (3/5)</option>
                                            <option value="2">⭐⭐ مقبول (2/5)</option>
                                            <option value="1">⭐ ضعيف (1/5)</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="status" value="completed">

                <!-- أزرار الإرسال والاعتماد -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                        <a href="{{ route('evaluation-followup.evaluations.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-success btn-lg px-4 shadow">
                                <i class="fas fa-check-circle me-1"></i> حفظ واعتماد التقييم
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const trainingSelect = document.getElementById('training_id');
    const trainingWrapper = document.getElementById('training-select-wrapper');
    const autoDataCard = document.getElementById('training-auto-data-card');
    const officialSection = document.getElementById('official-training-evaluation-section');
    const employmentSection = document.getElementById('employment-section');

    // Metadata elements
    const metaTitle = document.getElementById('meta-training-title');
    const metaTrainer = document.getElementById('meta-trainer-name');
    const metaDept = document.getElementById('meta-department');
    const metaPeriod = document.getElementById('meta-period');
    const metaBeneficiaries = document.getElementById('meta-beneficiaries');
    const metaLocation = document.getElementById('meta-location');

    function updateTrainingMetadata() {
        if (!trainingSelect || !trainingSelect.value) {
            if (autoDataCard) autoDataCard.style.display = 'none';
            return;
        }

        const selected = trainingSelect.options[trainingSelect.selectedIndex];
        if (!selected) return;

        const title = selected.getAttribute('data-title') || selected.text;
        const trainer = selected.getAttribute('data-trainer') || 'غير محدد';
        const dept = selected.getAttribute('data-dept') || 'مكتب تدريب الخريجين';
        const start = selected.getAttribute('data-start') || '';
        const end = selected.getAttribute('data-end') || start;
        const beneficiaries = selected.getAttribute('data-beneficiaries') || '25';
        const location = selected.getAttribute('data-location') || 'جامعة طرابلس';

        let periodText = start;
        if (end && end !== start) {
            periodText = `من ${start} إلى ${end}`;
        }

        if (metaTitle) metaTitle.textContent = title;
        if (metaTrainer) metaTrainer.textContent = trainer;
        if (metaDept) metaDept.textContent = dept;
        if (metaPeriod) metaPeriod.textContent = periodText;
        if (metaBeneficiaries) metaBeneficiaries.textContent = beneficiaries + ' مستفيد';
        if (metaLocation) metaLocation.textContent = location;

        if (autoDataCard) {
            autoDataCard.style.display = 'block';
        }
    }

    function toggleFormSections() {
        const val = typeSelect ? typeSelect.value : 'training';
        if (val === 'training') {
            if (trainingWrapper) trainingWrapper.style.display = 'block';
            if (officialSection) officialSection.style.display = 'block';
            if (employmentSection) employmentSection.style.display = 'none';
            if (trainingSelect) trainingSelect.required = true;
            updateTrainingMetadata();
        } else {
            if (trainingWrapper) trainingWrapper.style.display = 'none';
            if (officialSection) officialSection.style.display = 'none';
            if (employmentSection) employmentSection.style.display = 'block';
            if (autoDataCard) autoDataCard.style.display = 'none';
            if (trainingSelect) trainingSelect.required = false;
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleFormSections);
    }

    if (trainingSelect) {
        trainingSelect.addEventListener('change', updateTrainingMetadata);
    }

    // Live calculation for Session Criteria (1-9) -> #10 Percentage
    const sessionSelects = document.querySelectorAll('.session-criterion-select');
    const sessionProgress = document.getElementById('session-progress');
    const sessionPercentage = document.getElementById('session-percentage');
    const sessionBadge = document.getElementById('session-score-badge');

    function calculateSessionStats() {
        let total = 0;
        let count = 0;
        sessionSelects.forEach(sel => {
            const val = parseFloat(sel.value);
            if (!isNaN(val) && val > 0) {
                total += val;
                count++;
            }
        });

        if (count > 0) {
            const avg = total / count;
            const pct = Math.round((avg / 5) * 100);
            if (sessionProgress) sessionProgress.style.width = pct + '%';
            if (sessionPercentage) sessionPercentage.textContent = pct + '%';
            if (sessionBadge) sessionBadge.textContent = `نسبة إجمالي الجلسة: ${pct}% (${avg.toFixed(2)} / 5)`;
        }
    }

    sessionSelects.forEach(sel => sel.addEventListener('change', calculateSessionStats));

    // Live calculation for Trainer Criteria (1-7) -> #8 Percentage
    const trainerSelects = document.querySelectorAll('.trainer-criterion-select');
    const trainerProgress = document.getElementById('trainer-progress');
    const trainerPercentage = document.getElementById('trainer-percentage');
    const trainerBadge = document.getElementById('trainer-score-badge');

    function calculateTrainerStats() {
        let total = 0;
        let count = 0;
        trainerSelects.forEach(sel => {
            const val = parseFloat(sel.value);
            if (!isNaN(val) && val > 0) {
                total += val;
                count++;
            }
        });

        if (count > 0) {
            const avg = total / count;
            const pct = Math.round((avg / 5) * 100);
            if (trainerProgress) trainerProgress.style.width = pct + '%';
            if (trainerPercentage) trainerPercentage.textContent = pct + '%';
            if (trainerBadge) trainerBadge.textContent = `نسبة إجمالي المدرب: ${pct}% (${avg.toFixed(2)} / 5)`;
        }
    }

    trainerSelects.forEach(sel => sel.addEventListener('change', calculateTrainerStats));

    // Initialize on load
    toggleFormSections();
    calculateSessionStats();
    calculateTrainerStats();

    // Auto-select first training if none selected
    if (trainingSelect && !trainingSelect.value && trainingSelect.options.length > 1) {
        trainingSelect.selectedIndex = 1;
        updateTrainingMetadata();
    }
});
</script>
@endsection