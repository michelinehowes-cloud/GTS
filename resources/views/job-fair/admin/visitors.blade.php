@extends('layouts.app')

@section('title', 'سجل وإدارة زوار المعرض - ' . $fair->title)

@push('styles')
<style>

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

    .pill-filter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.35rem 0.9rem;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.15s;
    }
    .pill-filter:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .pill-filter.active {
        background: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
    }

    .checkin-btn {
        border-radius: 50px;
        padding: 4px 12px;
        font-size: 0.78rem;
        font-weight: 700;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }
    .checkin-btn.attended {
        background: #dcfce7;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .checkin-btn.absent {
        background: #f1f5f9;
        color: #64748b;
        border-color: #cbd5e1;
    }
    .checkin-btn.absent:hover {
        background: #dcfce7;
        color: #15803d;
        border-color: #86efac;
    }

    @media print {
        body * { visibility: hidden; }
        #gatePosterPrintArea, #gatePosterPrintArea * { visibility: visible; }
        #gatePosterPrintArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            padding: 2rem;
            background: #ffffff !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" dir="rtl">

    {{-- الشريط العلوي الموحد مع الشعارات الرسمية المتطابقة مع صفحة المعرض العامة --}}
    @include('job-fair.admin.partials.header', [
        'fair' => $fair,
        'page' => 'visitors',
        'title' => 'سجل وإدارة زوار المعرض',
        'subtitle' => $fair->title . ' — متابعة الحضور عند البوابات، تسجيل الزوار وإصدار التذاكر الرقمية'
    ])

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- ══════════════════════════════════
         بطاقات الإحصائيات (باستخدام stat-card الموحد)
    ══════════════════════════════════ --}}
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'إجمالي الزوار',
            'value' => $stats['total'] ?? 0,
            'icon' => 'fas fa-id-badge',
            'color' => 'primary',
            'description' => 'المسجلين في قاعدة البيانات'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'حضروا المعرض',
            'value' => $stats['attended'] ?? 0,
            'icon' => 'fas fa-user-check',
            'color' => 'success',
            'description' => ($stats['attendance_rate'] ?? 0) . '% نسبة الحضور'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'باحثون عن عمل وطلاب',
            'value' => ($stats['job_seekers'] ?? 0) + ($stats['students'] ?? 0),
            'icon' => 'fas fa-user-graduate',
            'color' => 'info',
            'description' => 'الخريجون والطلبة'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'ممثلو شركات وأعمال',
            'value' => $stats['company_reps'] ?? 0,
            'icon' => 'fas fa-building',
            'color' => 'warning',
            'description' => 'أصحاب العمل والمؤسسات'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-4 col-xl',
            'title' => 'أكاديميون وأولياء أمور',
            'value' => ($stats['academics'] ?? 0) + ($stats['parents'] ?? 0) + ($stats['general'] ?? 0),
            'icon' => 'fas fa-users',
            'color' => 'secondary',
            'description' => 'الضيوف والزوار العامين'
        ])
    </div>

    {{-- ══════════════════════════════════
         صندوق البحث والفلاتر
    ══════════════════════════════════ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('job-fair.admin.visitors.index', $fair->id) }}" id="visitorsFilterForm">
                <div class="row g-2 align-items-center">
                    <!-- حقل البحث -->
                    <div class="col-lg-5 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="ابحث بالاسم، الهاتف، التخصص، أو رقم التذكرة..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- حالة الحضور -->
                    <div class="col-lg-3 col-md-6">
                        <select name="attended" class="form-select" onchange="this.form.submit()">
                            <option value="">جميع حالات الحضور</option>
                            <option value="yes" {{ request('attended') === 'yes' ? 'selected' : '' }}>✅ الحاضرون فقط (Checked-in)</option>
                            <option value="no" {{ request('attended') === 'no' ? 'selected' : '' }}>⏳ لم يحضروا بعد</option>
                        </select>
                    </div>

                    <!-- المدينة -->
                    <div class="col-lg-2 col-md-4">
                        <select name="city" class="form-select" onchange="this.form.submit()">
                            <option value="">كافة المدن</option>
                            @foreach($cities as $c)
                                <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- زر تصفية -->
                    <div class="col-lg-2 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-filter me-1"></i> تصفية
                        </button>
                        @if(request()->hasAny(['search', 'type', 'attended', 'city']))
                            <a href="{{ route('job-fair.admin.visitors.index', $fair->id) }}" class="btn btn-outline-danger" title="إلغاء الفلاتر">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- أزرار الفئات السريعة -->
                <div class="d-flex align-items-center gap-2 flex-wrap mt-3 pt-3 border-top">
                    <span class="text-muted small fw-bold">الفئات:</span>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id] + request()->except('type', 'page')) }}" class="pill-filter {{ !request('type') ? 'active' : '' }}">
                        الكل ({{ $stats['total'] ?? 0 }})
                    </a>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id, 'type' => 'job_seeker'] + request()->except('page', 'type')) }}" class="pill-filter {{ request('type') === 'job_seeker' ? 'active' : '' }}">
                        💼 باحث عن عمل ({{ $stats['job_seekers'] ?? 0 }})
                    </a>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id, 'type' => 'student'] + request()->except('page', 'type')) }}" class="pill-filter {{ request('type') === 'student' ? 'active' : '' }}">
                        🎓 طالب ({{ $stats['students'] ?? 0 }})
                    </a>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id, 'type' => 'company_rep'] + request()->except('page', 'type')) }}" class="pill-filter {{ request('type') === 'company_rep' ? 'active' : '' }}">
                        🏢 ممثل شركة ({{ $stats['company_reps'] ?? 0 }})
                    </a>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id, 'type' => 'academic'] + request()->except('page', 'type')) }}" class="pill-filter {{ request('type') === 'academic' ? 'active' : '' }}">
                        👨‍🏫 أكاديمي ({{ $stats['academics'] ?? 0 }})
                    </a>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id, 'type' => 'parent'] + request()->except('page', 'type')) }}" class="pill-filter {{ request('type') === 'parent' ? 'active' : '' }}">
                        👨‍👩‍👧 ولي أمر ({{ $stats['parents'] ?? 0 }})
                    </a>
                    <a href="{{ route('job-fair.admin.visitors.index', ['fair' => $fair->id, 'type' => 'general'] + request()->except('page', 'type')) }}" class="pill-filter {{ request('type') === 'general' ? 'active' : '' }}">
                        ✨ زائر عام ({{ $stats['general'] ?? 0 }})
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════
         جدول سجل الزوار
    ══════════════════════════════════ --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>رقم التذكرة</th>
                        <th>اسم الزائر</th>
                        <th>الصفة / الفئة</th>
                        <th>الهاتف والبريد</th>
                        <th>التخصص أو الجهة</th>
                        <th>المدينة</th>
                        <th class="text-center">تسجيل الدخول (Check-in)</th>
                        <th class="text-center" style="width: 140px;">الإجراءات والـ QR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $index => $visitor)
                        <tr id="visitor-row-{{ $visitor->id }}">
                            <td class="text-center text-muted small">{{ $visitors->firstItem() + $index }}</td>
                            
                            {{-- رقم التذكرة --}}
                            <td>
                                <span class="badge bg-light text-primary border" onclick="copyText('{{ $visitor->ticket_number }}', 'تم نسخ رقم التذكرة!')" style="cursor: pointer; font-family: monospace;" title="انقر للنسخ">
                                    {{ $visitor->ticket_number }}
                                </span>
                            </td>

                            {{-- اسم الزائر --}}
                            <td>
                                <div class="fw-bold text-dark">{{ $visitor->name }}</div>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $visitor->created_at->diffForHumans() }}</small>
                            </td>

                            {{-- الفئة --}}
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $visitor->visitor_type_label }}
                                </span>
                            </td>

                            {{-- الهاتف والبريد --}}
                            <td>
                                <div dir="ltr" style="text-align: right;">
                                    <a href="tel:{{ $visitor->phone }}" class="text-decoration-none fw-semibold text-primary small">
                                        {{ $visitor->phone }}
                                    </a>
                                </div>
                                @if($visitor->email)
                                    <div class="text-muted small text-truncate" style="max-width: 160px;" dir="ltr">
                                        {{ $visitor->email }}
                                    </div>
                                @endif
                            </td>

                            {{-- التخصص والجهة --}}
                            <td>
                                <div class="small fw-semibold text-dark">{{ $visitor->specialization ?: ($visitor->education_level_label ?: '—') }}</div>
                                @if($visitor->organization)
                                    <small class="text-muted">{{ $visitor->organization }}</small>
                                @endif
                            </td>

                            {{-- المدينة --}}
                            <td>
                                <span class="text-muted small">{{ $visitor->city ?: '—' }}</span>
                            </td>

                            {{-- الحضور عند البوابات --}}
                            <td class="text-center">
                                <button type="button" 
                                        class="checkin-btn {{ $visitor->attended ? 'attended' : 'absent' }}"
                                        onclick="toggleVisitorCheckIn({{ $visitor->id }}, this)">
                                    <i class="fas {{ $visitor->attended ? 'fa-check-circle' : 'fa-circle' }} me-1"></i>
                                    <span>{{ $visitor->attended ? 'حاضر' : 'تسجيل حضور' }}</span>
                                </button>
                                @if($visitor->check_in_at)
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        {{ $visitor->check_in_at->format('h:i A') }}
                                    </div>
                                @endif
                            </td>

                            {{-- الإجراءات والـ QR --}}
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- عرض التذكرة الرقمية -->
                                    <a href="{{ route('job-fair.visitor.ticket', $visitor->ticket_number) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="عرض التذكرة">
                                        <i class="fas fa-id-badge"></i>
                                    </a>

                                    <!-- نسخ رابط التذكرة والـ QR -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyVisitorTicketLink('{{ route('job-fair.visitor.ticket', $visitor->ticket_number) }}', this)" title="نسخ رابط التذكرة والـ QR">
                                        <i class="fas fa-qrcode"></i>
                                    </button>

                                    <!-- حذف السجل -->
                                    <form action="{{ route('job-fair.admin.visitors.destroy', $visitor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف سجل الزائر «{{ $visitor->name }}»؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-user-friends fa-3x mb-3 text-secondary opacity-50"></i>
                                <h5>لا توجد تسجيلات زوار مطابقة</h5>
                                <p class="small text-muted mb-3">يمكنك تسجيل زائر جديد يدوياً أو نشر رمز الـ QR للبوابة.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adminVisitorRegisterModal">
                                    <i class="fas fa-user-plus me-1"></i> تسجيل زائر جديد الآن
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visitors->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">عرض {{ $visitors->firstItem() }} إلى {{ $visitors->lastItem() }} من إجمالي {{ $visitors->total() }} زائر</span>
                <div>{{ $visitors->links() }}</div>
            </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════
     MODAL 1: نموذج تسجيل زائر جديد
     (مزود بشعارات الشركاء: المعرض + المكتب + شركة الواحة)
