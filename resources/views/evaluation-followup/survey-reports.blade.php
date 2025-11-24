@extends('layouts.app')

@section('title', 'تقارير الاستبيانات')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">تقارير الاستبيانات</h1>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i> طباعة التقرير
            </button>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-4 border-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الاستبيانات</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['total_surveys'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-poll fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-4 border-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    الاستبيانات النشطة</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['active_surveys'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-4 border-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    إجمالي الردود</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['total_responses'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-4 border-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    متوسط نسبة الإكمال</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['average_completion_rate'] ?? 0, 1) }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surveys List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الاستبيانات</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان الاستبيان</th>
                                <th>الجمهور المستهدف</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>عدد الردود</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveys as $survey)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $survey->title }}</td>
                                    <td>
                                        @switch($survey->target_audience)
                                            @case('graduates')
                                                <span class="badge bg-primary">الخريجين</span>
                                                @break
                                            @case('companies')
                                                <span class="badge bg-info">الشركات</span>
                                                @break
                                            @case('training_coordinators')
                                                <span class="badge bg-secondary">منسقي التدريب</span>
                                                @break
                                            @default
                                                <span class="badge bg-dark">الكل</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($survey->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>{{ $survey->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $survey->responses->count() }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض النتائج
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد استبيانات متاحة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
