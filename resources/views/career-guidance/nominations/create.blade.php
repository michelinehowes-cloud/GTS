@extends('layouts.app')

@section('title', 'ترشيح خريج لفرصة عمل')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>ترشيح خريج لفرصة عمل</h3>
                    <a href="{{ route('career-guidance.nominations') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('career-guidance.nominations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- اختيار الخريج -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">اختيار الخريج</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="graduate_id" class="form-label">الخريج *</label>
                                            <select class="form-select @error('graduate_id') is-invalid @enderror" 
                                                    id="graduate_id" name="graduate_id" required>
                                                <option value="">اختر الخريج</option>
                                                @foreach($graduates as $graduate)
                                                    <option value="{{ $graduate->id }}" 
                                                            {{ old('graduate_id') == $graduate->id ? 'selected' : '' }}>
                                                        {{ $graduate->name }} - {{ $graduate->major }} ({{ $graduate->graduation_year }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('graduate_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- معلومات الخريج المختار -->
                                        <div id="graduate-info" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                            <h6>معلومات الخريج:</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small><strong>التخصص:</strong> <span id="info-major"></span></small>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>سنة التخرج:</strong> <span id="info-year"></span></small>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>المعدل:</strong> <span id="info-gpa"></span></small>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>الحالة:</strong> <span id="info-status"></span></small>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <small><strong>المهارات:</strong> <span id="info-skills"></span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- اختيار فرصة العمل -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">اختيار فرصة العمل</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="job_opportunity_id" class="form-label">فرصة العمل *</label>
                                            <select class="form-select @error('job_opportunity_id') is-invalid @enderror" 
                                                    id="job_opportunity_id" name="job_opportunity_id" required>
                                                <option value="">اختر فرصة العمل</option>
                                                @foreach($opportunities as $opportunity)
                                                    <option value="{{ $opportunity->id }}" 
                                                            {{ old('job_opportunity_id') == $opportunity->id ? 'selected' : '' }}>
                                                        {{ $opportunity->title }} - {{ $opportunity->company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('job_opportunity_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- معلومات الفرصة المختارة -->
                                        <div id="opportunity-info" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                            <h6>معلومات الفرصة:</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small><strong>الشركة:</strong> <span id="info-company"></span></small>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>النوع:</strong> <span id="info-type"></span></small>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>المكان:</strong> <span id="info-location"></span></small>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>المقاعد:</strong> <span id="info-seats"></span></small>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <small><strong>المهارات المطلوبة:</strong> <span id="info-required-skills"></span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أسباب الترشيح -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">أسباب الترشيح</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="matching_reasons" class="form-label">أسباب التطابق بين الخريج والفرصة *</label>
                                    <textarea class="form-control @error('matching_reasons') is-invalid @enderror" 
                                              id="matching_reasons" name="matching_reasons" rows="4" 
                                              placeholder="اذكر أسباب مناسبة الخريج لهذه الفرصة..." required>{{ old('matching_reasons') }}</textarea>
                                    @error('matching_reasons')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">مثال: تطابق التخصص، المهارات المتوافقة، الخبرات السابقة، إلخ.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="nomination_notes" class="form-label">ملاحظات إضافية</label>
                                    <textarea class="form-control @error('nomination_notes') is-invalid @enderror" 
                                              id="nomination_notes" name="nomination_notes" rows="3" 
                                              placeholder="ملاحظات إضافية حول الترشيح...">{{ old('nomination_notes') }}</textarea>
                                    @error('nomination_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراء -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('career-guidance.nominations') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>ترشيح الخريج
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// بيانات الخريجين
const graduatesData = @json($graduates->keyBy('id'));
// بيانات فرص العمل
const opportunitiesData = @json($opportunities->keyBy('id'));

// تحديث معلومات الخريج عند الاختيار
document.getElementById('graduate_id').addEventListener('change', function() {
    const graduateId = this.value;
    const graduateInfo = document.getElementById('graduate-info');
    const summaryGraduate = document.getElementById('summary-graduate');
    
    if (graduateId && graduatesData[graduateId]) {
        const graduate = graduatesData[graduateId];
        
        // تحديث معلومات الخريج
        document.getElementById('info-major').textContent = graduate.major;
        document.getElementById('info-year').textContent = graduate.graduation_year;
        document.getElementById('info-gpa').textContent = graduate.gpa || 'غير محدد';
        document.getElementById('info-status').textContent = getEmploymentStatusText(graduate.employment_status);
        document.getElementById('info-skills').textContent = graduate.skills ? graduate.skills.join(', ') : 'لا توجد مهارات';
        
        // إظهار قسم المعلومات
        graduateInfo.style.display = 'block';
    } else {
        graduateInfo.style.display = 'none';
    }
});

// تحديث معلومات فرصة العمل عند الاختيار
document.getElementById('job_opportunity_id').addEventListener('change', function() {
    const opportunityId = this.value;
    const opportunityInfo = document.getElementById('opportunity-info');
    
    if (opportunityId && opportunitiesData[opportunityId]) {
        const opportunity = opportunitiesData[opportunityId];
        
        // تحديث معلومات الفرصة
        document.getElementById('info-company').textContent = opportunity.company.name;
        document.getElementById('info-type').textContent = getOpportunityTypeText(opportunity.type);
        document.getElementById('info-location').textContent = opportunity.location;
        document.getElementById('info-seats').textContent = opportunity.seats;
        document.getElementById('info-required-skills').textContent = opportunity.required_skills ? 
            opportunity.required_skills.join(', ') : 'غير محدد';
        
        // إظهار قسم المعلومات
        opportunityInfo.style.display = 'block';
    } else {
        opportunityInfo.style.display = 'none';
    }
});

// دوال مساعدة
function getEmploymentStatusText(status) {
    const statuses = {
        'employed': 'موظف',
        'unemployed': 'غير موظف',
        'seeking_opportunities': 'باحث عن فرص',
        'continuing_education': 'مستمر في التعليم'
    };
    return statuses[status] || status;
}

function getOpportunityTypeText(type) {
    const types = {
        'job': 'وظيفة',
        'training': 'تدريب',
        'internship': 'تدريب عملي'
    };
    return types[type] || type;
}
</script>
@endsection
