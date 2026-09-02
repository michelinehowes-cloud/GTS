@extends('layouts.app')

@section('title', 'تفاصيل الخريج - ' . $graduate->name)

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
            ['label' => $graduate->name, 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 56px; height: 56px;">
                {{ mb_substr($graduate->name, 0, 1) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h2 class="text-primary fw-bold mb-0">{{ $graduate->name }}</h2>
                    @if($graduate->employment_status == 'employed')
                        <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">
                            <i class="fas fa-check-circle me-1"></i> موظف
                        </span>
                    @elseif($graduate->employment_status == 'seeking_opportunities')
                        <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">
                            <i class="fas fa-search me-1"></i> باحث عن عمل
                        </span>
                    @elseif($graduate->employment_status == 'unemployed')
                        <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">
                            <i class="fas fa-times-circle me-1"></i> عاطل عن العمل
                        </span>
                    @else
                        <span class="badge bg-light text-info border border-info rounded-pill px-3 py-1">
                            <i class="fas fa-graduation-cap me-1"></i> مستكمل للدراسة
                        </span>
                    @endif
                </div>
                <div class="text-muted small mt-1">
                    {{ $graduate->major }} &bull; {{ $graduate->faculty ?? $graduate->university }} &bull; دفعة {{ $graduate->graduation_year }}
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="btn btn-primary-modern">
                <i class="fas fa-paper-plane me-1"></i> ترشيح لفرصة
            </a>
            <a href="{{ route($prefix . '.graduates.edit', $graduate->id) }}" class="btn btn-outline-warning-modern">
                <i class="fas fa-edit me-1"></i> تعديل البيانات
            </a>
            <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- المعلومات الشخصية والأكاديمية -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-user me-2"></i>البيانات الشخصية والأكاديمية
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                                <span class="fw-bold text-dark text-break small">{{ $graduate->email ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">رقم الهاتف</small>
                                <span class="fw-bold text-dark">{{ $graduate->phone ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الجامعة</small>
                                <span class="fw-bold text-dark">{{ $graduate->university ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الكلية / القطاع</small>
                                <span class="fw-bold text-dark">{{ $graduate->faculty ?? ($graduate->sector ?? 'غير محدد') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">التخصص</small>
                                <span class="fw-bold text-primary">{{ $graduate->major }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الدرجة والمعدل</small>
                                <span class="fw-bold text-dark">{{ $graduate->degree ?? 'بكالوريوس' }} @if($graduate->gpa) ({{ $graduate->gpa }}%) @endif</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">العنوان / المدينة</small>
                                <span class="fw-bold text-dark">{{ $graduate->address ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">LinkedIn</small>
                                @if($graduate->linkedin_url)
                                    <a href="{{ $graduate->linkedin_url }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                        <i class="fab fa-linkedin me-1"></i> فتح الرابط
                                    </a>
                                @else
                                    <span class="text-muted">غير متوفر</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المهارات والسيرة الذاتية -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-tools me-2"></i>المهارات والخبرات والسيرة الذاتية
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- المهارات -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark small mb-2">المهارات التقنية والمهنية:</h6>
                        <div>
                            @if(!empty($graduate->skills))
                                @php
                                    $skills = is_array($graduate->skills) ? $graduate->skills : explode(',', $graduate->skills);
                                @endphp
                                @foreach($skills as $skill)
                                    @if(trim($skill))
                                        <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill me-1 mb-1">{{ trim($skill) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-muted small">لم يتم تسجيل مهارات بعد</span>
                            @endif
                        </div>
                    </div>

                    <!-- اللغات -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark small mb-2">اللغات:</h6>
                        <div>
                            @if(!empty($graduate->languages))
                                @php
                                    $languages = is_array($graduate->languages) ? $graduate->languages : explode(',', $graduate->languages);
                                @endphp
                                @foreach($languages as $lang)
                                    @if(trim($lang))
                                        <span class="badge bg-light text-info border border-info px-3 py-2 rounded-pill me-1 mb-1">{{ trim($lang) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="badge bg-light text-secondary border border-secondary px-3 py-2 rounded-pill">العربية</span>
                            @endif
                        </div>
                    </div>

                    <!-- السيرة الذاتية -->
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">السيرة الذاتية (CV)</h6>
                                    <small class="text-muted">
                                        {{ $graduate->cv_path ? basename($graduate->cv_path) : 'لم يتم رفع سيرة ذاتية بعد' }}
                                    </small>
                                </div>
                            </div>
                            @if($graduate->cv_path)
                                <div class="d-flex gap-2">
                                    <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-info-modern">
                                        <i class="fas fa-eye me-1"></i> عرض
                                    </a>
                                    <a href="{{ Storage::url($graduate->cv_path) }}" download class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-download me-1"></i> تحميل
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- سجل الترشيحات الخاصة بالخريج -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-paper-plane me-2"></i>سجل ترشيحات الخريج ({{ $graduate->nominations->count() }})
            </h5>
            <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="btn btn-sm btn-primary-modern">
                <i class="fas fa-plus me-1"></i> ترشيح جديد
            </a>
        </div>
        <div class="card-body p-0">
            @if($graduate->nominations->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-secondary small text-uppercase fw-bold" width="60">#</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold">فرصة العمل</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold">الشركة</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold text-center">حالة الترشيح</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold text-center">الحالة النهائية</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold text-center">تاريخ الترشيح</th>
                                <th class="py-3 text-secondary small text-uppercase fw-bold text-center">المرشح بواسطة</th>
                                <th class="py-3 px-4 text-secondary small text-uppercase fw-bold text-end" width="120">الإجراءات</th>
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
                                            <span class="badge bg-light text-success border border-success rounded-pill px-3 py-2">مقبول</span>
                                        @elseif($nomination->status == 'rejected')
                                            <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-2">مرفوض</span>
                                        @elseif($nomination->status == 'pending')
                                            <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-2">قيد المراجعة</span>
                                        @else
                                            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-2">{{ $nomination->status_text ?? $nomination->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($nomination->final_status == 'hired')
                                            <span class="badge bg-light text-success border border-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> تم التوظيف
                                            </span>
                                        @elseif($nomination->final_status == 'not_hired')
                                            <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-2">
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
                                                class="btn btn-sm btn-outline-info-modern" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route($prefix . '.nominations.edit-status', $nomination->id) }}"
                                                class="btn btn-sm btn-outline-warning-modern" title="تحديث الحالة">
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
                    <i class="fas fa-paper-plane display-4 text-muted mb-3 opacity-50"></i>
                    <h6 class="text-dark fw-bold">لا توجد ترشيحات لهذا الخريج حتى الآن</h6>
                    <p class="text-muted small mb-3">يمكنك ترشيح الخريج للوظائف الشاغرة المتوافقة مع تخصصه</p>
                    <a href="{{ route($prefix . '.nominations.create', ['graduate_id' => $graduate->id]) }}" class="btn btn-primary-modern px-4">
                        <i class="fas fa-plus me-1"></i> إنشاء أول ترشيح
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection