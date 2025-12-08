@extends('layouts.app')

@section('title', 'التدريبات')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'التدريبات', 'active' => true],
        ]
    ])

    <div class="row">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-graduation-cap me-2"></i>قائمة برامج التدريب
                    </h5>
                    <a href="{{ route('trainings.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-plus me-2"></i>إضافة تدريب جديد
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 border-0">العنوان</th>
                                    <th class="py-3 border-0">الفئة</th>
                                    <th class="py-3 border-0">تاريخ البداية</th>
                                    <th class="py-3 border-0">تاريخ النهاية</th>
                                    <th class="py-3 border-0">الحالة</th>
                                    <th class="py-3 border-0 text-end">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainings as $training)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $training->title }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $training->category }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $training->start_date }}
                                        </td>
                                        <td class="text-muted small">
                                            <i class="far fa-calendar-check me-1"></i>
                                            {{ $training->end_date }}
                                        </td>
                                        <td>
                                            @if($training->status == 'active')
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1 small"></i>نشط
                                                </span>
                                            @elseif($training->status == 'inactive')
                                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                                    <i class="fas fa-pause-circle me-1 small"></i>متوقف
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                                    {{ $training->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('trainings.show', $training) }}" class="btn btn-sm btn-outline-info-modern" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('trainings.edit', $training) }}" class="btn btn-sm btn-outline-warning-modern" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('trainings.destroy', $training) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger-modern" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا التدريب؟')" title="حذف">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                                                <p class="text-muted fw-bold">لا توجد برامج تدريب مضافة حالياً</p>
                                                <a href="{{ route('trainings.create') }}" class="btn btn-sm btn-primary-modern mt-2">
                                                    إضافة أول تدريب
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        {{ $trainings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection