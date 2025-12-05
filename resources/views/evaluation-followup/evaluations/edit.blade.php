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
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">التدريب <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" name="training_id" required>
                                        <option value="">-- اختر --</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}" {{ old('training_id', $evaluation->evaluatable_id) == $training->id ? 'selected' : '' }}>{{ $training->title }}</option>
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

                        <!-- المحتوى -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white"><i class="fas fa-book-open"></i> تقييم المحتوى التدريبي</div>
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
                                        $currentContent = old('content', $evaluation->content_evaluation ?? []);
                                    @endphp
                                    @foreach($content as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="content[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5" {{ ($currentContent[$key] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4" {{ ($currentContent[$key] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3" {{ ($currentContent[$key] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                <option value="2" {{ ($currentContent[$key] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                <option value="1" {{ ($currentContent[$key] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- أداء المدرب -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark"><i class="fas fa-chalkboard-teacher"></i> تقييم أداء المدرب</div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $trainer = [
                                            'knowledge' => 'المعرفة والخبرة',
                                            'communication' => 'مهارات التواصل',
                                            'interaction' => 'التفاعل مع المتدربين',
                                            'time_management' => 'إدارة الوقت',
                                            'motivation' => 'القدرة على التحفيز'
                                        ];
                                        $currentTrainer = old('trainer', $evaluation->trainer_evaluation ?? []);
                                    @endphp
                                    @foreach($trainer as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <select name="trainer[{{ $key }}]" class="form-select">
                                                <option value="">-- اختر --</option>
                                                <option value="5" {{ ($currentTrainer[$key] ?? '') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ ممتاز</option>
                                                <option value="4" {{ ($currentTrainer[$key] ?? '') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ جيد جداً</option>
                                                <option value="3" {{ ($currentTrainer[$key] ?? '') == '3' ? 'selected' : '' }}>⭐⭐⭐ جيد</option>
                                                <option value="2" {{ ($currentTrainer[$key] ?? '') == '2' ? 'selected' : '' }}>⭐⭐ مقبول</option>
                                                <option value="1" {{ ($currentTrainer[$key] ?? '') == '1' ? 'selected' : '' }}>⭐ ضعيف</option>
                                            </select>
                                        </div>
                                    @endforeach
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
    document.getElementById('type').addEventListener('change', function() {
        document.getElementById('training-section').style.display = 'none';
        document.getElementById('employment-section').style.display = 'none';

        if (this.value === 'training') {
            document.getElementById('training-section').style.display = 'block';
        } else if (this.value === 'employment') {
            document.getElementById('employment-section').style.display = 'block';
        }
    });
    </script>
@endsection
