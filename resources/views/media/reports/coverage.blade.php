@extends('layouts.app')

@section('title', 'تقرير تغطية التدريب: ' . $training->title)

@section('page-title', 'تقرير تغطية التدريب: ' . $training->title)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>تقرير تغطية التدريب
                </h5>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print me-1"></i>طباعة
                    </button>
                    <a href="{{ route('media.trainings.show', $training) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>العودة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- رأس التقرير -->
                <div class="report-header text-center mb-4">
                    <h2 class="text-primary mb-2">جامعة طرابلس</h2>
                    <h4 class="mb-2">مكتب تدريب الخريجين</h4>
                    <h3 class="text-primary mb-4">تقرير تغطية إعلامية</h3>
                    <hr>
                </div>

                <!-- معلومات التدريب -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-center mb-3">معلومات التدريب</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th class="bg-light" width="20%">عنوان التدريب</th>
                                <td colspan="3"><strong>{{ $training->title }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light">الوصف</th>
                                <td colspan="3">{{ $training->description }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">تاريخ البدء</th>
                                <td>{{ $training->start_date->format('d/m/Y') }}</td>
                                <th class="bg-light">تاريخ الانتهاء</th>
                                <td>{{ $training->end_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">الموقع</th>
                                <td>{{ $training->location }}</td>
                                <th class="bg-light">عدد المقاعد</th>
                                <td>{{ $training->seats }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">النوع</th>
                                <td>{{ $training->getTypeArabicAttribute() }}</td>
                                <th class="bg-light">المدة</th>
                                <td>{{ $training->duration }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">المنسق</th>
                                <td>{{ $training->coordinator ? $training->coordinator->name : 'غير محدد' }}</td>
                                <th class="bg-light">الحالة</th>
                                <td>
                                    <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }}">
                                        {{ $training->getStatusArabicAttribute() }}
                                    </span>
                                </td>
                            </tr>
                            @if($training->company)
                            <tr>
                                <th class="bg-light">الشركة المنفذة</th>
                                <td colspan="3">{{ $training->company->name }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- إحصائيات التغطية -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-center mb-3">إحصائيات التغطية الإعلامية</h4>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h3 class="text-primary">{{ $media->count() }}</h3>
                                        <p class="mb-0">إجمالي الوسائط</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h3 class="text-success">{{ $media->where('file_type', 'image')->count() }}</h3>
                                        <p class="mb-0">صور</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h3 class="text-info">{{ $media->where('file_type', 'video')->count() }}</h3>
                                        <p class="mb-0">فيديوهات</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h3 class="text-warning">{{ $media->where('is_active', true)->count() }}</h3>
                                        <p class="mb-0">وسائط نشطة</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول الوسائط -->
                @if($media->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-center mb-3">تفاصيل الوسائط</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>النوع</th>
                                        <th>الوصف</th>
                                        <th>تاريخ الرفع</th>
                                        <th>الرافع</th>
                                        <th>الحالة</th>
                                        <th>المكان</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($media as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <i class="fas fa-{{ $item->file_type === 'image' ? 'image' : 'video' }} me-1"></i>
                                            {{ $item->file_type === 'image' ? 'صورة' : 'فيديو' }}
                                        </td>
                                        <td>{{ $item->caption ?: 'بدون وصف' }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $item->uploader->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                                {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->is_welcome_page_media)
                                                <span class="badge bg-warning">واجهة الترحيب</span>
                                            @else
                                                <span class="badge bg-info">التدريب</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- معرض الصور (للطباعة) -->
                @if($media->where('file_type', 'image')->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="text-center mb-3">معرض الصور</h4>
                        <div class="row">
                            @foreach($media->where('file_type', 'image')->where('is_active', true) as $image)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <img src="{{ Storage::url($image->file_path) }}" class="card-img-top" alt="{{ $image->caption }}" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <small class="text-muted">{{ $image->caption ?: 'صورة بدون وصف' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- خاتمة التقرير -->
                <div class="row">
                    <div class="col-12">
                        <div class="border-top pt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>تاريخ إنشاء التقرير:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                                    <p><strong>حالة التغطية:</strong>
                                        <span class="badge bg-{{ $training->media_coverage_status == 'covered' ? 'success' : ($training->media_coverage_status == 'pending' ? 'warning' : 'secondary') }} ms-2">
                                            {{ $training->getMediaCoverageStatusText() }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p><strong>أعده:</strong> {{ auth()->user()->name }}</p>
                                    <p><strong>المسمى الوظيفي:</strong> مسؤول الميديا</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .card-header .btn,
    .btn {
        display: none !important;
    }

    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }

    body {
        font-size: 12px;
    }

    .table th,
    .table td {
        padding: 0.5rem;
    }

    .report-header {
        margin-bottom: 2rem;
    }

    .card-body {
        padding: 1rem;
    }
}

.report-header {
    border-bottom: 2px solid #0d6efd;
    padding-bottom: 1rem;
}

.table th {
    background-color: #f8f9fa !important;
    font-weight: bold;
}

.badge {
    font-size: 0.8rem;
}
</style>
@endpush
