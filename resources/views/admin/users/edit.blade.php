@extends('layouts.app')

@section('title', 'تعديل موظف وصلاحياته - ' . $user->name)

@section('content')
<div class="container-fluid py-3">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="تعديل موظف وصلاحياته: {{ $user->name }}"
        subtitle="تعديل بيانات الحساب وتخصيص صلاحيات الوصول الدقيقة بما يتوافق مع مهام الموظف"
        icon="fas fa-user-edit"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة الموظفين والصلاحيات', 'url' => route('admin.users')],
            ['label' => 'تعديل: ' . $user->name]
        ]"
        badge="{{ $user->isProtectedSuperAdmin() ? 'مالك النظام (المدير المحمي)' : 'الدور: ' . $user->role_name }}"
        badgeIcon="{{ $user->isProtectedSuperAdmin() ? 'fas fa-crown text-warning' : 'fas fa-id-badge' }}"
    >
        <a href="{{ route('admin.users') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-4 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="fas fa-arrow-right fs-6"></i>
            <span>العودة للقائمة</span>
        </a>
    </x-page-hero>

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle fs-5"></i>
            <strong class="fs-6">يرجى تصحيح الأخطاء التالية قبل المتابعة:</strong>
        </div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="userEditForm">
        @csrf
        @method('PUT')

        <!-- بطاقة البيانات الأساسية -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fas fa-id-card text-primary me-2"></i> 1. البيانات الشخصية وبيانات الحساب
                </h5>
                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill">
                    معرف الحساب: #{{ $user->id }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold text-muted">اسم الموظف بالكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold text-muted">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-bold text-muted">كلمة مرور جديدة (اختياري)</label>
                        <input type="password" class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror" 
                               id="password" name="password" minlength="8" 
                               placeholder="اتركه فارغاً للإبقاء على كلمة المرور الحالية">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label small fw-bold text-muted">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control form-control-lg rounded-3" 
                               id="password_confirmation" name="password_confirmation" minlength="8" 
                               placeholder="أعد كتابة كلمة المرور في حال قمت بتغييرها">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-muted">رقم الهاتف للتواصل</label>
                        <input type="text" class="form-control form-control-lg rounded-3 @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                               placeholder="09XXXXXXXX">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label small fw-bold text-muted">الدور الوظيفي <span class="text-danger">*</span></label>
                        <select name="role" id="role_selector" class="form-select form-select-lg rounded-3 @error('role') is-invalid @enderror" 
                                {{ $user->isProtectedSuperAdmin() ? 'disabled' : 'required' }}>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>
                                🛡️ موظف مخصص الصلاحيات (Custom Staff)
                            </option>
                            @if(auth()->user()->isAdmin())
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    👑 مدير عام النظام (Full Super Admin)
                                </option>
                            @endif
                            <option value="training_coordinator" {{ old('role', $user->role) == 'training_coordinator' ? 'selected' : '' }}>
                                🎓 منسق البرامج التدريبية
                            </option>
                            <option value="partnership_officer" {{ old('role', $user->role) == 'partnership_officer' ? 'selected' : '' }}>
                                🏢 مسؤول الشراكات وعلاقات الشركات
                            </option>
                            <option value="career_guidance_officer" {{ old('role', $user->role) == 'career_guidance_officer' ? 'selected' : '' }}>
                                🧭 مسؤول التوجيه والإرشاد المهني
                            </option>
                            <option value="evaluation_followup" {{ old('role', $user->role) == 'evaluation_followup' ? 'selected' : '' }}>
                                📊 مسؤول التقييم والمتابعة والاستبيانات
                            </option>
                            <option value="media_officer" {{ old('role', $user->role) == 'media_officer' ? 'selected' : '' }}>
                                📢 مسؤول الإعلام والنشر الرقمي
                            </option>
                        </select>
                        @if($user->isProtectedSuperAdmin())
                            <input type="hidden" name="role" value="admin">
                            <small class="text-warning d-block mt-1">
                                <i class="fas fa-lock me-1"></i> لا يمكن تغيير دور مدير النظام الأساسي (حماية النظام).
                            </small>
                        @endif
                    </div>
                </div>

                @if($user->isProtectedSuperAdmin())
                <div class="alert alert-warning border-0 rounded-3 mt-3">
                    <i class="fas fa-crown text-warning me-2"></i>
                    <strong>حساب المالك الأساسي المحمي:</strong> يتمتع هذا الحساب بأعلى مستويات الحماية في المنظومة، ولا يمكن لأي مدير نظام آخر أو موظف تعديل أي من بياناتك أو سحب صلاحياتك الإدارية.
                </div>
                @elseif($user->isAdmin())
                <div class="alert alert-info border-0 rounded-3 mt-3">
                    <i class="fas fa-user-shield text-info me-2"></i>
                    <strong>ملاحظة لمدير النظام:</strong> يتمتع هذا الحساب بصلاحيات إدارية كاملة عبر النظام.
                </div>
                @endif
            </div>
        </div>

        <!-- بطاقة مصفوفة الصلاحيات -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fas fa-sliders-h text-primary me-2"></i> 2. الصلاحيات الممنوحة للموظف
                </h5>
            </div>
            <div class="card-body p-4">
                @include('admin.users.partials.permissions-matrix', [
                    'groupedPermissions' => $groupedPermissions,
                    'userPermissionIds' => old('permissions', $userPermissionIds)
                ])
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border mb-5">
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-times me-1"></i> إلغاء والتراجع
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold fs-6 shadow">
                <i class="fas fa-save me-2"></i> حفظ وتحديث التغييرات والصلاحيات
            </button>
        </div>
    </form>
</div>
@endsection
