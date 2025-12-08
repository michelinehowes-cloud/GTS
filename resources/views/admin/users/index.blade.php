@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'المستخدمين', 'active' => true],
        ]
    ])

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold mb-0">
            <i class="fas fa-users me-2"></i> إدارة المستخدمين
        </h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus me-2"></i> إضافة مستخدم جديد
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="card-modern mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-modern">بحث</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control-modern border-start-0 ps-0" 
                               placeholder="الاسم، البريد، الهاتف..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern">الدور الوظيفي</label>
                    <select name="role" class="form-select-modern">
                        <option value="">كل الأدوار</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مدير النظام</option>
                        <option value="graduate" {{ request('role') == 'graduate' ? 'selected' : '' }}>خريج</option>
                        <option value="company" {{ request('role') == 'company' ? 'selected' : '' }}>شركة</option>
                        <option value="career_guidance_officer" {{ request('role') == 'career_guidance_officer' ? 'selected' : '' }}>مسؤول إرشاد مهني</option>
                        <option value="training_coordinator" {{ request('role') == 'training_coordinator' ? 'selected' : '' }}>منسق تدريب</option>
                        <option value="partnership_officer" {{ request('role') == 'partnership_officer' ? 'selected' : '' }}>مسؤول شراكات</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-modern">الحالة</label>
                    <select name="status" class="form-select-modern">
                        <option value="">الكل</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-modern w-100">
                        تصفية النتائج
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-list me-2"></i>قائمة المستخدمين المسجلين
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold">المستخدم</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الدور</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الحالة</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">تاريخ التسجيل</th>
                            <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($users as $user)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @switch($user->role)
                                    @case('admin') <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">مدير النظام</span> @break
                                    @case('graduate') <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">خريج</span> @break
                                    @case('company') <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">شركة</span> @break
                                    @case('career_guidance_officer') <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">إرشاد مهني</span> @break
                                    @case('training_coordinator') <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">منسق تدريب</span> @break
                                    @case('partnership_officer') <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">مسؤول شراكات</span> @break
                                    @default <span class="badge bg-light text-dark border rounded-pill px-3">{{ $user->role }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check-circle me-1"></i> نشط</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="fas fa-ban me-1"></i> مجمد</span>
                                @endif
                            </td>
                            <td><span class="text-muted">{{ $user->created_at->format('Y-m-d') }}</span></td>
                            <td class="px-4 text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info-modern" data-bs-toggle="modal" data-bs-target="#changePasswordModal{{ $user->id }}" title="تغيير كلمة المرور">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning-modern" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-secondary-modern' : 'btn-outline-success-modern' }}" title="{{ $user->is_active ? 'تجميد' : 'تنشيط' }}">
                                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger-modern rounded-start-0" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Change Password Modal -->
                        <div class="modal fade" id="changePasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">تغيير كلمة المرور: {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.users.change-password', $user->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label-modern">كلمة المرور الجديدة</label>
                                                <input type="password" name="password" class="form-control-modern" required minlength="8">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label-modern">تأكيد كلمة المرور</label>
                                                <input type="password" name="password_confirmation" class="form-control-modern" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary-modern">حفظ التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="mb-3">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-users fa-3x text-muted opacity-50"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted">لا يوجد مستخدمين مطابقين للبحث</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-3 border-top">
                {{ $users->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection