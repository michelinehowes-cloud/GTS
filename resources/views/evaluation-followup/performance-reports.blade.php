@extends('layouts.app')

@section('title', 'تقارير الأداء')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">تقارير الأداء</h1>
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
                                    إجمالي تقييمات الأداء</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $performanceStats['total_performance_evaluations'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                    متوسط الأداء العام</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($performanceStats['average_performance_score'] ?? 0, 2) }} / 5.0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Performance Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">توزيع مستويات الأداء</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $distribution = $performanceStats['performance_distribution'] ?? [];
                            $total = $performanceStats['total_performance_evaluations'] > 0 ? $performanceStats['total_performance_evaluations'] : 1;
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>ممتاز (4.5 - 5.0)</span>
                                <span>{{ $distribution['excellent'] ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ (($distribution['excellent'] ?? 0) / $total) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>جيد (3.5 - 4.4)</span>
                                <span>{{ $distribution['good'] ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ (($distribution['good'] ?? 0) / $total) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>متوسط (2.5 - 3.4)</span>
                                <span>{{ $distribution['average'] ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ (($distribution['average'] ?? 0) / $total) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>أقل من المتوسط (1.5 - 2.4)</span>
                                <span>{{ $distribution['below_average'] ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-orange" role="progressbar"
                                    style="width: {{ (($distribution['below_average'] ?? 0) / $total) * 100 }}%; background-color: #fd7e14;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>ضعيف (0 - 1.4)</span>
                                <span>{{ $distribution['poor'] ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar"
                                    style="width: {{ (($distribution['poor'] ?? 0) / $total) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Areas for Improvement -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">مجالات تحتاج للتحسين</h6>
                    </div>
                    <div class="card-body">
                        @if(count($performanceStats['areas_for_improvement'] ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>المجال</th>
                                            <th>متوسط التقييم</th>
                                            <th>التوصيات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($performanceStats['areas_for_improvement'] as $area)
                                            <tr>
                                                <td>{{ $area['area'] }}</td>
                                                <td class="text-danger fw-bold">{{ $area['average_score'] }}</td>
                                                <td>{{ $area['recommendation'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-success">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <p>لا توجد مجالات حرجة تحتاج للتحسين حالياً. الأداء العام جيد!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">أفضل المتدربين أداءً</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>المتدرب</th>
                                <th>البرنامج التدريبي</th>
                                <th>التقييم</th>
                                <th>تاريخ التقييم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($performanceStats['top_performers'] ?? [] as $evaluation)
                                <tr>
                                    <td>{{ $evaluation->user->name ?? 'غير معروف' }}</td>
                                    <td>{{ $evaluation->training->title ?? 'غير محدد' }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $evaluation->average_score }} / 5</span>
                                    </td>
                                    <td>{{ $evaluation->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد بيانات متاحة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- All Evaluations Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">سجل التقييمات الكامل</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المتدرب</th>
                                <th>البرنامج التدريبي</th>
                                <th>المقيم</th>
                                <th>الدرجة</th>
                                <th>الملاحظات</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluations as $evaluation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $evaluation->user->name ?? 'غير معروف' }}</td>
                                    <td>{{ $evaluation->training->title ?? 'غير محدد' }}</td>
                                    <td>{{ $evaluation->evaluator->name ?? 'غير معروف' }}</td>
                                    <td>
                                        @if($evaluation->average_score >= 4)
                                            <span class="text-success fw-bold">{{ $evaluation->average_score }}</span>
                                        @elseif($evaluation->average_score >= 3)
                                            <span class="text-primary fw-bold">{{ $evaluation->average_score }}</span>
                                        @elseif($evaluation->average_score >= 2)
                                            <span class="text-warning fw-bold">{{ $evaluation->average_score }}</span>
                                        @else
                                            <span class="text-danger fw-bold">{{ $evaluation->average_score }}</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($evaluation->comments, 50) }}</td>
                                    <td>{{ $evaluation->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد تقييمات مسجلة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection