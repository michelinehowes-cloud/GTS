@extends('layouts.app')

@section('title', 'إضافة خريج جديد')



@section('content')
<div class="container-fluid py-4">
<div class="registration-container">
<div class="registration-card">

            <!-- Header -->
            <div class="card-header">
                <h2><i class="fas fa-user-graduate me-2"></i> تسجيل خريج جديد</h2>
                <p>انضم إلينا للاستفادة من خدمات التدريب والتوظيف</p>
            </div>

            <!-- Body -->
            <div class="card-body">
                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">البيانات الشخصية</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">البيانات الأكاديمية</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">المهارات والخبرات</div>
                    </div>
                </div>

                <!-- Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-circle me-2"></i>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('career-guidance.graduates.store') }}" id="registrationForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Section 1: Personal Info -->
                    <div class="form-section" id="section-1">
                        <h5 class="section-title">
                            <i class="fas fa-user"></i>
                            البيانات الشخصية
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    الاسم الرباعي <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required placeholder="الاسم كما هو في الهوية">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="national_id" class="form-label">
                                    رقم القيد <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                    id="national_id" name="national_id" value="{{ old('national_id') }}" required
                                    placeholder="رقم القيد بالجامعة">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    البريد الإلكتروني <span class="required">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required placeholder="example@domain.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    رقم الهاتف <span class="required">*</span>
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            

                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">
                                    تاريخ الميلاد <span class="required">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label">
                                    الجنس <span class="required">*</span>
                                </label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                    name="gender" required>
                                    <option value="">اختر الجنس</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">
                                    المدينة <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                    name="city" value="{{ old('city') }}" required placeholder="طرابلس">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="address" class="form-label">
                                    العنوان بالتفصيل <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address') }}" required
                                    placeholder="الحي - الشارع">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Academic Info -->
                    <div class="form-section mt-5" id="section-2">
                        <h5 class="section-title">
                            <i class="fas fa-graduation-cap"></i>
                            البيانات الأكاديمية
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="university" class="form-label">
                                    الجامعة <span class="required">*</span>
                                </label>
                                <select class="form-select @error('university') is-invalid @enderror" id="university"
                                    name="university" required>
                                    <option value="">اختر الجامعة</option>
                                    <option value="جامعة طرابلس" {{ old('university') == 'جامعة طرابلس' ? 'selected' : '' }}>جامعة طرابلس</option>
                                </select>
                                @error('university')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sector" class="form-label">
                                    القطاع <span class="required">*</span>
                                </label>
                                <select class="form-select @error('sector') is-invalid @enderror" id="sector"
                                    name="sector" required disabled>
                                    <option value="">اختر القطاع</option>
                                </select>
                                <input type="hidden" id="old_sector" value="{{ old('sector') }}">
                                @error('sector')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="faculty" class="form-label">
                                    الكلية <span class="required">*</span>
                                </label>
                                <select class="form-select @error('faculty') is-invalid @enderror" id="faculty"
                                    name="faculty" required disabled>
                                    <option value="">اختر الكلية</option>
                                </select>
                                <input type="hidden" id="old_faculty" value="{{ old('faculty') }}">
                                @error('faculty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="qualification" class="form-label">
                                    المؤهل العلمي <span class="required">*</span>
                                </label>
                                <select class="form-select @error('qualification') is-invalid @enderror"
                                    id="qualification" name="qualification" required>
                                    <option value="">اختر المؤهل</option>
                                    <option value="بكالوريوس" {{ old('qualification') == 'بكالوريوس' ? 'selected' : '' }}>
                                        بكالوريوس</option>
                                    <option value="ليسانس" {{ old('qualification') == 'ليسانس' ? 'selected' : '' }}>ليسانس
                                    </option>
                                    <option value="ماجستير" {{ old('qualification') == 'ماجستير' ? 'selected' : '' }}>
                                        ماجستير</option>
                                    <option value="دكتوراه" {{ old('qualification') == 'دكتوراه' ? 'selected' : '' }}>
                                        دكتوراه</option>
                                    <option value="دبلوم" {{ old('qualification') == 'دبلوم' ? 'selected' : '' }}>دبلوم
                                    </option>
                                    <option value="دبلوم عالي" {{ old('qualification') == 'دبلوم عالي' ? 'selected' : '' }}>دبلوم عالي</option>
                                </select>
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="specialization" class="form-label">
                                    التخصص <span class="required">*</span>
                                </label>
                                <select class="form-select @error('specialization') is-invalid @enderror"
                                    id="specialization" name="specialization" required disabled>
                                    <option value="">اختر التخصص</option>
                                </select>
                                <input type="hidden" id="old_specialization" value="{{ old('specialization') }}">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="graduation_year" class="form-label">
                                    سنة التخرج <span class="required">*</span>
                                </label>
                                <input type="number" class="form-control @error('graduation_year') is-invalid @enderror"
                                    id="graduation_year" name="graduation_year" value="{{ old('graduation_year') }}"
                                    required min="1950" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                                @error('graduation_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="gpa" class="form-label">
                                    المعدل التراكمي
                                </label>
                                <input type="number" class="form-control @error('gpa') is-invalid @enderror" id="gpa"
                                    name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="4"
                                    placeholder="من 4.00">
                                @error('gpa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Skills & Experience -->
                    <div class="form-section mt-5" id="section-3">
                        <h5 class="section-title">
                            <i class="fas fa-briefcase"></i>
                            المهارات والخبرات
                        </h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="experiences" class="form-label">
                                    الخبرة العملية
                                </label>
                                <textarea class="form-control @error('experiences') is-invalid @enderror"
                                    id="experiences" name="experiences" rows="3"
                                    placeholder="اذكر خبراتك العملية السابقة إن وجدت...">{{ old('experiences') }}</textarea>
                                @error('experiences')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="skills" class="form-label">
                                    المهارات
                                </label>
                                <input type="text" class="form-control @error('skills') is-invalid @enderror"
                                    id="skills" name="skills" value="{{ old('skills') }}"
                                    placeholder="برمجة، تصميم، إدارة وقت">
                                <small class="help-text">افصل بين المهارات بفاصلة (,)</small>
                                @error('skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="languages" class="form-label">
                                    اللغات
                                </label>
                                <input type="text" class="form-control @error('languages') is-invalid @enderror"
                                    id="languages" name="languages" value="{{ old('languages') }}"
                                    placeholder="العربية، الإنجليزية">
                                <small class="help-text">افصل بين اللغات بفاصلة (,)</small>
                                @error('languages')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="cv" class="form-label">السيرة الذاتية (PDF)</label>
                                <input type="file" class="form-control @error('cv') is-invalid @enderror" id="cv" name="cv" accept=".pdf">
                                @error('cv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn-register" id="submitBtn">
                            <i class="fas fa-user-plus me-2"></i>
                            تسجيل حساب جديد
                        </button>
                    </div>
                </form>
            </div>

            
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
submitBtn.classList.add('loading');
submitBtn.disabled = true;
});
}
</script>
@endsection
