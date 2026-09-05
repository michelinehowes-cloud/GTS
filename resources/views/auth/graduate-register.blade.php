<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل خريج جديد - جامعة طرابلس</title>

    <!-- Bootstrap 5.3 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: #f8f9fa; /* Light gray background matching Bento UI */
            min-height: 100vh;
            padding: 40px 0;
        }

        .registration-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .registration-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 4px solid #f59e0b; /* Gold accent */
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            pointer-events: none !important;
        }

        .card-header h2 {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .card-header p {
            opacity: 0.95;
            font-size: 1.1rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 40px 30px;
        }

        .section-title {
            color: #1565c0;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #f59e0b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            font-size: 1.5rem;
            color: #1565c0;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-label .required {
            color: #ef4444;
            margin-right: 3px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 0.25rem rgba(21, 101, 192, 0.15);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .password-toggle {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #1565c0;
        }

        .password-field {
            position: relative;
        }

        .btn-register {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            border: none;
            border-radius: 14px;
            padding: 16px 50px;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(21, 101, 192, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(21, 101, 192, 0.4);
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        .card-footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .card-footer a {
            color: #1565c0;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .card-footer a:hover {
            color: #0d3882;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .help-text {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 5px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 0 20px;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step::before {
            content: '';
            position: absolute;
            top: 15px;
            right: 50%;
            width: 100%;
            height: 3px;
            background: #e5e7eb;
            z-index: 0;
        }

        .step:first-child::before {
            display: none;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #9ca3af;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(21, 101, 192, 0.35);
        }

        .step-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 8px;
            font-weight: 600;
        }

        .step.active .step-label {
            color: #1565c0;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px 0;
            }

            .registration-container {
                padding: 0 10px;
            }

            .card-header {
                padding: 30px 20px;
            }

            .card-header h2 {
                font-size: 1.5rem;
            }

            .card-header p {
                font-size: 0.95rem;
            }

            .card-body {
                padding: 25px 20px;
            }

            .section-title {
                font-size: 1.1rem;
            }

            .btn-register {
                width: 100%;
                padding: 14px 30px;
                font-size: 1.1rem;
            }

            .progress-steps {
                padding: 0 10px;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }

            .step-label {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .step-label {
                display: none;
            }

            .progress-steps {
                margin-bottom: 20px;
            }
        }

        /* Loading Animation */
        .btn-register.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-register.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
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
                <form method="POST" action="{{ route('graduate.register.store') }}" id="registrationForm">
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
                                <label for="password" class="form-label">
                                    كلمة المرور <span class="required">*</span>
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required minlength="8">
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                                </div>
                                <small class="help-text">8 أحرف على الأقل</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    تأكيد كلمة المرور <span class="required">*</span>
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required minlength="8">
                                    <i class="fas fa-eye password-toggle"
                                        onclick="togglePassword('password_confirmation')"></i>
                                </div>
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
                                    المعدل التراكمي (%)
                                </label>
                                <input type="number" class="form-control @error('gpa') is-invalid @enderror" id="gpa"
                                    name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="100"
                                    placeholder="مثال: 85.50">
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

            <!-- Footer -->
            <div class="card-footer">
                <p class="mb-0">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- University Data Script -->
    <script src="{{ asset('js/university-data.js') }}"></script>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Progress steps animation
        const sections = document.querySelectorAll('.form-section');
        const steps = document.querySelectorAll('.step');

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const sectionId = entry.target.id;
                    const stepNumber = sectionId.split('-')[1];

                    steps.forEach(step => {
                        step.classList.remove('active');
                    });

                    const activeStep = document.querySelector(`.step[data-step="${stepNumber}"]`);
                    if (activeStep) {
                        activeStep.classList.add('active');
                    }
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            observer.observe(section);
        });

        // Form submission loading state
        const form = document.getElementById('registrationForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function () {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Auto-scroll to first error
        window.addEventListener('load', function () {
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        });
    </script>
</body>

</html>