@extends('layouts.app')

@section('title', 'ترشيحات فرصة العمل - ' . $opportunity->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- بطاقة العنوان الرئيسية -->
            <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-4">
                                    <h1 class="h3 mb-2">ترشيحات الفرصة</h1>
                                    <h2 class="h5 mb-0 text-white-50">{{ $opportunity->title }}</h2>
                                    <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                                        <span class="badge bg-white bg-opacity-20 text-white">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $opportunity->company->name }}
                                        </span>
                                        <span class="badge bg-white bg-opacity-20 text-white">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $opportunity->location }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group-vertical w-100">
                                <a href="{{ route('job-opportunities.show', $opportunity->id) }}" 
                                   class="btn btn-light btn-lg mb-2 text-dark">
                                    <i class="fas fa-eye me-2"></i>عرض التفاصيل
                                </a>
                                <a href="{{ route('job-opportunities.index') }}" 
                                   class="btn btn-outline-light">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 border-0" style="background: #d4edda; color: #155724; border-left: 4px solid #28a745;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                        <div class="flex-grow-1">
                            <h5 class="mb-1 text-success">تم بنجاح!</h5>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- بطاقة الإحصائيات السريعة -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-users fa-3x mb-3 opacity-75"></i>
                            <h2 class="display-6 fw-bold">{{ $nominations->count() }}</h2>
                            <h6 class="mb-0">إجمالي الترشيحات</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 opacity-75"></i>
                            <h2 class="display-6 fw-bold">{{ $nominations->where('status', 'accepted')->count() }}</h2>
                            <h6 class="mb-0">مقبولة</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-clock fa-3x mb-3 opacity-75"></i>
                            <h2 class="display-6 fw-bold">{{ $nominations->where('status', 'pending')->count() }}</h2>
                            <h6 class="mb-0">قيد المراجعة</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-building fa-3x mb-3 opacity-75"></i>
                            <h2 class="display-6 fw-bold">{{ $nominations->where('status', 'sent_to_company')->count() }}</h2>
                            <h6 class="mb-0">مرسلة للشركة</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة قائمة الترشيحات -->
            <div class="card shadow-sm border-0" style="border: 1px solid #e2e8f0;">
                <div class="card-header bg-transparent border-0 py-3" style="border-bottom: 2px solid #2c5aa0;">
                    <h4 class="mb-0" style="color: #2c5aa0;">
                        <i class="fas fa-list me-2"></i>قائمة الترشيحات
                    </h4>
                </div>
                <div class="card-body">
                    @if($nominations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light" style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="border-0" style="color: #2c5aa0;">#</th>
                                        <th class="border-0" style="color: #2c5aa0;">الخريج</th>
                                        <th class="border-0" style="color: #2c5aa0;">المعلومات الأكاديمية</th>
                                        <th class="border-0" style="color: #2c5aa0;">الحالة</th>
                                        <th class="border-0" style="color: #2c5aa0;">تاريخ الترشيح</th>
                                        <th class="border-0 text-center" style="color: #2c5aa0;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nominations as $nomination)
                                    <tr class="border-bottom">
                                        <td class="fw-bold" style="color: #2c5aa0;">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-circle p-2" style="background-color: #2c5aa0; color: white;">
                                                        <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1" style="color: #1e293b;">{{ $nomination->graduate->name }}</h6>
                                                    <small class="text-muted">{{ $nomination->graduate->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <small class="d-block">
                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                    {{ $nomination->graduate->major }}
                                                </small>
                                                <small class="d-block">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $nomination->graduate->graduation_year }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => '#f59e0b',
                                                    'sent_to_company' => '#3b82f6',
                                                    'under_review' => '#8b5cf6',
                                                    'interview_scheduled' => '#10b981',
                                                    'accepted' => '#10b981',
                                                    'rejected' => '#ef4444',
                                                    'withdrawn' => '#6b7280'
                                                ];
                                                $statusIcons = [
                                                    'pending' => 'clock',
                                                    'sent_to_company' => 'paper-plane',
                                                    'under_review' => 'search',
                                                    'interview_scheduled' => 'calendar-check',
                                                    'accepted' => 'check-circle',
                                                    'rejected' => 'times-circle',
                                                    'withdrawn' => 'ban'
                                                ];
                                            @endphp
                                            <span class="badge py-2 px-3 text-white" style="background-color: {{ $statusColors[$nomination->status] }}; border-radius: 8px;">
                                                <i class="fas fa-{{ $statusIcons[$nomination->status] }} me-1"></i>
                                                {{ $nomination->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <small>{{ $nomination->created_at->format('Y-m-d') }}</small>
                                                <br>
                                                <small>{{ $nomination->created_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('admin.career-guidance.graduates.show', $nomination->graduate->id) }}" 
                                                   class="btn btn-sm rounded-pill" 
                                                   title="عرض ملف الخريج"
                                                   style="background-color: #2c5aa0; color: white; border: none;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm rounded-pill"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#statusModal{{ $nomination->id }}"
                                                        title="تغيير الحالة"
                                                        style="background-color: #f59e0b; color: white; border: none;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>

                                            <!-- Modal لتغيير الحالة -->
                                            <div class="modal fade" id="statusModal{{ $nomination->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                                        <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%); border-radius: 15px 15px 0 0;">
                                                            <h5 class="modal-title">
                                                                <i class="fas fa-edit me-2"></i>تغيير حالة الترشيح
                                                            </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('admin.career-guidance.nominations.update-status', $nomination->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="status" class="form-label fw-bold" style="color: #2c5aa0;">اختر الحالة الجديدة</label>
                                                                    <select name="status" id="status" class="form-select form-select-lg" required style="border-color: #2c5aa0;">
                                                                        <option value="pending" {{ $nomination->status == 'pending' ? 'selected' : '' }}>🕒 قيد المراجعة</option>
                                                                        <option value="sent_to_company" {{ $nomination->status == 'sent_to_company' ? 'selected' : '' }}>📨 مرسل للشركة</option>
                                                                        <option value="under_review" {{ $nomination->status == 'under_review' ? 'selected' : '' }}>🔍 قيد الدراسة</option>
                                                                        <option value="interview_scheduled" {{ $nomination->status == 'interview_scheduled' ? 'selected' : '' }}>✅ مقابلة مجدولة</option>
                                                                        <option value="accepted" {{ $nomination->status == 'accepted' ? 'selected' : '' }}>🎉 مقبول</option>
                                                                        <option value="rejected" {{ $nomination->status == 'rejected' ? 'selected' : '' }}>❌ مرفوض</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="button" class="btn rounded-pill" data-bs-dismiss="modal" style="background-color: #6b7280; color: white;">إلغاء</button>
                                                                <button type="submit" class="btn rounded-pill text-white" style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);">
                                                                    <i class="fas fa-save me-2"></i>حفظ التغيير
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-users fa-5x" style="color: #cbd5e1;"></i>
                            </div>
                            <h4 class="mb-3" style="color: #64748b;">لا توجد ترشيحات</h4>
                            <p class="mb-4" style="color: #94a3b8;">لم يتم ترشيح أي خريج لهذه الفرصة بعد</p>
                            <a href="{{ route('admin.career-guidance.nominations.create') }}" class="btn btn-lg rounded-pill text-white" style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);">
                                <i class="fas fa-plus me-2"></i>ترشيح خريج جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.badge {
    border-radius: 8px;
    font-size: 0.8rem;
}

.table th {
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.rounded-pill {
    border-radius: 50px !important;
}

.modal-content {
    border-radius: 15px;
    border: none;
}

/* تخصيص الألوان الأساسية */
:root {
    --primary-color: #2c5aa0;
    --primary-dark: #1e3a8a;
    --secondary-color: #f59e0b;
    --success-color: #10b981;
    --info-color: #3b82f6;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
}
</style>
@endsection
