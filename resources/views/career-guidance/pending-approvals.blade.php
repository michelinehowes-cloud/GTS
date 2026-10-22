@extends('layouts.app')

@section('title', 'طلبات تسجيل الخريجين المعلقة والتدقيق الذكي')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'إدارة الإرشاد المهني', 'url' => auth()->user()->isAdmin() ? route('admin.career-guidance.dashboard') : route('career-guidance.dashboard')],
            ['label' => 'طلبات تسجيل الخريجين', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="card-modern mb-4 p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light-warning text-warning fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.4rem;">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <h2 class="text-primary fw-bold mb-1 fs-4">طلبات تسجيل الخريجين المعلقة</h2>
                    <p class="text-muted small mb-0">مراجعة وتدقيق واعتماد طلبات إنشاء حسابات الخريجين الجدد في المنظومة بالذكاء الاصطناعي</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- زر فحص وتدقيق الطلبات بالذكاء الاصطناعي -->
                @if($pendingUsers->count() > 0)
                <button type="button" id="btnRunAiAudit" class="btn btn-sm btn-gradient-ai text-white rounded-pill px-3 py-2 shadow-sm fw-bold d-flex align-items-center gap-2" onclick="runAiAudit()">
                    <i class="fas fa-magic text-warning" id="aiIcon"></i>
                    <span id="aiButtonText">✨ فحص وتدقيق الطلبات بالذكاء الاصطناعي</span>
                    <span class="spinner-border spinner-border-sm text-light d-none" id="aiSpinner" role="status"></span>
                </button>
                @endif

                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-6">
                    <i class="fas fa-clock me-1"></i> {{ $pendingUsers->total() ?? $pendingUsers->count() }} طلب قيد الانتظار
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($pendingUsers->count() > 0)
    <!-- لوحة نتائج التدقيق الذكي (AI Audit Banner) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden ai-summary-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 fw-bold">
                        <i class="fas fa-robot me-1 text-primary"></i> نتائج التدقيق والتحقق الذكي
                    </span>
                    <span class="text-muted small">فحص تلقائي للاسم ورقم القيد الجامعي والاتساق الأكاديمي وبيانات الاتصال والتسلسل الزمني</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- زر سريع لتحديد السليمة فقط في الشيك بوكس -->
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm fw-bold" onclick="selectByVerdict('valid')">
                        <i class="fas fa-check-circle me-1"></i> تحديد السليمة (85%+ 🟢)
                    </button>
                    <!-- زر اعتماد السليمة فوراً -->
                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold" onclick="approveValidImmediately()">
                        <i class="fas fa-bolt me-1"></i> اعتماد السليمة فوراً (85%+ 🟢)
                    </button>
                </div>
            </div>

            <!-- بطاقات الإحصائيات الذكية -->
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="p-3 rounded-3 bg-white border border-success border-opacity-25 shadow-sm d-flex align-items-center justify-content-between h-100">
                        <div>
                            <div class="text-muted small mb-1 fw-semibold">طلبات مكتملة وموثوقة (ثقة 85%+ ومعدل 50%+)</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fs-4 fw-bold text-success" id="countValid">{{ $auditSummary['valid'] ?? 0 }}</span>
                                <span class="text-muted small">طلب (جاهز للاعتماد)</span>
                            </div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="fas fa-check-circle fs-5"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="p-3 rounded-3 bg-white border border-warning border-opacity-25 shadow-sm d-flex align-items-center justify-content-between h-100">
                        <div>
                            <div class="text-muted small mb-1 fw-semibold">تحتاج تدقيق ومراجعة</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fs-4 fw-bold text-warning" id="countWarning">{{ $auditSummary['warning'] ?? 0 }}</span>
                                <span class="text-muted small">طلب (ملاحظات تدقيق)</span>
                            </div>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="fas fa-exclamation-circle fs-5"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="p-3 rounded-3 bg-white border border-danger border-opacity-25 shadow-sm d-flex align-items-center justify-content-between h-100">
                        <div>
                            <div class="text-muted small mb-1 fw-semibold">بيانات مشبوهة أو غير متطابقة</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fs-4 fw-bold text-danger" id="countInvalid">{{ $auditSummary['invalid'] ?? 0 }}</span>
                                <span class="text-muted small">طلب (يُوصى بالرفض)</span>
                            </div>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="fas fa-times-circle fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="card-modern">
        <!-- Card Header & Bulk Actions Toolbar -->
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-list-check me-2"></i>قائمة الطلبات قيد المراجعة والاعتماد
                    </h5>
                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 small">
                        {{ $pendingUsers->total() ?? $pendingUsers->count() }} طلب
                    </span>
                </div>

                @if($pendingUsers->count() > 0)
                <!-- أزرار الإجراءات للشيك بوكس -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- شارة عدد العناصر المحددة -->
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1.5 fw-bold small">
                        المحدد: <span id="selectedCountDisplay" class="fs-6">0</span>
                    </span>

                    <!-- اعتماد المحدد -->
                    <button type="button" id="btnBulkApprove" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm disabled" onclick="submitBulkAction('approve')">
                        <i class="fas fa-check-double me-1"></i> اعتماد المحدد (<span id="selectedCountApprove">0</span>)
                    </button>

                    <!-- رفض المحدد -->
                    <button type="button" id="btnBulkReject" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm disabled" onclick="submitBulkAction('reject')">
                        <i class="fas fa-trash-alt me-1"></i> رفض المحدد
                    </button>

                    <!-- اعتماد الكل بنقرة واحدة -->
                    <form id="approveAllForm" action="{{ route('career-guidance.bulk-approve-graduates') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <input type="hidden" name="approve_all" value="1">
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm" onclick="confirmApproveAll({{ $pendingUsers->total() ?? $pendingUsers->count() }})">
                            <i class="fas fa-clipboard-check me-1"></i> اعتماد الكل
                        </button>
                    </form>
                </div>
                @endif
            </div>

            @if($pendingUsers->count() > 0)
            <!-- شريط خيارات الشيك بوكس السريعة (Checkbox Quick Selectors) -->
            <div class="pt-3 mt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light bg-opacity-50 px-2 py-2 rounded-3">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="text-muted small fw-bold me-1">
                        <i class="fas fa-check-square me-1 text-primary"></i>خيارات التحديد بالشيك بوكس:
                    </span>
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="selectAllCheckboxes()">
                        <i class="fas fa-check-double me-1"></i>تحديد الكل
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="deselectAllCheckboxes()">
                        <i class="fas fa-square me-1"></i>إلغاء التحديد
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" onclick="selectByVerdict('valid')">
                        <i class="fas fa-check-circle me-1"></i>السليمة فقط (🟢)
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 fw-semibold" onclick="selectByVerdict('warning')">
                        <i class="fas fa-exclamation-triangle me-1"></i>تحتاج مراجعة (🟡)
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" onclick="selectByVerdict('invalid')">
                        <i class="fas fa-times-circle me-1"></i>المشبوهة (🔴)
                    </button>
                </div>

                <div class="text-muted small">
                    انقر على أي شيك بوكس أو صف لتحديده
                </div>
            </div>
            @endif
        </div>

        <div class="card-body p-0">
            @if($pendingUsers->count() > 0)
                <!-- Form للإجراءات الجماعية -->
                <form id="bulkActionForm" method="POST" action="">
                    @csrf

                    <!-- 1. عرض الجدول للشاشات المتوسطة والكبيرة (Desktop & Tablet Table) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-3 text-center" style="width: 44px;">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input shadow-none custom-checkbox-main" title="تحديد / إلغاء تحديد الكل">
                                    </th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 px-3">الخريج</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">بيانات الاتصال</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">المؤهل والتخصص</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center" style="min-width: 140px;">تدقيق الذكاء الاصطناعي</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">تاريخ الطلب</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center" style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                    @php
                                        $eval = $audits[$user->id] ?? \App\Http\Controllers\GraduateRegistrationController::evaluateGraduateApplication($user);
                                        $verdict = $eval['verdict'] ?? 'valid';
                                        $score = $eval['score'] ?? 100;
                                        $badgeColor = $eval['badge_color'] ?? 'success';
                                    @endphp
                                    <tr id="rowUser{{ $user->id }}" class="user-row-item" data-user-id="{{ $user->id }}" data-verdict="{{ $verdict }}">
                                        <!-- خانة الشيك بوكس -->
                                        <td class="px-3 text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox shadow-none custom-checkbox-item" data-verdict="{{ $verdict }}" onchange="updateBulkButtonsState()">
                                        </td>
                                        <td class="px-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm" style="width: 42px; height: 42px; font-size: 1.15rem;">
                                                    {{ mb_substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ $user->name }}</div>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-id-card me-1 text-primary"></i>رقم القيد: <span class="fw-bold text-dark">{{ $user->national_id ?? 'غير متوفر' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark mb-1">
                                                <i class="fas fa-envelope me-1 text-muted"></i>{{ $user->email }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-phone me-1 text-muted"></i><span dir="ltr">{{ $user->phone ?? 'لا يوجد هاتف' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-primary mb-1">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ $user->specialization ?? $user->major ?? 'غير محدد' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $user->faculty ?? 'جامعة طرابلس' }} @if($user->graduation_year) <span class="badge bg-light text-dark border ms-1">{{ $user->graduation_year }}</span> @endif
                                            </div>
                                        </td>
                                        <!-- تدقيق الذكاء الاصطناعي -->
                                        <td class="text-center" id="aiBadgeCell{{ $user->id }}">
                                            <div class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $eval['summary'] }} - {{ $eval['recommendation'] }}">
                                                <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} border-opacity-25 rounded-pill px-2.5 py-1.5 fw-bold small d-inline-flex align-items-center gap-1 cursor-pointer" onclick="openDetailsModal({{ $user->id }})">
                                                    @if($verdict === 'valid')
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        <span>سليم وموثوق ({{ $score }}%)</span>
                                                    @elseif($verdict === 'warning')
                                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                                        <span>يحتاج مراجعة ({{ $score }}%)</span>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                        <span>غير متطابق ({{ $score }}%)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <div>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <!-- زر التفاصيل الكاملة (عين) -->
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center action-btn-circle" style="width: 34px; height: 34px;" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}" title="عرض التفاصيل ونتائج التدقيق">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <!-- زر الموافقة والتفعيل -->
                                                <button type="button" class="btn btn-sm btn-success rounded-circle d-inline-flex align-items-center justify-content-center action-btn-circle" style="width: 34px; height: 34px;" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="approveDirectly({{ $user->id }}, this.getAttribute('data-user-name'))" title="موافقة واعتماد فوري">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <!-- زر الرفض والحذف -->
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center action-btn-circle" style="width: 34px; height: 34px;" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="rejectDirectly({{ $user->id }}, this.getAttribute('data-user-name'))" title="رفض الطلب">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. عرض بطاقات الهواتف المحمولة (Mobile Card View) -->
                    <div class="d-md-none p-3">
                        <div class="d-flex flex-column gap-3">
                            @foreach($pendingUsers as $user)
                                @php
                                    $eval = $audits[$user->id] ?? \App\Http\Controllers\GraduateRegistrationController::evaluateGraduateApplication($user);
                                    $verdict = $eval['verdict'] ?? 'valid';
                                    $score = $eval['score'] ?? 100;
                                    $badgeColor = $eval['badge_color'] ?? 'success';
                                @endphp
                                <div class="card border rounded-3 shadow-sm mobile-user-card" id="mobileCard{{ $user->id }}" data-user-id="{{ $user->id }}" data-verdict="{{ $verdict }}">
                                    <!-- رأس البطاقة مع الشيك بوكس -->
                                    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox user-checkbox-mobile shadow-none custom-checkbox-item" data-verdict="{{ $verdict }}" onchange="syncMobileCheckbox({{ $user->id }}, this.checked)">
                                            <div class="fw-bold text-dark fs-6">{{ $user->name }}</div>
                                        </div>
                                        <!-- شارة التدقيق الذكي -->
                                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} border-opacity-25 rounded-pill px-2 py-1 small">
                                            @if($verdict === 'valid')
                                                🟢 سليم ({{ $score }}%)
                                            @elseif($verdict === 'warning')
                                                🟡 مراجعة ({{ $score }}%)
                                            @else
                                                🔴 مشبوه ({{ $score }}%)
                                            @endif
                                        </span>
                                    </div>

                                    <!-- تفاصيل البطاقة -->
                                    <div class="card-body p-3">
                                        <div class="mb-2">
                                            <div class="fw-semibold text-primary small">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ $user->specialization ?? $user->major ?? 'غير محدد' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $user->faculty ?? 'جامعة طرابلس' }} • {{ $user->graduation_year ?? 'غير محدد' }}
                                            </div>
                                        </div>

                                        <div class="row g-2 pt-2 border-top small text-muted">
                                            <div class="col-12">
                                                <i class="fas fa-id-card me-1 text-primary"></i>رقم القيد: <strong class="text-dark">{{ $user->national_id ?? 'غير متوفر' }}</strong>
                                            </div>
                                            <div class="col-12">
                                                <i class="fas fa-envelope me-1 text-secondary"></i>{{ $user->email }}
                                            </div>
                                            <div class="col-12">
                                                <i class="fas fa-phone me-1 text-secondary"></i><span dir="ltr">{{ $user->phone ?? 'غير متوفر' }}</span>
                                            </div>
                                            <div class="col-12">
                                                <i class="fas fa-calendar-alt me-1 text-secondary"></i>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '' }} ({{ $user->created_at ? $user->created_at->diffForHumans() : '' }})
                                            </div>
                                        </div>
                                    </div>

                                    <!-- أزرار الإجراءات للبطاقة -->
                                    <div class="card-footer bg-light py-2 px-3 border-top d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}">
                                            <i class="fas fa-eye me-1"></i>التفاصيل
                                        </button>
                                        <div class="d-flex gap-1.5">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="rejectDirectly({{ $user->id }}, this.getAttribute('data-user-name'))">
                                                <i class="fas fa-times me-1"></i>رفض
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="approveDirectly({{ $user->id }}, this.getAttribute('data-user-name'))">
                                                <i class="fas fa-check me-1"></i>اعتماد
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>

                <!-- Hidden Single Action Forms for Direct Actions -->
                <form id="singleApproveForm" method="POST" action="" style="display: none;">
                    @csrf
                </form>
                <form id="singleRejectForm" method="POST" action="" style="display: none;">
                    @csrf
                </form>

                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted small">عرض {{ $pendingUsers->firstItem() ?? 1 }} إلى {{ $pendingUsers->lastItem() ?? $pendingUsers->count() }} من أصل {{ $pendingUsers->total() ?? $pendingUsers->count() }} طلب</span>
                    <div>
                        {{ $pendingUsers->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <div class="rounded-circle bg-light-success text-success d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="text-dark fw-bold mb-1">لا توجد طلبات تسجيل معلقة حالياً</h5>
                    <p class="text-muted mb-0 small">جميع طلبات تسجيل الخريجين تمت مراجعتها واعتمادها بنجاح.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals خارج الجدول لتفادي أي مشاكل في العرض أو التجاوب --}}
