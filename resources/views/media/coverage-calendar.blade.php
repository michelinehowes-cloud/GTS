@extends('layouts.app')

@section('title', 'تقويم وجدول التغطيات الإعلامية — المركز الإعلامي')
@section('page-title', 'تقويم التغطيات الإعلامية الميدانية')

@push('styles')
<style>
    .cal-stat-card {
        border: none;
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .cal-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- الهيدر الملكي الموحد -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #0d3882 0%, #1e40af 50%, #0369a1 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-15 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 56px; height: 56px; font-size: 1.6rem;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">تقويم وجدول التغطيات الإعلامية</h4>
                        <p class="text-white-50 small mb-0">
                            متابعة مواعيد البرامج التدريبية وجدولة التغطيات الميدانية (فيديو / فوتوغراف / صحافة) دون استهلاك مساحة السيرفر.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('media.live-studio') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm">
                        <i class="fas fa-satellite-dish me-1"></i> استوديو البث الحي
                    </a>
                    <a href="{{ route('media.dashboard') }}" class="btn btn-outline-light rounded-pill px-3 py-2">
                        <i class="fas fa-arrow-right me-1"></i> لوحة الميديا
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات السريعة -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card cal-stat-card bg-white shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي البرامج والفعاليات</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                        <i class="fas fa-graduation-cap fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card cal-stat-card bg-white shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">تمت التغطية والتوثيق</span>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['covered'] }}</h3>
                    </div>
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card cal-stat-card bg-white shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">بانتظار التغطية</span>
                        <h3 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h3>
                    </div>
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card cal-stat-card bg-white shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">لا تتطلب تغطية</span>
                        <h3 class="fw-bold mb-0 text-secondary">{{ $stats['not_required'] }}</h3>
                    </div>
                    <div class="rounded-3 bg-secondary bg-opacity-10 text-secondary p-3">
                        <i class="fas fa-minus-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول التغطيات الإعلامية لبرامج التدريب -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-camera-retro text-primary me-2"></i>جدول مهام التغطية الإعلامية للمحطات التدريبية
            </h6>
            <span class="text-muted small"><i class="fas fa-cloud text-info me-1"></i>حفظ سحابي عبر الروابط الخارجية</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr class="text-secondary small fw-bold">
                        <th class="py-3 px-3 text-center" style="width: 50px;">#</th>
                        <th class="py-3" style="min-width: 260px;">البرنامج التدريبي</th>
                        <th class="py-3" style="min-width: 170px;">المكان / القاعة</th>
                        <th class="py-3 text-center" style="width: 140px;">تاريخ الانطلاق</th>
                        <th class="py-3 text-center" style="width: 140px;">تاريخ الختام</th>
                        <th class="py-3 text-center" style="width: 140px;">حالة التغطية</th>
                        <th class="py-3 text-center" style="width: 160px;">تحديث الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $training)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $training->title }}</div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>{{ $training->instructor_name ?? ($training->trainer->name ?? 'غير محدد') }}
                                </small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1">
                                    <i class="fas fa-map-marker-alt text-warning me-1"></i>{{ $training->location ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="font-monospace small text-primary fw-bold">
                                    {{ $training->start_date ? $training->start_date->format('Y-m-d') : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="font-monospace small text-dark">
                                    {{ $training->end_date ? $training->end_date->format('Y-m-d') : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($training->media_coverage_status === 'covered')
                                    <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-3 py-1.5 fw-bold border border-success border-opacity-25">
                                        <i class="fas fa-check-circle me-1"></i>تمت التغطية
                                    </span>
                                @elseif($training->media_coverage_status === 'pending')
                                    <span class="badge bg-warning bg-opacity-15 text-warning rounded-pill px-3 py-1.5 fw-bold border border-warning border-opacity-25 text-dark">
                                        <i class="fas fa-hourglass-half me-1"></i>بانتظار التغطية
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-15 text-secondary rounded-pill px-3 py-1.5 fw-bold border">
                                        غير مطلوبة
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('media.coverage-calendar.update', $training->id) }}" method="POST" class="d-inline-flex gap-1 m-0">
                                    @csrf
                                    <select name="media_coverage_status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()" style="font-size: 0.78rem;">
                                        <option value="pending" {{ $training->media_coverage_status === 'pending' ? 'selected' : '' }}>⏳ بانتظار التغطية</option>
                                        <option value="covered" {{ $training->media_coverage_status === 'covered' ? 'selected' : '' }}>✅ تمت التغطية</option>
                                        <option value="not_required" {{ $training->media_coverage_status === 'not_required' ? 'selected' : '' }}>⚪ غير مطلوبة</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">لا توجد برامج تدريبية مسجلة حالياً.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
