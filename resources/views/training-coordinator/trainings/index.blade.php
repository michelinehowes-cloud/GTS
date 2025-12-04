@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التدريب')
@section('page-title', 'لوحة تحكم منسق التدريب')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="h5 mb-0"><i class="fas fa-graduation-cap me-2"></i> إدارة برامج التدريب</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus me-2"></i>إضافة برنامج تدريب جديد
                        </a>
                        <span class="text-muted fw-bold">عدد البرامج: {{ $trainings->count() }}</span>
                    </div>

                    @if($trainings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">اسم البرنامج</th>
                                        <th style="width: 15%;">النوع</th>
                                        <th style="width: 10%;">الحالة</th>
                                        <th style="width: 15%;">تاريخ البدء</th>
                                        <th style="width: 15%;">تاريخ الانتهاء</th>
                                        <th style="width: 20%;" class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                @foreach($trainings as $training)
                <tr>
                    {{-- تم تصحيح ترتيب الخلايا ليطابق عناوين الجدول --}}
                    <td>
                        <strong class="text-primary">{{ $training->title }}</strong>
                        <br><small class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                    </td>
                    <td>
                        @switch($training->type)
                            @case('workshop') <span class="badge bg-info">ورشة عمل</span> @break
                            @case('course') <span class="badge bg-primary">دورة</span> @break
                            @case('seminar') <span class="badge bg-secondary">ندوة</span> @break
                            @case('internship') <span class="badge bg-success">تدريب عملي</span> @break
                            @default <span class="badge bg-warning">{{ $training->type }}</span>
                        @endswitch
                    </td>
                    <td>
                        <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'inactive' ? 'warning' : 'secondary') }}">
                            {{ $training->status == 'active' ? 'نشط' : ($training->status == 'inactive' ? 'متوقف' : 'مكتمل') }}
                        </span>
                    </td>
                    <td><small>{{ $training->start_date }}</small></td>
                    <td><small>{{ $training->end_date ?? 'غير محدد' }}</small></td>
                    
                   <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="btn btn-info" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('training-coordinator.trainings.edit', $training->id) }}" class="btn btn-warning" title="تعديل البرنامج">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('training-coordinator.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا البرنامج؟')" title="حذف البرنامج">
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
        <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">لا توجد برامج تدريب حتى الآن</h4>
        <p class="text-muted mb-4">يمكنك البدء بإضافة أول برنامج تدريب</p>
        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i>إضافة أول برنامج تدريب
        </a>
    </div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection