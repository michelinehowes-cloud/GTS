@extends('layouts.guest')

@section('title', 'تسجيل حساب شركة أو مؤسسة جديدة - مكتب تدريب الخريجين')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #f0f7ff 0%, #e2eafc 50%, #edf2f7 100%);
        min-height: 100vh;
    }
    .register-container { max-width: 900px; margin: 2rem auto; }
    .company-reg-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }
    .reg-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%);
        padding: 2.5rem 2rem;
        color: #ffffff;
        position: relative;
    }
    .reg-header::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(90deg, #f59e0b, #10b981, #38bdf8);
    }
    /* ===== Section header matching admin forms ===== */
    .form-section-badge {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .9rem;
        flex-shrink: 0;
    }
    /* ===== Inputs ===== */
    .input-icon-group { position: relative; }
    .input-icon-group .form-control {
        border-radius: 10px;
        padding: 0.65rem 0.9rem 0.65rem 2.5rem;
        border: 1.5px solid #cbd5e1;
        font-size: 0.92rem;
        direction: rtl;
    }
    .input-icon-group .form-control:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
    }
    .input-icon-group .field-icon {
        position: absolute;
        left: 12px; top: 50%; transform: translateY(-50%);
        color: #94a3b8;
        font-size: .85rem;
        pointer-events: none;
    }
    .form-control-plain {
        border-radius: 10px;
        padding: 0.65rem 0.9rem;
        border: 1.5px solid #cbd5e1;
        font-size: 0.92rem;
    }
    .form-control-plain:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        outline: none;
    }
    /* ===== Partnership type cards ===== */
    .partner-type-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        user-select: none;
        height: 100%;
        display: flex; align-items: center; gap: 0.75rem;
    }
    .partner-type-card:hover { border-color: #93c5fd; background: #f8fafc; transform: translateY(-2px); }
    .partner-type-checkbox:checked + .partner-type-card {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
    }
    .partner-type-checkbox:checked + .partner-type-card .type-icon { background: #0284c7; color: #ffffff; }
    .partner-type-checkbox { display: none; }
    .type-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: #e0f2fe; color: #0284c7;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; transition: all 0.2s ease; flex-shrink: 0;
    }
    /* ===== Submit button ===== */
    .btn-submit-reg {
        background: linear-gradient(135deg, #0284c7 0%, #1e3a8a 100%);
        border: none; color: #ffffff;
        font-weight: 700; padding: 0.85rem 2.5rem;
        border-radius: 12px; font-size: 1.05rem;
        box-shadow: 0 8px 20px rgba(2, 132, 199, 0.25);
        transition: all 0.2s ease;
    }
    .btn-submit-reg:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(2, 132, 199, 0.35);
        color: #ffffff;
    }
    textarea.form-control-plain { resize: vertical; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="register-container">
        
        <!-- Top bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2 text-dark">
                <img src="{{ asset('images/uot_logo.png') }}" alt="شعار جامعة طرابلس" height="42"
                     onerror="this.src='{{ asset('images/office-logo.png') }}'">
                <div>
                    <div class="fw-bold fs-6 text-primary">جامعة طرابلس</div>
                    <small class="text-muted">مكتب تدريب الخريجين</small>
                </div>
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-right me-1"></i> العودة للرئيسية
            </a>
        </div>

        <div class="company-reg-card">
            <!-- Hero Header -->
            <div class="reg-header text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3 p-3" style="width:64px;height:64px;">
                    <i class="fas fa-handshake fs-2 text-warning"></i>
                </div>
                <h3 class="fw-bold mb-2">طلب انضمام وتسجيل شركة / مؤسسة شريكة</h3>
                <p class="mb-0 text-white-50" style="font-size:.95rem;max-width:650px;margin:0 auto;">
                    انضم إلى شبكة شركاء مكتب تدريب الخريجين بجامعة طرابلس للإعلان عن الشواغر الوظيفية وبرامج التدريب الميداني والتعاون الأكاديمي.
                </p>
            </div>

            <div class="card-body p-4 p-md-5">
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-triangle fs-5"></i>
                        <h6 class="mb-0 fw-bold">يرجى تصحيح الأخطاء التالية:</h6>
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('company.register.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- =================== القسم الأول: بيانات الشركة =================== --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="form-section-badge" style="background:linear-gradient(135deg,#0ea5e9,#6366f1);">
                            <i class="fas fa-building text-white"></i>
                        </span>
                        <h6 class="fw-bold text-dark mb-0">بيانات الشركة أو المؤسسة</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="company_name" class="form-label small fw-bold text-dark">
                                اسم الشركة أو المؤسسة الرسمي <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-building field-icon"></i>
                                <input type="text" name="company_name" id="company_name"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name') }}"
                                       placeholder="مثال: شركة التقنية الحديثة القابضة" required>
                            </div>
                            @error('company_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="industry" class="form-label small fw-bold text-dark">
                                مجال ونشاط الشركة <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-industry field-icon"></i>
                                <input type="text" name="industry" id="industry"
                                       class="form-control @error('industry') is-invalid @enderror"
                                       value="{{ old('industry') }}"
                                       placeholder="تقنية معلومات، هندسة، خدمات مالية..." required>
                            </div>
                            @error('industry')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="city" class="form-label small fw-bold text-dark">
                                المدينة <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-city field-icon"></i>
                                <input type="text" name="city" id="city"
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', 'طرابلس') }}"
                                       placeholder="مثال: طرابلس" required>
                            </div>
                            @error('city')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="address" class="form-label small fw-bold text-dark">
                                العنوان التفصيلي للمقر <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-map-marker-alt field-icon"></i>
                                <input type="text" name="address" id="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address') }}"
                                       placeholder="الشارع، المنطقة، أقرب نقطة دالة" required>
                            </div>
                            @error('address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="phone" class="form-label small fw-bold text-dark">
                                هاتف مقر الشركة / البدالة
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-phone field-icon"></i>
                                <input type="tel" name="phone" id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}"
                                       placeholder="021XXXXXXX أو 09XXXXXXXX">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="website" class="form-label small fw-bold text-dark">الموقع الإلكتروني الرسمي</label>
                            <div class="input-icon-group">
                                <i class="fas fa-globe field-icon"></i>
                                <input type="url" name="website" id="website"
                                       class="form-control @error('website') is-invalid @enderror"
                                       value="{{ old('website') }}"
                                       placeholder="https://example.ly">
                            </div>
                            @error('website')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="logo" class="form-label small fw-bold text-dark">شعار الشركة (Logo)</label>
                            <input type="file" name="logo" id="logo"
                                   class="form-control-plain w-100 @error('logo') is-invalid @enderror"
                                   accept="image/*">
                            <small class="text-muted" style="font-size:.75rem;">PNG, JPG, WEBP بحد أقصى 2MB</small>
                            @error('logo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label small fw-bold text-dark">نبذة تعريفية موجزة عن الشركة</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control-plain w-100 @error('description') is-invalid @enderror"
                                      placeholder="اكتب نبذة تعريفية عن أنشطة الشركة ورؤيتها...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- =================== القسم الثاني: أنواع الشراكة =================== --}}
                    <hr class="my-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="form-section-badge" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">
                            <i class="fas fa-handshake text-white"></i>
                        </span>
                        <h6 class="fw-bold text-dark mb-0">
                            مجالات الشراكة المطلوبة مع الجامعة
                            <span class="text-danger">*</span>
                            <small class="text-muted fw-normal me-2">(يمكن اختيار أكثر من نوع)</small>
                        </h6>
                    </div>

                    @php
                        $typesList = [
                            'employment'       => ['label' => 'توظيف الخريجين',       'desc' => 'نشر الشواغر واستقطاب كفاءات الجامعة',        'icon' => 'fa-user-tie'],
                            'training'         => ['label' => 'تدريب ميداني وتأهيل',  'desc' => 'استقبال طلاب وخريجين في برامج تدريب عملي',  'icon' => 'fa-graduation-cap'],
                            'logistic_support' => ['label' => 'رعاية ودعم لوجستي',   'desc' => 'رعاية فعاليات ومعارض التوظيف',               'icon' => 'fa-award'],
                            'academic'         => ['label' => 'تعاون أكاديمي وبحثي', 'desc' => 'مشاريع تخرج، بحوث مشتركة، تطوير مناهج',    'icon' => 'fa-flask'],
                            'workshops'        => ['label' => 'ورش عمل وندوات',       'desc' => 'محاضرات وورش نقل المعرفة للطلاب',           'icon' => 'fa-chalkboard-teacher'],
                        ];
                        $oldSelected = old('partnership_types', ['employment', 'training']);
                    @endphp

                    <div class="row g-2 g-md-3 mb-4">
                        @foreach($typesList as $val => $info)
                        <div class="col-md-6 col-lg-4">
                            <input type="checkbox" name="partnership_types[]" id="type_{{ $val }}"
                                   value="{{ $val }}" class="partner-type-checkbox"
                                   {{ in_array($val, (array)$oldSelected) ? 'checked' : '' }}>
                            <label for="type_{{ $val }}" class="partner-type-card">
                                <div class="type-icon">
                                    <i class="fas {{ $info['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:.9rem;">{{ $info['label'] }}</div>
                                    <small class="text-muted" style="font-size:.75rem;line-height:1.2;display:block;">{{ $info['desc'] }}</small>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    {{-- =================== القسم الثالث: مسؤول الاتصال وحساب الدخول =================== --}}
                    <hr class="my-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="form-section-badge" style="background:linear-gradient(135deg,#059669,#10b981);">
                            <i class="fas fa-id-card text-white"></i>
                        </span>
                        <h6 class="fw-bold text-dark mb-0">بيانات مسؤول الاتصال وحساب تسجيل الدخول</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="contact_name" class="form-label small fw-bold text-dark">
                                اسم مسؤول الاتصال أو ممثل الشركة <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-user field-icon"></i>
                                <input type="text" name="contact_name" id="contact_name"
                                       class="form-control @error('contact_name') is-invalid @enderror"
                                       value="{{ old('contact_name') }}"
                                       placeholder="الاسم الثلاثي" required>
                            </div>
                            @error('contact_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="contact_position" class="form-label small fw-bold text-dark">
                                المسمى الوظيفي لمسؤول الاتصال
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-briefcase field-icon"></i>
                                <input type="text" name="contact_position" id="contact_position"
                                       class="form-control @error('contact_position') is-invalid @enderror"
                                       value="{{ old('contact_position') }}"
                                       placeholder="مثال: مدير الموارد البشرية، مسؤول العلاقات">
                            </div>
                            @error('contact_position')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label small fw-bold text-dark">
                                رقم هاتف الاتصال المباشر <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-mobile-alt field-icon"></i>
                                <input type="tel" name="contact_phone" id="contact_phone"
                                       class="form-control @error('contact_phone') is-invalid @enderror"
                                       value="{{ old('contact_phone') }}"
                                       placeholder="09XXXXXXXX" required>
                            </div>
                            @error('contact_phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-dark">
                                البريد الإلكتروني الرسمي (اسم المستخدم) <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-envelope field-icon"></i>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="info@company.ly أو hr@company.ly" required>
                            </div>
                            <small class="text-muted" style="font-size:.75rem;">سيُستخدم هذا البريد لتسجيل الدخول واستلام الإشعارات الرسمية</small>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- =================== القسم الرابع: بيانات حساب الدخول =================== --}}
                        <div class="col-12 mt-2">
                            <hr class="my-2">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="form-section-badge" style="background:linear-gradient(135deg,#f59e0b,#f97316);">
                                    <i class="fas fa-key text-white"></i>
                                </span>
                                <h6 class="fw-bold text-dark mb-0">بيانات حساب تسجيل الدخول</h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-bold text-dark">
                                كلمة المرور <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-lock field-icon"></i>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="8 أحرف على الأقل" required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label small fw-bold text-dark">
                                تأكيد كلمة المرور <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-group">
                                <i class="fas fa-check-double field-icon"></i>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control"
                                       placeholder="أعد كتابة كلمة المرور" required>
                            </div>
                        </div>
                    </div>

                    <!-- تنبيه المراجعة -->
                    <div class="alert alert-info border-0 rounded-3 p-3 mb-4 d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle fs-3 text-info flex-shrink-0"></i>
                        <div class="small">
                            <strong>ملاحظة هامة:</strong> بعد إرسال الطلب، سيتم مراجعة بيانات الشركة من قبل فريق إدارة الشراكات للتأكد من صحتها واعتماد الحساب. سيصلكم إشعار بالاعتماد وتفعيل صلاحية الدخول ونشر الفرص فور استكمال المراجعة.
                        </div>
                    </div>

                    <x-honeypot />
                    <div class="mb-4">
                        <x-turnstile action="register_company" />
                    </div>

                    <!-- زر الإرسال -->
                    <div class="text-center pt-2">
                        <button type="submit" class="btn btn-submit-reg">
                            <i class="fas fa-paper-plane me-2"></i> إرسال طلب تسجيل الشركة
                        </button>
                        <div class="mt-3">
                            <span class="text-muted small">هل تمتلك شركتكم حساباً مفعلاً بالفعل؟</span>
                            <a href="{{ url('/?open_login=1') }}" class="fw-bold text-primary text-decoration-none small ms-1">
                                تسجيل الدخول من هنا
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
