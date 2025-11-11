@extends('layouts.training-coordinator')

@section('title', 'إدارة طلبات التدريب')
@section('page-title', 'إدارة طلبات التدريب')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تفاصيل برنامج التدريب</h3>
                    <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>معلومات البرنامج</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">اسم البرنامج</th>
                                    <td>{{ $training->title }}</td>
                                </tr>
                                <tr>
                                    <th>نوع البرنامج</th>
                                    <td>
                                        @switch($training->type)
                                            @case('workshop') ورشة عمل @break
                                            @case('course') دورة @break
                                            @case('seminar') ندوة @break
                                            @case('internship') تدريب عملي @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>المدة</th>
                                    <td>{{ $training->duration }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ البدء</th>
                                    <td>{{ $training->start_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الانتهاء</th>
                                    <td>{{ $training->end_date->format('Y-m-d') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>التفاصيل الإضافية</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">المكان</th>
                                    <td>{{ $training->location }}</td>
                                </tr>
                                <tr>
                                    <th>عدد المقاعد</th>
                                    <td>{{ $training->seats }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }}">
                                            {{ $training->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $training->description }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
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
                                <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-secondary">
                                    <i class="fas fa-list me-2"></i>عرض جميع البرامج
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection