@extends('layouts.app')

@section('title', 'طلبات التسجيل المعلقة')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header mb-4">
                    <h1 class="page-title">
                        <i class="fas fa-user-clock me-2"></i>
                        طلبات التسجيل المعلقة
                    </h1>
                    <p class="text-muted">مراجعة والموافقة على حسابات الخريجين الجدد</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if($pendingUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>رقم الهاتف</th>
                                    <th>الجامعة / التخصص</th>
                                    <th>تاريخ التسجيل</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <div class="small text-muted">{{ $user->national_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            <div>{{ $user->university }}</div>
                                            <div class="small text-muted">{{ $user->specialization }}</div>
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('career-guidance.approve-graduate', $user->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('هل أنت متأكد من الموافقة على هذا الحساب؟')">
                                                        <i class="fas fa-check me-1"></i> موافقة
                                                    </button>
                                                </form>
                                                <form action="{{ route('career-guidance.reject-graduate', $user->id) }}"
                                                    method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('هل أنت متأكد من رفض هذا الطلب وحذفه؟')">
                                                        <i class="fas fa-times me-1"></i> رفض
                                                    </button>
                                                </form>
                                            </div>
                                            <button type="button" class="btn btn-info btn-sm ms-1" data-bs-toggle="modal"
                                                data-bs-target="#userModal{{ $user->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Modal للتفاصيل -->
                                            <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تفاصيل الخريج: {{ $user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>الاسم الكامل:</strong> {{ $user->name }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>رقم الهوية:</strong> {{ $user->national_id }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>البريد الإلكتروني:</strong> {{ $user->email }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>رقم الهاتف:</strong> {{ $user->phone }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>تاريخ الميلاد:</strong> {{ $user->date_of_birth }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>الجنس:</strong>
                                                                    {{ $user->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>المدينة:</strong> {{ $user->city }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>العنوان:</strong> {{ $user->address }}
                                                                </div>
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>الجامعة:</strong> {{ $user->university }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>المؤهل العلمي:</strong> {{ $user->qualification }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>التخصص:</strong> {{ $user->specialization }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>سنة التخرج:</strong> {{ $user->graduation_year }}
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <strong>المعدل التراكمي:</strong> {{ $user->gpa ?? 'غير محدد' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">إغلاق</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingUsers->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success fa-3x"></i>
                        </div>
                        <h4>لا توجد طلبات معلقة</h4>
                        <p class="text-muted">جميع طلبات التسجيل تمت مراجعتها.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection