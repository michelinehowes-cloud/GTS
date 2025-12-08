@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'الملف الشخصي', 'active' => true],
            ]
        ])

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 bg-success-subtle text-success-emphasis" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- البيانات الشخصية والأكاديمية -->
                    <div class="col-lg-8">
                        <div class="card-modern mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="card-title mb-0 text-primary fw-bold">
                                    <i class="fas fa-user-edit me-2"></i>
                                    البيانات الشخصية
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('graduate.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-3">
                                        <!-- الاسم الكامل -->
                                        <div class="col-md-6">
                                            <label for="name" class="form-label-modern">الاسم الكامل <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                                <input type="text" class="form-control-modern border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            </div>
                                            @error('name')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- البريد الإلكتروني -->
                                        <div class="col-md-6">
                                            <label for="email" class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                                <input type="email" class="form-control-modern border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            </div>
                                            @error('email')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- رقم الهاتف -->
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label-modern">رقم الهاتف</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                                <input type="text" class="form-control-modern border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                            </div>
                                            @error('phone')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- تاريخ الميلاد -->
                                        <div class="col-md-6">
                                            <label for="date_of_birth" class="form-label-modern">تاريخ الميلاد</label>
                                            <input type="date" class="form-control-modern @error('date_of_birth') is-invalid @enderror" 
                                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                            @error('date_of_birth')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- الجنس -->
                                        <div class="col-md-6">
                                            <label for="gender" class="form-label-modern">الجنس</label>
                                            <select class="form-select-modern @error('gender') is-invalid @enderror" id="gender" name="gender">
                                                <option value="" disabled>اختر الجنس</option>
                                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                            </select>
                                            @error('gender')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- المدينة -->
                                        <div class="col-md-6">
                                            <label for="city" class="form-label-modern">المدينة</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                <input type="text" class="form-control-modern border-start-0 ps-0 @error('city') is-invalid @enderror" 
                                                    id="city" name="city" value="{{ old('city', $user->city) }}">
                                            </div>
                                            @error('city')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-12 border-bottom pb-2 mb-3">
                                            <h6 class="text-primary fw-bold"><i class="fas fa-graduation-cap me-2"></i>البيانات الأكاديمية</h6>
                                        </div>

                                        <!-- المؤهل العلمي -->
                                        <div class="col-md-6">
                                            <label for="qualification" class="form-label-modern">المؤهل العلمي</label>
                                            <select class="form-select-modern @error('qualification') is-invalid @enderror" id="qualification" name="qualification">
                                                <option value="" disabled>اختر المؤهل</option>
                                                <option value="بكالوريوس" {{ old('qualification', $user->qualification) == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                                <option value="ماجستير" {{ old('qualification', $user->qualification) == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                                                <option value="دكتوراه" {{ old('qualification', $user->qualification) == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                                                <option value="دبلوم عالي" {{ old('qualification', $user->qualification) == 'دبلوم عالي' ? 'selected' : '' }}>دبلوم عالي</option>
                                            </select>
                                            @error('qualification')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- الجامعة -->
                                        <div class="col-md-6">
                                            <label for="university_id" class="form-label-modern">الجامعة</label>
                                            <select class="form-select-modern @error('university_id') is-invalid @enderror" id="university_id" name="university_id">
                                                <option value="">اختر الجامعة</option>
                                                <!-- سيتم ملؤها بواسطة JavaScript -->
                                            </select>
                                            <input type="hidden" id="old_university_id" value="{{ old('university_id', $user->university_id) }}">
                                            @error('university_id')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- القطاع -->
                                        <div class="col-md-6">
                                            <label for="sector_id" class="form-label-modern">القطاع</label>
                                            <select class="form-select-modern @error('sector_id') is-invalid @enderror" id="sector_id" name="sector_id" disabled>
                                                <option value="">اختر القطاع</option>
                                            </select>
                                            <input type="hidden" id="old_sector_id" value="{{ old('sector_id', $user->sector_id) }}">
                                            @error('sector_id')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- الكلية -->
                                        <div class="col-md-6">
                                            <label for="faculty_id" class="form-label-modern">الكلية</label>
                                            <select class="form-select-modern @error('faculty_id') is-invalid @enderror" id="faculty_id" name="faculty_id" disabled>
                                                <option value="">اختر الكلية</option>
                                            </select>
                                            <input type="hidden" id="old_faculty_id" value="{{ old('faculty_id', $user->faculty_id) }}">
                                            @error('faculty_id')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- التخصص -->
                                        <div class="col-md-6">
                                            <label for="specialization_id" class="form-label-modern">التخصص</label>
                                            <select class="form-select-modern @error('specialization_id') is-invalid @enderror" id="specialization_id" name="specialization_id" disabled>
                                                <option value="">اختر التخصص</option>
                                            </select>
                                            <input type="hidden" id="old_specialization_id" value="{{ old('specialization_id', $user->specialization_id) }}">
                                            @error('specialization_id')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- سنة التخرج -->
                                        <div class="col-md-3">
                                            <label for="graduation_year" class="form-label-modern">سنة التخرج</label>
                                            <input type="number" class="form-control-modern @error('graduation_year') is-invalid @enderror" 
                                                id="graduation_year" name="graduation_year" value="{{ old('graduation_year', $user->graduation_year) }}" 
                                                min="1950" max="{{ date('Y') + 1 }}">
                                            @error('graduation_year')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- المعدل التراكمي -->
                                        <div class="col-md-3">
                                            <label for="gpa" class="form-label-modern">المعدل التراكمي</label>
                                            <input type="number" class="form-control-modern @error('gpa') is-invalid @enderror" 
                                                id="gpa" name="gpa" value="{{ old('gpa', $user->gpa) }}" min="0" max="4" step="0.01" 
                                                placeholder="من 4.00">
                                            @error('gpa')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- اللغات -->
                                        <div class="col-12">
                                            <label for="languages" class="form-label-modern">اللغات</label>
                                            <input type="text" class="form-control-modern @error('languages') is-invalid @enderror" 
                                                id="languages" name="languages" 
                                                value="{{ old('languages', is_array($user->languages) ? implode(', ', $user->languages) : $user->languages) }}" 
                                                placeholder="العربية، الانجليزية، الفرنسية...">
                                            @error('languages')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary-modern px-5">
                                            <i class="fas fa-save me-2"></i>
                                            حفظ التغييرات
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- قسم السيرة الذاتية (للهاتف المحمول والأجهزة الصغيرة) -->
                        <div class="d-lg-none">
                             <!-- نفس كود السيرة الذاتية سيتم تكراره هنا أو يمكن إعادة ترتيب الـ layout لكن سأبقيه بسيطاً للآن -->
                        </div>
                    </div>

                    <!-- القسم الجانبي (CV, Password, Info) -->
                    <div class="col-lg-4">
                        <!-- ملف السيرة الذاتية -->
                        <div class="card-modern mb-4">
                            <div class="card-header bg-success text-white py-3 border-bottom">
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    السيرة الذاتية (CV)
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('graduate.cv.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    @if($user->cv_path)
                                        <div class="alert alert-success border-0 bg-success-subtle text-success-emphasis mb-3">
                                            <i class="fas fa-check-circle me-1"></i> تم رفع الملف
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3 p-2 border rounded bg-light">
                                            <div class="text-truncate" style="max-width: 150px;">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <small class="fw-bold">{{ basename($user->cv_path) }}</small>
                                            </div>
                                            <div>
                                                <a href="{{ route('graduate.cv.download') }}" class="btn btn-sm btn-outline-primary-modern"><i class="fas fa-download"></i></a>
                                                <a href="{{ route('graduate.cv.view') }}" target="_blank" class="btn btn-sm btn-outline-info-modern"><i class="fas fa-eye"></i></a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-3">
                                            <i class="fas fa-info-circle me-1"></i> يرجى رفع سيرتك الذاتية
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="cv" class="form-label-modern">رفع ملف جديد (PDF)</label>
                                        <input type="file" class="form-control-modern" id="cv" name="cv" accept=".pdf" required>
                                    </div>

                                    <button type="submit" class="btn btn-success-modern w-100">
                                        <i class="fas fa-cloud-upload-alt me-2"></i> تحديث
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- تغيير كلمة المرور -->
                        <div class="card-modern mb-4">
                            <div class="card-header bg-warning text-dark py-3 border-bottom">
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="fas fa-key me-2"></i>
                                    تغيير كلمة المرور
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('graduate.password.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label-modern">الحالية <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control-modern" 
                                            id="current_password" name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label-modern">الجديدة <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control-modern" 
                                            id="password" name="password" required minlength="8">
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label-modern">تأكيد <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control-modern" id="password_confirmation" name="password_confirmation" required>
                                    </div>

                                    <button type="submit" class="btn btn-warning-modern text-dark w-100">
                                        <i class="fas fa-lock me-2"></i> تحديث
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- معلومات -->
                        <div class="card-modern">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted d-block">تاريخ التسجيل: {{ $user->created_at->format('Y-m-d') }}</small>
                                <small class="text-muted d-block">آخر تحديث: {{ $user->updated_at->format('Y-m-d') }}</small>
                            </div>
                        </div>
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
        // تفعيل الـ Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection