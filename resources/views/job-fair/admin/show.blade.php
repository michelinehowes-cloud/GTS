@extends('layouts.app')

@section('title', $fair->title . ' - تفاصيل المعرض')

@push('styles')
<style>
    .job-fair-detail-hero {
        background: linear-gradient(135deg, #045db0 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(4, 93, 176, 0.15);
        position: relative;
        overflow: hidden;
    }

    .fair-actions-toolbar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        width: 100%;
        margin-top: 1rem;
    }

    @media (min-width: 992px) {
        .fair-actions-toolbar {
            display: flex;
            flex-wrap: wrap;
            width: auto;
            margin-top: 0;
        }
    }

    @media (max-width: 767.98px) {
        .fair-actions-toolbar {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.4rem;
        }
        .fair-actions-toolbar .btn,
        .fair-actions-toolbar .form-select {
            font-size: 0.78rem !important;
            padding: 0.45rem 0.5rem !important;
            width: 100% !important;
            text-align: center;
            justify-content: center;
        }
    }

    .reg-mobile-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 0.85rem;
        margin-bottom: 0.6rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'إدارة معارض التوظيف', 'url' => route('job-fair.admin.index')],
            ['label' => $fair->title, 'active' => true],
        ]
    ])

    <!-- Hero Header -->
    <div class="job-fair-detail-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('job-fair.admin.index') }}" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h2 class="fw-bold mb-1 fs-4" style="color: #fef08a !important;">
                        <i class="fas fa-store me-2"></i>{{ $fair->title }}
                    </h2>
                    <div class="d-flex align-items-center flex-wrap gap-3 text-white-50 small">
                        <span><i class="fas fa-calendar-alt me-1 text-warning"></i>{{ $fair->event_date->format('d/m/Y') }}</span>
                        @if($fair->location)
                        <span><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $fair->location }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- شبكة الإجراءات السريعة -->
            <div class="fair-actions-toolbar">
                <!-- تغيير الحالة -->
                <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="m-0">
                    @csrf
                    <select name="status" class="form-select form-select-sm rounded-pill fw-bold" style="background: #ffffff; color: #045db0; border: none;" onchange="this.form.submit()">
                        @foreach(['draft'=>'مسودة','published'=>'منشور','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
                        <option value="{{ $val }}" {{ $fair->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                
                <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="btn btn-sm btn-warning rounded-pill fw-bold text-dark d-inline-flex align-items-center justify-content-center" style="background: #fef08a; border: none;">
                    <i class="fas fa-qrcode me-1"></i>ماسح QR
                </a>

                <a href="{{ route('job-fair.admin.live', $fair->id) }}" class="btn btn-sm btn-danger rounded-pill d-inline-flex align-items-center justify-content-center" target="_blank">
                    <i class="fas fa-tv me-1"></i>بث مباشر
                </a>

                <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="btn btn-sm btn-outline-light rounded-pill d-inline-flex align-items-center justify-content-center">
                    <i class="fas fa-file-excel me-1"></i>تصدير
                </a>

                <a href="{{ route('job-fair.public') }}" class="btn btn-sm btn-outline-light rounded-pill d-inline-flex align-items-center justify-content-center" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>صفحة عامة
                </a>

                <form action="{{ route('job-fair.admin.reset-attendance', $fair->id) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill w-100 d-inline-flex align-items-center justify-content-center" onclick="return confirm('هل أنت متأكد من رغبتك في إعادة تهيئة الحضور؟')">
                        <i class="fas fa-undo me-1"></i>تصفير
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- بطاقات الإحصائيات (2x2 على الموبايل و4 على الديسكتوب) -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'خريج مسجل',
            'value' => $stats['total_registered'] ?? 0,
            'icon' => 'fas fa-user-graduate',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'حضروا المعرض',
            'value' => $stats['total_attended'] ?? 0,
            'icon' => 'fas fa-user-check',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'شركة مشاركة',
            'value' => $stats['total_companies'] ?? 0,
            'icon' => 'fas fa-building',
            'color' => 'warning'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'يوم متبقي',
            'value' => $stats['days_remaining'] ?? 0,
            'icon' => 'fas fa-clock',
            'color' => 'info'
        ])
    </div>

    <div class="row g-3 g-md-4">
        <!-- الشركات المشاركة -->
        <div class="col-12 col-lg-5">
            <div class="card-modern h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold mb-0 text-dark fs-6">
                        <i class="fas fa-building me-2 text-primary"></i>الشركات المشاركة
                    </h5>
                    <button class="btn btn-sm btn-primary-modern rounded-pill" data-bs-toggle="collapse" data-bs-target="#addCompanyForm">
                        <i class="fas fa-plus me-1"></i>إضافة شركة
                    </button>
                </div>

                <!-- Add Company Form Collapse -->
                <div class="collapse mb-3" id="addCompanyForm">
                    <form action="{{ route('job-fair.admin.add-company', $fair->id) }}" method="POST"
                          class="p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label-modern small fw-bold">اختر الشركة</label>
                                <select name="company_id" class="form-select form-select-sm" required>
                                    <option value="">-- اختر شركة --</option>
                                    @php $existingIds = $fair->companies->pluck('company_id')->toArray(); @endphp
                                    @foreach(App\Models\Company::whereNotIn('id', $existingIds)->get() as $co)
                                    <option value="{{ $co->id }}">{{ $co->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" name="booth_number" class="form-control form-control-sm" placeholder="رقم الجناح (A1)">
                            </div>
                            <div class="col-6">
                                <input type="number" name="available_positions" class="form-control form-control-sm" placeholder="عدد الوظائف" min="0">
                            </div>
                            <div class="col-12">
                                <textarea name="requirements" class="form-control form-control-sm" rows="2" placeholder="متطلبات التوظيف..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-sm btn-primary-modern w-100 rounded-pill">
                                    <i class="fas fa-check me-1"></i>تأكيد إضافة الشركة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Companies List -->
                @if($fair->companies->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">لا توجد شركات مضافة في هذا المعرض حتى الآن</p>
                @else
                <div class="d-flex flex-column gap-2">
                    @foreach($fair->companies as $fc)
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 1rem;">
                                <i class="fas fa-industry"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $fc->company->name }}</h6>
                                <div class="d-flex gap-1 flex-wrap mt-1">
                                    @if($fc->booth_number)
                                    <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">جناح {{ $fc->booth_number }}</span>
                                    @endif
                                    @if($fc->available_positions)
                                    <span class="badge bg-light text-success border" style="font-size: 0.7rem;">{{ $fc->available_positions }} وظائف</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('job-fair.admin.remove-company', [$fair->id, $fc->company_id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
                                    onclick="return confirm('هل أنت متأكد من حذف الشركة من المعرض؟')" style="width: 32px; height: 32px;" title="حذف">
                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- قائمة الخريجين المسجلين -->
        <div class="col-12 col-lg-7">
            <div class="card-modern h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold mb-0 text-dark fs-6">
                        <i class="fas fa-user-graduate me-2 text-primary"></i>الخريجون المسجلون
                    </h5>
                    <span class="badge bg-primary rounded-pill px-2 py-1">{{ $stats['total_registered'] ?? 0 }}</span>
                </div>

                @if($registrations->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">لا توجد تسجيلات حتى الآن</p>
                @else
                {{-- 🖥️ عرض سطح المكتب --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3 border-0 small fw-bold">الرقم</th>
                                <th class="py-2 border-0 small fw-bold">الاسم</th>
                                <th class="py-2 border-0 small fw-bold">التخصص</th>
                                <th class="py-2 border-0 small fw-bold">الحضور</th>
                                <th class="py-2 border-0 text-center small fw-bold">البطاقة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $reg)
                            <tr>
                                <td class="px-3"><span class="badge bg-light text-primary border" style="font-size: 0.72rem;">{{ $reg->registration_number }}</span></td>
                                <td class="fw-bold text-dark">{{ $reg->graduate->name }}</td>
                                <td><span class="text-muted small">{{ $reg->graduate->major ?? '—' }}</span></td>
                                <td>
                                    @if($reg->attended)
                                    <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;"><i class="fas fa-check me-1"></i>حضر</span>
                                    @else
                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size: 0.7rem;">غائب</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('job-fair.my-ticket', $reg->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" target="_blank" style="font-size: 0.75rem;">
                                        <i class="fas fa-ticket-alt me-1"></i>عرض
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📱 عرض الموبايل --}}
                <div class="d-md-none">
                    @foreach($registrations as $reg)
                    <div class="reg-mobile-card">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark mb-0 fs-6">{{ $reg->graduate->name }}</h6>
                            @if($reg->attended)
                            <span class="badge bg-success text-white rounded-pill" style="font-size: 0.68rem;">حضر</span>
                            @else
                            <span class="badge bg-secondary text-white rounded-pill" style="font-size: 0.68rem;">غائب</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                            <span><i class="fas fa-graduation-cap me-1"></i>{{ $reg->graduate->major ?? '—' }}</span>
                            <a href="{{ route('job-fair.my-ticket', $reg->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" target="_blank" style="font-size: 0.75rem;">
                                <i class="fas fa-ticket-alt me-1"></i>التذكرة
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(method_exists($registrations, 'links'))
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $registrations->links() }}
                    </div>
                @endif
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