══════════════════════════════════ --}}
<div class="modal fade" id="adminVisitorRegisterModal" tabindex="-1" aria-labelledby="adminVisitorRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <!-- شريط شعارات الشركاء الثلاثة في أعلى النموذج -->
            <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-3">
                    @if($fair && $fair->logo_url)
                        <img src="{{ $fair->logo_url }}" alt="{{ $fair->title }}" style="height: 44px; max-width: 130px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                    @else
                        <img src="{{ asset('images/job_fair_logo.png') }}" alt="معرض التوظيف" style="height: 44px; max-width: 130px; object-fit: contain;">
                    @endif
                    <div style="width: 1px; height: 32px; background: #cbd5e1;"></div>
                    <img src="{{ asset('images/gto_logo.jpg') }}" alt="مكتب تدريب الخريجين" style="height: 44px; max-width: 130px; object-fit: contain;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                    <div style="width: 1px; height: 32px; background: #cbd5e1;"></div>
                    <img src="{{ asset('images/wahaexpo_logo.png') }}" alt="شركة الواحة للمعارض" style="height: 44px; max-width: 130px; object-fit: contain;">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-header border-0 pb-0 pt-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" id="adminVisitorRegisterModalLabel">
                        <i class="fas fa-id-card text-primary me-2"></i>تسجيل زائر / ضيف رسمي
                    </h5>
                    <p class="text-muted small mb-0">{{ $fair->title }} — إصدار بطاقة رقمية فورية وتذكرة دخول</p>
                </div>
            </div>

            <form id="adminVisitorRegisterForm" action="{{ route('job-fair.visitor.register', $fair->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div id="adminVisitorFormAlert" class="alert d-none mb-3" role="alert"></div>

                    <div class="row g-3">
                        <!-- الاسم الكامل -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الاسم الكامل / الرباعي <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="الاسم الثلاثي أو الرباعي" required>
                        </div>

                        <!-- رقم الهاتف -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم الهاتف / واتساب <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="مثال: 0912345678" required dir="ltr">
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">البريد الإلكتروني <span class="text-muted fw-normal">(اختياري)</span></label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" dir="ltr">
                        </div>

                        <!-- فئة الزائر -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">صفة الزائر / الفئة <span class="text-danger">*</span></label>
                            <select name="visitor_type" class="form-select" required>
                                <option value="" disabled selected>-- اختر صفة الزائر --</option>
                                <option value="job_seeker">💼 خريج باحث عن عمل</option>
                                <option value="student">🎓 طالب جامعي / ثانوي</option>
                                <option value="company_rep">🏢 ممثل شركة / صاحب عمل</option>
                                <option value="academic">👨‍🏫 عضو هيئة تدريس / أكاديمي</option>
                                <option value="parent">👨‍👩‍👧 ولي أمر / عائلة خريج</option>
                                <option value="general">✨ مهتم / زائر عام</option>
                            </select>
                        </div>

                        <!-- المؤهل العلمي -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">المستوى التعليمي</label>
                            <select name="education_level" class="form-select">
                                <option value="" selected>-- غير محدد --</option>
                                <option value="ثانوي">ثانوي أو ما يعادله</option>
                                <option value="دبلوم">دبلوم متوسط / عالي</option>
                                <option value="بكالوريوس">بكالوريوس / ليسانس</option>
                                <option value="ماجستير">ماجستير</option>
                                <option value="دكتوراه">دكتوراه</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>

                        <!-- التخصص -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">التخصص الأكاديمي أو المهني</label>
                            <input type="text" name="specialization" class="form-control" placeholder="مثال: هندسة برمجيات، محاسبة...">
                        </div>

                        <!-- جهة العمل / المؤسسة -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">جهة العمل أو المؤسسة</label>
                            <input type="text" name="organization" class="form-control" placeholder="اسم الكلية أو الشركة">
                        </div>

                        <!-- المدينة -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">المدينة / الإقامة</label>
                            <input type="text" name="city" class="form-control" placeholder="طرابلس، مصراتة، بنغازي...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" id="btnAdminSubmitVisitor" class="btn btn-primary px-4">
                        <i class="fas fa-check-circle me-1"></i> تسجيل وإصدار التذكرة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL 2: رمز الاستجابة السريعة (QR Code) لتسجيل الزوار
     (إمكانية نسخ الرابط، نسخ صورة الـ QR، تحميلها، وطباعة بوستر البوابة)
