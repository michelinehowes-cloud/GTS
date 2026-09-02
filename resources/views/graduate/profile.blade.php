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
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                            <i class="fas fa-user-edit me-2 text-primary"></i>البيانات الشخصية
                        </h5>
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
                                        id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تاريخ الميلاد -->
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label small fw-bold text-secondary">تاريخ الميلاد</label>
                                <input type="date" class="form-control rounded-3 @error('date_of_birth') is-invalid @enderror" 
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
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
                                        id="city" name="city" value="{{ old('city', $user->city) }}">
                                </div>
                                @error('city')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- البيانات الأكاديمية -->
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>البيانات الأكاديمية
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <!-- المؤهل العلمي -->
                            <div class="col-md-6">
                                <label for="qualification" class="form-label small fw-bold text-secondary">المؤهل العلمي</label>
                                <select class="form-select rounded-3 @error('qualification') is-invalid @enderror" id="qualification" name="qualification">
                                    <option value="" disabled>اختر المؤهل</option>
                                    <option value="بكالوريوس" {{ old('qualification', $user->qualification) == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                    <option value="ماجستير" {{ old('qualification', $user->qualification) == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                                    <option value="دكتوراه" {{ old('qualification', $user->qualification) == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                                    <option value="دبلوم عالي" {{ old('qualification', $user->qualification) == 'دبلوم عالي' ? 'selected' : '' }}>دبلوم عالي</option>
                                </select>
                                @error('qualification')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الجامعة -->
                            <div class="col-md-6">
                                <label for="university_id" class="form-label small fw-bold text-secondary">الجامعة</label>
                                <select class="form-select rounded-3 @error('university_id') is-invalid @enderror" id="university_id" name="university_id">
                                    <option value="">اختر الجامعة</option>
                                </select>
                                <input type="hidden" id="old_university_id" value="{{ old('university_id', $user->university_id) }}">
                                @error('university_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- القطاع -->
                            <div class="col-md-6">
                                <label for="sector_id" class="form-label small fw-bold text-secondary">القطاع</label>
                                <select class="form-select rounded-3 @error('sector_id') is-invalid @enderror" id="sector_id" name="sector_id" disabled>
                                    <option value="">اختر القطاع</option>
                                </select>
                                <input type="hidden" id="old_sector_id" value="{{ old('sector_id', $user->sector_id) }}">
                                @error('sector_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الكلية -->
                            <div class="col-md-6">
                                <label for="faculty_id" class="form-label small fw-bold text-secondary">الكلية</label>
                                <select class="form-select rounded-3 @error('faculty_id') is-invalid @enderror" id="faculty_id" name="faculty_id" disabled>
                                    <option value="">اختر الكلية</option>
                                </select>
                                <input type="hidden" id="old_faculty_id" value="{{ old('faculty_id', $user->faculty_id) }}">
                                @error('faculty_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- التخصص -->
                            <div class="col-md-6">
                                <label for="specialization_id" class="form-label small fw-bold text-secondary">التخصص</label>
                                <select class="form-select rounded-3 @error('specialization_id') is-invalid @enderror" id="specialization_id" name="specialization_id" disabled>
                                    <option value="">اختر التخصص</option>
                                </select>
                                <input type="hidden" id="old_specialization_id" value="{{ old('specialization_id', $user->specialization_id) }}">
                                @error('specialization_id')
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
                            <div class="col-12">
                                <label for="languages" class="form-label small fw-bold text-secondary">اللغات المتقنة</label>
                                <input type="text" class="form-control rounded-3 @error('languages') is-invalid @enderror" 
                                    id="languages" name="languages" 
                                    value="{{ old('languages', is_array($user->languages) ? implode(', ', $user->languages) : $user->languages) }}" 
                                    placeholder="العربية، الإنجليزية...">
                                @error('languages')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold w-100 w-sm-auto shadow-sm">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection