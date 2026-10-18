@extends('layouts.app')

@section('title', 'تعديل فرصة العمل - ' . $opportunity->title)

@section('content')

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6>يوجد أخطاء في البيانات:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <x-bento-form title="تعديل فرصة عمل: {{ $opportunity->title }}" subtitle="تحديث تفاصيل الفرصة الوظيفية ومتطلباتها" icon="fa-edit" :backRoute="route('job-opportunities.index')">
<form action="{{ route('job-opportunities.update', $opportunity->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">عنوان الفرصة *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $opportunity->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_id" class="form-label">الشركة *</label>
                                @if(auth()->user()->isCompany())
                                    <input type="hidden" name="company_id" value="{{ $opportunity->company_id }}">
                                    <input type="text" class="form-control bg-light" value="{{ $opportunity->company->name ?? '' }}" readonly>
                                @else
                                    <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                        <option value="">اختر الشركة</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id', $opportunity->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        {{-- تخصيص الوظيفة لمعرض التوظيف --}}
                        @if(isset($participatingFairs) && $participatingFairs->count() > 0)
                            @php
                                $isFairLinked = old('is_for_job_fair', !empty($opportunity->job_fair_id));
                                $currentFairId = old('job_fair_id', $opportunity->job_fair_id);
                            @endphp
                            <div class="card border border-warning-subtle rounded-4 mb-4 shadow-xs" style="background: linear-gradient(135deg, #fffdf5 0%, #fefce8 100%);">
                                <div class="card-body p-3.5">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" role="switch" id="is_for_job_fair" name="is_for_job_fair" value="1" {{ $isFairLinked ? 'checked' : '' }} style="cursor: pointer; width: 2.5rem; height: 1.3rem;">
                                            <label class="form-check-label fw-bold text-dark mb-0 fs-6" for="is_for_job_fair" style="cursor: pointer;">
                                                <i class="fas fa-calendar-alt text-warning me-1"></i>
                                                تخصيص هذه الوظيفة لمعرض التوظيف
                                            </label>
                                        </div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                            <i class="fas fa-shield-alt me-1"></i>تخضع لاعتماد إدارة المعرض
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-0 ms-md-4 ps-md-2">
                                        عند تفعيل هذا الخيار، سيتم ربط الوظيفة بجناح الشركة في المعرض ومؤشرات التوظيف الحية.
                                    </p>

                                    <div id="job_fair_selection_wrapper" class="mt-3 ps-md-4 {{ $isFairLinked ? '' : 'd-none' }}">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-7">
                                                <label for="job_fair_id" class="form-label small fw-bold text-dark mb-1">اختر معرض التوظيف المستهدف <span class="text-danger">*</span></label>
                                                <select name="job_fair_id" id="job_fair_id" class="form-select @error('job_fair_id') is-invalid @enderror">
                                                    @if($participatingFairs->count() > 1)
                                                        <option value="">-- اختر المعرض --</option>
                                                    @endif
                                                    @foreach($participatingFairs as $fair)
                                                        <option value="{{ $fair->id }}" {{ $currentFairId == $fair->id ? 'selected' : '' }}>
                                                            {{ $fair->title }} ({{ $fair->event_date ? $fair->event_date->format('Y-m-d') : 'مستمر' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('job_fair_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-5">
                                                <div class="alert alert-info py-2 px-3 small rounded-3 mb-0 border-0 bg-info-subtle text-info-emphasis">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    <strong>إشعار:</strong> لا تظهر الوظيفة في المعرض أو في مسح السير إلا بعد اعتمادها من الجهة المختصة.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description', $opportunity->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">نوع الفرصة *</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="job" {{ old('type', $opportunity->type) == 'job' ? 'selected' : '' }}>وظيفة</option>
                                    <option value="training" {{ old('type', $opportunity->type) == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="internship" {{ old('type', $opportunity->type) == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="contract_type" class="form-label">نوع العقد</label>
                                <select name="contract_type" id="contract_type" class="form-select @error('contract_type') is-invalid @enderror">
                                    <option value="">-- اختياري للتدريب أو اختر نوع العقد --</option>
                                    <option value="full_time" {{ old('contract_type', $opportunity->contract_type) == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                    <option value="part_time" {{ old('contract_type', $opportunity->contract_type) == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                    <option value="contract" {{ old('contract_type', $opportunity->contract_type) == 'contract' ? 'selected' : '' }}>عقد</option>
                                    <option value="freelance" {{ old('contract_type', $opportunity->contract_type) == 'freelance' ? 'selected' : '' }}>عمل حر</option>
                                </select>
                                @error('contract_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">الحالة *</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="new" {{ old('status', $opportunity->status) == 'new' ? 'selected' : '' }}>جديدة</option>
                                    <option value="open" {{ old('status', $opportunity->status) == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                    <option value="closed" {{ old('status', $opportunity->status) == 'closed' ? 'selected' : '' }}>مغلقة</option>
                                    <option value="completed" {{ old('status', $opportunity->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">المكان *</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $opportunity->location) }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="seats" class="form-label">عدد المقاعد *</label>
                                <input type="number" class="form-control @error('seats') is-invalid @enderror" 
                                       id="seats" name="seats" value="{{ old('seats', $opportunity->seats) }}" min="1" required>
                                @error('seats')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">تاريخ البدء *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $opportunity->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $opportunity->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="application_deadline" class="form-label">آخر موعد للتقديم *</label>
                                <input type="date" class="form-control @error('application_deadline') is-invalid @enderror" 
                                       id="application_deadline" name="application_deadline" value="{{ old('application_deadline', $opportunity->application_deadline->format('Y-m-d')) }}" required>
                                @error('application_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salary" class="form-label">الراتب</label>
                                <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                       id="salary" name="salary" value="{{ old('salary', $opportunity->salary) }}" min="0" step="0.01">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="required_experience" class="form-label">الخبرة المطلوبة</label>
                                <input type="text" class="form-control @error('required_experience') is-invalid @enderror" 
                                       id="required_experience" name="required_experience" value="{{ old('required_experience', $opportunity->required_experience) }}">
                                @error('required_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="required_specializations" class="form-label">التخصصات المطلوبة</label>
                                <select name="required_specializations[]" id="required_specializations" 
                                        class="form-select @error('required_specializations') is-invalid @enderror" multiple style="min-height: 140px;">
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization }}" {{ in_array($specialization, old('required_specializations', $opportunity->required_specializations ?? [])) ? 'selected' : '' }}>
                                            {{ $specialization }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">اضغط Ctrl لاختيار أكثر من تخصص</div>
                                @error('required_specializations')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="required_skills" class="form-label">المهارات المطلوبة</label>
                                <select name="required_skills[]" id="required_skills" 
                                        class="form-select @error('required_skills') is-invalid @enderror" multiple style="min-height: 140px;">
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill }}" {{ in_array($skill, old('required_skills', $opportunity->required_skills ?? [])) ? 'selected' : '' }}>
                                            {{ $skill }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">اضغط Ctrl لاختيار أكثر من مهارة</div>
                                @error('required_skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">المزايا</label>
                            <textarea class="form-control @error('benefits') is-invalid @enderror" 
                                      id="benefits" name="benefits" rows="3">{{ old('benefits', $opportunity->benefits) }}</textarea>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">المتطلبات العامة</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                      id="requirements" name="requirements" rows="3">{{ old('requirements', $opportunity->requirements) }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('job-opportunities.show', $opportunity->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
</x-bento-form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isForJobFair = document.getElementById('is_for_job_fair');
    const jobFairWrapper = document.getElementById('job_fair_selection_wrapper');
    const jobFairSelect = document.getElementById('job_fair_id');
    if (isForJobFair && jobFairWrapper) {
        isForJobFair.addEventListener('change', function() {
            if (this.checked) {
                jobFairWrapper.classList.remove('d-none');
                if (jobFairSelect && jobFairSelect.options.length === 1) {
                    jobFairSelect.selectedIndex = 0;
                }
            } else {
                jobFairWrapper.classList.add('d-none');
            }
        });
    }
});
</script>
@endsection