@if($pendingUsers->count() > 0)
@foreach($pendingUsers as $user)
@php
    $eval = $audits[$user->id] ?? \App\Http\Controllers\GraduateRegistrationController::evaluateGraduateApplication($user);
    $verdict = $eval['verdict'] ?? 'valid';
    $score = $eval['score'] ?? 100;
    $badgeColor = $eval['badge_color'] ?? 'success';
@endphp
<div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1" aria-labelledby="userModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px; font-size: 1.25rem;">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="userModalLabel{{ $user->id }}">{{ $user->name }}</h5>
                        <small class="text-white-50">طلب تسجيل حساب خريج جديد • {{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4 text-end" dir="rtl">
                <!-- قسم تقرير التدقيق الذكي (AI Audit Report Box) -->
                <div class="p-3 rounded-3 mb-4 border border-{{ $badgeColor }} border-opacity-25 bg-{{ $badgeColor }} bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-robot text-{{ $badgeColor }} fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">تقرير التدقيق الذكي بالذكاء الاصطناعي</h6>
                        </div>
                        <span class="badge bg-{{ $badgeColor }} text-white rounded-pill px-3 py-1 fw-bold">
                            درجة الموثوقية: {{ $score }}% ({{ $eval['badge_text'] }})
                        </span>
                    </div>
                    <p class="small text-dark mb-2"><strong>الخلاصة:</strong> {{ $eval['summary'] }} — {{ $eval['recommendation'] }}</p>

                    @if(!empty($eval['flags']) && count($eval['flags']) > 0)
                        <div class="mt-2 pt-2 border-top border-{{ $badgeColor }} border-opacity-25">
                            <span class="small fw-bold text-dark d-block mb-1">الملاحظات المرصودة:</span>
                            <ul class="mb-0 small text-dark ps-3 pe-0">
                                @foreach($eval['flags'] as $flag)
                                    <li class="mb-1 text-{{ $flag['type'] === 'danger' ? 'danger' : ($flag['type'] === 'warning' ? 'warning' : 'info') }}">
                                        <strong>{{ $flag['title'] ?? 'ملاحظة' }}:</strong> {{ $flag['msg'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="small text-success mt-1">
                            <i class="fas fa-check-circle me-1"></i> جميع المدخلات (الاسم، رقم القيد الجامعي، التخصص والكلية، الهاتف، البريد، تاريخ الميلاد) مستوفية للشروط بنسبة 100%.
                        </div>
                    @endif
                </div>

                <!-- 1. المعلومات الشخصية والاتصال -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center">
                        <i class="fas fa-user-circle me-2"></i>المعلومات الشخصية وبيانات الاتصال
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">الاسم الكامل</small>
                                <strong class="text-dark">{{ $user->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">رقم القيد الجامعي</small>
                                <strong class="text-dark">{{ $user->national_id ?? 'غير مسجل' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">تاريخ تقديم الطلب</small>
                                <strong class="text-dark">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'غير متوفر' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                                <strong class="text-dark text-break">{{ $user->email }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">رقم الهاتف</small>
                                <strong class="text-dark" dir="ltr">{{ $user->phone ?? 'غير متوفر' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">الجنس</small>
                                <strong class="text-dark">
                                    @if($user->gender == 'male' || $user->gender == 'ذكر')
                                        ذكر
                                    @elseif($user->gender == 'female' || $user->gender == 'أنثى')
                                        أنثى
                                    @else
                                        {{ $user->gender ?? 'غير محدد' }}
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">المدينة / العنوان</small>
                                <strong class="text-dark">{{ $user->city ?? $user->address ?? 'غير محدد' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. المؤهل الأكاديمي -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center">
                        <i class="fas fa-graduation-cap me-2"></i>المؤهل الأكاديمي والجامعي
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">الجامعة / المؤسسة</small>
                                <strong class="text-dark">{{ $user->university ?? 'جامعة طرابلس' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">الكلية / القسم</small>
                                <strong class="text-dark">{{ $user->faculty ?? 'غير محدد' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">التخصص</small>
                                <strong class="text-primary">{{ $user->specialization ?? $user->major ?? 'غير محدد' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">المؤهل العلمي</small>
                                <strong class="text-dark">{{ $user->qualification ?? $user->degree ?? 'بكالوريوس' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">سنة التخرج</small>
                                <strong class="text-dark">{{ $user->graduation_year ?? 'غير محدد' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">المعدل التراكمي (%)</small>
                                <strong class="text-dark">{{ $user->gpa ? $user->gpa . '%' : 'غير مسجل' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. المهارات واللغات -->
                <div class="mb-3">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center">
                        <i class="fas fa-tools me-2"></i>المهارات واللغات
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-2">المهارات المكتسبة</small>
                                <div>
                                    @php
                                        $skills = is_array($user->skills) ? $user->skills : (is_string($user->skills) ? json_decode($user->skills, true) : []);
                                    @endphp
                                    @if(!empty($skills) && is_array($skills))
                                        @foreach($skills as $skill)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 me-1 mb-1">{{ $skill }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">لم يتم تسجيل مهارات بعد</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-2">اللغات</small>
                                <div>
                                    @php
                                        $languages = is_array($user->languages) ? $user->languages : (is_string($user->languages) ? json_decode($user->languages, true) : []);
                                    @endphp
                                    @if(!empty($languages) && is_array($languages))
                                        @foreach($languages as $lang)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 me-1 mb-1">{{ $lang }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">العربية</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->experiences)
                <!-- 4. الخبرات السابقة -->
                <div class="mb-2">
                    <h6 class="fw-bold text-primary mb-2 d-flex align-items-center">
                        <i class="fas fa-briefcase me-2"></i>الخبرات السابقة
                    </h6>
                    <div class="p-3 bg-light rounded-3 border">
                        <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $user->experiences }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Modal Footer with Actions -->
            <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">إغلاق</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger px-3 rounded-pill" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="rejectDirectly({{ $user->id }}, this.getAttribute('data-user-name'))">
                        <i class="fas fa-times me-1"></i> رفض الطلب
                    </button>
                    <button type="button" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="approveDirectly({{ $user->id }}, this.getAttribute('data-user-name'))">
                        <i class="fas fa-check-circle me-1"></i> موافقة واعتماد الحساب
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

{{-- نافذة تأكيد الإجراءات بهوية المنصة الرسمية (GTS Brand Confirmation Modal) --}}
<div class="modal fade" id="gtsConfirmModal" tabindex="-1" aria-labelledby="gtsModalTitle" aria-hidden="true" dir="rtl" style="font-family: 'Tajawal', sans-serif; z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- شريط علوي بلون الهوية المتغير ديناميكياً -->
            <div id="gtsModalTopBar" style="height: 6px; background: linear-gradient(90deg, #10b981, #0d3882); transition: all 0.3s ease;"></div>
            
            <div class="modal-body p-4 text-center">
                <!-- أيقونة التأكيد بهوية المنصة مع تأثير النبض اللوني -->
                <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" id="gtsModalIconBox" style="width: 74px; height: 74px; transition: all 0.3s ease;">
                    <i id="gtsModalIcon" class="fas fa-check-circle fa-2x"></i>
                </div>

                <!-- عنوان النافذة -->
                <h5 class="fw-bold text-dark mb-2" id="gtsModalTitle" style="font-size: 1.25rem;">تأكيد الإجراء</h5>

                <!-- كرت معلومات الخريج أو الطلبات -->
                <div class="p-3 rounded-3 my-3 text-end" id="gtsModalTargetBox" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-user-graduate text-primary" id="gtsModalTargetIcon"></i>
                        <span class="fw-bold text-dark fs-6" id="gtsModalTargetName">اسم الخريج</span>
                    </div>
                    <div class="small text-muted" id="gtsModalTargetSubtext">طلب تسجيل جديد قيد المراجعة والاعتماد</div>
                </div>

                <!-- نص الرسالة التوضيحية -->
                <p class="text-muted small mb-0 px-2 lh-base" id="gtsModalMessage">
                    سيتم تفعيل حساب الخريج فوراً وتمكينه من الدخول للمنظومة والتقديم على الفرص التدريبية.
                </p>
            </div>

            <!-- أزرار الإجراءات بهوية المنصة -->
            <div class="modal-footer border-0 bg-light bg-opacity-75 px-4 py-3 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-muted fw-bold border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> تراجع
                </button>
                <button type="button" class="btn rounded-pill px-4 py-2 fw-bold text-white shadow-sm" id="gtsModalConfirmBtn">
                    <i class="fas fa-check-circle me-1"></i> نعم، متابعة
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for AI Gradient Buttons & Checkboxes */
    .btn-gradient-ai {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #6366f1 100%);
        border: none;
        transition: all 0.3s ease;
    }
    .btn-gradient-ai:hover {
        background: linear-gradient(135deg, #172554 0%, #2563eb 50%, #4f46e5 100%);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        transform: translateY(-1px);
    }
    .ai-summary-card {
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.8) 0%, rgba(245, 243, 255, 0.9) 100%);
        border: 1px solid rgba(199, 210, 254, 0.6) !important;
    }
    .user-row-item.table-active {
        background-color: rgba(59, 130, 246, 0.08) !important;
    }
    .mobile-user-card.card-selected {
        border-color: #3b82f6 !important;
        background-color: rgba(239, 246, 255, 0.5) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15) !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .action-btn-circle {
        flex-shrink: 0;
        transition: transform 0.15s ease;
    }
    .action-btn-circle:hover {
        transform: scale(1.08);
    }
    .custom-checkbox-main, .custom-checkbox-item {
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
        border-color: #cbd5e1;
    }
    .custom-checkbox-main:checked, .custom-checkbox-item:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    /* تحسينات بصرية لهوية نافذة التأكيد GTS */
    .icon-box-approve {
        background: rgba(16, 185, 129, 0.12) !important;
        color: #10b981 !important;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.2) !important;
    }
    .icon-box-reject {
        background: rgba(239, 68, 68, 0.12) !important;
        color: #ef4444 !important;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.2) !important;
    }
    .icon-box-bulk {
        background: rgba(13, 56, 130, 0.12) !important;
        color: #0d3882 !important;
        box-shadow: 0 0 20px rgba(13, 56, 130, 0.2) !important;
    }
</style>

<!-- مكتبة التنبيهات المتقدمة لمنع حجب مربعات الحوار في المتصفح -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript للإجراءات الجماعية والتفاعلية والتدقيق الذكي -->
<script>
    // تحديد / إلغاء تحديد الكل
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = selectAllCheckbox.checked;
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
                highlightRowOrCard(cb, isChecked);
            });
            updateBulkButtonsState();
        });
    }

    // تمييز الصف أو البطاقة بصرياً عند التحديد
    function highlightRowOrCard(checkbox, isChecked) {
        const userId = checkbox.value;
        const row = document.getElementById('rowUser' + userId);
        const card = document.getElementById('mobileCard' + userId);

        if (row) {
            if (isChecked) row.classList.add('table-active');
            else row.classList.remove('table-active');
        }
        if (card) {
            if (isChecked) card.classList.add('card-selected');
            else card.classList.remove('card-selected');
        }
    }

    // مزامنة حالة الشيك بوكس بين الهواتف وسطح المكتب
    function syncMobileCheckbox(userId, isChecked) {
        const desktopBoxes = document.querySelectorAll(`table .user-checkbox[value="${userId}"]`);
        desktopBoxes.forEach(cb => cb.checked = isChecked);
        highlightRowOrCard({ value: userId }, isChecked);
        updateBulkButtonsState();
    }

    // عند تغيير أي شيك بوكس في الجدول
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('user-checkbox')) {
            const isChecked = e.target.checked;
            const userId = e.target.value;
            // مزامنة الهواتف إذا كان التغيير من الجدول والعكس
            const counterpartBoxes = document.querySelectorAll(`.user-checkbox[value="${userId}"]`);
            counterpartBoxes.forEach(cb => cb.checked = isChecked);
            highlightRowOrCard(e.target, isChecked);
            updateBulkButtonsState();
        }
    });

    // تحديث حالة أزرار الإجراءات الجماعية والعدادات
    function updateBulkButtonsState() {
        // جمع كل القيم الفريدة المحددة
        const checkedValues = new Set();
        document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
            if (cb.value) checkedValues.add(cb.value);
        });

        const count = checkedValues.size;
        const btnApprove = document.getElementById('btnBulkApprove');
        const btnReject = document.getElementById('btnBulkReject');
        const countSpan = document.getElementById('selectedCountApprove');
        const countDisplay = document.getElementById('selectedCountDisplay');

        if (countSpan) countSpan.textContent = count;
        if (countDisplay) countDisplay.textContent = count;

        if (count > 0) {
            if (btnApprove) btnApprove.classList.remove('disabled');
            if (btnReject) btnReject.classList.remove('disabled');
        } else {
            if (btnApprove) btnApprove.classList.add('disabled');
            if (btnReject) btnReject.classList.add('disabled');
        }

        // تحديث حالة مربع تحديد الكل
        const allUniqueValues = new Set();
        document.querySelectorAll('.user-checkbox').forEach(cb => allUniqueValues.add(cb.value));
        if (selectAllCheckbox && allUniqueValues.size > 0) {
            selectAllCheckbox.checked = (count === allUniqueValues.size);
            selectAllCheckbox.indeterminate = (count > 0 && count < allUniqueValues.size);
        }
    }

    // وظيفة: تحديد الكل بالشيك بوكس
    function selectAllCheckboxes() {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = true;
            highlightRowOrCard(cb, true);
        });
        if (selectAllCheckbox) selectAllCheckbox.checked = true;
        updateBulkButtonsState();
    }

    // وظيفة: إلغاء التحديد بالشيك بوكس
    function selectAll() { selectAllCheckboxes(); }
    function deselectAllCheckboxes() {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = false;
            highlightRowOrCard(cb, false);
        });
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkButtonsState();
    }
    function deselectAll() { deselectAllCheckboxes(); }

    // وظيفة: تحديد الشيك بوكس بحسب النتيجة الذكية (valid | warning | invalid)
    function selectByVerdict(targetVerdict) {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            const verdict = cb.getAttribute('data-verdict');
            if (verdict === targetVerdict) {
                cb.checked = true;
                highlightRowOrCard(cb, true);
            } else {
                cb.checked = false;
                highlightRowOrCard(cb, false);
            }
        });
        updateBulkButtonsState();

        const count = new Set(Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value)).size;
        if (targetVerdict === 'valid') {
            showToastMessage(`تم تحديد ${count} طلب سليم وموثوق (جاهزة للاعتماد الفوري).`, 'success');
        } else if (targetVerdict === 'warning') {
            showToastMessage(`تم تحديد ${count} طلب بحاجة لمراجعة وتدقيق.`, 'warning');
        } else if (targetVerdict === 'invalid') {
            showToastMessage(`تم تحديد ${count} طلب يحتوي على بيانات مشبوهة أو غير متطابقة.`, 'danger');
        }
    }

    // اعتماد السليمة فوراً من زر البانر
    function approveValidImmediately() {
        selectByVerdict('valid');
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('لا توجد طلبات مصنفة كسليمة للاعتماد الفوري.');
            return;
        }
        submitBulkAction('approve');
    }

    // تنفيذ فحص الذكاء الاصطناعي عبر AJAX
    function runAiAudit() {
        const btn = document.getElementById('btnRunAiAudit');
        const textSpan = document.getElementById('aiButtonText');
        const spinner = document.getElementById('aiSpinner');
        const icon = document.getElementById('aiIcon');

        if (btn) btn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (icon) icon.classList.add('d-none');
        if (textSpan) textSpan.textContent = 'جاري تدقيق وفحص صحة المدخلات بالذكاء الاصطناعي...';

        fetch("{{ route('career-guidance.ai-audit-graduates') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // تحديث العدادات في بطاقة الملخص
                const cValid = document.getElementById('countValid');
                const cWarning = document.getElementById('countWarning');
                const cInvalid = document.getElementById('countInvalid');
                if (cValid) cValid.textContent = data.counts.valid || 0;
                if (cWarning) cWarning.textContent = data.counts.warning || 0;
                if (cInvalid) cInvalid.textContent = data.counts.invalid || 0;

                // تحديث الشارات في الجدول والبطاقات
                if (data.results) {
                    Object.keys(data.results).forEach(userId => {
                        const res = data.results[userId];
                        const cell = document.getElementById('aiBadgeCell' + userId);
                        if (cell) {
                            let iconClass = res.verdict === 'valid' ? 'fa-check-circle text-success' : (res.verdict === 'warning' ? 'fa-exclamation-triangle text-warning' : 'fa-times-circle text-danger');
                            cell.innerHTML = `
                                <div class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="${res.summary}">
                                    <span class="badge bg-${res.badge_color} bg-opacity-10 text-${res.badge_color} border border-${res.badge_color} border-opacity-25 rounded-pill px-2.5 py-1.5 fw-bold small d-inline-flex align-items-center gap-1 cursor-pointer" onclick="openDetailsModal(${userId})">
                                        <i class="fas ${iconClass}"></i>
                                        <span>${res.verdict === 'valid' ? 'سليم وموثوق' : (res.verdict === 'warning' ? 'يحتاج مراجعة' : 'غير متطابق')} (${res.score}%)</span>
                                    </span>
                                </div>
                            `;
                        }

                        // تحديث data-verdict في الشيك بوكس والصفوف
                        const boxes = document.querySelectorAll(`.user-checkbox[value="${userId}"]`);
                        boxes.forEach(b => b.setAttribute('data-verdict', res.verdict));

                        const row = document.getElementById('rowUser' + userId);
                        if (row) row.setAttribute('data-verdict', res.verdict);

                        const card = document.getElementById('mobileCard' + userId);
                        if (card) card.setAttribute('data-verdict', res.verdict);
                    });
                }

                showToastMessage(`اكتمل الفحص: ${data.counts.valid} طلب سليم، ${data.counts.warning} بحاجة لمراجعة، ${data.counts.invalid} غير متطابق.`, 'success');
            } else {
                alert(data.message || 'تعذر استكمال التدقيق الذكي.');
            }
        })
        .catch(error => {
            console.error('AI Audit Error:', error);
            showToastMessage('اكتمل التدقيق بالاعتماد على الفحص المحلي للبيانات.', 'info');
        })
        .finally(() => {
            if (btn) btn.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (icon) icon.classList.remove('d-none');
            if (textSpan) textSpan.textContent = '✨ إعادة الفحص بالذكاء الاصطناعي';
        });
    }

    // ══════════════════════════════════════════════════════════════
    // نظام التأكيد والانتظار الموحد بهوية المنصة (GTS Branded Confirm & Wave Loading)
    // ══════════════════════════════════════════════════════════════
    let pendingConfirmCallback = null;
    let gtsModalInstance = null;

    function getGtsConfirmModal() {
        const modalEl = document.getElementById('gtsConfirmModal');
        if (!modalEl) return null;
        if (!gtsModalInstance && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            gtsModalInstance = new bootstrap.Modal(modalEl, {
                backdrop: true,
                keyboard: true
            });
        }
        return gtsModalInstance;
    }

    function openGtsConfirmModal({
        actionType = 'approve', // 'approve' | 'reject' | 'bulk-approve' | 'bulk-reject' | 'approve-all'
        title = 'تأكيد الإجراء',
        targetName = '',
        targetSubtext = '',
        message = '',
        confirmBtnText = 'نعم، متابعة',
        confirmBtnIcon = 'fas fa-check-circle',
        confirmBtnClass = 'btn-success',
        loadingTitle = 'جاري المعالجة...',
        loadingSubtext = 'يرجى الانتظار لحظات، يجري الربط الآمن وحفظ البيانات...',
        onConfirm = null
    }) {
        // إغلاق أي modal تفاصيل مفتوح مسبقاً لمنع تداخل شاشات الـ Backdrop
        document.querySelectorAll('.modal.show').forEach(m => {
            if (m.id !== 'gtsConfirmModal') {
                const inst = bootstrap.Modal.getInstance(m);
                if (inst) inst.hide();
            }
        });

        const topBar = document.getElementById('gtsModalTopBar');
        const iconBox = document.getElementById('gtsModalIconBox');
        const icon = document.getElementById('gtsModalIcon');
        const titleEl = document.getElementById('gtsModalTitle');
        const targetIcon = document.getElementById('gtsModalTargetIcon');
        const targetNameEl = document.getElementById('gtsModalTargetName');
        const targetSubtextEl = document.getElementById('gtsModalTargetSubtext');
        const messageEl = document.getElementById('gtsModalMessage');
        const confirmBtn = document.getElementById('gtsModalConfirmBtn');

        if (titleEl) titleEl.textContent = title;
        if (targetNameEl) targetNameEl.textContent = targetName;
        if (targetSubtextEl) targetSubtextEl.textContent = targetSubtext;
        if (messageEl) messageEl.textContent = message;

        // تطبيق الألوان والأيقونات بهوية المنظومة
        if (actionType === 'approve') {
            if (topBar) topBar.style.background = 'linear-gradient(90deg, #10b981, #059669)';
            if (iconBox) {
                iconBox.className = 'mb-3 d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm icon-box-approve';
            }
            if (icon) icon.className = 'fas fa-user-check fa-2x';
            if (targetIcon) targetIcon.className = 'fas fa-user-graduate text-success';
            if (confirmBtn) {
                confirmBtn.className = 'btn btn-success rounded-pill px-4 py-2 fw-bold text-white shadow-sm';
                confirmBtn.innerHTML = `<i class="${confirmBtnIcon} me-1"></i> ${confirmBtnText}`;
            }
        } else if (actionType === 'reject' || actionType === 'bulk-reject') {
            if (topBar) topBar.style.background = 'linear-gradient(90deg, #ef4444, #dc2626)';
            if (iconBox) {
                iconBox.className = 'mb-3 d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm icon-box-reject';
            }
            if (icon) icon.className = actionType === 'bulk-reject' ? 'fas fa-user-slash fa-2x' : 'fas fa-user-times fa-2x';
            if (targetIcon) targetIcon.className = 'fas fa-trash-alt text-danger';
            if (confirmBtn) {
                confirmBtn.className = 'btn btn-danger rounded-pill px-4 py-2 fw-bold text-white shadow-sm';
                confirmBtn.innerHTML = `<i class="${confirmBtnIcon} me-1"></i> ${confirmBtnText}`;
            }
        } else {
            // bulk-approve | approve-all
            if (topBar) topBar.style.background = 'linear-gradient(90deg, #0d3882, #10b981)';
            if (iconBox) {
                iconBox.className = 'mb-3 d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm icon-box-bulk';
            }
            if (icon) icon.className = actionType === 'approve-all' ? 'fas fa-clipboard-check fa-2x text-primary' : 'fas fa-check-double fa-2x text-primary';
            if (targetIcon) targetIcon.className = 'fas fa-users-cog text-primary';
            if (confirmBtn) {
                confirmBtn.className = 'btn btn-success rounded-pill px-4 py-2 fw-bold text-white shadow-sm';
                confirmBtn.innerHTML = `<i class="${confirmBtnIcon} me-1"></i> ${confirmBtnText}`;
            }
        }

        // حفظ دالة التنفيذ
        pendingConfirmCallback = function() {
            // إظهار شاشة الانتظار الرسمية للمنظومة بموجة الألوان
            if (typeof window.showLoadingWave === 'function') {
                window.showLoadingWave(loadingTitle, loadingSubtext);
            }
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        };

        const modal = getGtsConfirmModal();
        if (modal) {
            modal.show();
        } else {
            // كحل بديل آمن إن لم تكن المكتبة مهيأة
            if (confirm(`${title}\n${targetName}\n${message}`)) {
                pendingConfirmCallback();
            }
        }
    }

    // ربط نقرة زر التأكيد داخل النافذة
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('gtsModalConfirmBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const modal = getGtsConfirmModal();
                if (modal) {
                    modal.hide();
                }
                if (typeof pendingConfirmCallback === 'function') {
                    const callback = pendingConfirmCallback;
                    pendingConfirmCallback = null;
                    callback();
                }
            });
        }
    });

    // إرسال الإجراء الجماعي (اعتماد أو رفض المحدد بالشيك بوكس)
    function submitBulkAction(action) {
        const checkedValues = new Set();
        document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
            if (cb.value) checkedValues.add(cb.value);
        });

        const count = checkedValues.size;
        if (count === 0) {
            showToastMessage('يرجى تحديد خريج واحد على الأقل عبر مربعات الاختيار أولاً.', 'warning');
            return;
        }

        const form = document.getElementById('bulkActionForm');
        if (!form) return;

        if (action === 'approve') {
            form.action = "{{ route('career-guidance.bulk-approve-graduates') }}";
            openGtsConfirmModal({
                actionType: 'bulk-approve',
                title: 'تأكيد الاعتماد الجماعي للطلبات',
                targetName: `اعتماد عدد (${count}) طلب خريج`,
                targetSubtext: 'الطلبات المحددة عبر مربعات الاختيار',
                message: `هل أنت متأكد من رغبتك في اعتماد وتفعيل جميع حسابات الخريجين المحددة (${count} خريج) دفعة واحدة؟`,
                confirmBtnText: `اعتماد (${count}) طلبات فوراً`,
                confirmBtnIcon: 'fas fa-check-double',
                loadingTitle: 'جاري الاعتماد الجماعي...',
                loadingSubtext: 'يرجى الانتظار لحظات، يجري تفعيل الحسابات المحددة ومزامنة السجلات...',
                onConfirm: () => form.submit()
            });
        } else if (action === 'reject') {
            form.action = "{{ route('career-guidance.bulk-reject-graduates') }}";
            openGtsConfirmModal({
                actionType: 'bulk-reject',
                title: 'تأكيد الرفض والحذف الجماعي',
                targetName: `رفض وحذف عدد (${count}) طلب خريج`,
                targetSubtext: 'الطلبات المحددة عبر مربعات الاختيار',
                message: `تحذير: سيتم حذف جميع طلبات الخريجين المحددة (${count} طلب) نهائياً من قاعدة البيانات ولا يمكن استرجاعها.`,
                confirmBtnText: `رفض وحذف (${count}) طلبات`,
                confirmBtnIcon: 'fas fa-trash-alt',
                loadingTitle: 'جاري الحذف الجماعي...',
                loadingSubtext: 'يرجى الانتظار لحظات، يجري حذف الطلبات وتنظيف السجلات...',
                onConfirm: () => form.submit()
            });
        }
    }

    // اعتماد الكل بنقرة واحدة
    function confirmApproveAll(count) {
        const form = document.getElementById('approveAllForm');
        if (!form) return;

        openGtsConfirmModal({
            actionType: 'approve-all',
            title: 'اعتماد جميع الطلبات المعلقة',
            targetName: `جميع طلبات الخريجين المعلقة (${count})`,
            targetSubtext: 'اعتماد شامل لجميع الطلبات دون استثناء',
            message: `سيتم اعتماد وتفعيل كافة طلبات تسجيل الخريجين المعلقة (${count}) دفعة واحدة وتمكينهم من الدخول للمنظومة.`,
            confirmBtnText: 'اعتماد جميع الطلبات فوراً',
            confirmBtnIcon: 'fas fa-clipboard-check',
            loadingTitle: 'جاري اعتماد جميع الحسابات...',
            loadingSubtext: 'يرجى الانتظار لحظات، يجري مزامنة وتفعيل كافة الحسابات التدريبية...',
            onConfirm: () => form.submit()
        });
    }

    // تنفيذ الموافقة الفردية
    function approveDirectly(userId, userName) {
        userName = userName || 'هذا الخريج';
        const form = document.getElementById('singleApproveForm');
        if (!form) return;
        form.action = `/career-guidance/pending-approvals/${userId}/approve`;

        openGtsConfirmModal({
            actionType: 'approve',
            title: 'تأكيد اعتماد حساب الخريج',
            targetName: userName,
            targetSubtext: 'طلب تسجيل خريج جديد قيد المراجعة',
            message: 'عند الاعتماد، سيتم تفعيل حساب الخريج فوراً وتمكينه من تسجيل الدخول والتقديم على البرامج التدريبية والمعارض الوظيفية.',
            confirmBtnText: 'موافقة وتفعيل الحساب',
            confirmBtnIcon: 'fas fa-check-circle',
            loadingTitle: 'جاري تفعيل الحساب...',
            loadingSubtext: 'يرجى الانتظار لحظات، يجري تفعيل السجل وإرسال إشعار الترحيب...',
            onConfirm: () => form.submit()
        });
    }

    // تنفيذ الرفض الفردي
    function rejectDirectly(userId, userName) {
        userName = userName || 'هذا الخريج';
        const form = document.getElementById('singleRejectForm');
        if (!form) return;
        form.action = `/career-guidance/pending-approvals/${userId}/reject`;

        openGtsConfirmModal({
            actionType: 'reject',
            title: 'تأكيد رفض طلب التسجيل',
            targetName: userName,
            targetSubtext: 'طلب تسجيل جديد سيتم حذفه من المنظومة',
            message: 'تحذير: سيتم رفض هذا الطلب وحذف بياناته نهائياً من قاعدة البيانات ولا يمكن التراجع عن هذا الإجراء.',
            confirmBtnText: 'نعم، رفض وحذف الطلب',
            confirmBtnIcon: 'fas fa-trash-alt',
            loadingTitle: 'جاري حذف الطلب...',
            loadingSubtext: 'يرجى الانتظار لحظات، يجري حذف الطلب وتنظيف البيانات...',
            onConfirm: () => form.submit()
        });
    }

    // فتح نافذة التفاصيل
    function openDetailsModal(userId) {
        const modalEl = document.getElementById('userModal' + userId);
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // إظهار تنبيه عائم سريع
    function showToastMessage(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} shadow-lg rounded-pill position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-2.5 z-3 d-flex align-items-center gap-2 border-0`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `<i class="fas fa-info-circle"></i> <span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }, 3500);
    }
</script>
@endsection