══════════════════════════════════ --}}
<div class="modal fade" id="registrationQrModal" tabindex="-1" aria-labelledby="registrationQrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            
            <!-- شريط الشعارات الرسمية الثلاثة -->
            <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $fair->logo_url ?? asset('images/job_fair_logo.png') }}" alt="{{ $fair->title }}" style="height: 44px; max-width: 130px; object-fit: contain;" onerror="this.src='{{ asset('images/job_fair_logo.png') }}'">
                    <div style="width: 1px; height: 32px; background: #cbd5e1;"></div>
                    <img src="{{ asset('images/gto_logo.jpg') }}" alt="مكتب التدريب" style="height: 44px; max-width: 130px; object-fit: contain;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                    <div style="width: 1px; height: 32px; background: #cbd5e1;"></div>
                    <img src="{{ asset('images/wahaexpo_logo.png') }}" alt="شركة الواحة" style="height: 44px; max-width: 130px; object-fit: contain;">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center p-4">
                <h5 class="fw-bold text-dark mb-1">رمز الـ QR لتسجيل زوار المعرض</h5>
                <p class="text-muted small mb-3">
                    امسح الرمز بكاميرا الهاتف للوصول الفوري لنموذج تسجيل الحضور الرسمي
                </p>

                <!-- حاوية الـ QR -->
                <div id="qrcodeContainer" class="d-flex justify-content-center p-3 bg-light rounded-3 border mb-3 mx-auto" style="width: fit-content;"></div>

                <!-- حقل الرابط مع زر النسخ -->
                <div class="input-group mb-3" dir="ltr">
                    <input type="text" id="visitorRegPublicUrl" class="form-control bg-light text-center small" value="{{ route('job-fair.public', $fair->id) }}#visitor-register" readonly>
                    <button class="btn btn-primary" type="button" onclick="copyVisitorUrlInput(this)">
                        <i class="fas fa-copy me-1"></i>نسخ الرابط
                    </button>
                </div>

                <!-- أزرار الإجراءات للـ QR -->
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyQrImageToClipboard(this)">
                        <i class="fas fa-clone me-1"></i> نسخ صورة الـ QR
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadQrCodeImage()">
                        <i class="fas fa-download me-1"></i> تحميل الصورة (PNG)
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printGatePoster()">
                        <i class="fas fa-print me-1"></i> طباعة بوستر البوابة (A4)
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     قسم طباعة بوستر البوابة المخفي (A4 Printable Area)
══════════════════════════════════ --}}
<div id="gatePosterPrintArea" style="display: none;">
    <div style="text-align: center; border: 3px double #045db0; padding: 30px; border-radius: 16px; font-family: 'Cairo', sans-serif;">
        <div style="display: flex; justify-content: center; align-items: center; gap: 30px; margin-bottom: 25px;">
            <img src="{{ $fair->logo_url ?? asset('images/job_fair_logo.png') }}" style="height: 70px; max-width: 170px; object-fit: contain;">
            <img src="{{ asset('images/gto_logo.jpg') }}" style="height: 70px; max-width: 170px; object-fit: contain;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
            <img src="{{ asset('images/wahaexpo_logo.png') }}" style="height: 70px; max-width: 170px; object-fit: contain;">
        </div>

        <h2 style="font-size: 26pt; color: #045db0; font-weight: 800; margin-bottom: 10px;">{{ $fair->title }}</h2>
        <h4 style="font-size: 16pt; color: #555; margin-bottom: 25px;">جامعة طرابلس — مكتب تدريب الخريجين</h4>

        <div style="background: #f8fafc; border: 2px dashed #045db0; border-radius: 20px; padding: 25px; display: inline-block; margin-bottom: 25px;">
            <div id="gatePosterQrContainer"></div>
        </div>

        <h3 style="font-size: 20pt; color: #1e293b; margin-bottom: 15px;">
            امسح الرمز للتسجيل الفوري كزائر للمعرض
        </h3>
        <p style="font-size: 13pt; color: #64748b;">
            احصل على بطاقتك وتذكرتك الرقمية فوراً • بالتعاون مع شركة الواحة لتنظيم المعارض والمؤتمرات
        </p>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function() {
    // 1. توليد كود الـ QR لنموذج التسجيل العام
    const regUrl = document.getElementById('visitorRegPublicUrl').value;
    const qrContainer = document.getElementById('qrcodeContainer');

    if (qrContainer && typeof QRCode !== 'undefined') {
        new QRCode(qrContainer, {
            text: regUrl,
            width: 200,
            height: 200,
            colorDark: "#1e293b",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    // 2. تسجيل زائر جديد عبر AJAX
    const adminVForm = document.getElementById('adminVisitorRegisterForm');
    if (adminVForm) {
        adminVForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnAdminSubmitVisitor');
            const alertBox = document.getElementById('adminVisitorFormAlert');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري التسجيل...';
            alertBox.className = 'alert d-none mb-3';

            const formData = new FormData(adminVForm);

            fetch(adminVForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data: data })))
            .then(res => {
                if (res.ok && res.data.success) {
                    alertBox.className = 'alert alert-success d-block mb-3 fw-bold';
                    alertBox.innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + (res.data.message || 'تم تسجيل الزائر بنجاح!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    alertBox.className = 'alert alert-danger d-block mb-3';
                    let errorMsg = res.data.message || 'حدث خطأ أثناء التسجيل، يرجى مراجعة البيانات.';
                    if (res.data.errors) {
                        const list = Object.values(res.data.errors).map(err => `<li>${err[0]}</li>`).join('');
                        errorMsg += `<ul class="mb-0 mt-2 text-start">${list}</ul>`;
                    }
                    alertBox.innerHTML = errorMsg;
                }
            })
            .catch(err => {
                console.error(err);
                adminVForm.submit();
            });
        });
    }
})();

