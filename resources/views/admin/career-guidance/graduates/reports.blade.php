@extends('layouts.app')

@section('title', 'التقارير والإحصائيات - الإرشاد المهني')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>التقارير والإحصائيات</h3>
                </div>
                <div class="card-body">
                    <!-- إحصائيات سريعة -->
                    <div class="row mb-5">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                إجمالي الخريجين</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalGraduates'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                الخريجين الموظفين</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['employedGraduates'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                الترشيحات المقبولة</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['acceptedNominations'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                نسبة النجاح</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $stats['totalNominations'] > 0 ? round(($stats['acceptedNominations'] / $stats['totalNominations']) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقارير مفصلة -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>التوزيع حسب التخصص</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $majors = \App\Models\GraduateData::selectRaw('major, count(*) as count')
                                            ->groupBy('major')
                                            ->orderBy('count', 'desc')
                                            ->get();
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>التخصص</th>
                                                    <th>عدد الخريجين</th>
                                                    <th>النسبة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($majors as $major)
                                                <tr>
                                                    <td>{{ $major->major }}</td>
                                                    <td>{{ $major->count }}</td>
                                                    <td>{{ round(($major->count / $stats['totalGraduates']) * 100, 1) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>حالة التوظيف</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $employmentStats = \App\Models\GraduateData::selectRaw('employment_status, count(*) as count')
                                            ->groupBy('employment_status')
                                            ->get();
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>الحالة</th>
                                                    <th>عدد الخريجين</th>
                                                    <th>النسبة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employmentStats as $stat)
                                                @php
                                                    $statusText = [
                                                        'employed' => 'موظف',
                                                        'unemployed' => 'غير موظف',
                                                        'seeking_opportunities' => 'باحث عن فرص',
                                                        'continuing_education' => 'مستكمل للدراسة'
                                                    ];
                                                @endphp
                                                <tr>
                                                    <td>{{ $statusText[$stat->employment_status] ?? $stat->employment_status }}</td>
                                                    <td>{{ $stat->count }}</td>
                                                    <td>{{ round(($stat->count / $stats['totalGraduates']) * 100, 1) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إحصائيات الترشيحات -->
                    <div class="card">
                        <div class="card-header">
                            <h5>إحصائيات الترشيحات</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $nominationStats = \App\Models\Nomination::selectRaw('status, count(*) as count')
                                    ->groupBy('status')
                                    ->get();
                            @endphp
                            <div class="row">
                                @foreach($nominationStats as $stat)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h3>{{ $stat->count }}</h3>
                                            <small class="text-muted">{{ $stat->status_text }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
