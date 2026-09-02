@extends('layouts.app')

@section('title', 'طلبات تسجيل الخريجين المعلقة')

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
                <div class="rounded-circle bg-light-warning text-warning fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.4rem;">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <h2 class="text-primary fw-bold mb-1 fs-4">طلبات تسجيل الخريجين المعلقة</h2>
                    <p class="text-muted small mb-0">مراجعة وتدقيق واعتماد طلبات إنشاء حسابات الخريجين الجدد في المنظومة</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
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
                <!-- أزرار الإجراءات الجماعية -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- اعتماد المحدد -->
                    <button type="button" id="btnBulkApprove" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm disabled" onclick="submitBulkAction('approve')">
                        <i class="fas fa-check-double me-1"></i> اعتماد المحدد (<span id="selectedCountApprove">0</span>)
                    </button>
                    <!-- اعتماد الكل بنقرة واحدة -->
                    <form action="{{ route('career-guidance.bulk-approve-graduates') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('هل أنت متأكد من رغبتك في اعتماد جميع طلبات الخريجين المعلقة ({{ $pendingUsers->total() ?? $pendingUsers->count() }}) دفعة واحدة؟')">
                        @csrf
                        <input type="hidden" name="approve_all" value="1">
                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm">
                            <i class="fas fa-clipboard-check me-1"></i> اعتماد جميع الطلبات (الكل)
                        </button>
                    </form>
                    <!-- رفض المحدد -->
                    <button type="button" id="btnBulkReject" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm disabled" onclick="submitBulkAction('reject')">
                        <i class="fas fa-trash-alt me-1"></i> رفض المحدد
                    </button>
                </div>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($pendingUsers->count() > 0)
                <!-- Form للإجراءات الجماعية -->
                <form id="bulkActionForm" method="POST" action="">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-3 text-center" style="width: 40px;">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input shadow-none" title="تحديد / إلغاء تحديد الكل">
                                    </th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 px-3">الخريج</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">بيانات الاتصال</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">المؤهل والتخصص</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">تاريخ الطلب</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center" style="width: 160px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                    <tr id="rowUser{{ $user->id }}">
                                        <td class="px-3 text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox shadow-none" onchange="updateBulkButtonsState()">
                                        </td>
                                        <td class="px-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm" style="width: 42px; height: 42px; font-size: 1.15rem;">
                                                    {{ mb_substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ $user->name }}</div>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-id-card me-1 text-primary"></i>رقم القيد: {{ $user->national_id ?? 'غير متوفر' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark mb-1">
                                                <i class="fas fa-envelope me-1 text-muted"></i>{{ $user->email }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-phone me-1 text-muted"></i>{{ $user->phone ?? 'لا يوجد هاتف' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-primary mb-1">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ $user->specialization ?? $user->major ?? 'غير محدد' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $user->university ?? 'جامعة طرابلس' }} @if($user->faculty) - {{ $user->faculty }} @endif @if($user->graduation_year) <span class="badge bg-light text-dark border ms-1">{{ $user->graduation_year }}</span> @endif
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <div>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <!-- زر التفاصيل الكاملة (عين) -->
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}" title="عرض التفاصيل الكاملة">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <!-- زر الموافقة والتفعيل -->
                                                <button type="button" class="btn btn-sm btn-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" onclick="approveDirectly({{ $user->id }}, '{{ addslashes($user->name) }}')" title="موافقة واعتماد فوري">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <!-- زر الرفض والحذف -->
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" onclick="rejectDirectly({{ $user->id }}, '{{ addslashes($user->name) }}')" title="رفض الطلب">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                <!-- 1. المعلومات الشخصية والاتصال -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center">
                        <i class="fas fa-id-card me-2"></i>المعلومات الشخصية وبيانات الاتصال
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
                                <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                                <strong class="text-dark text-break">{{ $user->email }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <small class="text-muted d-block mb-1">رقم الهاتف</small>
                                <strong class="text-dark">{{ $user->phone ?? 'غير متوفر' }}</strong>
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
                                <strong class="text-dark">{{ $user->university ?? 'غير محدد' }}</strong>
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
                    <button type="button" class="btn btn-outline-danger px-3 rounded-pill" onclick="rejectDirectly({{ $user->id }}, '{{ addslashes($user->name) }}')">
                        <i class="fas fa-times me-1"></i> رفض الطلب
                    </button>
                    <button type="button" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm" onclick="approveDirectly({{ $user->id }}, '{{ addslashes($user->name) }}')">
                        <i class="fas fa-check-circle me-1"></i> موافقة واعتماد الحساب
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

<!-- JavaScript للإجراءات الجماعية والتفاعلية -->
<script>
    // تحديد / إلغاء تحديد الكل
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateBulkButtonsState();
        });
    }

    // تحديث حالة أزرار الإجراءات الجماعية
    function updateBulkButtonsState() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        const btnApprove = document.getElementById('btnBulkApprove');
        const btnReject = document.getElementById('btnBulkReject');
        const countSpan = document.getElementById('selectedCountApprove');

        if (countSpan) countSpan.textContent = count;

        if (count > 0) {
            if (btnApprove) btnApprove.classList.remove('disabled');
            if (btnReject) btnReject.classList.remove('disabled');
        } else {
            if (btnApprove) btnApprove.classList.add('disabled');
            if (btnReject) btnReject.classList.add('disabled');
        }

        // تحديث حالة مربع تحديد الكل
        const allBoxes = document.querySelectorAll('.user-checkbox');
        if (selectAllCheckbox && allBoxes.length > 0) {
            selectAllCheckbox.checked = (checkedBoxes.length === allBoxes.length);
            selectAllCheckbox.indeterminate = (checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length);
        }
    }

    // إرسال الإجراء الجماعي
    function submitBulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;

        if (count === 0) {
            alert('يرجى تحديد خريج واحد على الأقل.');
            return;
        }

        const form = document.getElementById('bulkActionForm');
        if (action === 'approve') {
            if (confirm(`هل أنت متأكد من رغبتك في الموافقة على ${count} خريج وتفعيل حساباتهم؟`)) {
                form.action = "{{ route('career-guidance.bulk-approve-graduates') }}";
                form.submit();
            }
        } else if (action === 'reject') {
            if (confirm(`هل أنت متأكد من رغبتك في رفض ${count} طلب وحذفها نهائياً؟`)) {
                form.action = "{{ route('career-guidance.bulk-reject-graduates') }}";
                form.submit();
            }
        }
    }

    // تنفيذ الموافقة الفردية
    function approveDirectly(userId, userName) {
        if (confirm(`هل أنت متأكد من رغبتك في الموافقة على حساب ${userName} وتفعيله؟`)) {
            const form = document.getElementById('singleApproveForm');
            form.action = `/career-guidance/pending-approvals/${userId}/approve`;
            form.submit();
        }
    }

    // تنفيذ الرفض الفردي
    function rejectDirectly(userId, userName) {
        if (confirm(`هل أنت متأكد من رغبتك في رفض طلب ${userName} وحذفه؟`)) {
            const form = document.getElementById('singleRejectForm');
            form.action = `/career-guidance/pending-approvals/${userId}/reject`;
            form.submit();
        }
    }
</script>
@endsection