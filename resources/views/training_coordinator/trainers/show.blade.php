@extends('layouts.app')

@section('title', 'تفاصيل المدرب')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chalkboard-teacher text-primary"></i>
                تفاصيل المدرب
            </h1>
            <div>
                <a href="{{ route('training-coordinator.trainers.edit', $trainer->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('training-coordinator.trainers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للقائمة
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        @if($trainer->photo)
                            <img src="{{ Storage::url($trainer->photo) }}" alt="{{ $trainer->name }}"
                                class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 150px; height: 150px; font-size: 3rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <h4>{{ $trainer->name }}</h4>
                        <p class="text-muted">{{ $trainer->specialization }}</p>

                        @if($trainer->linkedin_url)
                            <a href="{{ $trainer->linkedin_url }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">المعلومات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>البريد الإلكتروني:</strong></div>
                            <div class="col-md-8">{{ $trainer->email ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>الهاتف:</strong></div>
                            <div class="col-md-8">{{ $trainer->phone ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>التخصص:</strong></div>
                            <div class="col-md-8">{{ $trainer->specialization }}</div>
                        </div>
                        @if($trainer->bio)
                            <div class="row">
                                <div class="col-md-4"><strong>النبذة:</strong></div>
                                <div class="col-md-8">{{ $trainer->bio }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">التدريبات المرتبطة ({{ $trainer->trainings->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($trainer->trainings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>عنوان التدريب</th>
                                            <th>النوع</th>
                                            <th>تاريخ البدء</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trainer->trainings as $training)
                                            <tr>
                                                <td>{{ $training->title }}</td>
                                                <td>{{ $training->type_arabic }}</td>
                                                <td>{{ $training->start_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $training->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $training->status_arabic }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">لا توجد تدريبات مرتبطة بهذا المدرب حالياً</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection