@extends('layouts.app')

@section('title', 'تعديل بيانات الخريج')

@php
    $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : 'career-guidance';
@endphp

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($prefix . '.dashboard')],
            ['label' => 'بيانات الخريجين', 'url' => route($prefix . '.graduates')],
            ['label' => $graduate->name, 'url' => route($prefix . '.graduates.show', $graduate->id)],
            ['label' => 'تعديل البيانات', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-user-edit me-2"></i>تعديل بيانات الخريج
            </h2>
            <div class="text-muted small mt-1">تحديث وتعديل بيانات الخريج: {{ $graduate->name }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route($prefix . '.graduates.show', $graduate->id) }}" class="btn btn-outline-info-modern">
                <i class="fas fa-eye me-1"></i> عرض البروفايل
            </a>
            <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Errors -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <strong><i class="fas fa-exclamation-circle me-2"></i>يرجى تصحيح الأخطاء التالية:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-edit me-2"></i>نموذج تعديل بيانات الخريج
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route($prefix . '.graduates.update', $graduate->id) }}" id="registrationForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section 1: Personal Info -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                        <i class="fas fa-user me-2"></i>1. البيانات الشخصية
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label-modern">الاسم الرباعي <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-modern @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $graduate->name) }}" required placeholder="الاسم كما هو في الهوية">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="national_id" class="form-label-modern">رقم القيد <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-modern @error('national_id') is-invalid @enderror"
                                id="national_id" name="national_id" value="{{ old('national_id', $graduate->national_id) }}" required
                                placeholder="رقم القيد بالجامعة">
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control-modern @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $graduate->email) }}" required placeholder="example@domain.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label-modern">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control-modern @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $graduate->phone) }}" required placeholder="09xxxxxxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label-modern">تاريخ الميلاد <span class="text-danger">*</span></label>
                            <input type="date" class="form-control-modern @error('date_of_birth') is-invalid @enderror"
                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($graduate->date_of_birth)->format('Y-m-d')) }}" required>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label-modern">الجنس <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('gender') is-invalid @enderror" id="gender"
                                name="gender" required>
                                <option value="">اختر الجنس</option>
                                <option value="male" {{ old('gender', $graduate->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender', $graduate->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label-modern">المدينة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-modern @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city', $graduate->city) }}" required placeholder="طرابلس">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="address" class="form-label-modern">العنوان بالتفصيل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-modern @error('address') is-invalid @enderror"
                                id="address" name="address" value="{{ old('address', $graduate->address) }}" required
                                placeholder="الحي - الشارع">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="employment_status" class="form-label-modern">حالة التوظيف <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('employment_status') is-invalid @enderror" id="employment_status"
                                name="employment_status" required>
                                <option value="seeking_opportunities" {{ old('employment_status', $graduate->employment_status) == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                                <option value="employed" {{ old('employment_status', $graduate->employment_status) == 'employed' ? 'selected' : '' }}>موظف</option>
                                <option value="unemployed" {{ old('employment_status', $graduate->employment_status) == 'unemployed' ? 'selected' : '' }}>عاطل عن العمل</option>
                                <option value="continuing_education" {{ old('employment_status', $graduate->employment_status) == 'continuing_education' ? 'selected' : '' }}>مستكمل للدراسة</option>
                            </select>
                            @error('employment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Academic Info -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                        <i class="fas fa-graduation-cap me-2"></i>2. البيانات الأكاديمية
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="university" class="form-label-modern">الجامعة <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('university') is-invalid @enderror" id="university"
                                name="university" required>
                                <option value="">اختر الجامعة</option>
                                <option value="جامعة طرابلس" {{ old('university', $graduate->university) == 'جامعة طرابلس' ? 'selected' : '' }}>جامعة طرابلس</option>
                            </select>
                            @error('university')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sector" class="form-label-modern">القطاع <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('sector') is-invalid @enderror" id="sector"
                                name="sector" required disabled>
                                <option value="">اختر القطاع</option>
                            </select>
                            <input type="hidden" id="old_sector" value="{{ old('sector', $graduate->sector) }}">
                            @error('sector')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="faculty" class="form-label-modern">الكلية <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('faculty') is-invalid @enderror" id="faculty"
                                name="faculty" required disabled>
                                <option value="">اختر الكلية</option>
                            </select>
                            <input type="hidden" id="old_faculty" value="{{ old('faculty', $graduate->faculty) }}">
                            @error('faculty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="qualification" class="form-label-modern">المؤهل العلمي <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('qualification') is-invalid @enderror"
                                id="qualification" name="qualification" required>
                                <option value="">اختر المؤهل</option>
                                <option value="بكالوريوس" {{ old('qualification', $graduate->degree) == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                <option value="ليسانس" {{ old('qualification', $graduate->degree) == 'ليسانس' ? 'selected' : '' }}>ليسانس</option>
                                <option value="ماجستير" {{ old('qualification', $graduate->degree) == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                                <option value="دكتوراه" {{ old('qualification', $graduate->degree) == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                                <option value="دبلوم" {{ old('qualification', $graduate->degree) == 'دبلوم' ? 'selected' : '' }}>دبلوم</option>
                                <option value="دبلوم عالي" {{ old('qualification', $graduate->degree) == 'دبلوم عالي' ? 'selected' : '' }}>دبلوم عالي</option>
                            </select>
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="specialization" class="form-label-modern">التخصص <span class="text-danger">*</span></label>
                            <select class="form-select-modern @error('specialization') is-invalid @enderror"
                                id="specialization" name="specialization" required disabled>
                                <option value="">اختر التخصص</option>
                            </select>
                            <input type="hidden" id="old_specialization" value="{{ old('specialization', $graduate->major) }}">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="graduation_year" class="form-label-modern">سنة التخرج <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-modern @error('graduation_year') is-invalid @enderror"
                                id="graduation_year" name="graduation_year" value="{{ old('graduation_year', $graduate->graduation_year) }}"
                                required min="1950" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                            @error('graduation_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="gpa" class="form-label-modern">المعدل التراكمي (%)</label>
                            <input type="number" class="form-control-modern @error('gpa') is-invalid @enderror" id="gpa"
                                name="gpa" value="{{ old('gpa', $graduate->gpa) }}" step="0.01" min="0" max="100"
                                placeholder="مثال: 85.50">
                            @error('gpa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Skills & Experience -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                        <i class="fas fa-briefcase me-2"></i>3. المهارات والخبرات
                    </h6>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="experiences" class="form-label-modern">الخبرة العملية</label>
                            <textarea class="form-control-modern @error('experiences') is-invalid @enderror"
                                id="experiences" name="experiences" rows="3"
                                placeholder="اذكر خبراتك العملية السابقة إن وجدت...">{{ old('experiences', $graduate->experiences) }}</textarea>
                            @error('experiences')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="skills" class="form-label-modern">المهارات</label>
                            <input type="text" class="form-control-modern @error('skills') is-invalid @enderror"
                                id="skills" name="skills" value="{{ old('skills', is_array($graduate->skills) ? implode(', ', $graduate->skills) : $graduate->skills) }}"
                                placeholder="برمجة، تصميم، إدارة وقت">
                            <small class="text-muted">افصل بين المهارات بفاصلة (,)</small>
                            @error('skills')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="languages" class="form-label-modern">اللغات</label>
                            <input type="text" class="form-control-modern @error('languages') is-invalid @enderror"
                                id="languages" name="languages" value="{{ old('languages', is_array($graduate->languages) ? implode(', ', $graduate->languages) : $graduate->languages) }}"
                                placeholder="العربية، الإنجليزية">
                            <small class="text-muted">افصل بين اللغات بفاصلة (,)</small>
                            @error('languages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="cv" class="form-label-modern">السيرة الذاتية (PDF)</label>
                            @if($graduate->cv_path)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank" class="badge bg-light text-primary border border-primary text-decoration-none px-3 py-2">
                                        <i class="fas fa-file-pdf me-1"></i> عرض السيرة الذاتية الحالية
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control-modern @error('cv') is-invalid @enderror" id="cv" name="cv" accept=".pdf">
                            @error('cv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary-modern px-5 py-2" id="submitBtn">
                        <i class="fas fa-save me-2"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/university-data.js') }}"></script>
<script>
const form = document.getElementById('registrationForm');
const submitBtn = document.getElementById('submitBtn');
if(form){
    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
    });
}
</script>
@endsection
