@extends('layouts.app')

@section('title', 'تفاصيل الخريج - ' . $graduate->name)

@php
    $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : 'career-guidance';
@endphp

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="{{ $graduate->name }}"
        subtitle="{{ $graduate->major }} • {{ $graduate->faculty ?? ($graduate->university ?? 'جامعة طرابلس') }} • دفعة {{ $graduate->graduation_year ?? 'غير محدد' }}{{ $graduate->city ? ' • ' . $graduate->city : '' }}"
        icon="fas fa-user-graduate"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($prefix . '.dashboard')],
            ['label' => 'بيانات الخريجين', 'url' => route($prefix . '.graduates')],
            ['label' => $graduate->name, 'active' => true],
        ]"
        :badge="$graduate->employment_status == 'employed' ? 'موظف حالياً' : ($graduate->employment_status == 'seeking_opportunities' ? 'باحث عن فرصة عمل' : ($graduate->employment_status == 'unemployed' ? 'عاطل عن العمل' : 'مستكمل للدراسة'))"
        :badgeIcon="$graduate->employment_status == 'employed' ? 'fas fa-check-circle' : ($graduate->employment_status == 'seeking_opportunities' ? 'fas fa-search' : ($graduate->employment_status == 'unemployed' ? 'fas fa-times-circle' : 'fas fa-graduation-cap'))"
        secondaryBadge="رقم القيد: {{ $graduate->national_id ?? 'غير مسجل' }}"
        secondaryBadgeIcon="fas fa-id-card"
    >
        <button type="button" class="btn btn-warning fw-bold text-dark px-3 py-2 shadow-sm rounded-pill d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#quickNominateModal">
            <i class="fas fa-paper-plane"></i>
            <span>ترشيح فوري</span>
        </button>
        <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="btn btn-outline-light px-3 py-2 rounded-pill d-flex align-items-center gap-1.5">
            <i class="fas fa-external-link-alt"></i>
            <span>ترشيح متقدم</span>
        </a>
        <a href="{{ route($prefix . '.graduates.edit', $graduate->id) }}" class="btn btn-light bg-white text-primary fw-bold px-3 py-2 rounded-pill d-flex align-items-center gap-1.5">
            <i class="fas fa-user-edit"></i>
            <span>تعديل الملف</span>
        </a>
        <button type="button" class="btn btn-outline-light px-3 py-2 rounded-pill d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" title="إعادة تعيين كلمة مرور الحساب">
            <i class="fas fa-key"></i>
            <span>كلمة المرور</span>
        </button>
        <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-light px-3 py-2 rounded-pill d-flex align-items-center gap-1.5">
            <i class="fas fa-arrow-right"></i>
            <span>رجوع</span>
        </a>
    </x-page-hero>

    <!-- Alerts -->
    @if(session('success') || session('password_success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
                <div>
                    <strong class="d-block mb-1">تم بنجاح!</strong>
                    <span>{{ session('success') ?? session('password_success') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fs-4 me-3 text-danger"></i>
                <div>
                    <strong class="d-block mb-1">تنبيه:</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fs-4 me-3 text-danger"></i>
                <div>
                    <strong class="d-block mb-1">حدث خطأ في البيانات:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quick Bento Summary Metrics Bar -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-chalkboard-teacher fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ $trainingApplications->count() }}</div>
                        <div class="text-muted small">البرامج التدريبية المسجلة</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-award fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ $certificates->count() }}</div>
                        <div class="text-muted small">الشهادات المعتمدة الصادرة</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-calendar-check fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ $jobFairRegistrations->count() }}</div>
                        <div class="text-muted small">معارض وملتقيات مسجل بها</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-briefcase fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ $graduate->nominations->count() }}</div>
                        <div class="text-muted small">ترشيحات وظيفية سابقة</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4 mb-4">
        <!-- 1. البيانات الشخصية والأكاديمية الكاملة -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-user-graduate me-2"></i>البيانات الشخصية والأكاديمية
                    </h5>
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small">ملف موثق</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-envelope text-secondary me-1"></i> البريد الإلكتروني</small>
                                <span class="fw-bold text-dark text-break small">{{ $graduate->email ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-phone text-secondary me-1"></i> رقم الهاتف</small>
                                <span class="fw-bold text-dark" dir="ltr">{{ $graduate->phone ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-id-card text-secondary me-1"></i> رقم القيد</small>
                                <span class="fw-bold text-dark">{{ $graduate->national_id ?? 'غير مسجل' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-birthday-cake text-secondary me-1"></i> تاريخ الميلاد والجنس</small>
                                <span class="fw-bold text-dark">
                                    {{ $graduate->date_of_birth ? \Carbon\Carbon::parse($graduate->date_of_birth)->format('Y-m-d') : 'غير محدد' }}
                                    @if($graduate->gender)
                                        <span class="text-muted small">({{ $graduate->gender == 'male' ? 'ذكر' : ($graduate->gender == 'female' ? 'أنثى' : $graduate->gender) }})</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-university text-secondary me-1"></i> الجامعة والكلية</small>
                                <span class="fw-bold text-dark">{{ $graduate->university ?? 'جامعة طرابلس' }} - {{ $graduate->faculty ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-book-open text-secondary me-1"></i> التخصص والقسم</small>
                                <span class="fw-bold text-primary">{{ $graduate->major }} @if($graduate->specialization && $graduate->specialization !== $graduate->major) - {{ $graduate->specialization }} @endif</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-award text-secondary me-1"></i> المؤهل والمعدل</small>
                                <span class="fw-bold text-dark">
                                    {{ $graduate->degree ?? 'بكالوريوس' }}
                                    @if($graduate->gpa)
                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-1">{{ $graduate->gpa }}%</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-map-marked-alt text-secondary me-1"></i> المدينة والعنوان</small>
                                <span class="fw-bold text-dark">{{ $graduate->city ?? '' }} {{ $graduate->address ? ' - ' . $graduate->address : 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fab fa-linkedin text-primary me-1"></i> حساب LinkedIn</small>
                                @if($graduate->linkedin_url)
                                    <a href="{{ $graduate->linkedin_url }}" target="_blank" class="fw-bold text-primary text-decoration-none small">
                                        <i class="fas fa-external-link-alt me-1"></i> زيارة الحساب المهني
                                    </a>
                                @else
                                    <span class="text-muted small">غير متوفر</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <small class="text-muted d-block mb-1"><i class="fas fa-globe text-info me-1"></i> معرض الأعمال / Portfolio</small>
                                @if($graduate->portfolio_url)
                                    <a href="{{ $graduate->portfolio_url }}" target="_blank" class="fw-bold text-info text-decoration-none small">
                                        <i class="fas fa-external-link-alt me-1"></i> تصفح معرض الأعمال
                                    </a>
                                @else
                                    <span class="text-muted small">غير متوفر</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. المهارات والخبرات والسيرة الذاتية -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-briefcase me-2"></i>المهارات والخبرات والسيرة الذاتية
                    </h5>
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small">جاهزية التوظيف</span>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- المهارات -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-tools text-primary me-1"></i> المهارات التقنية والمهنية:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @if(!empty($graduate->skills))
                                    @php
                                        $skills = is_array($graduate->skills) ? $graduate->skills : (is_string($graduate->skills) ? explode(',', $graduate->skills) : []);
                                    @endphp
                                    @forelse($skills as $skill)
                                        @if(trim($skill))
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-semibold">{{ trim($skill) }}</span>
                                        @endif
                                    @empty
                                        <span class="text-muted small">لم يتم تسجيل مهارات بعد</span>
                                    @endforelse
                                @else
                                    <span class="text-muted small">لم يتم تسجيل مهارات بعد</span>
                                @endif
                            </div>
                        </div>

                        <!-- اللغات -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-language text-info me-1"></i> اللغات المتقنة:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @if(!empty($graduate->languages))
                                    @php
                                        $languages = is_array($graduate->languages) ? $graduate->languages : (is_string($graduate->languages) ? explode(',', $graduate->languages) : []);
                                    @endphp
                                    @forelse($languages as $lang)
                                        @if(trim($lang))
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill fw-semibold">{{ trim($lang) }}</span>
                                        @endif
                                    @empty
                                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">العربية</span>
                                    @endforelse
                                @else
                                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">العربية</span>
                                @endif
                            </div>
                        </div>

                        <!-- الخبرات السابقة -->
                        @if($graduate->work_experience)
                            <div class="mb-4 p-3 bg-light rounded-3">
                                <h6 class="fw-bold text-dark small mb-1"><i class="fas fa-history text-secondary me-1"></i> الخبرات العملية السابقة:</h6>
                                <p class="text-secondary small mb-0 text-break" style="white-space: pre-line;">{{ $graduate->work_experience }}</p>
                            </div>
                        @endif

                        <!-- الشهادات الذاتية إن وجدت -->
                        @if($graduate->certifications)
                            <div class="mb-4 p-3 bg-light rounded-3">
                                <h6 class="fw-bold text-dark small mb-1"><i class="fas fa-certificate text-secondary me-1"></i> شهادات إضافية مسجلة:</h6>
                                <p class="text-secondary small mb-0">{{ $graduate->certifications }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- السيرة الذاتية -->
                    <div class="p-3 border rounded-3 bg-light mt-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">ملف السيرة الذاتية (CV)</h6>
                                    <small class="text-muted">
                                        {{ $graduate->cv_path ? basename($graduate->cv_path) : 'لم يتم رفع سيرة ذاتية بعد' }}
                                    </small>
                                </div>
                            </div>
                            @if($graduate->cv_path)
                                <div class="d-flex gap-2">
                                    <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> فتح ومعاينة
                                    </a>
                                    <a href="{{ Storage::url($graduate->cv_path) }}" download class="btn btn-sm btn-success rounded-pill px-3">
                                        <i class="fas fa-download me-1"></i> تحميل الملف
                                    </a>
                                </div>
                            @else
                                <span class="badge bg-secondary text-white rounded-pill px-3 py-1">غير متوفر</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. البرامج والورش التدريبية التي يحضرها الخريج (CRUCIAL) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        البرامج والورش التدريبية التي حضرها / يحضرها الخريج
                    </h5>
                    <small class="text-muted">بيانات التسجيل والحضور الفعلي المعتمدة لمسؤولي الإرشاد المهني للترشيح</small>
                </div>
            </div>
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 fs-7">
                إجمالي البرامج: {{ $trainingApplications->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            @if($trainingApplications->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-secondary small fw-bold" width="60">#</th>
                                <th class="py-3 text-secondary small fw-bold">اسم البرنامج التدريبي</th>
                                <th class="py-3 text-secondary small fw-bold">المدرب / الجهة المنفذة</th>
                                <th class="py-3 text-secondary small fw-bold text-center">الفترة الزمنية</th>
                                <th class="py-3 text-secondary small fw-bold text-center">حالة القبول والتسجيل</th>
                                <th class="py-3 text-secondary small fw-bold text-center">الحضور الفعلي</th>
                                <th class="py-3 px-4 text-secondary small fw-bold text-center">تاريخ التقديم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainingApplications as $app)
                                <tr>
                                    <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $app->training->title ?? 'تدريب غير محدد' }}</div>
                                        <div class="text-muted small">
                                            @if(!empty($app->training->category))
                                                <span class="badge bg-light text-primary border border-primary border-opacity-25 px-2 py-0 me-1">{{ $app->training->category }}</span>
                                            @endif
                                            @if(!empty($app->training->location))
                                                <i class="fas fa-map-marker-alt text-muted ms-1"></i> {{ $app->training->location }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small fw-semibold">
                                            {{ $app->training->instructor_name ?? ($app->training->company->name ?? 'مركز تدريب الجامعة') }}
                                        </div>
                                        @if($app->training->duration)
                                            <div class="text-muted extra-small">
                                                <i class="far fa-clock me-1"></i> المدة: {{ $app->training->duration }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center text-secondary small">
                                        @if($app->training && $app->training->start_date)
                                            <div>من: {{ $app->training->start_date->format('Y-m-d') }}</div>
                                            <div>إلى: {{ $app->training->end_date ? $app->training->end_date->format('Y-m-d') : 'مستمر' }}</div>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(in_array($app->status, ['approved', 'accepted']))
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-check-circle me-1"></i> مقبول / نشط
                                            </span>
                                        @elseif($app->status == 'completed')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-graduation-cap me-1"></i> أتم البرنامج
                                            </span>
                                        @elseif($app->status == 'pending')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-clock me-1"></i> قيد المراجعة
                                            </span>
                                        @elseif($app->status == 'rejected')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-times-circle me-1"></i> غير مقبول
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1">{{ $app->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-light text-dark border px-2 py-1 mb-1 fw-bold">
                                                {{ $app->attended_days_count }} يوم حضور
                                            </span>
                                            @php
                                                $pct = $app->attendance_percentage;
                                                $barColor = $pct >= 80 ? 'bg-success' : ($pct >= 50 ? 'bg-info' : 'bg-warning');
                                            @endphp
                                            <div class="progress w-75" style="height: 6px;" title="نسبة الحضور: {{ $pct }}%">
                                                <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted extra-small mt-1">{{ $pct }}%</small>
                                        </div>
                                    </td>
                                    <td class="px-4 text-center text-muted small">
                                        {{ $app->applied_at ? $app->applied_at->format('Y-m-d') : ($app->created_at ? $app->created_at->format('Y-m-d') : '--') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-user-clock display-5 text-muted mb-3 opacity-50"></i>
                    <h6 class="text-dark fw-bold">لم يتم العثور على برامج تدريبية مسجلة لهذا الخريج</h6>
                    <p class="text-muted small mb-0">يمكن للخريج التقديم على البرامج التدريبية المتاحة عبر المنصة</p>
                </div>
            @endif
        </div>
    </div>

    <!-- 4. الشهادات المعتمدة الصادرة للخريج -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fas fa-award"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        الشهادات الأكاديمية والمهنية المعتمدة
                    </h5>
                    <small class="text-muted">شهادات موثقة برمز التحقق صادرة عبر المنصة</small>
                </div>
            </div>
            <span class="badge bg-success text-white rounded-pill px-3 py-2 fs-7">
                إجمالي الشهادات: {{ $certificates->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            @if($certificates->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-secondary small fw-bold" width="60">#</th>
                                <th class="py-3 text-secondary small fw-bold">عنوان الشهادة</th>
                                <th class="py-3 text-secondary small fw-bold">كود الشهادة الرسمي</th>
                                <th class="py-3 text-secondary small fw-bold text-center">نوع الشهادة</th>
                                <th class="py-3 text-secondary small fw-bold text-center">الساعات</th>
                                <th class="py-3 text-secondary small fw-bold text-center">تاريخ الإصدار</th>
                                <th class="py-3 px-4 text-secondary small fw-bold text-end" width="140">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $cert)
                                <tr>
                                    <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $cert->title }}</div>
                                        @if($cert->company_name || ($cert->company && $cert->company->name))
                                            <div class="text-muted extra-small">
                                                <i class="fas fa-building me-1"></i> بالشراكة مع: {{ $cert->company_name ?? $cert->company->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                            {{ $cert->certificate_code }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                            {{ $cert->type_label ?? 'شهادة معتمدة' }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-secondary small">
                                        {{ $cert->hours ? $cert->hours . ' ساعة' : '--' }}
                                    </td>
                                    <td class="text-center text-muted small">
                                        {{ $cert->issue_date ? $cert->issue_date->format('Y-m-d') : ($cert->created_at ? $cert->created_at->format('Y-m-d') : '--') }}
                                    </td>
                                    <td class="px-4 text-end">
                                        <a href="{{ route('graduate.certificates.show', $cert->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-eye me-1"></i> عرض وطباعة
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-certificate display-5 text-muted mb-3 opacity-50"></i>
                    <h6 class="text-dark fw-bold">لا توجد شهادات معتمدة صادرة لهذا الخريج بعد</h6>
                    <p class="text-muted small mb-0">تصدر الشهادات آلياً فور استكمال شروط الحضور واجتياز متطلبات التدريب</p>
                </div>
            @endif
        </div>
    </div>

    <!-- 5. المشاركة في معارض وملتقيات التوظيف -->
    @if($jobFairRegistrations->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                            المشاركة في معارض وملتقيات التوظيف
                        </h5>
                        <small class="text-muted">سجل الحضور والتفاعل في الفعاليات الرسمية</small>
                    </div>
                </div>
                <span class="badge bg-info text-white rounded-pill px-3 py-2 fs-7">
                    الفعاليات: {{ $jobFairRegistrations->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-secondary small fw-bold" width="60">#</th>
                                <th class="py-3 text-secondary small fw-bold">اسم الفعالية / المعرض</th>
                                <th class="py-3 text-secondary small fw-bold">رقم التسجيل</th>
                                <th class="py-3 text-secondary small fw-bold text-center">حالة الحضور</th>
                                <th class="py-3 text-secondary small fw-bold text-center">وقت تسجيل الدخول</th>
                                <th class="py-3 px-4 text-secondary small fw-bold">مجالات الاهتمام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobFairRegistrations as $fairReg)
                                <tr>
                                    <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $fairReg->jobFair->title ?? 'معرض توظيف' }}</div>
                                        @if($fairReg->jobFair && $fairReg->jobFair->start_date)
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($fairReg->jobFair->start_date)->format('Y-m-d') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                            {{ $fairReg->registration_number ?? 'JF-REG' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($fairReg->attended)
                                            <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                                <i class="fas fa-check-circle me-1"></i> حضر المعرض
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                                                مسجل فقط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted small">
                                        {{ $fairReg->check_in_at ? $fairReg->check_in_at->format('Y-m-d H:i') : '--' }}
                                    </td>
                                    <td class="px-4 text-secondary small">
                                        {{ $fairReg->interests ?? 'الفرص التقنية والوظائف الشاغرة' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- 6. سجل الترشيحات الوظيفية الخاصة بالخريج -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        سجل ترشيحات الخريج للوظائف ({{ $graduate->nominations->count() }})
                    </h5>
                    <small class="text-muted">متابعة نتائج ومراحل التوظيف لدى الشركات الشريكة</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-warning fw-bold text-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickNominateModal">
                    <i class="fas fa-plus me-1"></i> ترشيح فوري
                </button>
                <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> ترشيح مخصص
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($graduate->nominations->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-secondary small fw-bold" width="60">#</th>
                                <th class="py-3 text-secondary small fw-bold">فرصة العمل</th>
                                <th class="py-3 text-secondary small fw-bold">الشركة</th>
                                <th class="py-3 text-secondary small fw-bold text-center">حالة الترشيح</th>
                                <th class="py-3 text-secondary small fw-bold text-center">الحالة النهائية</th>
                                <th class="py-3 text-secondary small fw-bold text-center">تاريخ الترشيح</th>
                                <th class="py-3 text-secondary small fw-bold text-center">المرشح بواسطة</th>
                                <th class="py-3 px-4 text-secondary small fw-bold text-end" width="120">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($graduate->nominations as $nomination)
                                <tr>
                                    <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route($prefix . '.nominations.show', $nomination->id) }}" class="text-dark fw-bold text-decoration-none">
                                            {{ $nomination->jobOpportunity->title ?? 'فرصة غير محددة' }}
                                        </a>
                                    </td>
                                    <td class="text-primary fw-bold">{{ $nomination->jobOpportunity->company->name ?? 'شركة غير محددة' }}</td>
                                    <td class="text-center">
                                        @if($nomination->status == 'accepted')
                                            <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">مقبول</span>
                                        @elseif($nomination->status == 'rejected')
                                            <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">مرفوض</span>
                                        @elseif($nomination->status == 'pending')
                                            <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">قيد المراجعة</span>
                                        @else
                                            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1">{{ $nomination->status_text ?? $nomination->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($nomination->final_status == 'hired')
                                            <span class="badge bg-success text-white rounded-pill px-3 py-1 shadow-sm">
                                                <i class="fas fa-check-circle me-1"></i> تم التوظيف
                                            </span>
                                        @elseif($nomination->final_status == 'not_hired')
                                            <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                                <i class="fas fa-times-circle me-1"></i> لم يتم التوظيف
                                            </span>
                                        @else
                                            <span class="text-muted small">قيد المتابعة</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted small">{{ $nomination->nominated_at ? $nomination->nominated_at->format('Y-m-d') : '--' }}</td>
                                    <td class="text-center text-muted small">{{ $nomination->nominator->name ?? 'غير محدد' }}</td>
                                    <td class="px-4 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route($prefix . '.nominations.show', $nomination->id) }}"
                                                class="btn btn-sm btn-outline-info" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route($prefix . '.nominations.edit-status', $nomination->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="تحديث الحالة">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-paper-plane display-5 text-muted mb-3 opacity-50"></i>
                    <h6 class="text-dark fw-bold">لا توجد ترشيحات سابقة لهذا الخريج</h6>
                    <p class="text-muted small mb-3">يمكنك الآن ترشيح الخريج مباشرة لفرص العمل المتاحة بما يتوافق مع تدريباته ومؤهلاته</p>
                    <button type="button" class="btn btn-warning fw-bold text-dark px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#quickNominateModal">
                        <i class="fas fa-paper-plane me-1"></i> ترشيح الخريج الآن
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================== Modal 1: الترشيح الفوري لفرصة عمل ==================== -->
<div class="modal fade" id="quickNominateModal" tabindex="-1" aria-labelledby="quickNominateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-paper-plane fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="quickNominateModalLabel">ترشيح فوري لفرصة عمل</h5>
                        <small class="text-white-50">ترشيح الخريج: {{ $graduate->name }} ({{ $graduate->major }})</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route($prefix . '.nominations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="graduate_id" value="{{ $graduate->id }}">
                <input type="hidden" name="redirect_to_graduate" value="1">

                <div class="modal-body p-4">
                    <!-- تنبيه معلومات الخريج الجاهزة -->
                    <div class="alert alert-light border rounded-3 mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="fw-bold text-dark">{{ $graduate->name }}</span>
                                <span class="text-muted small ms-2">| {{ $graduate->degree ?? 'بكالوريوس' }} {{ $graduate->major }} (دفعة {{ $graduate->graduation_year }})</span>
                            </div>
                            <div class="text-primary small fw-semibold">
                                <i class="fas fa-award me-1"></i> {{ $certificates->count() }} شهادات | <i class="fas fa-chalkboard-teacher me-1"></i> {{ $trainingApplications->count() }} برامج تدريبية
                            </div>
                        </div>
                    </div>

                    <!-- اختيار فرصة العمل -->
                    <div class="mb-3">
                        <label for="job_opportunity_id" class="form-label fw-bold text-dark">
                            اختيار فرصة العمل الشاغرة <span class="text-danger">*</span>
                        </label>
                        @if($openOpportunities->isNotEmpty())
                            <select class="form-select form-select-lg rounded-3" id="job_opportunity_id" name="job_opportunity_id" required>
                                <option value="" disabled selected>-- اختر فرصة العمل المناسبة --</option>
                                @foreach($openOpportunities as $opp)
                                    <option value="{{ $opp->id }}">
                                        {{ $opp->title }} - {{ $opp->company->name ?? 'شركة' }} ({{ $opp->location ?? 'طرابلس' }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning border-0 rounded-3 mb-0">
                                <i class="fas fa-exclamation-circle me-1"></i> لا توجد فرص عمل مفتوحة حالياً في النظام. يمكنك إضافة فرصة عمل أولاً.
                            </div>
                        @endif
                    </div>

                    <!-- أسباب الترشيح وملاءمة المؤهلات -->
                    <div class="mb-3">
                        <label for="matching_reasons" class="form-label fw-bold text-dark">
                            أسباب الترشيح وملاءمة المؤهلات والمهارات <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control rounded-3" id="matching_reasons" name="matching_reasons" rows="3" required placeholder="اشرح سبب ملاءمة الخريج وتدريباته مع متطلبات هذه الوظيفة...">خريج متميز في {{ $graduate->major }} ويمتلك مهارات مناسبة وحضر البرامج التدريبية المعتمدة بالجامعة.</textarea>
                        <small class="text-muted">هذا النص سيظهر للشركة الشريكة ومسؤولي التوظيف لدعم فرصة الخريج.</small>
                    </div>

                    <!-- ملاحظات إضافية -->
                    <div class="mb-2">
                        <label for="nomination_notes" class="form-label fw-bold text-dark">ملاحظات إضافية للمتابعة (اختياري)</label>
                        <textarea class="form-control rounded-3" id="nomination_notes" name="nomination_notes" rows="2" placeholder="أي ملاحظات داخلية تود تسجيلها لمسؤولي الإرشاد والتوظيف..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark rounded-pill px-4 shadow-sm" @if($openOpportunities->isEmpty()) disabled @endif>
                        <i class="fas fa-check-circle me-1"></i> تأكيد وإرسال الترشيح
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== Modal 2: تغيير كلمة المرور ==================== -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white p-3">
                <h5 class="modal-title fw-bold fs-6" id="resetPasswordModalLabel">
                    <i class="fas fa-key me-2"></i> تغيير كلمة مرور حساب الخريج
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route($prefix . '.graduates.reset-password', $graduate->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-3">
                        <i class="fas fa-user-circle me-1"></i> إعادة تعيين كلمة المرور لحساب الخريج: <strong>{{ $graduate->name }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="show_modal_new_password" class="form-label fw-bold small text-muted">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="show_modal_new_password" name="new_password" required minlength="8" placeholder="8 أحرف على الأقل">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('show_modal_new_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="show_modal_new_password_confirmation" class="form-label fw-bold small text-muted">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="show_modal_new_password_confirmation" name="new_password_confirmation" required minlength="8" placeholder="أعد إدخال كلمة المرور">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('show_modal_new_password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> حفظ وتحديث كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
</script>
@endsection