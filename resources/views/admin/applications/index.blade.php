@extends('layouts.app')

@section('title', 'إدارة طلبات التدريب')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>إدارة طلبات التدريب</h3>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary fs-6">
                            إجمالي الطلبات: {{ $applications->count() }}
                        </span>
                        <span class="badge bg-warning fs-6">
                            قيد المراجعة: {{ $applications->where('status', 'pending')->count() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($applications->count() > 0)
                       <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>الخريج</th>
                <th>برنامج التدريب</th>
                <th>منسق التدريب</th>
                <th>تاريخ التقديم</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <strong>{{ $application->user->name ?? 'غير محدد' }}</strong>
                    <br><small class="text-muted">{{ $application->user->email ?? 'لا يوجد بريد' }}</small>
                </td>
                <td>
                    <strong>{{ $application->training->title ?? 'غير محدد' }}</strong>
                    <br><small class="text-muted">
                        @if($application->training)
                            @switch($application->training->type)
                                @case('workshop') ورشة عمل @break
                                @case('course') دورة @break
                                @case('seminar') ندوة @break
                                @case('internship') تدريب عملي @break
                                @default {{ $application->training->type }}
                            @endswitch
                        @else
                            نوع غير محدد
                        @endif
                    </small>
                </td>
                <td>
                    {{ $application->training->coordinator->name ?? 'غير محدد' }}
                </td>
                <td>{{ $application->applied_at ? $application->applied_at->format('Y-m-d') : 'غير محدد' }}</td>
                <td>
    @if($application->status == 'pending')
        <span class="badge bg-warning">قيد المراجعة</span>
    @elseif($application->status == 'approved')
        <span class="badge bg-success">مقبول</span>
    @else
        <span class="badge bg-danger">مرفوض</span>
    @endif
</td>
                <td>
    <div class="btn-group" role="group">
        <!-- زر الموافقة -->
        @if($application->status == 'pending')
        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success btn-sm" title="موافقة">
                <i class="fas fa-check"></i> موافقة
            </button>
        </form>
        @endif

        <!-- زر الرفض -->
        @if($application->status == 'pending')
        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" title="رفض">
                <i class="fas fa-times"></i> رفض
            </button>
        </form>
        @endif

        <!-- زر إعادة التعيين (إذا كان مرفوضاً أو مقبولاً) -->
        @if($application->status != 'pending')
        <form action="{{ route('admin.applications.pending', $application->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm" title="إعادة للمراجعة">
                <i class="fas fa-redo"></i> إعادة
            </button>
        </form>
        @endif

        <!-- زر حذف الطلب -->
        <form action="{{ route('applications.destroy', $application->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm" 
                    onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')"
                    title="حذف الطلب">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">لا توجد طلبات تدريب حالياً</h4>
                            <p class="text-muted">سيظهر هنا جميع طلبات الخريجين لبرامج التدريب</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// يمكنك إضافة أي scripts إضافية هنا لاحقاً
console.log('صفحة طلبات التدريب جاهزة');
</script>
@endsection