// نسخ نص إلى الحافظة
function copyText(text, successMsg) {
    navigator.clipboard.writeText(text).then(() => {
        showToast(successMsg || 'تم النسخ بنجاح!');
    }).catch(() => {
        prompt('انسخ النص التالي:', text);
    });
}

// نسخ رابط التسجيل العام
function copyVisitorUrlInput(btn) {
    const input = document.getElementById('visitorRegPublicUrl');
    copyText(input.value, 'تم نسخ رابط تسجيل الزوار بنجاح!');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-1"></i>تم النسخ!';
    setTimeout(() => { btn.innerHTML = orig; }, 2000);
}

// نسخ رابط تذكرة الزائر
function copyVisitorTicketLink(url, btn) {
    copyText(url, 'تم نسخ رابط بطاقة الزائر!');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check text-success"></i>';
    setTimeout(() => { btn.innerHTML = orig; }, 2000);
}

// تسجيل / إلغاء حضور الزائر لحظياً (Check-in Toggle)
function toggleVisitorCheckIn(visitorId, btn) {
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch(`/admin/job-fair/visitors/${visitorId}/check-in`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            if (data.attended) {
                btn.className = 'checkin-btn attended';
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> <span>حاضر</span>';
            } else {
                btn.className = 'checkin-btn absent';
                btn.innerHTML = '<i class="fas fa-circle me-1"></i> <span>تسجيل حضور</span>';
            }
            showToast(data.message);
        } else {
            btn.innerHTML = origHtml;
            alert(data.message || 'تعذر تحديث حالة الحضور.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        console.error(err);
        alert('حدث خطأ في الاتصال بالخادم.');
    });
}

