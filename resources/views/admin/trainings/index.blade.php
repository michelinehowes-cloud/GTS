@extends('layouts.app')

@section('title', 'إدارة برامج التدريب')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم المدير', 'url' => route('admin.dashboard')],
            ['label' => 'إدارة برامج التدريب', 'active' => true],
        ]
    ])

    <!-- رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-graduation-cap me-2"></i>إدارة برامج التدريب والتأهيل
            </h2>
            <div class="text-muted small mt-1">متابعة كافة البرامج التدريبية المضافة وإدارتها</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-primary-modern">
                <i class="fas fa-clipboard-list me-1"></i> إدارة طلبات التدريب
            </a>
            <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary-modern">
                <i class="fas fa-plus-circle me-1"></i> إضافة برنامج جديد
            </a>
        </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'إجمالي البرامج',
            'value' => $trainings->count(),
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary',
            'description' => 'جميع البرامج المسجلة'
        ])
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'برامج نشطة',
            'value' => $trainings->where('status', 'active')->count(),
            'icon' => 'fas fa-play-circle',
            'color' => 'success',
            'description' => 'متاحة للتسجيل والحضور'
        ])
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'برامج مكتملة',
            'value' => $trainings->where('status', 'completed')->count(),
            'icon' => 'fas fa-check-double',
            'color' => 'info',
            'description' => 'انتهت فترتها التدريبية'
        ])
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'متوقفة / غير نشطة',
            'value' => $trainings->where('status', 'inactive')->count(),
            'icon' => 'fas fa-pause-circle',
            'color' => 'secondary',
            'description' => 'موقوفة مؤقتاً'
        ])
    </div>

    <!-- جدول البرامج التدريبية -->
    <div class="row">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-list me-2"></i>قائمة البرامج والدورات التدريبية
                    </h5>
                    
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                        <input type="text" id="admin-training-search" class="form-control bg-light border-0" placeholder="بحث بالبرنامج أو النوع...">
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($trainings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-3 border-0" style="width: 40px;">#</th>
                                        <th class="py-3 border-0">اسم البرنامج التدريبي</th>
                                        <th class="py-3 border-0">الشركة / الجهة</th>
                                        <th class="py-3 border-0">النوع</th>
                                        <th class="py-3 border-0">الفترة الزمنية</th>
                                        <th class="py-3 border-0 text-center">الحالة</th>
                                        <th class="py-3 border-0 text-center" style="min-width: 170px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trainings as $training)
                                    <tr class="admin-training-row" data-title="{{ $training->title }}" data-type="{{ $training->type_arabic }}">
                                        <td class="px-3 fw-bold text-muted small">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.trainings.show', $training->id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                        {{ $training->title }}
                                                    </a>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 250px; font-size: 0.75rem;">
                                                        {{ $training->description ? Str::limit($training->description, 50) : 'لا يوجد وصف' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark small"><i class="fas fa-building text-muted me-1"></i>{{ $training->company->name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border rounded-pill px-2 py-1">
                                                {{ $training->type_arabic ?? 'تدريب' }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <div><i class="far fa-calendar-alt me-1 text-success"></i>{{ $training->start_date ? $training->start_date->format('Y-m-d') : 'غير محدد' }}</div>
                                            @if($training->end_date)
                                                <div class="mt-1"><i class="far fa-calendar-check me-1 text-danger"></i>{{ $training->end_date->format('Y-m-d') }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($training->status == 'active')
                                                <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">
                                                    <i class="fas fa-check-circle me-1 small"></i>نشط
                                                </span>
                                            @elseif($training->status == 'completed')
                                                <span class="badge bg-light text-info border border-info rounded-pill px-3 py-1">
                                                    <i class="fas fa-flag-checkered me-1 small"></i>مكتمل
                                                </span>
                                            @else
                                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                                                    <i class="fas fa-pause-circle me-1 small"></i>متوقف
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- سجل الحضور اليومي -->
                                                <a href="{{ route('admin.trainings.attendance', $training->id) }}" class="btn btn-outline-warning-modern" title="سجل ومصفوفة الحضور">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>

                                                <!-- ماسح QR -->
                                                @if($training->status == 'active')
                                                <a href="{{ route('admin.trainings.scanner', $training->id) }}" class="btn btn-outline-primary-modern ms-1" title="ماسح الـ QR اليومي">
                                                    <i class="fas fa-qrcode"></i>
                                                </a>
                                                @endif

                                                <!-- عرض التفاصيل -->
                                                <a href="{{ route('admin.trainings.show', $training->id) }}" class="btn btn-outline-info-modern ms-1" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- تعديل -->
                                                <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-outline-primary-modern ms-1" title="تعديل البرنامج">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- حذف -->
                                                <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('هل أنت متأكد من حذف هذا البرنامج التدريبي؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger-modern" title="حذف البرنامج">
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
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-graduation-cap fa-4x text-muted mb-3 opacity-50"></i>
                                <h4 class="text-muted fw-bold">لا توجد برامج تدريب مسجلة حالياً</h4>
                                <p class="text-muted small">ابدأ بإضافة أول برنامج تدريبي ليتمكن الخريجون من التقديم عليه</p>
                                <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary-modern mt-2">
                                    <i class="fas fa-plus-circle me-1"></i> إضافة برنامج جديد
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('admin-training-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.admin-training-row').forEach(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const type = (row.getAttribute('data-type') || '').toLowerCase();
            row.style.display = (title.includes(q) || type.includes(q)) ? '' : 'none';
        });
    });
</script>
@endpush