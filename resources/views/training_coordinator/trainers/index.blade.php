@extends('layouts.app')

@section('title', 'إدارة المدربين')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chalkboard-teacher text-primary"></i>
                إدارة المدربين
            </h1>
            <a href="{{ route('training-coordinator.trainers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة مدرب جديد
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>الاسم</th>
                                <th>التخصص</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th>عدد التدريبات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainers as $trainer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($trainer->photo)
                                                <img src="{{ Storage::url($trainer->photo) }}" alt="{{ $trainer->name }}"
                                                    class="rounded-circle me-2"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            {{ $trainer->name }}
                                        </div>
                                    </td>
                                    <td>{{ $trainer->specialization }}</td>
                                    <td>{{ $trainer->email ?? '-' }}</td>
                                    <td>{{ $trainer->phone ?? '-' }}</td>
                                    <td>{{ $trainer->trainings_count }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('training-coordinator.trainers.show', $trainer->id) }}"
                                                class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('training-coordinator.trainers.edit', $trainer->id) }}"
                                                class="btn btn-sm btn-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('training-coordinator.trainers.destroy', $trainer->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا المدرب؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                                            <p>لا يوجد مدربين مضافين حالياً</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $trainers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection