@extends("layouts.app")

@section("title", "سجل ومصفوفة الحضور اليومي - " . $training->title)
@section("page-title", "سجل حضور الدورة التدريبية")

@push('styles')
<style>
    /* ====================================
       Attendance Table & Matrix Styles
    ==================================== */
    .attendance-matrix-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .attendance-matrix-table th {
        background: #f8fafc;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 12px 10px;
        text-align: center;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .attendance-matrix-table td {
        padding: 12px 10px;
        vertical-align: middle;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }
    .attendance-matrix-table tbody tr:hover {
        background-color: rgba(241, 245, 249, 0.6);
    }
    .sticky-col-trainee {
        position: sticky;
        right: 0;
        background: #fff;
        z-index: 2;
        text-align: right !important;
        box-shadow: -4px 0 6px -2px rgba(0,0,0,0.04);
        min-width: 220px;
    }
    .attendance-matrix-table tbody tr:hover .sticky-col-trainee {
        background: #f8fafc;
    }
    .attendance-cell-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        position: relative;
    }
    .attendance-cell-btn:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    .cell-present {
        background-color: #d1fae5;
        color: #059669;
        border-color: #a7f3d0;
    }
    .cell-absent {
        background-color: #fee2e2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .cell-future {
        background-color: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: pointer;
    }
    .cell-excused {
        background-color: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .today-column-highlight {
        background-color: #fefce8 !important;
    }

    /* Print Template */
    @media print {
        body * {
            visibility: hidden;
        }
        #print-attendance-sheet, #print-attendance-sheet * {
            visibility: visible;
        }
        #print-attendance-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background: #fff;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@php
    $isAdmin = auth()->user()->role === 'admin';
    $prefix  = $isAdmin ? 'admin' : 'training-coordinator';
@endphp

@section("content")
<div class="container-fluid py-4 no-print">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => $isAdmin ? 'لوحة تحكم المدير' : 'لوحة تحكم منسق التدريب', 'url' => route($prefix . '.dashboard')],
            ['label' => 'إدارة برامج التدريب', 'url' => route($prefix . '.trainings')],
            ['label' => Str::limit($training->title, 25), 'url' => route($prefix . '.trainings.show', $training->id)],
            ['label' => 'سجل ومصفوفة الحضور اليومي', 'active' => true],
        ]
    ])

    {{-- Header Banner --}}
    <div class="card-modern mb-4 p-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%); color: white; border: none;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px; font-size: 1.8rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 text-white">{{ $training->title }}</h3>
                    <div class="text-white-50 small d-flex align-items-center gap-3 flex-wrap">
                        <span><i class="fas fa-building me-1"></i>{{ $training->company->name ?? 'مكتب تدريب الخريجين' }}</span>
                        <span><i class="fas fa-calendar-alt me-1"></i>من {{ $training->start_date ? $training->start_date->format('Y-m-d') : '---' }} إلى {{ $training->end_date ? $training->end_date->format('Y-m-d') : '---' }}</span>
                        <span><i class="fas fa-clock me-1"></i>{{ $totalDays }} أيام تدريبية</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route($prefix . '.trainings.scanner', $training->id) }}" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);">
                    <i class="fas fa-qrcode me-2"></i> فتح ماسح الـ QR
                </a>
                <button onclick="window.print()" class="btn btn-light fw-bold px-3 py-2" style="border-radius: 12px;">
                    <i class="fas fa-print me-1"></i> طباعة الكشف
                </button>
                <a href="{{ route($prefix . '.trainings.attendance.export', $training->id) }}" class="btn btn-outline-light fw-bold px-3 py-2" style="border-radius: 12px;">
                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                </a>
                <a href="{{ route($prefix . '.trainings.show', $training->id) }}" class="btn btn-outline-light" style="border-radius: 12px;">
                    <i class="fas fa-arrow-right me-1"></i> تفاصيل الدورة
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">إجمالي المتدربين المقبولين</div>
                        <div class="fs-3 fw-bold text-primary mb-0">{{ $totalApproved }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">متدرب مؤهل للالتحاق</div>
                    </div>
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">أيام التدريب الإجمالية</div>
                        <div class="fs-3 fw-bold text-info mb-0">{{ $totalDays }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">جلسات تدريبية مقررة</div>
                    </div>
                    <div class="rounded-circle bg-light text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-calendar-day fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">حضور اليوم ({{ now()->format('Y-m-d') }})</div>
                        <div class="fs-3 fw-bold text-success mb-0">
                            <span id="today-attended-count-val">{{ $todayAttendedCount }}</span> 
                            <small class="fs-6 text-muted">/ {{ $totalApproved }}</small>
                        </div>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <span id="today-pct-val">{{ $totalApproved > 0 ? round(($todayAttendedCount / $totalApproved) * 100) : 0 }}</span>% نسبة حضور اليوم
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">متوسط نسبة الحضور العامة</div>
                        <div class="fs-3 fw-bold text-warning mb-0">
                            <span id="overall-attendance-rate-val">{{ $overallAttendanceRate }}</span>%
                        </div>
                        <div class="text-muted small" style="font-size: 0.75rem;">إجمالي حضور جميع الأيام</div>
                    </div>
                    <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Attendance Matrix --}}
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0 text-primary fw-bold">
                    <i class="fas fa-table me-2"></i>مصفوفة الحضور اليومي الشاملة
                </h5>
                <div class="text-muted small mt-1">انقر على أي خلية لتبديل حالة حضور الخريج يدوياً فوراً</div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 small">
                    <span class="d-flex align-items-center"><span class="badge bg-success p-1 rounded-circle me-1" style="width:10px;height:10px;"></span> حاضر</span>
                    <span class="d-flex align-items-center"><span class="badge bg-danger p-1 rounded-circle me-1" style="width:10px;height:10px;"></span> غائب</span>
                    <span class="d-flex align-items-center"><span class="badge bg-info p-1 rounded-circle me-1" style="width:10px;height:10px;"></span> معذور</span>
                </div>
                <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                    <input type="text" id="trainee-search" class="form-control bg-light border-0" placeholder="بحث عن متدرب...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($applications->count() > 0)
                <div class="table-responsive">
                    <table class="attendance-matrix-table table mb-0" id="matrix-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th class="sticky-col-trainee">اسم المتدرب / الخريج</th>
                                <th style="min-width: 110px;">الرقم الجامعي</th>
                                @foreach($trainingDays as $day)
                                    <th class="{{ $day['is_today'] ? 'today-column-highlight' : '' }}" style="min-width: 80px;">
                                        <div class="fw-bold">{{ $day['day_name'] }}</div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">يوم {{ $day['day_number'] }}</div>
                                        <div class="badge {{ $day['is_today'] ? 'bg-primary' : 'bg-light text-dark border' }} p-1" style="font-size: 0.7rem;">
                                            {{ \Carbon\Carbon::parse($day['date'])->format('m/d') }}
                                        </div>
                                    </th>
                                @endforeach
                                <th style="min-width: 90px;">أيام الحضور</th>
                                <th style="min-width: 100px;">نسبة الحضور</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $index => $app)
                                @php
                                    $trainee = $app->user;
                                    $userAttendances = $attendancesByUser[$trainee->id] ?? collect();
                                    $userAttendancesByDate = $userAttendances->keyBy(fn($a) => $a->date->format('Y-m-d'));
                                    $presentCount = $userAttendances->where('status', 'present')->count();
                                    $traineePct = $totalDays > 0 ? round(($presentCount / $totalDays) * 100) : 0;
                                @endphp
                                <tr class="trainee-row" data-name="{{ $trainee->name }}" data-id="{{ $trainee->id }}">
                                    <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td class="sticky-col-trainee">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                                {{ mb_substr($trainee->name, 0, 1) }}
                                            </div>
                                            <div class="lh-sm">
                                                <a href="{{ route('graduate.profile.public', $trainee->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary">
                                                    {{ $trainee->name }}
                                                </a>
                                                <div class="text-muted small" style="font-size: 0.75rem;">
                                                    {{ $trainee->graduateData->faculty ?? 'خريج' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small font-monospace">
                                            {{ $trainee->graduateData->university_id ?? $trainee->graduateData->national_id ?? '---' }}
                                        </span>
                                    </td>

                                    {{-- Daily Check-in Cells --}}
                                    @foreach($trainingDays as $day)
                                        @php
                                            $att = $userAttendancesByDate[$day['date']] ?? null;
                                            $cellStatus = $att ? $att->status : ($day['is_future'] ? 'future' : 'absent');
                                        @endphp
                                        <td class="{{ $day['is_today'] ? 'today-column-highlight' : '' }}">
                                            <button type="button"
                                                    class="attendance-cell-btn cell-{{ $cellStatus }}"
                                                    data-user-id="{{ $trainee->id }}"
                                                    data-date="{{ $day['date'] }}"
                                                    data-status="{{ $att ? $att->status : 'absent' }}"
                                                    onclick="toggleAttendanceCell(this)"
                                                    title="{{ $att && $att->attended_at ? 'حاضر (سجل: ' . $att->attended_at->format('h:i A') . ')' : ($day['is_future'] ? 'جلسة قادمة - انقر للتسجيل' : 'غائب - انقر لتسجيل الحضور') }}">
                                                @if($cellStatus === 'present')
                                                    <i class="fas fa-check"></i>
                                                @elseif($cellStatus === 'excused')
                                                    <i class="fas fa-info"></i>
                                                @elseif($cellStatus === 'late')
                                                    <i class="fas fa-clock"></i>
                                                @elseif($day['is_future'])
                                                    <i class="fas fa-minus small opacity-25"></i>
                                                @else
                                                    <i class="fas fa-times"></i>
                                                @endif
                                            </button>
                                        </td>
                                    @endforeach

                                    {{-- Total Attended Days --}}
                                    <td>
                                        <span class="fw-bold text-dark count-display" id="user-count-{{ $trainee->id }}">
                                            {{ $presentCount }}
                                        </span>
                                        <span class="text-muted small">/ {{ $totalDays }}</span>
                                    </td>

                                    {{-- Attendance Percentage --}}
                                    <td>
                                        <span class="badge {{ $traineePct >= 80 ? 'bg-success' : ($traineePct >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1 font-monospace pct-display" id="user-pct-{{ $trainee->id }}">
                                            {{ $traineePct }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-friends display-4 text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-dark mb-1">لا يوجد متدربون مقبولون حتى الآن</h5>
                    <p class="text-muted small mb-0">عند قبول طلبات المتدربين ستظهر أسماؤهم تلقائياً في هذا الكشف</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Printable Official Attendance Sheet (Visible only on print) --}}
<div id="print-attendance-sheet" class="d-none d-print-block">
    <div style="border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 25%; text-align: right;">
                    <h6 class="fw-bold mb-1">جامعة طرابلس</h6>
                    <small class="d-block">مكتب تدريب الخريجين</small>
                    <small class="d-block">كشف الحضور والغياب الرسمي</small>
                </td>
                <td style="width: 50%; text-align: center;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="height: 60px; object-fit: contain;">
                    <h5 class="fw-bold mt-1 mb-0">{{ $training->title }}</h5>
                    <small class="text-muted">المدرب: {{ $training->instructor_name ?? '---' }} | المقر: {{ $training->location ?? 'جامعة طرابلس' }}</small>
                </td>
                <td style="width: 25%; text-align: left;">
                    <small class="d-block">تاريخ البدء: {{ $training->start_date ? $training->start_date->format('Y-m-d') : '---' }}</small>
                    <small class="d-block">تاريخ الانتهاء: {{ $training->end_date ? $training->end_date->format('Y-m-d') : '---' }}</small>
                    <small class="d-block">تاريخ الطباعة: {{ now()->format('Y-m-d') }}</small>
                </td>
            </tr>
        </table>
    </div>

    <table class="table table-bordered table-sm text-center" style="font-size: 0.82rem;">
        <thead class="table-light">
            <tr>
                <th style="width: 30px;">#</th>
                <th style="text-align: right;">اسم المتدرب</th>
                <th>الرقم الجامعي</th>
                <th>الكلية</th>
                @foreach($trainingDays as $day)
                    <th>يوم {{ $day['day_number'] }}<br><small>{{ \Carbon\Carbon::parse($day['date'])->format('m/d') }}</small></th>
                @endforeach
                <th>الأيام</th>
                <th>النسبة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $index => $app)
                @php
                    $trainee = $app->user;
                    $userAttendances = $attendancesByUser[$trainee->id] ?? collect();
                    $userAttendancesByDate = $userAttendances->keyBy(fn($a) => $a->date->format('Y-m-d'));
                    $presentCount = $userAttendances->where('status', 'present')->count();
                    $traineePct = $totalDays > 0 ? round(($presentCount / $totalDays) * 100) : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;" class="fw-bold">{{ $trainee->name }}</td>
                    <td class="font-monospace">{{ $trainee->graduateData->university_id ?? $trainee->graduateData->national_id ?? '---' }}</td>
                    <td>{{ $trainee->graduateData->faculty ?? '---' }}</td>
                    @foreach($trainingDays as $day)
                        @php
                            $att = $userAttendancesByDate[$day['date']] ?? null;
                        @endphp
                        <td>
                            @if($att && $att->status === 'present')
                                ✔
                            @elseif($att && $att->status === 'excused')
                                ع
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    <td class="fw-bold">{{ $presentCount }} / {{ $totalDays }}</td>
                    <td class="fw-bold">{{ $traineePct }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <table style="width: 100%; text-align: center;">
            <tr>
                <td style="width: 33%;">
                    <strong>منسق التدريب</strong><br><br>
                    <span>....................................</span>
                </td>
                <td style="width: 33%;">
                    <strong>مدرب البرنامج</strong><br><br>
                    <span>....................................</span>
                </td>
                <td style="width: 33%;">
                    <strong>مدير مكتب تدريب الخريجين</strong><br><br>
                    <span>....................................</span>
                </td>
            </tr>
        </table>
    </div>
</div>

@push('scripts')
<script>
const TOGGLE_URL = "{{ route($prefix . '.trainings.attendance.toggle', $training->id) }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

function toggleAttendanceCell(btn) {
    const userId = btn.getAttribute('data-user-id');
    const date   = btn.getAttribute('data-date');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(TOGGLE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({
            user_id: parseInt(userId),
            date: date,
            status: 'toggle'
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            btn.className = 'attendance-cell-btn cell-' + data.new_status;
            btn.setAttribute('data-status', data.new_status);
            
            if (data.new_status === 'present') {
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.title = 'حاضر (سجل: ' + (data.attended_time || 'الآن') + ')';
            } else if (data.new_status === 'excused') {
                btn.innerHTML = '<i class="fas fa-info"></i>';
                btn.title = 'معذور';
            } else {
                btn.innerHTML = '<i class="fas fa-times"></i>';
                btn.title = 'غائب - انقر لتسجيل الحضور';
            }

            // تحديث عدادات هذا المستخدم
            const countEl = document.getElementById('user-count-' + userId);
            const pctEl   = document.getElementById('user-pct-' + userId);
            if (countEl) countEl.textContent = data.user_attended_count;
            if (pctEl) {
                pctEl.textContent = data.user_percentage + '%';
                pctEl.className = 'badge rounded-pill px-3 py-1 font-monospace pct-display ' + 
                    (data.user_percentage >= 80 ? 'bg-success' : (data.user_percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger'));
            }

            // تحديث بطاقات الإحصائيات العلوية في الوقت الفعلي
            if (data.today_attended_count !== undefined) {
                const todayCountEl = document.getElementById('today-attended-count-val');
                const todayPctEl   = document.getElementById('today-pct-val');
                const overallRateEl = document.getElementById('overall-attendance-rate-val');
                if (todayCountEl) todayCountEl.textContent = data.today_attended_count;
                if (todayPctEl) todayPctEl.textContent = data.today_percentage;
                if (overallRateEl) overallRateEl.textContent = data.overall_attendance_rate;
            }
        } else {
            alert(data.message || 'حدث خطأ أثناء التحديث.');
            btn.innerHTML = '<i class="fas fa-times"></i>';
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times"></i>';
        console.error(err);
        alert('فشل الاتصال بالخادم.');
    });
}

// بحث سريع بالاسم
document.getElementById('trainee-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.trainee-row').forEach(row => {
        const name = (row.getAttribute('data-name') || '').toLowerCase();
        row.style.display = name.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
