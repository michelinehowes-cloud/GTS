@extends('layouts.app')

@section('title', 'سجل الرقابة والعمليات الأمنية المتقدم')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- الشريط الأزرق الموحد المعتمد -->
    <x-page-hero
        title="سجل الرقابة والعمليات الأمنية (Tamper-Proof Audit Trail)"
        subtitle="نظام توثيق رقابي أمني مشدد غير قابل للتعديل أو الحذف، يوثق كافة حركات المصادقة، التعديلات الهيكلية، التنبيهات الأمنية والنسخ الاحتياطي"
        icon="fas fa-shield-alt"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة المدير', 'url' => route('dashboard')],
            ['label' => 'سجل الرقابة والعمليات']
        ]"
        badge="محصن ضد التلاعب (Immutable Log)"
        badgeIcon="fas fa-lock"
    >
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                <i class="fas fa-check-circle me-1"></i> حماية السجلات 100% نشطة
            </span>
            <a href="{{ route('admin.backup.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-database text-primary"></i>
                <span>النسخ الاحتياطي</span>
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-light bg-white text-secondary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-users-cog"></i>
                <span>المستخدمين</span>
            </a>
        </div>
    </x-page-hero>

    <!-- بطاقات المؤشرات الأمنية السريعة -->
    @if(isset($stats))
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي الحركات الموثقة</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total'] ?? 0) }}</h3>
                        <small class="text-success"><i class="fas fa-lock me-1"></i>سجل رقابي محمي</small>
                    </div>
                    <div class="rounded-4 bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">تنبيهات الأمان والحظر</span>
                        <h3 class="fw-bold mb-0 text-danger">{{ number_format($stats['security_alerts'] ?? 0) }}</h3>
                        <small class="text-muted">محاولات وصول/تخمين محظورة</small>
                    </div>
                    <div class="rounded-4 bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-shield-virus fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">عمليات الدخول والمصادقة</span>
                        <h3 class="fw-bold mb-0 text-primary">{{ number_format($stats['auth_events'] ?? 0) }}</h3>
                        <small class="text-muted">تسجيل دخول وخروج وتغيير كلمة السر</small>
                    </div>
                    <div class="rounded-4 bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-user-shield fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">تعديلات البيانات (Mutations)</span>
                        <h3 class="fw-bold mb-0 text-success">{{ number_format($stats['mutations'] ?? 0) }}</h3>
                        <small class="text-muted">إضافة، تعديل، وحذف النماذج</small>
                    </div>
                    <div class="rounded-4 bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-database fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- بطاقة البحث والتصفية السريعة -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <!-- أزرار التصفية السريعة -->
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                <span class="text-muted small fw-bold me-2"><i class="fas fa-filter text-primary me-1"></i> تصنيف سريع:</span>
                <a href="{{ route('admin.reports.audit-logs') }}" 
                   class="btn btn-sm rounded-pill {{ !request('action') ? 'btn-primary' : 'btn-light border text-dark' }}">
                    الكل ({{ $stats['total'] ?? 0 }})
                </a>
                <a href="{{ route('admin.reports.audit-logs', ['action' => 'security']) }}" 
                   class="btn btn-sm rounded-pill {{ request('action') === 'security' ? 'btn-danger' : 'btn-light border text-danger' }}">
                    <i class="fas fa-shield-virus me-1"></i> تنبيهات الأمان ({{ $stats['security_alerts'] ?? 0 }})
                </a>
                <a href="{{ route('admin.reports.audit-logs', ['action' => 'auth']) }}" 
                   class="btn btn-sm rounded-pill {{ request('action') === 'auth' ? 'btn-info text-white' : 'btn-light border text-primary' }}">
                    <i class="fas fa-key me-1"></i> المصادقة والدخول ({{ $stats['auth_events'] ?? 0 }})
                </a>
                <a href="{{ route('admin.reports.audit-logs', ['action' => 'update']) }}" 
                   class="btn btn-sm rounded-pill {{ request('action') === 'update' ? 'btn-warning text-dark' : 'btn-light border text-dark' }}">
                    <i class="fas fa-edit me-1"></i> التعديلات
                </a>
                <a href="{{ route('admin.reports.audit-logs', ['action' => 'delete']) }}" 
                   class="btn btn-sm rounded-pill {{ request('action') === 'delete' ? 'btn-dark' : 'btn-light border text-dark' }}">
                    <i class="fas fa-trash-alt me-1"></i> الحذف
                </a>
                <a href="{{ route('admin.reports.audit-logs', ['action' => 'backup']) }}" 
                   class="btn btn-sm rounded-pill {{ request('action') === 'backup' ? 'btn-purple text-white' : 'btn-light border text-dark' }}" style="background-color: {{ request('action') === 'backup' ? '#6f42c1' : 'transparent' }};">
                    <i class="fas fa-hdd me-1"></i> النسخ الاحتياطي
                </a>
            </div>

            <!-- نموذج البحث المتعدد -->
            <form action="{{ route('admin.reports.audit-logs') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-12 col-12">
                    <label class="form-label small fw-bold text-muted">البحث الشامل</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 rounded-end-3" 
                               value="{{ request('search') }}" placeholder="ابحث باسم المستخدم، البريد، الإجراء، الكيان أو IP...">
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <label class="form-label small fw-bold text-muted">فئة الإجراء</label>
                    <select name="action" class="form-select rounded-3">
                        <option value="">جميع الإجراءات</option>
                        <option value="security" {{ request('action') == 'security' ? 'selected' : '' }}>🛡️ تنبيهات الأمان والحظر (Security)</option>
                        <option value="auth" {{ request('action') == 'auth' ? 'selected' : '' }}>🔑 المصادقة وتسجيل الدخول (Auth)</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>➕ إضافة وإنشاء (Create)</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>✏️ تعديل وتحديث (Update)</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>🗑️ حذف وإلغاء (Delete)</option>
                        <option value="backup" {{ request('action') == 'backup' ? 'selected' : '' }}>💾 النسخ الاحتياطي (Backup)</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <label class="form-label small fw-bold text-muted">الكيان / الوحدة المتأثرة</label>
                    <select name="entity" class="form-select rounded-3">
                        <option value="">جميع الكيانات</option>
                        <option value="User" {{ request('entity') == 'User' ? 'selected' : '' }}>المستخدمين (User)</option>
                        <option value="Training" {{ request('entity') == 'Training' ? 'selected' : '' }}>التدريبات (Training)</option>
                        <option value="Company" {{ request('entity') == 'Company' ? 'selected' : '' }}>الشركات والشركاء (Company)</option>
                        <option value="JobFair" {{ request('entity') == 'JobFair' ? 'selected' : '' }}>معرض التوظيف (JobFair)</option>
                        <option value="JobOpportunity" {{ request('entity') == 'JobOpportunity' ? 'selected' : '' }}>فرص العمل (JobOpportunity)</option>
                        <option value="TrainingApplication" {{ request('entity') == 'TrainingApplication' ? 'selected' : '' }}>طلبات التدريب (Application)</option>
                        <option value="DatabaseBackup" {{ request('entity') == 'DatabaseBackup' ? 'selected' : '' }}>النسخ الاحتياطي (Backup)</option>
                        <option value="Security" {{ request('entity') == 'Security' ? 'selected' : '' }}>أمان المنظومة (Security)</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-2 flex-grow-1 shadow-sm">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                    @if(request()->hasAny(['search', 'action', 'entity']))
                        <a href="{{ route('admin.reports.audit-logs') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 shadow-sm" title="إعادة تعيين التصفية">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- جدول سجل العمليات -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-check text-primary fs-5"></i>
                <h6 class="fw-bold mb-0 text-dark">سجلات الأنشطة المحفوظة (Append-Only Immutable Records)</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill">
                    <i class="fas fa-lock me-1 text-success"></i> سجل غير قابل للتعديل
                </span>
                <span class="badge bg-light text-primary border px-3 py-1.5 rounded-pill">
                    @if(method_exists($auditLogs, 'total'))
                        {{ $auditLogs->total() }} حركة مسجلة
                    @else
                        {{ count($auditLogs) }} حركة
                    @endif
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="py-3 px-4 border-0">#المعرف</th>
                            <th class="py-3 border-0">المنفذ (User)</th>
                            <th class="py-3 border-0">نوع الإجراء (Action)</th>
                            <th class="py-3 border-0">تفاصيل العملية والكيان</th>
                            <th class="py-3 border-0 text-center">التغييرات (Diff)</th>
                            <th class="py-3 border-0">عنوان IP والجهاز</th>
                            <th class="py-3 px-4 border-0 text-end">التوقيت والتاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                        <tr>
                            <td class="px-4 text-muted fw-bold small">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="fas fa-lock text-muted opacity-50" style="font-size: 0.65rem;" title="سجل محصن وغير قابل للتعديل"></i>
                                    #{{ $log->id }}
                                </span>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark small d-block">{{ $log->user->name }}</strong>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill">
                                        <i class="fas fa-robot me-1"></i> النظام التلقائي
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $actionLower = strtolower($log->action ?? '');
                                    $badgeClass = 'bg-secondary';
                                    $badgeIcon = 'fa-info-circle';
                                    
                                    if(str_starts_with($actionLower, 'security_') || str_contains($actionLower, 'blocked') || str_contains($actionLower, 'denied')) {
                                        $badgeClass = 'bg-danger';
                                        $badgeIcon = 'fa-shield-virus';
                                    } elseif(str_starts_with($actionLower, 'auth_') || str_contains($actionLower, 'login') || str_contains($actionLower, 'logout')) {
                                        $badgeClass = 'bg-info text-white';
                                        $badgeIcon = 'fa-key';
                                    } elseif(str_starts_with($actionLower, 'backup_')) {
                                        $badgeClass = 'bg-dark';
                                        $badgeIcon = 'fa-database';
                                    } elseif(str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy')) {
                                        $badgeClass = 'bg-danger text-white';
                                        $badgeIcon = 'fa-trash-alt';
                                    } elseif(str_contains($actionLower, 'update') || str_contains($actionLower, 'edit')) {
                                        $badgeClass = 'bg-warning text-dark';
                                        $badgeIcon = 'fa-edit';
                                    } elseif(str_contains($actionLower, 'create') || str_contains($actionLower, 'store')) {
                                        $badgeClass = 'bg-success';
                                        $badgeIcon = 'fa-plus-circle';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2.5 py-1.5 rounded-pill shadow-none d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                    <i class="fas {{ $badgeIcon }}"></i>
                                    <span>{{ $log->action }}</span>
                                </span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $log->description }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <span>الكيان: <strong>{{ $log->entity }}</strong></span>
                                        @if($log->entity_id)
                                            <span class="badge bg-light text-muted border ms-1">#{{ $log->entity_id }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $hasOld = !empty($log->old_values);
                                    $hasNew = !empty($log->new_values);
                                @endphp
                                @if($hasOld || $hasNew)
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-view-diff"
                                            style="font-size: 0.75rem;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#auditDiffModal"
                                            data-log-id="{{ $log->id }}"
                                            data-action="{{ $log->action }}"
                                            data-entity="{{ $log->entity }}"
                                            data-entity-id="{{ $log->entity_id }}"
                                            data-user="{{ $log->user ? $log->user->name : 'النظام' }}"
                                            data-ip="{{ $log->ip_address }}"
                                            data-time="{{ $log->timestamp ? $log->timestamp->format('Y-m-d H:i:s') : '—' }}"
                                            data-old='@json($log->old_values)'
                                            data-new='@json($log->new_values)'>
                                        <i class="fas fa-search-plus me-1"></i> فحص التغييرات
                                    </button>
                                @else
                                    <span class="text-muted small" style="font-size: 0.75rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <code class="text-primary small">{{ $log->ip_address ?? '127.0.0.1' }}</code>
                                    @if($log->user_agent)
                                        <div class="text-muted small text-truncate" style="max-width: 180px; font-size: 0.7rem;" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 text-end">
                                <span class="text-dark small fw-bold d-block">
                                    {{ $log->timestamp ? $log->timestamp->format('Y-m-d') : ($log->created_at ? $log->created_at->format('Y-m-d') : '—') }}
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $log->timestamp ? $log->timestamp->format('H:i:s A') : ($log->created_at ? $log->created_at->format('H:i:s A') : '') }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="mb-3">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-clipboard-check fa-3x text-muted opacity-50"></i>
                                    </div>
                                </div>
                                <h5 class="text-dark fw-bold mb-1">لا توجد حركات أمنية مسجلة بهذه المعايير</h5>
                                <p class="text-muted small mb-0">جميع الإجراءات والتعديلات والعمليات المستقبلية ستوثق آلياً في هذا السجل المحمي.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- الترقيم الآمن للصفحات -->
            @if(method_exists($auditLogs, 'hasPages') && $auditLogs->hasPages())
            <div class="p-3 border-top bg-light d-flex justify-content-center">
                {{ $auditLogs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- نافذة فحص الفروقات والتغييرات الأمنية (Audit Diff Modal) -->
<div class="modal fade" id="auditDiffModal" tabindex="-1" aria-labelledby="auditDiffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-shield-alt fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="auditDiffModalLabel">فحص تدقيق السجل الأمني</h6>
                        <small class="text-muted" id="modalSubTitle">معاينة تفاصيل وبيانات الحركة المسجلة</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- شريط بيانات الحركة -->
                <div class="bg-light rounded-3 p-3 mb-3 border">
                    <div class="row g-2 small">
                        <div class="col-sm-6 col-12">
                            <span class="text-muted">المعرف:</span> <strong id="modalLogId" class="text-dark">#</strong>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted">الإجراء:</span> <span id="modalAction" class="badge bg-primary"></span>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted">المنفذ:</span> <strong id="modalUser" class="text-dark">—</strong>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted">عنوان IP:</span> <code id="modalIp" class="text-primary">—</code>
                        </div>
                        <div class="col-12">
                            <span class="text-muted">التوقيت:</span> <strong id="modalTime" class="text-dark">—</strong>
                        </div>
                    </div>
                </div>

                <!-- جدول الفروقات والتغييرات -->
                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-exchange-alt text-primary"></i>
                    <span>الفروقات بين القيم السابقة والجديدة (Diff)</span>
                </h6>

                <div class="table-responsive rounded-3 border mb-3">
                    <table class="table table-bordered table-sm align-middle mb-0" id="modalDiffTable">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th style="width: 25%;">الحقل (Field)</th>
                                <th style="width: 37.5%;" class="text-danger"><i class="fas fa-minus-circle me-1"></i> القيمة السابقة (Old)</th>
                                <th style="width: 37.5%;" class="text-success"><i class="fas fa-plus-circle me-1"></i> القيمة الجديدة (New)</th>
                            </tr>
                        </thead>
                        <tbody id="modalDiffTableBody" class="small">
                            <!-- سيتم تعبئتها ديناميكياً بواسطة JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- شارة التحصين الأمني -->
                <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 rounded-3 mb-0" role="alert" style="font-size: 0.8rem;">
                    <i class="fas fa-check-double fs-6 text-success"></i>
                    <div>
                        <strong>سجل محمي ومحصن (Immutable & Tamper-Proof):</strong> هذه البيانات مشفرة ومحمية بقواعد برمجية تمنع أي محاولة تعديل أو حذف لضمان الشفافية والموثوقية القانونية.
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 px-4">
                <button type="button" class="btn btn-secondary rounded-3 px-4 btn-sm" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const diffButtons = document.querySelectorAll('.btn-view-diff');
    const modalLogId = document.getElementById('modalLogId');
    const modalAction = document.getElementById('modalAction');
    const modalUser = document.getElementById('modalUser');
    const modalIp = document.getElementById('modalIp');
    const modalTime = document.getElementById('modalTime');
    const tableBody = document.getElementById('modalDiffTableBody');

    diffButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const logId = this.getAttribute('data-log-id');
            const action = this.getAttribute('data-action');
            const entity = this.getAttribute('data-entity');
            const entityId = this.getAttribute('data-entity-id');
            const user = this.getAttribute('data-user');
            const ip = this.getAttribute('data-ip');
            const time = this.getAttribute('data-time');

            let oldValues = {};
            let newValues = {};

            try {
                oldValues = JSON.parse(this.getAttribute('data-old') || '{}') || {};
            } catch (e) {
                oldValues = {};
            }

            try {
                newValues = JSON.parse(this.getAttribute('data-new') || '{}') || {};
            } catch (e) {
                newValues = {};
            }

            modalLogId.textContent = '#' + logId;
            modalAction.textContent = action;
            modalUser.textContent = user;
            modalIp.textContent = ip;
            modalTime.textContent = time;

            // جمع جميع المفاتيح من القديم والجديد
            const allKeys = Array.from(new Set([...Object.keys(oldValues), ...Object.keys(newValues)]));

            tableBody.innerHTML = '';

            if (allKeys.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-3">لا توجد تفاصيل حقول إضافية مسجلة لهذه العملية.</td></tr>`;
            } else {
                allKeys.forEach(key => {
                    const oldVal = oldValues[key] !== undefined ? formatValue(oldValues[key]) : '<span class="text-muted">—</span>';
                    const newVal = newValues[key] !== undefined ? formatValue(newValues[key]) : '<span class="text-muted">—</span>';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="fw-bold text-dark font-monospace">${escapeHtml(key)}</td>
                        <td class="bg-danger bg-opacity-10 text-danger font-monospace">${oldVal}</td>
                        <td class="bg-success bg-opacity-10 text-success font-monospace">${newVal}</td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        });
    });

    function formatValue(val) {
        if (val === null) return '<em class="text-muted">null</em>';
        if (typeof val === 'boolean') return val ? '<code>true</code>' : '<code>false</code>';
        if (typeof val === 'object') return `<pre class="mb-0 p-1 bg-white rounded border" style="font-size:0.7rem; max-height:120px;">${escapeHtml(JSON.stringify(val, null, 2))}</pre>`;
        return escapeHtml(String(val));
    }

    function escapeHtml(string) {
        const div = document.createElement('div');
        div.innerText = string;
        return div.innerHTML;
    }
});
</script>
@endsection
