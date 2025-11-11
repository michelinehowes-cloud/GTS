{{-- ملف: resources/views/admin/reports/ --}}
@extends('layouts.app')

@section('title', 'تقرير الشركات التفصيلي')

@section('page-title', 'تقرير الشركات التفصيلي')

@section('content')
<!-- إحصائيات التقرير -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ $reportStats['total'] }}</h3>
                <small class="text-muted">إجمالي الشركات</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-success">{{ $reportStats['recent'] }}</h3>
                <small class="text-muted">جديدة (30 يوم)</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-warning">{{ $reportStats['by_industry']->count() }}</h3>
                <small class="text-muted">مجالات صناعية</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-info">{{ $companies->count() }}</h3>
                <small class="text-muted">في التقرير</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- توزيع الشركات حسب المجال -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>توزيع الشركات حسب المجال
                </h5>
            </div>
            <div class="card-body">
                @if($reportStats['by_industry']->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($reportStats['by_industry'] as $industry)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $industry->industry ?: 'غير محدد' }}</span>
                        <span class="custom-badge badge-primary">{{ $industry->count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">لا توجد بيانات</p>
                @endif
            </div>
        </div>
    </div>

    <!-- التقرير التفصيلي -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>التقرير التفصيلي للشركات
                </h5>
                <div>
                    <button class="btn btn-success me-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>طباعة التقرير
                    </button>
                    <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($companies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الشركة</th>
                                <th>المجال الصناعي</th>
                                <th>البريد الإلكتروني</th>
                                <th>رقم الهاتف</th>
                                <th>العنوان</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $company->name }}</strong>
                                </td>
                                <td>
                                    <span class="custom-badge badge-primary">{{ $company->industry ?: 'غير محدد' }}</span>
                                </td>
                                <td>{{ $company->email }}</td>
                                <td>{{ $company->phone }}</td>
                                <td>{{ Str::limit($company->address, 30) }}</td>
                                <td>{{ $company->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد بيانات للشركات</h5>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص التقرير -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">ملخص تقرير الشركات</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>الإحصائيات العامة:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-building text-primary me-2"></i>
                                إجمالي الشركات: <strong>{{ $reportStats['total'] }}</strong>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar-plus text-success me-2"></i>
                                شركات جديدة (30 يوم): <strong>{{ $reportStats['recent'] }}</strong>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-industry text-warning me-2"></i>
                                عدد المجالات الصناعية: <strong>{{ $reportStats['by_industry']->count() }}</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>معلومات التقرير:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                تاريخ التقرير: <strong>{{ now()->format('Y-m-d') }}</strong>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-user text-success me-2"></i>
                                مولد التقرير: <strong>{{ auth()->user()->name }}</strong>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-database text-warning me-2"></i>
                                عدد السجلات: <strong>{{ $companies->count() }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar-main, .card-header .btn, .sidebar {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .main-content {
        margin-right: 0 !important;
        width: 100% !important;
    }
}
</style>
@endsection