// نسخ صورة الـ QR إلى الحافظة
function copyQrImageToClipboard(btn) {
    const qrImg = document.querySelector('#qrcodeContainer img') || document.querySelector('#qrcodeContainer canvas');
    if (!qrImg) return;

    try {
        let canvas = qrImg;
        if (qrImg.tagName.toLowerCase() === 'img') {
            canvas = document.createElement('canvas');
            canvas.width = qrImg.naturalWidth || 200;
            canvas.height = qrImg.naturalHeight || 200;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(qrImg, 0, 0);
        }

        canvas.toBlob(blob => {
            if (navigator.clipboard && navigator.clipboard.write) {
                navigator.clipboard.write([
                    new ClipboardItem({ 'image/png': blob })
                ]).then(() => {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>تم النسخ!';
                    setTimeout(() => { btn.innerHTML = orig; }, 2000);
                    showToast('تم نسخ صورة الـ QR إلى الحافظة بنجاح!');
                }).catch(() => downloadQrCodeImage());
            } else {
                downloadQrCodeImage();
            }
        });
    } catch (e) {
        downloadQrCodeImage();
    }
}

// تحميل صورة الـ QR
function downloadQrCodeImage() {
    const qrImg = document.querySelector('#qrcodeContainer img') || document.querySelector('#qrcodeContainer canvas');
    if (!qrImg) return;

    let src = qrImg.tagName.toLowerCase() === 'canvas' ? qrImg.toDataURL('image/png') : qrImg.src;
    const a = document.createElement('a');
    a.href = src;
    a.download = 'job-fair-visitor-registration-qr.png';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    showToast('جاري تحميل صورة الـ QR...');
}

// طباعة بوستر بوابة المعرض A4
function printGatePoster() {
    const printArea = document.getElementById('gatePosterPrintArea');
    const posterQr = document.getElementById('gatePosterQrContainer');
    posterQr.innerHTML = '';

    const regUrl = document.getElementById('visitorRegPublicUrl').value;
    new QRCode(posterQr, {
        text: regUrl,
        width: 300,
        height: 300,
        colorDark: "#045db0",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    setTimeout(() => {
        printArea.style.display = 'block';
        window.print();
        printArea.style.display = 'none';
    }, 300);
}

// إشعار Toast بسيط
function showToast(msg) {
    const existing = document.getElementById('jfToastNotice');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'jfToastNotice';
    toast.style.cssText = 'position:fixed;bottom:25px;left:50%;transform:translateX(-50%);background:#1e293b;color:#ffffff;padding:10px 20px;border-radius:50px;box-shadow:0 8px 20px rgba(0,0,0,0.3);z-index:99999;font-weight:600;font-size:0.85rem;display:flex;align-items:center;gap:8px;font-family:Cairo,sans-serif;';
    toast.innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + msg;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
</script>
@endpush
