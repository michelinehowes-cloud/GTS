@extends('layouts.app')

@section('title', 'تعديل التقييم الشامل')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i> تعديل نموذج التقييم الشامل</h5>
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

                <form action="{{ route('evaluation-followup.evaluations.update', $evaluation->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- المعلومات الأساسية -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-info-circle"></i> المعلومات الأساسية
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">نوع التقييم <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="type" name="type" required>
                                        <option value="">-- اختر --</option>
                                        <option value="training" {{ old('type', $evaluation->evaluation_type) == 'training' ? 'selected' : '' }}>📚 تقييم تدريب</option>
                                        <option value="employment" {{ old('type', $evaluation->evaluation_type) == 'employment' ? 'selected' : '' }}>💼 تقييم توظيف</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3" id="training-wrapper">
                                    <label class="form-label fw-bold">التدريب <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="training_id" name="training_id" required>
                                        <option value="">-- اختر --</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}" 
                                                data-start="{{ $training->start_date }}" 
                                                data-end="{{ $training->end_date }}"
                                                {{ old('training_id', $evaluation->training_id) == $training->id ? 'selected' : '' }}>
                                                {{ $training->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">تاريخ التقييم <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg" name="evaluation_date" value="{{ old('evaluation_date', \Carbon\Carbon::parse($evaluation->evaluation_date)->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييم التدريب -->
                    <div id="training-section" style="display: {{ old('type', $evaluation->evaluation_type) == 'training' ? 'block' : 'none' }};">

                        <!-- التجهيزات -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white"><i class="fas fa-building"></i> تقييم التجهيزات والمرافق</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $facilities = [
                                            'room_quality' => 'جودة القاعة التدريبية',
                                            'equipment' => 'التجهيزات والأدوات',
                                            'comfort' => 'الراحة والإضاءة',
                                            'cleanliness' => 'النظافة والترتيب'
                                        ];
                                        $currentFacilities = old('facilities', $evaluation->facilities_evaluation ?? []);
                                    @endphp
                                    @foreach($facilities as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="facilities[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5" {{ ($currentFacilities[$key] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4" {{ ($currentFacilities[$key] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3" {{ ($currentFacilities[$key] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                <option value="2" {{ ($currentFacilities[$key] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                <option value="1" {{ ($currentFacilities[$key] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- تقييمات الأيام (للتدريبات متعددة الأيام) -->
                        <div id="day-evaluations-section">
                             <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-calendar-day"></i> تقييمات الأيام
                                </div>
                                <div class="card-body">
                                    <div id="day-evaluations-container">
                                        @php
                                            $contentData = old('daily_content', $evaluation->content_evaluation ?? []);
                                            // Fallback calculation if empty but training exists
                                            if (empty($contentData) && $evaluation->training) {
                                                $start = \Carbon\Carbon::parse($evaluation->training->start_date);
                                                $end = \Carbon\Carbon::parse($evaluation->training->end_date);
                                                $diffDays = $start->diffInDays($end) + 1;
                                                if ($diffDays > 1) {
                                                    for($i=1; $i<=$diffDays; $i++) {
                                                        $contentData["day_$i"] = [];
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @foreach($contentData as $dayKey => $values)
                                             @php 
                                                 $dayIndex = str_replace('day_', '', $dayKey);
                                                 $dayLabels = [
                                                    'clarity' => 'وضوح المحتوى',
                                                    'relevance' => 'الارتباط بالأهداف',
                                                    'engagement' => 'التفاعل والمشاركة'
                                                 ];
                                             @endphp
                                             <div class="card mb-3 border-light">
                                                <div class="card-header bg-light">
                                                    <strong>اليوم {{ $dayIndex }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-2 text-muted">تقييم محاور اليوم</h6>
                                                    <div class="row">
                                                        @foreach($dayLabels as $k => $l)
                                                            <div class="col-md-4 mb-2">
                                                                <label class="form-label small">{{ $l }}</label>
                                                                <select name="daily_content[{{ $dayKey }}][{{ $k }}]" class="form-select form-select-sm">
                                                                    <option value="">-- اختر --</option>
                                                                    <option value="5" {{ ($values[$k] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                                    <option value="4" {{ ($values[$k] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                                    <option value="3" {{ ($values[$k] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                                    <option value="2" {{ ($values[$k] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                                    <option value="1" {{ ($values[$k] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
                                                                </select>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- تقييمات المدربين -->
                        <div id="trainer-evaluations-section" style="display: block;">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <i class="fas fa-chalkboard-teacher"></i> تقييمات المدربين
                                </div>
                                <div class="card-body">
                                    <button type="button" id="add-trainer-btn" class="btn btn-sm btn-outline-primary mb-3"><i class="fas fa-plus"></i> إضافة مدرب للتقييم</button>
                                    <div id="trainer-evaluations-container">
                                        @foreach($evaluation->trainerEvaluations as $index => $trainerEval)
                                            <div class="row mb-3 border-bottom pb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">المدرب</label>
                                                    <select name="instructors[{{ $index }}][id]" class="form-select" required>
                                                        <option value="">-- اختر المدرب --</option>
                                                        @foreach($trainers as $trainer)
                                                            <option value="{{ $trainer->id }}" {{ $trainerEval->trainer_id == $trainer->id ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">التقييم العام</label>
                                                     <select name="instructors[{{ $index }}][rating]" class="form-select" required>
                                                        <option value="5" {{ $trainerEval->rating == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                        <option value="4" {{ $trainerEval->rating == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                        <option value="3" {{ $trainerEval->rating == 3 ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                        <option value="2" {{ $trainerEval->rating == 2 ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                        <option value="1" {{ $trainerEval->rating == 1 ? 'selected' : '' }}>⭐ ضعيف</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                     <label class="form-label">ملاحظات</label>
                                                     <input type="text" name="instructors[{{ $index }}][comments]" class="form-control" value="{{ $trainerEval->comments }}" placeholder="ملاحظات حول المدرب">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- التنظيم -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white"><i class="fas fa-tasks"></i> تقييم التنظيم والإدارة</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $organization = [
                                            'scheduling' => 'الجدول الزمني',
                                            'coordination' => 'التنسيق والتنظيم',
                                            'support' => 'الدعم الإداري',
                                            'communication_admin' => 'التواصل الإداري'
                                        ];
                                        $currentOrganization = old('organization', $evaluation->organization_evaluation ?? []);
                                    @endphp
                                    @foreach($organization as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="organization[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5" {{ ($currentOrganization[$key] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4" {{ ($currentOrganization[$key] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3" {{ ($currentOrganization[$key] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                <option value="2" {{ ($currentOrganization[$key] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                <option value="1" {{ ($currentOrganization[$key] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- الأثر -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white"><i class="fas fa-chart-line"></i> تقييم الأثر والاستفادة</div>
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
                                        $currentImpact = old('impact', $evaluation->impact_evaluation ?? []);
                                    @endphp
                                    @foreach($impact as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="impact[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5" {{ ($currentImpact[$key] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4" {{ ($currentImpact[$key] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3" {{ ($currentImpact[$key] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                <option value="2" {{ ($currentImpact[$key] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                <option value="1" {{ ($currentImpact[$key] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييم التوظيف -->
                    <div id="employment-section" style="display: {{ old('type', $evaluation->evaluation_type) == 'employment' ? 'block' : 'none' }};">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white"><i class="fas fa-building"></i> تقييم بيئة العمل</div>
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
                                        $currentEmployment = old('employment', $evaluation->employment_evaluation ?? []);
                                    @endphp
                                    @foreach($employment as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="employment[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5" {{ ($currentEmployment[$key] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4" {{ ($currentEmployment[$key] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3" {{ ($currentEmployment[$key] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                <option value="2" {{ ($currentEmployment[$key] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                <option value="1" {{ ($currentEmployment[$key] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
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
                                    <textarea class="form-control" name="strengths" rows="3" placeholder="ما هي نقاط القوة؟">{{ old('strengths', $evaluation->strengths) }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نقاط الضعف</label>
                                    <textarea class="form-control" name="weaknesses" rows="3" placeholder="ما هي نقاط الضعف؟">{{ old('weaknesses', $evaluation->weaknesses) }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التعليقات</label>
                                    <textarea class="form-control" name="comments" rows="3" placeholder="تعليقات إضافية">{{ old('comments', $evaluation->comments) }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">التوصيات</label>
                                    <textarea class="form-control" name="recommendations" rows="3" placeholder="توصياتك للتحسين">{{ old('recommendations', $evaluation->recommendations) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="status" value="{{ old('status', $evaluation->status) }}">

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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const trainingSelect = document.getElementById('training_id');
        const typeSelect = document.getElementById('type');
        const dayContainer = document.getElementById('day-evaluations-container');
        const daySection = document.getElementById('day-evaluations-section');
        const trainerContainer = document.getElementById('trainer-evaluations-container');
        const addTrainerBtn = document.getElementById('add-trainer-btn');

        // Initial setup for trainers (if any)
        if (addTrainerBtn) {
            addTrainerBtn.addEventListener('click', renderTrainerRow);
        }

        // Only attach change listener to Training if we want dynamic updates on change
        if (trainingSelect) {
            trainingSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption.value) return;

                const startDateStr = selectedOption.getAttribute('data-start');
                const endDateStr = selectedOption.getAttribute('data-end');

                // If training changed, we might want to reset/re-calculate days
                // Only do this if it's a NEW selection (user interaction), not initial load
                // Since this runs on 'change', it is fine
                
                dayContainer.innerHTML = ''; // Clear previous
                
                if (startDateStr && endDateStr) {
                    const start = new Date(startDateStr);
                    const end = new Date(endDateStr);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 

                    if (diffDays > 1) {
                         daySection.style.display = 'block';
                         for (let i = 1; i <= diffDays; i++) {
                             renderDayRow(i);
                         }
                    } else {
                        daySection.style.display = 'none';
                    }
                }
            });
        }
        
        // Handle Type Change
        const trainingWrapper = document.getElementById('training-wrapper');

        function toggleSections() {
            if (!typeSelect) return;
            const type = typeSelect.value;

            // Reset
            document.getElementById('training-section').style.display = 'none';
            document.getElementById('employment-section').style.display = 'none';
            if (trainingWrapper) trainingWrapper.style.display = 'none';
            if (trainingSelect) trainingSelect.required = false;

            if (type === 'training') {
                document.getElementById('training-section').style.display = 'block';
                if (trainingWrapper) trainingWrapper.style.display = 'block';
                if (trainingSelect) trainingSelect.required = true;
            } else if (type === 'employment') {
                document.getElementById('employment-section').style.display = 'block';
            }
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', toggleSections);
            toggleSections(); // Init
        }

        function renderDayRow(i) {
             const dayContent = {
                'clarity': 'وضوح المحتوى',
                'relevance': 'الارتباط بالأهداف',
                'engagement': 'التفاعل والمشاركة'
             };
             
             let optionsHtml = '';
             for (const [key, label] of Object.entries(dayContent)) {
                 optionsHtml += `
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">${label}</label>
                        <select name="daily_content[day_${i}][${key}]" class="form-select form-select-sm">
                            <option value="">-- اختر --</option>
                            <option value="5">⭐⭐⭐⭐⭐ ممتاز</option>
                            <option value="4">⭐⭐⭐⭐ جيد جداً</option>
                            <option value="3">⭐⭐⭐ جيد</option>
                            <option value="2">⭐⭐ مقبول</option>
                            <option value="1">⭐ ضعيف</option>
                        </select>
                    </div>
                 `;
             }

             const dayHtml = `
                <div class="card mb-3 border-light">
                    <div class="card-header bg-light">
                        <strong>اليوم ${i}</strong>
                    </div>
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">تقييم محاور اليوم</h6>
                        <div class="row">
                            ${optionsHtml}
                        </div>
                    </div>
                </div>
             `;
             dayContainer.insertAdjacentHTML('beforeend', dayHtml);
        }

        function renderTrainerRow() {
            const index = Date.now(); // Use timestamp for unique index in JS added rows
            const trainersOptions = `
                <option value="">-- اختر المدرب --</option>
                @foreach($trainers as $trainer)
                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                @endforeach
            `;
            
            const html = `
                <div class="row mb-3 border-bottom pb-3">
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
                    <div class="col-md-5">
                         <label class="form-label">ملاحظات</label>
                         <input type="text" name="instructors[${index}][comments]" class="form-control" placeholder="ملاحظات حول المدرب">
                    </div>
                    <div class="col-12 text-end">
                         <button type="button" class="btn btn-sm btn-danger remove-trainer-btn">حذف</button>
                    </div>
                </div>
            `;
            trainerContainer.insertAdjacentHTML('beforeend', html);
            
            // Re-attach delete listener
            const deleteBtns = document.querySelectorAll('.remove-trainer-btn');
            deleteBtns.forEach(btn => {
                btn.onclick = function() { this.closest('.row').remove(); };
            });
        }
        
        // Initial delete buttons for existing trainers (if we add them)
    });
    </script>
@endsection
