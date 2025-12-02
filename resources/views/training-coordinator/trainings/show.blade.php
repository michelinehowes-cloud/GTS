@extends('layouts.training-coordinator')

@section('title', 'تفاصيل برنامج التدريب')
@section('page-title', 'إدارة برامج التدريب')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل برنامج التدريب: {{ $training->title }}</h6>
                    <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- المعلومات الأساسية -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3 border-bottom pb-2">المعلومات الأساسية</h5>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th width="35%">اسم البرنامج</th>
                                    <td>{{ $training->title }}</td>
                                </tr>
                                <tr>
                                    <th>الفئة</th>
                                    <td>{{ $training->category ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>نوع البرنامج</th>
                                    <td>
                                        @switch($training->type)
                                            @case('workshop') ورشة عمل @break
                                            @case('course') دورة @break
                                            @case('seminar') ندوة @break
                                            @case('internship') تدريب عملي @break
                                            @default {{ $training->type }}
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>الشركة المنظمة</th>
                                    <td>{{ $training->company->name ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>منسق التدريب</th>
                                    <td>{{ $training->coordinator->name ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>اسم المدرب</th>
                                    <td>{{ $training->instructor_name ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }}">
                                            @if($training->status == 'active') نشط
                                            @elseif($training->status == 'completed') مكتمل
                                            @else غير نشط
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- تفاصيل التوقيت والمكان -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3 border-bottom pb-2">التوقيت والمكان</h5>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th width="35%">المدة</th>
                                    <td>{{ $training->duration }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ البدء</th>
                                    <td>{{ \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الانتهاء</th>
                                    <td>{{ \Carbon\Carbon::parse($training->end_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>المكان</th>
                                    <td>{{ $training->location }}</td>
                                </tr>
                                <tr>
                                    <th>عدد المقاعد</th>
                                    <td>{{ $training->seats }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الإنشاء</th>
                                    <td>{{ $training->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>آخر تحديث</th>
                                    <td>{{ $training->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- الوصف والأهداف -->
                        <div class="col-12">
                            <h5 class="text-primary mb-3 border-bottom pb-2">تفاصيل المحتوى</h5>
                            
                            <div class="mb-4">
                                <h6 class="font-weight-bold">وصف البرنامج:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->description }}
                                </div>
                            </div>

                            @if($training->objectives)
                            <div class="mb-4">
                                <h6 class="font-weight-bold">أهداف البرنامج:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->objectives }}
                                </div>
                            </div>
                            @endif

                            @if($training->requirements)
                            <div class="mb-4">
                                <h6 class="font-weight-bold">متطلبات البرنامج:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->requirements }}
                                </div>
                            </div>
                            @endif

                            @if($training->instructor_qualifications)
                            <div class="mb-4">
                                <h6 class="font-weight-bold">مؤهلات المدرب:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->instructor_qualifications }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4 border-top pt-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('training-coordinator.trainings.edit', $training->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>تعديل البرنامج
                            </a>
                            <form action="{{ route('training-coordinator.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                    <i class="fas fa-trash me-2"></i>حذف البرنامج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection