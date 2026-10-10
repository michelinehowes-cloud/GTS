@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="{{ $user->name }}"
        subtitle="{{ $user->email }} — منصة تدريب وتأهيل الخريجين"
        icon="fas fa-user-graduate"
        :breadcrumbs="[
            ['label' => 'لوحة التحكم', 'url' => route('graduate.dashboard')],
            ['label' => 'الملف الشخصي']
        ]"
        badge="خريج نشط"
        badgeIcon="fas fa-id-card"
        :secondaryBadge="$user->qualification ?? null"
    >
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    <div class="row g-4">
        <!-- البيانات الشخصية والأكاديمية (العمود الرئيسي) -->
        <div class="col-lg-8">
            <form action="{{ route('graduate.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- البيانات الشخصية -->
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                            <i class="fas fa-user-edit me-2 text-primary"></i>البيانات الشخصية
                        </h5>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small">
                            <i class="fas fa-id-badge me-1"></i> معلومات الهوية
                        </span>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <!-- الاسم الكامل -->
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-bold text-secondary">الاسم الكامل <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('name') is-invalid @enderror" 
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-secondary">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control rounded-end-3 border-start-0 @error('email') is-invalid @enderror" 
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-bold text-secondary">رقم الهاتف</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('phone') is-invalid @enderror" 
                                        id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="09xxxxxxxx">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الرقم الوطني / رقم القيد -->
                            <div class="col-md-6">
                                <label for="national_id" class="form-label small fw-bold text-secondary">الرقم الوطني / رقم القيد</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('national_id') is-invalid @enderror" 
                                        id="national_id" name="national_id" value="{{ old('national_id', $user->national_id) }}" placeholder="الرقم الوطني أو رقم القيد الجامعي">
                                </div>
                                @error('national_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تاريخ الميلاد -->
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label small fw-bold text-secondary">تاريخ الميلاد</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="far fa-calendar-alt text-muted"></i></span>
                                    @php
                                        $dobVal = $user->date_of_birth;
                                        $dobStr = $dobVal instanceof \DateTimeInterface ? $dobVal->format('Y-m-d') : (is_string($dobVal) ? substr($dobVal, 0, 10) : '');
                                    @endphp
                                    <input type="date" class="form-control rounded-end-3 border-start-0 @error('date_of_birth') is-invalid @enderror" 
                                        id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $dobStr) }}">
                                </div>
                                @error('date_of_birth')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الجنس -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label small fw-bold text-secondary">الجنس</label>
                                <select class="form-select rounded-3 @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="" disabled>اختر الجنس</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المدينة -->
                            <div class="col-md-6">
                                <label for="city" class="form-label small fw-bold text-secondary">المدينة</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('city') is-invalid @enderror" 
                                        id="city" name="city" value="{{ old('city', $user->city) }}" placeholder="مثال: طرابلس">
                                </div>
                                @error('city')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- العنوان التفصيلي -->
                            <div class="col-md-6">
                                <label for="address" class="form-label small fw-bold text-secondary">العنوان التفصيلي / السكن</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-home text-muted"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('address') is-invalid @enderror" 
                                        id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="المنطقة، الحي، الشارع...">
                                </div>
                                @error('address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- البيانات الأكاديمية وحالة العمل -->
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>البيانات الأكاديمية وحالة العمل
                        </h5>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small">
                            <i class="fas fa-university me-1"></i> التعليم والتخصص
                        </span>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <!-- المؤهل العلمي -->
                            <div class="col-md-6">
                                <label for="qualification" class="form-label small fw-bold text-secondary">المؤهل العلمي <span class="text-danger">*</span></label>
                                @php $currentDegree = old('qualification', old('degree', $user->qualification ?? $user->degree)); @endphp
                                <select class="form-select rounded-3 @error('qualification') is-invalid @enderror" id="qualification" name="qualification">
                                    <option value="" disabled>اختر المؤهل</option>
                                    <option value="بكالوريوس" {{ $currentDegree == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                    <option value="ليسانس" {{ $currentDegree == 'ليسانس' ? 'selected' : '' }}>ليسانس</option>
                                    <option value="ماجستير" {{ $currentDegree == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                                    <option value="دكتوراه" {{ $currentDegree == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                                    <option value="دبلوم" {{ $currentDegree == 'دبلوم' ? 'selected' : '' }}>دبلوم</option>
                                    <option value="دبلوم عالي" {{ $currentDegree == 'دبلوم عالي' ? 'selected' : '' }}>دبلوم عالي</option>
                                </select>
                                <input type="hidden" name="degree" id="hidden_degree" value="{{ $currentDegree }}">
                                @error('qualification')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الجامعة -->
                            <div class="col-md-6">
                                <label for="university" class="form-label small fw-bold text-secondary">الجامعة <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 @error('university') is-invalid @enderror" id="university" name="university">
                                    <option value="">اختر الجامعة</option>
                                    <option value="جامعة طرابلس" {{ old('university', $user->university ?? 'جامعة طرابلس') == 'جامعة طرابلس' ? 'selected' : '' }}>جامعة طرابلس</option>
                                </select>
                                <input type="hidden" id="old_university" value="{{ old('university', $user->university ?? 'جامعة طرابلس') }}">
                                @error('university')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- القطاع -->
                            <div class="col-md-6">
                                <label for="sector" class="form-label small fw-bold text-secondary">القطاع <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 @error('sector') is-invalid @enderror" id="sector" name="sector" disabled>
                                    <option value="">اختر القطاع</option>
                                </select>
                                <input type="hidden" id="old_sector" value="{{ old('sector', $user->sector) }}">
                                @error('sector')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الكلية -->
                            <div class="col-md-6">
                                <label for="faculty" class="form-label small fw-bold text-secondary">الكلية <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 @error('faculty') is-invalid @enderror" id="faculty" name="faculty" disabled>
                                    <option value="">اختر الكلية</option>
                                </select>
                                <input type="hidden" id="old_faculty" value="{{ old('faculty', $user->faculty) }}">
                                @error('faculty')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- التخصص -->
                            <div class="col-md-6">
                                <label for="specialization" class="form-label small fw-bold text-secondary">التخصص <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 @error('specialization') is-invalid @enderror" id="specialization" name="specialization" disabled>
                                    <option value="">اختر التخصص</option>
                                </select>
                                @php $currentSpec = old('specialization', old('major', $user->specialization ?? $user->major)); @endphp
                                <input type="hidden" id="old_specialization" value="{{ $currentSpec }}">
                                <input type="hidden" name="major" id="hidden_major" value="{{ $currentSpec }}">
                                @error('specialization')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- حالة التوظيف -->
                            <div class="col-md-6">
                                <label for="employment_status" class="form-label small fw-bold text-secondary">حالة التوظيف الحالية</label>
                                @php 
                                    $currentEmp = old('employment_status', $user->employment_status ?? $user->graduateData?->employment_status ?? 'seeking_opportunities'); 
                                @endphp
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-user-tie text-muted"></i></span>
                                    <select class="form-select rounded-end-3 border-start-0 @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status">
                                        <option value="seeking_opportunities" {{ $currentEmp == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن فرصة عمل</option>
                                        <option value="employed" {{ $currentEmp == 'employed' ? 'selected' : '' }}>موظف حالياً</option>
                                        <option value="unemployed" {{ $currentEmp == 'unemployed' ? 'selected' : '' }}>غير موظف / متفرغ</option>
                                        <option value="further_study" {{ in_array($currentEmp, ['further_study', 'continuing_education']) ? 'selected' : '' }}>مستكمل للدراسات العليا</option>
                                    </select>
                                </div>
                                @error('employment_status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- سنة التخرج -->
                            <div class="col-md-3 col-6">
                                <label for="graduation_year" class="form-label small fw-bold text-secondary">سنة التخرج</label>
                                <input type="number" class="form-control rounded-3 @error('graduation_year') is-invalid @enderror" 
                                    id="graduation_year" name="graduation_year" value="{{ old('graduation_year', $user->graduation_year) }}" 
                                    min="1950" max="{{ date('Y') + 1 }}">
                                @error('graduation_year')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المعدل التراكمي -->
                            <div class="col-md-3 col-6">
                                <label for="gpa" class="form-label small fw-bold text-secondary">المعدل (%)</label>
                                <input type="number" class="form-control rounded-3 @error('gpa') is-invalid @enderror" 
                                    id="gpa" name="gpa" value="{{ old('gpa', $user->gpa) }}" min="0" max="100" step="0.01" 
                                    placeholder="85.50">
                                @error('gpa')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- اللغات -->
                            <div class="col-md-6 col-12">
                                <label for="languages" class="form-label small fw-bold text-secondary">اللغات المتقنة</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-language text-muted"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('languages') is-invalid @enderror" 
                                        id="languages" name="languages" 
                                        value="{{ old('languages', $user->languages_text) }}" 
                                        placeholder="العربية، الإنجليزية، الفرنسية...">
                                </div>
                                @error('languages')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المهارات والخبرات والدورات التدريبية -->
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                            <i class="fas fa-tools me-2 text-primary"></i>المهارات والخبرات والدورات التدريبية
                        </h5>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-3 py-1.5 small">
                            <i class="fas fa-star me-1 text-warning"></i> الكفاءة المهنية
                        </span>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <!-- المهارات -->
                            <div class="col-12">
                                <label for="skills" class="form-label small fw-bold text-secondary">
                                    المهارات التقنية والشخصية
                                    <span class="text-muted fw-normal">(افصل بين المهارات بفاصلة)</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-laptop-code text-primary"></i></span>
                                    <input type="text" class="form-control rounded-end-3 border-start-0 @error('skills') is-invalid @enderror" 
                                        id="skills" name="skills" 
                                        value="{{ old('skills', $user->skills_text) }}" 
                                        placeholder="مثال: تحليل البيانات، إدارة المشاريع، بايثون، تصميم الجرافيك، حل المشكلات...">
                                </div>
                                
                                @php
                                    $skillsList = is_array($user->skills) ? $user->skills : (is_string($user->skills) ? array_filter(array_map('trim', explode(',', str_replace('،', ',', $user->skills)))) : []);
                                @endphp
                                @if(!empty($skillsList) && count($skillsList) > 0)
                                    <div class="d-flex flex-wrap gap-1.5 align-items-center mt-2 p-2 bg-light rounded-3">
                                        <small class="text-muted fw-bold me-2"><i class="fas fa-tags text-primary me-1"></i>المهارات الحالية المسجلة:</small>
                                        @foreach($skillsList as $sk)
                                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1.5" style="font-size: 0.78rem;">
                                                {{ $sk }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @error('skills')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الدورات التدريبية والشهادات المهنية الإضافية -->
                            <div class="col-12">
                                <label for="education" class="form-label small fw-bold text-secondary">
                                    الدورات التدريبية والشهادات المهنية الإضافية
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3 align-items-start pt-2"><i class="fas fa-certificate text-warning"></i></span>
                                    <textarea class="form-control rounded-end-3 border-start-0 @error('education') is-invalid @enderror" 
                                        id="education" name="education" rows="3" 
                                        placeholder="اذكر الدورات التدريبية وورش العمل والشهادات الاحترافية التي حصلت عليها خارج المنصة...">{{ old('education', $user->education ?? $user->graduateData?->certifications) }}</textarea>
                                </div>
                                <div class="form-text small text-muted mt-1">
                                    <i class="fas fa-info-circle me-1 text-primary"></i>يمكنك تدوين أي دورات تدريبية خارجية أو ورش عمل أو دبلومات مهنية لتعزيز فرصك في الترشيح الوظيفي.
                                </div>
                                @error('education')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الخبرات العملية السابقة -->
                            <div class="col-12">
                                <label for="experiences" class="form-label small fw-bold text-secondary">
                                    الخبرات العملية والتدريب الميداني
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3 align-items-start pt-2"><i class="fas fa-briefcase text-info"></i></span>
                                    <textarea class="form-control rounded-end-3 border-start-0 @error('experiences') is-invalid @enderror" 
                                        id="experiences" name="experiences" rows="3" 
                                        placeholder="اذكر خبراتك المهنية ومشاريعك السابقة، التدريب التعاوني في الشركات، أو الأعمال التطوعية...">{{ old('experiences', $user->experiences ?? $user->graduateData?->work_experience) }}</textarea>
                                </div>
                                @error('experiences')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold w-100 w-sm-auto shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>حفظ كافة التعديلات</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- القسم الجانبي (CV, كلمة المرور) -->
        <div class="col-lg-4">
            <!-- ملف السيرة الذاتية -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-file-pdf me-2 text-danger"></i>السيرة الذاتية (CV)
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('graduate.cv.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if($user->cv_path)
                            <div class="alert alert-success border-0 bg-light text-success rounded-3 mb-3 p-2 small">
                                <i class="fas fa-check-circle me-1"></i> ملف السيرة الذاتية مرفوع
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3 p-2 border rounded-3 bg-light">
                                <div class="text-truncate me-2" style="max-width: 140px;">
                                    <i class="fas fa-file-pdf text-danger me-1"></i>
                                    <small class="fw-bold">{{ basename($user->cv_path) }}</small>
                                </div>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <a href="{{ route('graduate.cv.download') }}" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-download"></i></a>
                                    <a href="{{ route('graduate.cv.view') }}" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-eye"></i></a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info border-0 bg-light text-info rounded-3 mb-3 p-2 small">
                                <i class="fas fa-info-circle me-1"></i> يرجى رفع سيرتك الذاتية (PDF)
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="cv" class="form-label small fw-bold text-secondary">اختيار ملف جديد</label>
                            <input type="file" class="form-control rounded-3" id="cv" name="cv" accept=".pdf" required>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-cloud-upload-alt me-2"></i>تحديث السيرة الذاتية
                        </button>
                    </form>
                </div>
            </div>

            <!-- تدريباتي في المنصة -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>تدريباتي في المنصة
                    </h5>
                    <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 text-decoration-none" style="font-size: 0.75rem;">
                        تصفح البرامج
                    </a>
                </div>
                <div class="card-body p-3">
                    @if(isset($myTrainings) && $myTrainings->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($myTrainings as $app)
                                <div class="p-2.5 rounded-3 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between">
                                    <div class="me-2 text-truncate" style="max-width: 170px;">
                                        <span class="fw-bold d-block text-truncate text-dark" style="font-size: 0.85rem;" title="{{ $app->training->title ?? 'برنامج تدريبي' }}">
                                            {{ $app->training->title ?? 'برنامج تدريبي' }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            <i class="far fa-calendar-alt me-1"></i>{{ $app->created_at->format('Y-m-d') }}
                                        </small>
                                    </div>
                                    <span class="badge rounded-pill px-2.5 py-1 {{ $app->status === 'approved' ? 'bg-success' : ($app->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}" style="font-size: 0.72rem;">
                                        {{ $app->status === 'approved' ? 'مقبول' : ($app->status === 'rejected' ? 'مرفوض' : 'قيد المراجعة') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-book-reader fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                            <p class="small mb-2">لم تلتحق بأي برامج تدريبية بعد</p>
                            <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                                <i class="fas fa-search me-1"></i>استكشاف برامج التدريب
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- شهاداتي المعتمدة -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-award me-2 text-warning"></i>شهاداتي المعتمدة
                    </h5>
                    <a href="{{ route('graduate.certificates.index') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2.5 py-1 text-decoration-none" style="font-size: 0.75rem;">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body p-3">
                    @if(isset($myCertificates) && $myCertificates->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($myCertificates as $cert)
                                <div class="p-2.5 rounded-3 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between">
                                    <div class="me-2 text-truncate" style="max-width: 170px;">
                                        <span class="fw-bold d-block text-truncate text-dark" style="font-size: 0.85rem;" title="{{ $cert->title }}">
                                            {{ $cert->title }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            <i class="fas fa-barcode me-1 text-secondary"></i>{{ $cert->certificate_code }}
                                        </small>
                                    </div>
                                    <a href="{{ route('graduate.certificates.index') }}" class="btn btn-sm btn-warning text-dark rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="معاينة الشهادة">
                                        <i class="fas fa-eye" style="font-size: 0.78rem;"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-certificate fa-2x mb-2 text-warning opacity-50 d-block"></i>
                            <p class="small mb-0">لا توجد شهادات صادرة حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- تغيير كلمة المرور -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-key me-2 text-warning"></i>تغيير كلمة المرور
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('graduate.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label small fw-bold text-secondary">كلمة المرور الحالية <span class="text-danger">*</span></label>
                            <input type="password" class="form-control rounded-3" 
                                id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-secondary">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <input type="password" class="form-control rounded-3" 
                                id="password" name="password" required minlength="8">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label small fw-bold text-secondary">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" class="form-control rounded-3" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark rounded-pill w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-lock me-2"></i>تحديث كلمة المرور
                        </button>
                    </form>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card border-0 rounded-4 shadow-sm bg-white">
                <div class="card-body p-3 text-center">
                    <small class="text-muted d-block mb-1">تاريخ التسجيل: <strong>{{ $user->created_at->format('Y-m-d') }}</strong></small>
                    <small class="text-muted d-block">آخر تحديث: <strong>{{ $user->updated_at->format('Y-m-d') }}</strong></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- University Data Script -->
    <script src="{{ asset('js/university-data.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qualSelect = document.getElementById('qualification');
            const hiddenDegree = document.getElementById('hidden_degree');
            const specSelect = document.getElementById('specialization');
            const hiddenMajor = document.getElementById('hidden_major');
            const secSelect = document.getElementById('sector');
            const facSelect = document.getElementById('faculty');

            if (qualSelect && hiddenDegree) {
                qualSelect.addEventListener('change', function () {
                    hiddenDegree.value = this.value;
                });
            }

            if (specSelect && hiddenMajor) {
                specSelect.addEventListener('change', function () {
                    hiddenMajor.value = this.value;
                });
            }

            // ضمان تفعيل الحقول عند إرسال النموذج حتى لا تُحذف قيمتها من الـ Request
            const profileForm = specSelect ? specSelect.closest('form') : null;
            if (profileForm) {
                profileForm.addEventListener('submit', function () {
                    if (secSelect) secSelect.disabled = false;
                    if (facSelect) facSelect.disabled = false;
                    if (specSelect) {
                        specSelect.disabled = false;
                        if (hiddenMajor && specSelect.value) {
                            hiddenMajor.value = specSelect.value;
                        }
                    }
                    if (qualSelect && hiddenDegree && qualSelect.value) {
                        hiddenDegree.value = qualSelect.value;
                    }
                });
            }
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
@endsection