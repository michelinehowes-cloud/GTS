@extends('layouts.app')

@section('title', 'سجل الرقابة والعمليات الأمنية')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- الشريط الأزرق الموحد المعتمد -->
    <x-page-hero
        title="سجل الرقابة والعمليات الأمنية (Audit Logs)"
        subtitle="متابعة وتدقيق أنشطة النظام وعمليات التعديل والإضافة والحذف مع توثيق عناوين IP والمستخدمين بدقة"
        icon="fas fa-history"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة المدير', 'url' => route('dashboard')],
            ['label' => 'إدارة الموظفين', 'url' => route('admin.users')],
            ['label' => 'سجل العمليات']
        ]"
        badge="نظام التدقيق الرقمي 2026"
        badgeIcon="fas fa-shield-alt"
    >
        <a href="{{ route('admin.users') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-users-cog fs-6"></i>
            <span>إدارة الموظفين</span>
        </a>
    </x-page-hero>

    <!-- بطاقة البحث والتصفية -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.reports.audit-logs') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5 col-12">
                    <label class="form-label small fw-bold text-muted">البحث السريع</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 rounded-end-3" 
                               value="{{ request('search') }}" placeholder="ابحث باسم المستخدم، البريد، الإجراء، أو IP...">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <label class="form-label small fw-bold text-muted">نوع الإجراء</label>
                    <select name="action" class="form-select rounded-3">
                        <option value="">جميع الإجراءات</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>إنشاء / إضافة (Create)</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>تعديل وتحديث (Update)</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>حذف وإلغاء (Delete)</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>تسجيل دخول (Login)</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 col-12">
                    <label class="form-label small fw-bold text-muted">الكيان / الوحدة</label>
                    <select name="entity" class="form-select rounded-3">
                        <option value="">جميع الكيانات</option>
                        <option value="User" {{ request('entity') == 'User' ? 'selected' : '' }}>المستخدمين (User)</option>
                        <option value="Company" {{ request('entity') == 'Company' ? 'selected' : '' }}>الشركات (Company)</option>
                        <option value="Training" {{ request('entity') == 'Training' ? 'selected' : '' }}>التدريبات (Training)</option>
                        <option value="JobFair" {{ request('entity') == 'JobFair' ? 'selected' : '' }}>معرض التوظيف (JobFair)</option>
                    </select>
                </div>
                <div class="col-md-2 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-2 flex-grow-1 shadow-sm">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                    @if(request()->hasAny(['search', 'action', 'entity']))
                        <a href="{{ route('admin.reports.audit-logs') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 shadow-sm" title="إعادة تعيين">
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
                <i class="fas fa-shield-virus text-primary fs-5"></i>
                <h6 class="fw-bold mb-0 text-dark">سجلات الأنشطة المحفوظة</h6>
            </div>
            <span class="badge bg-light text-primary border px-3 py-1.5 rounded-pill">
                @if(method_exists($auditLogs, 'total'))
                    {{ $auditLogs->total() }} سجل مسجل
                @else
                    {{ count($auditLogs) }} سجل
                @endif
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="py-3 px-4 border-0">المعرف</th>
                            <th class="py-3 border-0">المستخدم المنفذ</th>
                            <th class="py-3 border-0">الإجراء (Action)</th>
                            <th class="py-3 border-0">الكيان / السجل</th>
                            <th class="py-3 border-0">عنوان IP والجهاز</th>
                            <th class="py-3 px-4 border-0 text-end">التوقيت والتاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                        <tr>
                            <td class="px-4 text-muted fw-bold small">#{{ $log->id }}</td>
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
                                    if(str_contains($actionLower, 'create') || str_contains($actionLower, 'store')) $badgeClass = 'bg-success';
                                    elseif(str_contains($actionLower, 'update') || str_contains($actionLower, 'edit')) $badgeClass = 'bg-info text-white';
                                    elseif(str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy')) $badgeClass = 'bg-danger';
                                    elseif(str_contains($actionLower, 'login')) $badgeClass = 'bg-warning text-dark';
                                    elseif(str_contains($actionLower, 'status')) $badgeClass = 'bg-purple';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2.5 py-1.5 rounded-pill shadow-none" style="font-size: 0.78rem;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark small">{{ $log->entity }}</span>
                                @if($log->entity_id)
                                    <span class="badge bg-light text-muted border ms-1 small">#{{ $log->entity_id }}</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <code class="text-primary small">{{ $log->ip_address ?? '127.0.0.1' }}</code>
                                    @if($log->user_agent)
                                        <div class="text-muted small text-truncate" style="max-width: 220px; font-size: 0.7rem;" title="{{ $log->user_agent }}">
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
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-3">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-clipboard-check fa-3x text-muted opacity-50"></i>
                                    </div>
                                </div>
                                <h5 class="text-dark fw-bold mb-1">لا توجد حركات أمنية مسجلة</h5>
                                <p class="text-muted small mb-0">جميع الإجراءات والتعديلات المستقبلية ستوثق آلياً في هذا السجل.</p>
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
@endsection
