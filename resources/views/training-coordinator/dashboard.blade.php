@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التدريب')
@section('page-title', 'لوحة تحكم منسق التدريب')

@section('content')
    <div class="container-fluid">
        <!-- إحصائيات سريعة -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي برامج التدريب</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalTrainings'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                    البرامج النشطة</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['activeTrainings'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                                    البرامج المتوقفة</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactiveTrainings'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
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
                                    المتدربين المسجلين</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalTrainees'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- صف إضافي للإحصائيات -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    إجمالي الطلبات</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalApplications'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                    طلبات قيد المراجعة</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingApplications'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                    طلبات مقبولة</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approvedApplications'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    طلبات مرفوضة</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejectedApplications'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- محتوى إضافي -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">أحدث طلبات التدريب</h6>
                        <a href="{{ route('training-coordinator.applications') }}"
                            class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        @if($recentApplications->count() > 0)
                            <div class="list-group">
                                @foreach($recentApplications as $application)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $application->user->name }}</h6>
                                            <small class="text-muted">{{ $application->applied_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ $application->training->title }}</p>
                                        <span
                                            class="badge badge-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }}">
                                            {{ $application->status == 'pending' ? 'قيد المراجعة' : ($application->status == 'approved' ? 'مقبول' : 'مرفوض') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center">لا توجد طلبات تدريب حديثة</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">أحدث برامج التدريب</h6>
                        <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-sm btn-outline-primary">عرض
                            الكل</a>
                    </div>
                    <div class="card-body">
                        @if($recentTrainings->count() > 0)
                            <div class="list-group">
                                @foreach($recentTrainings as $training)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $training->title }}</h6>
                                            <small class="text-muted">{{ $training->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ $training->company->name ?? 'بدون شركة' }}</p>
                                        <span
                                            class="badge badge-{{ $training->status == 'active' ? 'success' : ($training->status == 'inactive' ? 'warning' : 'info') }}">
                                            {{ $training->status == 'active' ? 'نشط' : ($training->status == 'inactive' ? 'متوقف' : 'مكتمل') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center">لا توجد برامج تدريب حديثة</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقات سريعة للوصول -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                        <h5>برامج التدريب</h5>
                        <p>إدارة جميع برامج التدريب</p>
                        <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-light">الدخول</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>طلبات التدريب</h5>
                        <p>مراجعة طلبات المتدربين</p>
                        <a href="{{ route('training-coordinator.applications') }}" class="btn btn-light">الدخول</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-plus-circle fa-3x mb-3"></i>
                        <h5>إضافة برنامج</h5>
                        <p>إضافة برنامج تدريب جديد</p>
                        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-light">الدخول</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease;
                margin-bottom: 25px;
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            }

            .border-left-primary {
                border-left: 4px solid #4e73df !important;
            }

            .border-left-success {
                border-left: 4px solid #1cc88a !important;
            }

            .border-left-warning {
                border-left: 4px solid #f6c23e !important;
            }

            .border-left-info {
                border-left: 4px solid #36b9cc !important;
            }

            .border-left-secondary {
                border-left: 4px solid #858796 !important;
            }

            .border-left-danger {
                border-left: 4px solid #e74a3b !important;
            }

            .text-gray-800 {
                color: #5a5c69 !important;
            }

            .text-gray-300 {
                color: #dddfeb !important;
            }

            .list-group-item {
                border: 1px solid #e3e6f0;
                border-radius: 10px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }

            .list-group-item:hover {
                background-color: #f8f9fc;
                border-color: #4e73df;
            }
        </style>
    @endpush
@endsection