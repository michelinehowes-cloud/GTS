@extends('layouts.app')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">التقارير والإحصائيات</h1>

    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المستخدمين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Companies Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                إجمالي الشركات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCompanies }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Trainings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي التدريبات
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $totalTrainings }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Audit Logs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">آخر الحركات في النظام</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>الحدث</th>
                            <th>الوصف</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentAuditLogs as $log)
                        <tr>
                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                            <td>{{ $log->event }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">لا توجد حركات حديثة في النظام.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.reports.audit-logs') }}" class="btn btn-primary">عرض جميع الحركات</a>
            </div>
        </div>
    </div>

    <!-- Placeholder for Charts and other reports -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تقارير المستخدمين (مثال)</h6>
                </div>
                <div class="card-body">
                    <p>هنا يمكن عرض رسوم بيانية تفاعلية لتقارير المستخدمين.</p>
                    <a href="{{ route('admin.reports.users') }}" class="btn btn-info">عرض تقارير المستخدمين</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تقارير الشركات (مثال)</h6>
                </div>
                <div class="card-body">
                    <p>هنا يمكن عرض رسوم بيانية تفاعلية لتقارير الشركات.</p>
                    <a href="{{ route('admin.reports.companies') }}" class="btn btn-success">عرض تقارير الشركات</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تقارير التدريبات (مثال)</h6>
                </div>
                <div class="card-body">
                    <p>هنا يمكن عرض رسوم بيانية تفاعلية لتقارير التدريبات.</p>
                    <a href="{{ route('admin.reports.trainings') }}" class="btn btn-warning">عرض تقارير التدريبات</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
