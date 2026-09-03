@extends('layouts.app')

@section('title', 'ترشيح خريج لفرصة عمل')

@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : (request()->routeIs('partnership.*') ? 'partnership' : 'career-guidance');
@endphp

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($routePrefix . '.dashboard')],
            ['label' => 'إدارة الترشيحات', 'url' => route($routePrefix . '.nominations')],
            ['label' => 'ترشيح خريج لفرصة', 'active' => true],
        ]
    ])

    <x-bento-form title="إنشاء ترشيح وظيفي جديد" subtitle="ربط وترشيح خريج مؤهل لفرصة عمل أو تدريب لدى شركة شريكة" icon="fa-paper-plane" :backRoute="route($routePrefix . '.nominations')">
            <form action="{{ route($routePrefix . '.nominations.store') }}" method="POST">
                @csrf
                
                <div class="row g-4">
                    <!-- اختيار الخريج -->
                    <div class="col-lg-6">
                        <div class="p-4 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-user-graduate me-2"></i>1. اختيار الخريج
                            </h6>

                            <div class="mb-3">
                                <label for="graduate_id" class="form-label-modern">الخريج المستهدف <span class="text-danger">*</span></label>
                                <select class="form-select-modern @error('graduate_id') is-invalid @enderror" 
                                        id="graduate_id" name="graduate_id" required>
                                    <option value="">-- اختر الخريج من القائمة --</option>
                                    @foreach($graduates as $graduate)
                                        <option value="{{ $graduate->id }}" 
                                                {{ (old('graduate_id') == $graduate->id || request('graduate_id') == $graduate->id) ? 'selected' : '' }}>
                                            {{ $graduate->name }} - {{ $graduate->major }} ({{ $graduate->graduation_year }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('graduate_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- بطاقة معلومات الخريج المختار -->
                            <div id="graduate-info" class="p-3 bg-white border rounded-3 mt-3" style="display: none;">
                                <h6 class="fw-bold text-dark mb-2 border-bottom pb-2">بطاقة الخريج:</h6>
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted d-block">التخصص:</span>
                                        <strong id="info-major" class="text-dark"></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">سنة التخرج:</span>
                                        <strong id="info-year" class="text-dark"></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">المعدل:</span>
                                        <strong id="info-gpa" class="text-dark"></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">حالة التوظيف:</span>
                                        <strong id="info-status" class="text-dark"></strong>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <span class="text-muted d-block">المهارات المسجلة:</span>
                                        <span id="info-skills" class="badge bg-light text-primary border border-primary mt-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- اختيار فرصة العمل -->
                    <div class="col-lg-6">
                        <div class="p-4 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="fas fa-briefcase me-2"></i>2. اختيار الفرصة الوظيفية
                            </h6>

                            <div class="mb-3">
                                <label for="job_opportunity_id" class="form-label-modern">فرصة العمل أو التدريب <span class="text-danger">*</span></label>
                                <select class="form-select-modern @error('job_opportunity_id') is-invalid @enderror" 
                                        id="job_opportunity_id" name="job_opportunity_id" required>
                                    <option value="">-- اختر الفرصة المتاحة --</option>
                                    @foreach($opportunities as $opportunity)
                                        <option value="{{ $opportunity->id }}" 
                                                {{ (old('job_opportunity_id') == $opportunity->id || request('opportunity_id') == $opportunity->id) ? 'selected' : '' }}>
                                            {{ $opportunity->title }} - {{ $opportunity->company->name ?? 'غير محدد' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('job_opportunity_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- بطاقة معلومات الفرصة المختارة -->
                            <div id="opportunity-info" class="p-3 bg-white border rounded-3 mt-3" style="display: none;">
                                <h6 class="fw-bold text-dark mb-2 border-bottom pb-2">تفاصيل الفرصة:</h6>
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted d-block">الشركة:</span>
                                        <strong id="info-company" class="text-dark"></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">النوع:</span>
                                        <strong id="info-type" class="text-dark"></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">الموقع:</span>
                                        <strong id="info-location" class="text-dark"></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">المقاعد المتاحة:</span>
                                        <strong id="info-seats" class="text-dark"></strong>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <span class="text-muted d-block">المهارات المطلوبة:</span>
                                        <span id="info-required-skills" class="badge bg-light text-success border border-success mt-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- أسباب الترشيح والملاحظات -->
                    <div class="col-12">
                        <div class="p-4 border rounded-3 bg-light">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-clipboard-check me-2 text-warning"></i>3. مبررات وأسباب الترشيح
                            </h6>

                            <div class="mb-3">
                                <label for="matching_reasons" class="form-label-modern">أسباب التطابق ومبررات الترشيح <span class="text-danger">*</span></label>
                                <textarea class="form-control-modern @error('matching_reasons') is-invalid @enderror" 
                                          id="matching_reasons" name="matching_reasons" rows="3" 
                                          placeholder="اذكر أسباب مناسبة الخريج لهذه الفرصة (التخصص، الكفاءة، الدورات التدريبية، التميز الأكاديمي...)" required>{{ old('matching_reasons') }}</textarea>
                                @error('matching_reasons')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label for="nomination_notes" class="form-label-modern">ملاحظات إضافية (اختياري)</label>
                                <textarea class="form-control-modern @error('nomination_notes') is-invalid @enderror" 
                                          id="nomination_notes" name="nomination_notes" rows="2" 
                                          placeholder="أي ملاحظات إضافية لمسؤول التوظيف بالشركة...">{{ old('nomination_notes') }}</textarea>
                                @error('nomination_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراء -->
                    <div class="col-12 d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route($routePrefix . '.nominations') }}" class="btn btn-secondary px-4 py-2 rounded-pill">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-paper-plane me-2"></i> إرسال الترشيح
                        </button>
                    </div>
                </div>
            </form>
    </x-bento-form>
</div>
@endsection

@section('scripts')
<script>
// بيانات الخريجين
const graduatesData = @json($graduates->keyBy('id'));
// بيانات فرص العمل
const opportunitiesData = @json($opportunities->keyBy('id'));

function updateGraduateDetails() {
    const graduateId = document.getElementById('graduate_id').value;
    const graduateInfo = document.getElementById('graduate-info');
    
    if (graduateId && graduatesData[graduateId]) {
        const graduate = graduatesData[graduateId];
        
        document.getElementById('info-major').textContent = graduate.major || '--';
        document.getElementById('info-year').textContent = graduate.graduation_year || '--';
        document.getElementById('info-gpa').textContent = graduate.gpa || 'غير محدد';
        document.getElementById('info-status').textContent = getEmploymentStatusText(graduate.employment_status);
        
        let skillsText = 'لا توجد مهارات مسجلة';
        if (graduate.skills) {
            skillsText = Array.isArray(graduate.skills) ? graduate.skills.join(', ') : graduate.skills;
        }
        document.getElementById('info-skills').textContent = skillsText;
        
        graduateInfo.style.display = 'block';
    } else {
        graduateInfo.style.display = 'none';
    }
}

function updateOpportunityDetails() {
    const opportunityId = document.getElementById('job_opportunity_id').value;
    const opportunityInfo = document.getElementById('opportunity-info');
    
    if (opportunityId && opportunitiesData[opportunityId]) {
        const opportunity = opportunitiesData[opportunityId];
        
        document.getElementById('info-company').textContent = opportunity.company ? opportunity.company.name : 'غير محدد';
        document.getElementById('info-type').textContent = getOpportunityTypeText(opportunity.type);
        document.getElementById('info-location').textContent = opportunity.location || 'غير محدد';
        document.getElementById('info-seats').textContent = opportunity.seats || '1';
        
        let skillsText = 'غير محدد';
        if (opportunity.required_skills) {
            skillsText = Array.isArray(opportunity.required_skills) ? opportunity.required_skills.join(', ') : opportunity.required_skills;
        }
        document.getElementById('info-required-skills').textContent = skillsText;
        
        opportunityInfo.style.display = 'block';
    } else {
        opportunityInfo.style.display = 'none';
    }
}

document.getElementById('graduate_id').addEventListener('change', updateGraduateDetails);
document.getElementById('job_opportunity_id').addEventListener('change', updateOpportunityDetails);

// تشغيل الفحص عند التحميل الأولي
document.addEventListener('DOMContentLoaded', function() {
    updateGraduateDetails();
    updateOpportunityDetails();
});

// دوال مساعدة
function getEmploymentStatusText(status) {
    const statuses = {
        'employed': 'موظف',
        'unemployed': 'غير موظف',
        'seeking_opportunities': 'باحث عن عمل',
        'continuing_education': 'يواصل دراسته'
    };
    return statuses[status] || status || 'غير محدد';
}

function getOpportunityTypeText(type) {
    const types = {
        'job': 'وظيفة',
        'training': 'تدريب',
        'internship': 'تدريب عملي'
    };
    return types[type] || type || 'وظيفة';
}
</script>
@endsection


