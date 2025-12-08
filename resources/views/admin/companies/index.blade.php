@extends('layouts.app')

@section('title', 'إدارة الشركات')

@section('page-title', 'إدارة الشركات')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الشركات', 'active' => true],
        ]
    ])

    <!-- إحصائيات سريعة للشركات -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            @include('components.stat-card', [
                'title' => 'إجمالي الشركات',
                'value' => $companies->count(),
                'icon' => 'fas fa-building',
                'color' => 'primary',
                'description' => 'المسجلة في النظام'
            ])
        </div>
        <div class="col-xl-3 col-md-6">
            @include('components.stat-card', [
                'title' => 'أحدث إضافة',
                'value' => $companies->count() > 0 ? $companies->sortByDesc('created_at')->first()->created_at->format('Y-m-d') : '--',
                'icon' => 'fas fa-clock',
                'color' => 'info',
                'description' => 'تاريخ آخر تسجيل'
            ])
        </div>
        <!-- يمكن إضافة المزيد من الإحصائيات مستقبلاً -->
    </div>

    <!-- جدول الشركات -->
    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary fw-bold">
                    <i class="fas fa-list me-2"></i>قائمة الشركات
                </h5>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus me-2"></i>إضافة شركة جديدة
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($companies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold">#</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">اسم الشركة</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">البريد الإلكتروني</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">المجال الصناعي</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">العنوان</th>
                            <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">تاريخ الإضافة</th>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                                <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end">الإجراءات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($companies as $company)
                        <tr>
                            <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                        <span class="fw-bold">{{ substr($company->name, 0, 1) }}</span>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $company->name }}</div>
                                </div>
                            </td>
                            <td><a href="mailto:{{ $company->email }}" class="text-decoration-none">{{ $company->email }}</a></td>
                            <td>
                                @if($company->industry)
                                    <span class="badge bg-info-subtle text-info fw-normal px-2 py-1">{{ $company->industry }}</span>
                                @else
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>
                            <td><span class="text-muted small">{{ Str::limit($company->address ?? '--', 30) }}</span></td>
                            <td><span class="text-muted small">{{ $company->created_at->format('Y-m-d') }}</span></td>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                                <td class="px-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-warning-modern" data-bs-toggle="tooltip" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger-modern rounded-start-0" onclick="return confirm('هل أنت متأكد من حذف هذه الشركة؟')" data-bs-toggle="tooltip" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-building fa-3x text-muted opacity-50"></i>
                    </div>
                </div>
                <h5 class="text-muted mb-3">لا توجد شركات مسجلة</h5>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-plus me-2"></i>إضافة أول شركة
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection