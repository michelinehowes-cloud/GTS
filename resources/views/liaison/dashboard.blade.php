@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التكامل')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">لوحة تحكم منسق التكامل</h2>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            متدربين نشطين</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_trainees'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            خريجين جاهزين للتوظيف</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['job_ready_graduates'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">الانتقالات الحديثة من التدريب إلى التوظيف</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الخريج</th>
                                <th>برنامج التدريب</th>
                                <th>التقييم النهائي</th>
                                <th>تاريخ الإكمال</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCompletions as $enrollment)
                            <tr>
                                <td>{{ $enrollment->user->name }}</td>
                                <td>{{ $enrollment->training->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $enrollment->final_score >= 70 ? 'success' : 'warning' }}">
                                        {{ $enrollment->final_score }}%
                                    </span>
                                </td>
                                <td>{{ $enrollment->updated_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('liaison.create-career-plan', $enrollment->user->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        خطة توجيه
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection