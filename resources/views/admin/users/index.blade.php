@extends('layouts.app')

@section('title', 'إدارة الموظفين والصلاحيات')

@section('page-title', 'إدارة الموظفين والصلاحيات')

@push('styles')
<style>
    .users-table {
        table-layout: auto;
        width: 100%;
    }
    .users-table th, 
    .users-table td {
        padding: 0.65rem 0.6rem !important;
        vertical-align: middle;
        font-size: 0.84rem;
    }
    .action-circle-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .action-circle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="إدارة الموظفين والصلاحيات (RBAC)"
        subtitle="إضافة وإدارة حسابات موظفي النظام وتخصيص الصلاحيات الدقيقة مع أعلى معايير الأمان والرقابة"
        icon="fas fa-user-shield"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة الموظفين والصلاحيات']
        ]"
        badge="إجمالي الموظفين: {{ $statistics['total_staff'] }}"
        badgeIcon="fas fa-users-cog"
        secondaryBadge="نشطون: {{ $statistics['active_staff'] }}"
        secondaryBadgeIcon="fas fa-user-check"
    >
        <a href="{{ route('admin.reports.audit-logs') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-history fs-6"></i>
            <span>سجل الرقابة الأمنية</span>
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-user-plus fs-6"></i>
            <span>إضافة موظف جديد</span>
        </a>
    </x-page-hero>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">إجمالي الموظفين</div>
                        <h3 class="fw-bold text-dark mb-0">{{ $statistics['total_staff'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-users-cog fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">موظفون نشطون</div>
                        <h3 class="fw-bold text-success mb-0">{{ $statistics['active_staff'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">صلاحيات مخصصة (Staff)</div>
                        <h3 class="fw-bold text-info mb-0">{{ $statistics['custom_staff'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-sliders-h fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold mb-1">مديرو النظام (Admins)</div>
                        <h3 class="fw-bold text-warning mb-0">{{ $statistics['admins'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-crown fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.users') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">البحث المباشر</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" 
                               placeholder="بحث بالاسم، البريد الإلكتروني، أو الهاتف..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">الدور الوظيفي</label>
                    <select name="role" class="form-select bg-light border-0">
                        <option value="">جميع الأدوار</option>
                        @foreach($availableRoles as $roleKey => $roleLabel)
                            @if(!in_array($roleKey, ['graduate', 'company']))
                                <option value="{{ $roleKey }}" {{ request('role') == $roleKey ? 'selected' : '' }}>{{ $roleLabel }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">الحالة</label>
                    <select name="status" class="form-select bg-light border-0">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>حسابات نشطة فقط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>حسابات مجمدة فقط</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                    @if(request()->anyFilled(['search', 'role', 'status']))
                        <a href="{{ route('admin.users') }}" class="btn btn-light border rounded-3" title="إعادة تعيين">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fas fa-id-badge text-primary me-2"></i> قائمة موظفي النظام المسجلين
            </h5>
            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill">
                العدد المعروض: {{ $users->total() }} موظف
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 users-table">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="py-2.5 px-3 border-0" style="min-width: 160px;">بيانات الموظف</th>
                            <th class="py-2.5 px-2 border-0 text-nowrap">الدور الوظيفي</th>
                            <th class="py-2.5 px-2 border-0 text-nowrap">الصلاحيات</th>
                            <th class="py-2.5 px-2 border-0 text-nowrap text-center">الحالة</th>
                            <th class="py-2.5 px-2 border-0 text-nowrap">تاريخ التسجيل</th>
                            <th class="py-2.5 px-3 border-0 text-end text-nowrap" style="min-width: 120px;">إدارة الحساب</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($users as $user)
                        <tr>
                            <!-- بيانات الموظف -->
                            <td class="px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm text-white fw-bold flex-shrink-0" 
                                         style="width: 36px; height: 36px; font-size: 0.82rem; background: {{ $user->isAdmin() ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #2563eb, #1d4ed8)' }};">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden" style="max-width: 170px;">
                                        <div class="fw-bold text-dark text-truncate d-flex align-items-center gap-1" style="font-size: 0.84rem;" title="{{ $user->name }}">
                                            <span class="text-truncate">{{ $user->name }}</span>
                                            @if($user->isProtectedSuperAdmin())
                                                <span class="badge bg-warning bg-opacity-15 text-warning border border-warning px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.68rem;" title="مالك النظام - حساب محمي"><i class="fas fa-crown text-warning me-1"></i>المالك (محمي)</span>
                                            @endif
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.74rem;" title="{{ $user->email }}">
                                            <i class="fas fa-envelope me-1 opacity-75"></i>{{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- الدور الوظيفي -->
                            <td class="px-2 py-2 text-nowrap">
                                @switch($user->role)
                                    @case('admin')
                                        @if($user->isProtectedSuperAdmin())
                                            <span class="badge bg-warning bg-opacity-15 text-dark border border-warning px-2.5 py-1 rounded-pill fw-bold text-nowrap" style="font-size: 0.75rem;">
                                                <i class="fas fa-crown text-warning me-1"></i> مدير النظام (المالك)
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1 rounded-pill fw-bold text-nowrap" style="font-size: 0.75rem;">
                                                <i class="fas fa-user-shield text-warning me-1"></i> مدير النظام
                                            </span>
                                        @endif
                                        @break
                                    @case('staff')
                                        <span class="badge bg-info bg-opacity-10 text-primary border border-info px-2 py-1 rounded-pill fw-bold text-nowrap" style="font-size: 0.75rem;">
                                            <i class="fas fa-user-cog me-1"></i> موظف مخصص
                                        </span>
                                        @break
                                    @case('training_coordinator')
                                        <span class="badge bg-purple bg-opacity-10 border px-2 py-1 rounded-pill text-nowrap" style="color:#7c3aed; border-color:#c4b5fd; background-color: rgba(124, 58, 237, 0.08); font-size: 0.75rem;">
                                            <i class="fas fa-graduation-cap me-1"></i> منسق التدريب
                                        </span>
                                        @break
                                    @case('partnership_officer')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                            <i class="fas fa-handshake me-1"></i> مسؤول الشراكات
                                        </span>
                                        @break
                                    @case('career_guidance_officer')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                            <i class="fas fa-user-graduate me-1"></i> إرشاد مهني
                                        </span>
                                        @break
                                    @case('evaluation_followup')
                                        <span class="badge bg-teal bg-opacity-10 text-info border border-teal px-2 py-1 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                            <i class="fas fa-chart-line me-1"></i> تقييم ومتابعة
                                        </span>
                                        @break
                                    @case('media_officer')
                                        <span class="badge bg-orange bg-opacity-10 text-warning border border-warning px-2 py-1 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                            <i class="fas fa-photo-video me-1"></i> مسؤول الميديا
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill text-nowrap" style="font-size: 0.75rem;">{{ $user->role }}</span>
                                @endswitch
                            </td>

                            <!-- الصلاحيات الممنوحة -->
                            <td class="px-2 py-2 text-nowrap">
                                @if($user->isProtectedSuperAdmin())
                                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold text-nowrap" style="font-size: 0.75rem;">
                                        <i class="fas fa-crown me-1 text-warning"></i> وصول المالك الكامل (كافة الصلاحيات)
                                    </span>
                                @elseif($user->isAdmin())
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1 rounded-pill fw-bold text-nowrap" style="font-size: 0.75rem;">
                                        <i class="fas fa-user-shield me-1"></i> وصول إداري شامل (كافة الصلاحيات)
                                    </span>
                                @else
                                    @php
                                        $permCount = $user->permissions->count();
                                    @endphp
                                    @if($permCount > 0)
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0.5 shadow-none d-inline-flex align-items-center gap-1 text-nowrap"
                                                style="font-size: 0.76rem;"
                                                data-bs-toggle="modal" data-bs-target="#userPermModal{{ $user->id }}">
                                            <i class="fas fa-key text-primary"></i>
                                            <strong class="text-primary">{{ $permCount }}</strong>
                                            <span class="text-muted">صلاحيات</span>
                                        </button>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                            بدون صلاحيات
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <!-- حالة الحساب -->
                            <td class="px-2 py-2 text-center text-nowrap">
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-0.5 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                        <i class="fas fa-check-circle me-1"></i> نشط
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-0.5 rounded-pill text-nowrap" style="font-size: 0.75rem;">
                                        <i class="fas fa-ban me-1"></i> معطل
                                    </span>
                                @endif
                            </td>

                            <!-- تاريخ الإضافة -->
                            <td class="px-2 py-2 text-nowrap">
                                <span class="text-muted small text-nowrap font-monospace" style="font-size: 0.78rem;">
                                    <i class="far fa-calendar-alt me-1 opacity-75"></i>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '—' }}
                                </span>
                            </td>

                            <!-- الإجراءات -->
                            <td class="px-3 py-2 text-end text-nowrap">
                                <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                                    @if($user->isProtectedSuperAdmin())
                                        @if(auth()->id() === $user->id)
                                            <!-- تعديل المالك لبياناته بنفسه فقط -->
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="action-circle-btn text-primary" title="تعديل بيانات حسابك (المالك)">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="action-circle-btn text-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal{{ $user->id }}" title="تغيير كلمة المرور">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        @else
                                            <!-- عند مشاهدة المدير الآخر أو أي موظف لحساب المالك المحمي -->
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;" title="حساب المالك محمي بالكامل ولا يستطيع أحد تعديل بياناته">
                                                <i class="fas fa-lock text-warning me-1"></i> محمي بالكامل
                                            </span>
                                        @endif
                                    @else
                                        <!-- تعديل البيانات والصلاحيات لباقي المستخدمين والمدير غير المحمي -->
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="action-circle-btn text-primary" title="تعديل البيانات وتخصيص الصلاحيات">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- تغيير كلمة المرور -->
                                        <button type="button" class="action-circle-btn text-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal{{ $user->id }}" title="إعادة تعيين كلمة المرور">
                                            <i class="fas fa-key"></i>
                                        </button>

                                        <!-- تجميد / تنشيط الحساب -->
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                <button type="submit" class="action-circle-btn {{ $user->is_active ? 'text-secondary' : 'text-success' }}" 
                                                        title="{{ $user->is_active ? 'تجميد الحساب' : 'تنشيط الحساب' }}"
                                                        onclick="return confirm('هل أنت متأكد من {{ $user->is_active ? 'تجميد' : 'تنشيط' }} حساب الموظف {{ $user->name }}؟')">
                                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }}"></i>
                                                </button>
                                            </form>

                                            <!-- حذف الحساب -->
                                            @if(auth()->user()->isAdmin())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-circle-btn text-danger" 
                                                        onclick="return confirm('تحذير أمني: هل أنت متأكد من حذف حساب الموظف ({{ $user->name }}) نهائياً من النظام؟ لا يمكن التراجع عن هذه الخطوة.')" 
                                                        title="حذف الموظف نهائياً">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-3">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-users-slash fa-3x text-muted opacity-50"></i>
                                    </div>
                                </div>
                                <h5 class="text-dark fw-bold mb-1">لا يوجد موظفون مطابقون لخيارات البحث</h5>
                                <p class="text-muted small">يمكنك إعادة ضبط الفلاتر أو إضافة موظف جديد وتخصيص صلاحياته.</p>
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-plus me-1"></i> إضافة موظف جديد
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="p-3 border-top bg-light d-flex justify-content-center">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals تفاصيل الصلاحيات وتغيير كلمة المرور (خارج الجدول لضمان التموضع السليم في الشاشة ومنع مشاكل الـ Stacking Context) -->
@foreach($users as $user)
    <!-- Modal عرض تفاصيل صلاحيات الموظف -->
    <div class="modal fade" id="userPermModal{{ $user->id }}" tabindex="-1" aria-labelledby="userPermModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold mb-0" id="userPermModalLabel{{ $user->id }}">
                        <i class="fas fa-shield-alt me-2"></i> صلاحيات الموظف: {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border mb-3">
                        <div>
                            <strong class="text-dark">{{ $user->email }}</strong>
                            <div class="text-muted small">الدور: {{ $user->role_name }}</div>
                        </div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill">
                            {{ $user->permissions->count() }} صلاحية نشطة
                        </span>
                    </div>

                    <div class="row g-3">
                        @foreach($groupedPermissions as $moduleKey => $moduleData)
                            @php
                                $userModulePerms = $user->permissions->where('module', $moduleKey);
                            @endphp
                            @if($userModulePerms->count() > 0)
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                        <i class="{{ $moduleData['meta']['icon'] ?? 'fas fa-shield-alt' }}" style="color: {{ $moduleData['meta']['color'] ?? '#0d6efd' }};"></i>
                                        <strong class="small text-dark">{{ $moduleData['meta']['label'] ?? $moduleKey }}</strong>
                                        <span class="badge bg-light text-muted border ms-auto small">{{ $userModulePerms->count() }}</span>
                                    </div>
                                    <div class="card-body p-2">
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($userModulePerms as $perm)
                                                <li class="py-1 px-2 border-bottom border-light d-flex align-items-center gap-2">
                                                    <i class="fas fa-check-circle text-success small"></i>
                                                    <span>{{ $perm->display_name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach

                        {{-- أي صلاحيات غير مصنفة ضمن المجموعات الثمانية --}}
                        @php
                            $otherPerms = $user->permissions->whereNotIn('module', array_keys($groupedPermissions));
                        @endphp
                        @if($otherPerms->count() > 0)
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                        <i class="fas fa-cogs text-secondary"></i>
                                        <strong class="small text-dark">صلاحيات إضافية</strong>
                                        <span class="badge bg-light text-muted border ms-auto small">{{ $otherPerms->count() }}</span>
                                    </div>
                                    <div class="card-body p-2">
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($otherPerms as $perm)
                                                <li class="py-1 px-2 border-bottom border-light d-flex align-items-center gap-2">
                                                    <i class="fas fa-check-circle text-success small"></i>
                                                    <span>{{ $perm->display_name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 py-3 d-flex justify-content-between">
                    @if(!$user->isProtectedSuperAdmin() || auth()->id() === $user->id)
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-edit me-1"></i> تعديل هذه الصلاحيات
                    </a>
                    @endif
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تغيير كلمة المرور -->
    @if(!$user->isProtectedSuperAdmin() || auth()->id() === $user->id)
    <div class="modal fade" id="changePasswordModal{{ $user->id }}" tabindex="-1" aria-labelledby="changePasswordModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="changePasswordModalLabel{{ $user->id }}">
                        <i class="fas fa-key text-warning me-2"></i> تغيير كلمة المرور: {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.change-password', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">كلمة المرور الجديدة (8 خانات على الأقل)</label>
                            <input type="password" name="password" class="form-control rounded-3" required minlength="8" placeholder="أدخل كلمة المرور الجديدة">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" required minlength="8" placeholder="أعد إدخال كلمة المرور">
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3 border-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">حفظ كلمة المرور</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection