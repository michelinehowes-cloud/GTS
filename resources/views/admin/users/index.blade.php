{{-- ملف: resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-users-cog me-2"></i>قائمة المستخدمين
        </h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>إضافة مستخدم جديد
        </a>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>الدور</th>
                        <th>تاريخ التسجيل</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="user-avatar-sm">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'غير محدد' }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="custom-badge badge-danger">مدير النظام</span>
                            @elseif($user->role == 'training_coordinator')
                                <span class="custom-badge badge-warning">منسق التدريب</span>
                            @elseif($user->role == 'placement_coordinator')
                                <span class="custom-badge badge-primary">منسق التوظيف</span>
                            @else
                                <span class="custom-badge badge-success">خريج</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <span class="custom-badge badge-success">نشط</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <!-- زر التعديل -->
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- زر الحذف -->
                                @if($user->id != auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-secondary" disabled title="لا يمكن حذف حسابك">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">لا توجد مستخدمين مسجلين</h5>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-user-plus me-2"></i>إضافة أول مستخدم
            </a>
        </div>
        @endif
    </div>
</div>

<style>
.user-avatar-sm {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--university-blue), var(--primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 700;
    font-size: 1rem;
    box-shadow: 0 3px 10px rgba(30, 58, 138, 0.3);
    border: 2px solid var(--university-gold);
}

.custom-badge {
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #000 !important; /* اللون الأسود */
    border: 1px solid #dee2e6;
}

.badge-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca) !important;
    border-color: #fca5a5 !important;
}

.badge-warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
    border-color: #fcd34d !important;
}

.badge-primary {
    background: linear-gradient(135deg, #dbeafe, #93c5fd) !important;
    border-color: #60a5fa !important;
}

.badge-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
    border-color: #34d399 !important;
}
</style>
@endsection