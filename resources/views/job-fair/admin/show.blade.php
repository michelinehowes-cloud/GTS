@extends('layouts.app')

@section('title', $fair->title . ' - تفاصيل المعرض')

@push('styles')
<style>
    .job-fair-detail-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%) !important;
        color: #ffffff !important;
        border-radius: 18px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.18);
        position: relative;
        overflow: visible !important;
        border-bottom: 3px solid #f59e0b;
    }

    .fair-logo-badge {
        width: 80px;
        height: 80px;
        border-radius: 14px;
        background: #ffffff;
        padding: 5px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.16);
        object-fit: contain;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.95);
    }

    .fair-actions-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .fair-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.95rem;
        font-size: 0.84rem;
        font-weight: 600;
        border-radius: 50rem;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }
    .fair-btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
    }

    .fair-btn-glass {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(4px);
    }
    .fair-btn-glass:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.5);
    }

    .action-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
    }
    .action-circle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }

    .reg-mobile-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 0.85rem;
        margin-bottom: 0.6rem;
    }

    @media (max-width: 991.98px) {
        .job-fair-detail-hero {
            padding: 1.25rem 1rem;
        }
        .fair-actions-toolbar {
            width: 100%;
            margin-top: 1rem;
            justify-content: flex-start;
        }
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

    <!-- Hero Header مع إبراز الشعار والأزرار الموحدة -->
    <div class="job-fair-detail-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('job-fair.admin.index') }}" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.3);" title="العودة لقائمة المعارض">
                    <i class="fas fa-arrow-right"></i>
                </a>

                <!-- شعار المعرض البارز -->
                @if($fair->banner_image)
                    <img src="{{ asset('storage/' . $fair->banner_image) }}" alt="{{ $fair->title }}" class="fair-logo-badge">
                @else
                    <div class="fair-logo-badge d-flex align-items-center justify-content-center bg-white bg-opacity-20 text-white border-0">
                        <i class="fas fa-store fs-2"></i>
                    </div>
                @endif

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="fw-bold mb-0 fs-4 text-white">
                            {{ $fair->title }}
                        </h2>
                        @php
                            $statusColors = [
                                'draft' => ['bg' => 'bg-secondary', 'label' => 'مسودة'],
                                'published' => ['bg' => 'bg-success', 'label' => 'منشور ومتاح'],
                                'ongoing' => ['bg' => 'bg-warning text-dark', 'label' => 'جارٍ الآن 🟢'],
                                'completed' => ['bg' => 'bg-info text-dark', 'label' => 'منتهي']
                            ];
                            $st = $statusColors[$fair->status] ?? ['bg' => 'bg-light text-dark', 'label' => $fair->status];
                        @endphp
                        <span class="badge {{ $st['bg'] }} rounded-pill px-2.5 py-1 small fw-bold">
                            {{ $st['label'] }}
                        </span>
                    </div>

                    @if($fair->subtitle)
                        <p class="text-white-50 small mb-1 mt-0.5">{{ $fair->subtitle }}</p>
                    @endif

                    <div class="d-flex align-items-center flex-wrap gap-3 text-white-50 small mt-1">
                        <span><i class="fas fa-calendar-alt me-1 text-warning"></i>{{ $fair->event_date->format('Y-m-d') }}</span>
                        @if($fair->location)
                        <span><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $fair->location }}</span>
                        @endif
                        @if($fair->start_time)
                        <span><i class="fas fa-clock me-1 text-info"></i>{{ substr($fair->start_time, 0, 5) }} @if($fair->end_time) - {{ substr($fair->end_time, 0, 5) }} @endif</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات الموحدة والأنيقة -->
            <div class="fair-actions-toolbar">
                <!-- ماسح QR (ذهبي بارز) -->
                <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="fair-btn-action btn btn-warning text-dark shadow-sm">
                    <i class="fas fa-qrcode"></i>
                    <span>ماسح QR</span>
                </a>

                <!-- بث مباشر (أحمر ناعم) -->
                <a href="{{ route('job-fair.admin.live', $fair->id) }}" class="fair-btn-action btn btn-danger text-white shadow-sm" target="_blank" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none;">
                    <i class="fas fa-satellite-dish"></i>
                    <span>بث مباشر</span>
                </a>

                <!-- تعديل بيانات المعرض -->
                <a href="{{ route('job-fair.admin.edit', $fair->id) }}" class="fair-btn-action btn btn-light bg-white text-primary shadow-sm">
                    <i class="fas fa-edit"></i>
                    <span>تعديل</span>
                </a>

                <!-- تصدير Excel -->
                <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>تصدير</span>
                </a>

                <!-- صفحة المعرض العامة -->
                <a href="{{ route('job-fair.public') }}" class="fair-btn-action fair-btn-glass" target="_blank">
                    <i class="fas fa-globe text-info"></i>
                    <span>صفحة عامة</span>
                </a>

                <!-- قائمة تغيير الحالة منسدلة أنيقة -->
                <div class="dropdown d-inline-block position-relative">
                    <button class="fair-btn-action fair-btn-glass dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-sliders-h"></i>
                        <span>الحالة</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-1.5" style="min-width: 190px; z-index: 1060;">
                        @php
                            $statusIcons = [
                                'draft' => ['color' => 'text-secondary', 'icon' => 'fas fa-file-alt'],
                                'published' => ['color' => 'text-success', 'icon' => 'fas fa-check-circle'],
                                'ongoing' => ['color' => 'text-warning', 'icon' => 'fas fa-play-circle'],
                                'completed' => ['color' => 'text-primary', 'icon' => 'fas fa-flag-checkered']
                            ];
                        @endphp
                        @foreach(['draft'=>'مسودة','published'=>'منشور ومتاح','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
                        <li>
                            <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="{{ $val }}">
                                <button type="submit" class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between {{ $fair->status == $val ? 'active fw-bold' : '' }}">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="{{ $statusIcons[$val]['icon'] ?? 'fas fa-circle' }} {{ $fair->status == $val ? 'text-white' : ($statusIcons[$val]['color'] ?? '') }}"></i>
                                        <span>{{ $label }}</span>
                                    </span>
                                    @if($fair->status == $val)
                                        <i class="fas fa-check text-white ms-2"></i>
                                    @endif
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- تصفير الحضور -->
                <form action="{{ route('job-fair.admin.reset-attendance', $fair->id) }}" method="POST" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="fair-btn-action fair-btn-glass text-white-50" onclick="return confirm('تحذير: هل أنت متأكد من رغبتك في إعادة تهيئة سجلات الحضور بالكامل؟')" title="إعادة تعيين حضور المعرض">
                        <i class="fas fa-undo"></i>
                        <span>تصفير</span>
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
                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#addCompanyForm">
                        <i class="fas fa-plus"></i>
                        <span>إضافة شركة</span>
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
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2.5">
                            @if($fc->company && $fc->company->logo)
                                <img src="{{ Storage::url($fc->company->logo) }}" alt="{{ $fc->company->name }}" class="rounded-circle border p-0.5 bg-white shadow-xs" style="width: 38px; height: 38px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light text-primary border d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                    <i class="fas fa-building"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $fc->company->name }}</h6>
                                <div class="d-flex gap-1 flex-wrap mt-1">
                                    @if($fc->booth_number)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">جناح {{ $fc->booth_number }}</span>
                                    @endif
                                    @if($fc->available_positions)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.72rem;">{{ $fc->available_positions }} وظائف</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('job-fair.admin.remove-company', [$fair->id, $fc->company_id]) }}" method="POST" class="m-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-circle-btn text-danger"
                                    onclick="return confirm('هل أنت متأكد من حذف الشركة من المعرض؟')" title="حذف الشركة">
                                <i class="fas fa-trash-alt"></i>
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
