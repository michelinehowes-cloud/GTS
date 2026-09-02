@extends('layouts.app')

@section('title', 'إضافة موظف وتعيين الصلاحيات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة الموظفين والصلاحيات', 'url' => route('admin.users')],
            ['label' => 'إضافة موظف جديد', 'active' => true],
        ]
    ])

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-1">
                <i class="fas fa-user-plus me-2"></i> إضافة موظف جديد وتخصيص الصلاحيات
            </h2>
            <p class="text-muted small mb-0">
                أنشئ حساباً لموظف جديد وحدد له الصلاحيات الدقيقة التي يحتاجها لممارسة عمله فقط دون الوصول لباقي أجزاء النظام.
            </p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
        </a>
    </div>

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

    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
        @csrf

        <!-- بطاقة البيانات الأساسية -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fas fa-id-card text-primary me-2"></i> 1. البيانات الأساسية وبيانات الدخول
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold text-muted">اسم الموظف بالكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required 
                               placeholder="مثال: أحمد محمد علي">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold text-muted">البريد الإلكتروني المهني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required 
                               placeholder="user@university.edu.ly">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-bold text-muted">كلمة المرور المؤقتة <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror" 
                               id="password" name="password" required minlength="8" 
                               placeholder="لا تقل عن 8 خانات وتحتوي أرقاماً وحروفاً">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label small fw-bold text-muted">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-lg rounded-3" 
                               id="password_confirmation" name="password_confirmation" required minlength="8" 
                               placeholder="أعد إدخال كلمة المرور">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-muted">رقم الهاتف للتواصل</label>
                        <input type="text" class="form-control form-control-lg rounded-3 @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" 
                               placeholder="09XXXXXXXX">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label small fw-bold text-muted">الدور الوظيفي الأساسي <span class="text-danger">*</span></label>
                        <select name="role" id="role_selector" class="form-select form-select-lg rounded-3 @error('role') is-invalid @enderror" required>
                            <option value="">-- اختر الدور الوظيفي --</option>
                            <option value="staff" {{ old('role', 'staff') == 'staff' ? 'selected' : '' }}>
                                🛡️ موظف مخصص الصلاحيات (Custom Staff) - موصى به
                            </option>
                            @if(auth()->user()->isAdmin())
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    👑 مدير عام النظام (Full Super Admin)
                                </option>
                            @endif
                            <option value="training_coordinator" {{ old('role') == 'training_coordinator' ? 'selected' : '' }}>
                                🎓 منسق البرامج التدريبية
                            </option>
                            <option value="partnership_officer" {{ old('role') == 'partnership_officer' ? 'selected' : '' }}>
                                🏢 مسؤول الشراكات وعلاقات الشركات
                            </option>
                            <option value="career_guidance_officer" {{ old('role') == 'career_guidance_officer' ? 'selected' : '' }}>
                                🧭 مسؤول التوجيه والإرشاد المهني
                            </option>
                            <option value="evaluation_followup" {{ old('role') == 'evaluation_followup' ? 'selected' : '' }}>
                                📊 مسؤول التقييم والمتابعة والاستبيانات
                            </option>
                            <option value="media_officer" {{ old('role') == 'media_officer' ? 'selected' : '' }}>
                                📢 مسؤول الإعلام والنشر الرقمي
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="admin_role_notice" class="alert alert-warning border-0 rounded-3 mt-3 d-none">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <strong>تنبيه أمني:</strong> حساب مدير النظام (Admin) يحصل على كافة الصلاحيات الحالية والمستقبلية تلقائياً دون الحاجة لتحديد الصلاحيات الفردية.
                </div>
            </div>
        </div>

        <!-- بطاقة مصفوفة الصلاحيات (RBAC Bento Grid) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" id="permissions_section">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fas fa-sliders-h text-primary me-2"></i> 2. تخصيص صلاحيات الوصول الدقيقة (Permissions)
                </h5>
            </div>
            <div class="card-body p-4">
                @include('admin.users.partials.permissions-matrix', [
                    'groupedPermissions' => $groupedPermissions,
                    'userPermissionIds' => old('permissions', [])
                ])
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border mb-5">
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-times me-1"></i> إلغاء العملية
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold fs-6 shadow">
                <i class="fas fa-check-circle me-2"></i> حفظ الموظف وتفعيل الصلاحيات
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role_selector');
    const adminNotice = document.getElementById('admin_role_notice');
    const permSection = document.getElementById('permissions_section');

    function checkRoleState() {
        if (roleSelect.value === 'admin') {
            adminNotice.classList.remove('d-none');
            // Check all permissions
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
            const counter = document.getElementById('selected_perms_counter');
            if (counter) counter.textContent = '33';
        } else {
            adminNotice.classList.add('d-none');
        }
    }

    roleSelect.addEventListener('change', checkRoleState);
    checkRoleState();
});
</script>
@endsection
