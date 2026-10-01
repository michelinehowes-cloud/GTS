@extends('layouts.app')

@section('title', 'إدارة زوار وضيوف المعرض - ' . $fair->title)

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <!-- Header Row -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('job-fair.admin.index') }}" class="text-decoration-none text-muted">إدارة المعارض</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('job-fair.admin.show', $fair->id) }}" class="text-decoration-none text-muted">{{ $fair->title }}</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">سجل وإحصائيات الزوار</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                <i class="fas fa-id-badge text-success me-2"></i>
                إدارة زوار وضيوف المعرض
            </h1>
            <p class="text-muted mb-0">
                {{ $fair->title }} — متابعة تسجيلات الزوار، الحضور عند البوابات، وتصدير التقارير
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('job-fair.admin.visitors.export', $fair->id) }}" class="btn btn-outline-success shadow-sm">
                <i class="fas fa-file-excel me-1"></i> تصدير إكسيل (CSV)
            </a>
            <a href="{{ route('job-fair.public', $fair->id) }}" target="_blank" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-external-link-alt me-1"></i> معاينة صفحة المعرض
            </a>
            <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة لتفاصيل المعرض
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> يرجى مراجعة الأخطاء:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Cards Row -->
    <div class="row g-3 mb-4">
        <!-- إجمالي الزوار -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">إجمالي الزوار المسجلين</span>
                            <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-users fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- نسبة وتأكيد الحضور -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">الحضور الفعلي بالمعرض</span>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-success">{{ number_format($stats['attended']) }}</h3>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold">{{ $stats['attendance_rate'] }}%</span>
                            </div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الباحثون عن عمل والطلاب -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">باحثون عن عمل وطلبة</span>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-info">{{ number_format($stats['job_seekers'] + $stats['students']) }}</h3>
                                <span class="small text-muted">({{ $stats['job_seekers'] }} باحث / {{ $stats['students'] }} طالب)</span>
                            </div>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-graduate fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الشركات والأكاديميون وعائلات الخريجين -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block mb-1">شركات، أكاديميون، وعائلات</span>
                            <h3 class="fw-bold mb-0 text-warning">{{ number_format($stats['company_reps'] + $stats['academics'] + $stats['parents'] + $stats['general']) }}</h3>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Pills -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <span class="text-muted small fw-bold"><i class="fas fa-chart-pie me-1 text-primary"></i>توزيع فئات الزوار:</span>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill"><i class="fas fa-briefcase me-1"></i>باحثين عن عمل: <strong>{{ $stats['job_seekers'] }}</strong></span>
                    <span class="badge bg-light text-success border px-3 py-2 rounded-pill"><i class="fas fa-graduation-cap me-1"></i>طلاب: <strong>{{ $stats['students'] }}</strong></span>
                    <span class="badge bg-light text-purple border px-3 py-2 rounded-pill" style="color:#7e22ce;"><i class="fas fa-building me-1"></i>شركات: <strong>{{ $stats['company_reps'] }}</strong></span>
                    <span class="badge bg-light text-warning border px-3 py-2 rounded-pill"><i class="fas fa-chalkboard-teacher me-1"></i>أكاديميين: <strong>{{ $stats['academics'] }}</strong></span>
                    <span class="badge bg-light text-danger border px-3 py-2 rounded-pill"><i class="fas fa-users me-1"></i>أولياء أمور: <strong>{{ $stats['parents'] }}</strong></span>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill"><i class="fas fa-star me-1"></i>زوار عامين: <strong>{{ $stats['general'] }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('job-fair.admin.visitors.index', $fair->id) }}" class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="ابحث بالاسم، الهاتف، التذكرة، التخصص..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select name="type" class="form-select">
                        <option value="">جميع الفئات</option>
                        <option value="job_seeker" {{ request('type') == 'job_seeker' ? 'selected' : '' }}>باحث عن عمل</option>
                        <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>طالب</option>
                        <option value="company_rep" {{ request('type') == 'company_rep' ? 'selected' : '' }}>ممثل شركة</option>
                        <option value="academic" {{ request('type') == 'academic' ? 'selected' : '' }}>أكاديمي / تدريس</option>
                        <option value="parent" {{ request('type') == 'parent' ? 'selected' : '' }}>ولي أمر</option>
                        <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>زائر عام</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select name="attended" class="form-select">
                        <option value="">حالة الحضور (الكل)</option>
                        <option value="yes" {{ request('attended') == 'yes' ? 'selected' : '' }}>تم الحضور (حضر)</option>
                        <option value="no" {{ request('attended') == 'no' ? 'selected' : '' }}>لم يحضر بعد</option>
                    </select>
                </div>

                @if(isset($cities) && count($cities) > 0)
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="city" class="form-select">
                        <option value="">جميع المدن</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-lg-2 col-md-3 col-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> تصفية</button>
                    @if(request()->hasAny(['search', 'type', 'attended', 'city']))
                        <a href="{{ route('job-fair.admin.visitors.index', $fair->id) }}" class="btn btn-outline-secondary" title="إلغاء الفلاتر"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Visitors Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list-ul text-primary me-2"></i>
                قائمة الزوار والضيوف
                <span class="badge bg-secondary ms-2">{{ $visitors->total() }}</span>
            </h5>
            <small class="text-muted">عرض {{ $visitors->firstItem() ?? 0 }} إلى {{ $visitors->lastItem() ?? 0 }} من أصل {{ $visitors->total() }}</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">التذكرة الرقمية</th>
                        <th>اسم الزائر</th>
                        <th>رقم الهاتف</th>
                        <th>صفة الزائر</th>
                        <th>المؤهل والتخصص</th>
                        <th>الجهة / الكلية والمدينة</th>
                        <th>هدف الزيارة</th>
                        <th class="text-center">حالة الحضور</th>
                        <th class="pe-4 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $visitor)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('job-fair.visitor.ticket', $visitor->ticket_number) }}" target="_blank" class="fw-bold text-decoration-none font-monospace text-primary">
                                <i class="fas fa-qrcode me-1"></i>{{ $visitor->ticket_number }}
                            </a>
                            <div class="small text-muted">{{ $visitor->created_at->format('Y-m-d H:i') }}</div>
                        </td>

                        <td>
                            <div class="fw-bold text-dark">{{ $visitor->name }}</div>
                            @if($visitor->email)
                                <div class="small text-muted"><i class="fas fa-envelope me-1"></i>{{ $visitor->email }}</div>
                            @endif
                        </td>

                        <td dir="ltr" class="text-end">
                            <a href="tel:{{ $visitor->phone }}" class="text-decoration-none text-dark fw-semibold">
                                <i class="fas fa-phone-alt text-success me-1"></i>{{ $visitor->phone }}
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $visitor->phone) }}" target="_blank" class="btn btn-sm btn-link text-success p-0 ms-1" title="محادثة واتساب">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </td>

                        <td>
                            @php
                                $badgeClass = match($visitor->visitor_type) {
                                    'job_seeker' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                    'student' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                    'company_rep' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                                    'academic' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25',
                                    'parent' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                    default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill">
                                {{ $visitor->visitor_type_label }}
                            </span>
                        </td>

                        <td>
                            <div class="fw-semibold text-dark small">{{ $visitor->education_level ?: '—' }}</div>
                            <div class="small text-muted">{{ $visitor->specialization ?: '—' }}</div>
                        </td>

                        <td>
                            <div class="small text-dark fw-semibold">{{ $visitor->organization ?: '—' }}</div>
                            <div class="small text-muted"><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $visitor->city ?: 'طرابلس' }}</div>
                        </td>

                        <td>
                            <span class="small text-muted" title="{{ $visitor->notes }}">
                                {{ $visitor->visit_purpose ?: 'زيارة عامة' }}
                            </span>
                            @if($visitor->notes)
                                <i class="fas fa-info-circle text-info ms-1" data-bs-toggle="tooltip" title="{{ $visitor->notes }}"></i>
                            @endif
                        </td>

                        <td class="text-center">
                            <form action="{{ route('job-fair.admin.visitors.check-in', $visitor->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $visitor->attended ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 fw-bold" title="انقر لتعديل حالة الحضور">
                                    <i class="fas {{ $visitor->attended ? 'fa-check-circle' : 'fa-hourglass-start' }} me-1"></i>
                                    {{ $visitor->attended ? 'حضر' : 'لم يحضر' }}
                                </button>
                            </form>
                            @if($visitor->check_in_at)
                                <div class="text-muted" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($visitor->check_in_at)->format('H:i') }}</div>
                            @endif
                        </td>

                        <td class="pe-4 text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('job-fair.visitor.ticket', $visitor->ticket_number) }}" target="_blank" class="btn btn-outline-primary" title="معاينة التذكرة">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('job-fair.admin.visitors.destroy', $visitor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الزائر؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="حذف الزائر">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-id-badge fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="h6 mb-1">لا يوجد زوار مسجلين حتى الآن مطابقين للبحث</p>
                            <span class="small">يمكن للزوار التسجيل مباشرة عبر الضغط على "تسجيل زائر" من واجهة المعرض العامة</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visitors->hasPages())
        <div class="card-footer bg-white py-3 px-4 d-flex justify-content-center">
            {{ $visitors->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
