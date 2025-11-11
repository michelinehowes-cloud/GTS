{{-- ملف: resources/views/trainings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'التدريبات')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>قائمة التدريبات</h3>
                    <a href="{{ route('trainings.create') }}" class="btn btn-primary">إضافة تدريب جديد</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>الفئة</th>
                                    <th>تاريخ البداية</th>
                                    <th>تاريخ النهاية</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trainings as $training)
                                    <tr>
                                        <td>{{ $training->title }}</td>
                                        <td>{{ $training->category }}</td>
                                        <td>{{ $training->start_date }}</td>
                                        <td>{{ $training->end_date }}</td>
                                        <td>
                                            <span class="badge bg-{{ $training->status == 'active' ? 'success' : 'warning' }}">
                                                {{ $training->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('trainings.show', $training) }}" class="btn btn-info btn-sm">عرض</a>
                                            <a href="{{ route('trainings.edit', $training) }}" class="btn btn-warning btn-sm">تعديل</a>
                                            <form action="{{ route('trainings.destroy', $training) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $trainings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection