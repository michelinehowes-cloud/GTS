@extends('layouts.app')

@section('title', 'تقارير الشراكات والتوظيف')
@section('page-title', 'تقارير الشراكات')

@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#2563eb 100%);border-radius:20px;padding:30px 38px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(15,23,42,.35)}
.rpt-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.rpt-hero h1{font-size:1.75rem;font-weight:800;color:#fff;margin:0}
.rpt-hero p{color:rgba(255,255,255,.8);margin:6px 0 0;font-size:.92rem}
.rpt-badge{background:rgba(191,219,254,.2);border:1px solid rgba(191,219,254,.4);color:#bfdbfe;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.kpi-card{background:#fff;border-radius:18px;padding:22px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 22px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);transition:all .3s ease;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:18px 18px 0 0}
.kpi-card:hover{transform:translateY(-5px);box-shadow:0 12px 36px rgba(0,0,0,.12)}
.kpi-icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.45rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2.1rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info p{font-size:.84rem;color:#64748b;margin:4px 0 0;font-weight:500}
.kc-blue{--accent:#2563eb;--icon-bg:#eff6ff;--icon-color:#2563eb}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-indigo{--accent:#6366f1;--icon-bg:#eef2ff;--icon-color:#6366f1}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.kc-teal{--accent:#14b8a6;--icon-bg:#f0fdfa;--icon-color:#14b8a6}
.kc-purple{--accent:#8b5cf6;--icon-bg:#f5f3ff;--icon-color:#8b5cf6}
.chart-card{background:#fff;border-radius:18px;box-shadow:0 4px 22px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:22px}
.modern-table-wrap{background:#fff;border-radius:18px;box-shadow:0 4px 22px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);overflow:hidden}
.modern-table-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.modern-table-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.search-input{padding:8px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:.87rem;outline:none;transition:all .2s;width:220px}
.search-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
.modern-table{width:100%;border-collapse:separate;border-spacing:0}
.modern-table thead tr{background:#f8fafc}
.modern-table thead th{padding:12px 16px;font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;white-space:nowrap}
.modern-table tbody tr{transition:background .15s}
.modern-table tbody tr:hover{background:#f8fafc}
.modern-table tbody td{padding:14px 16px;font-size:.88rem;color:#334155;border-bottom:1px solid #f8fafc;vertical-align:middle}
.modern-table tbody tr:last-child td{border-bottom:none}
.badge-status{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.status-active{background:#d1fae5;color:#047857}
.status-review{background:#fef3c7;color:#b45309}
.status-ended{background:#fee2e2;color:#b91c1c}
.badge-type{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:#eff6ff;color:#1d4ed8}
</style>
@endpush

@section('content')
@php
$totalCo = $counts['totalCompanies'] ?? 0;
$activePart = $counts['activePartnerships'] ?? 0;
$approvedCo = $counts['approvedCompanies'] ?? 0;
$totalJobs = $counts['totalOpportunities'] ?? 0;
$openJobs = $counts['openOpportunities'] ?? 0;
$totalDocs = $counts['totalDocuments'] ?? 0;

$typeLabels = [
    'employment' => 'توظيف',
    'training' => 'تدريب',
    'logistic_support' => 'دعم لوجستي',
    'academic' => 'أكاديمي'
];

$statusLabels = [
    'active' => 'نشط',
    'under_review' => 'قيد المراجعة',
    'inactive' => 'غير نشط',
    'suspended' => 'معلق',
    'expired' => 'منتهٍ'
];

$jobTypeLabels = [
    'job' => 'وظيفة شاغرة',
    'training' => 'برنامج تدريب',
    'internship' => 'تدريب تعاوني',
    'full_time' => 'دوام كامل',
    'part_time' => 'دوام جزئي',
    'remote' => 'عن بُعد',
    'contract' => 'تعاقد',
];
@endphp

<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير وإحصائيات الشراكات"
    subtitle="نظرة تحليلية شاملة ومؤشرات حية لشبكة الشركات الشريكة، فرص العمل المتاحة، والاتفاقيات المبرمة"
    icon="fas fa-handshake"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => 'لوحة الشراكات والتوظيف', 'url' => route('partnership.dashboard')],
        ['label' => 'تقارير الشراكات']
    ]"
    badge="إدارة الشراكات والتوظيف"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير</span>
    </button>
</x-page-hero>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-blue">
            <div class="kpi-icon"><i class="fas fa-building"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $totalCo }}">0</h2>
                <p>إجمالي الشركات</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-green">
            <div class="kpi-icon"><i class="fas fa-handshake"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $activePart }}">0</h2>
                <p>شراكات نشطة</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-indigo">
            <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $approvedCo }}">0</h2>
                <p>شركات معتمدة</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-teal">
            <div class="kpi-icon"><i class="fas fa-briefcase"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $totalJobs }}">0</h2>
                <p>إجمالي الفرص</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-amber">
            <div class="kpi-icon"><i class="fas fa-door-open"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $openJobs }}">0</h2>
                <p>فرص عمل مفتوحة</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-purple">
            <div class="kpi-icon"><i class="fas fa-file-contract"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $totalDocs }}">0</h2>
                <p>وثائق واتفاقيات</p>
            </div>
        </div>
    </div>
</div>

{{-- Charts Grid --}}
<div class="row g-4 mb-4">
    {{-- Chart 1: Types --}}
    <div class="col-lg-6 col-xl-3">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon"><i class="fas fa-tags"></i></div>أنواع الشراكات</h5>
            </div>
            <div class="chart-body">
                <canvas id="typesChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 2: Statuses --}}
    <div class="col-lg-6 col-xl-3">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon" style="background:#ecfdf5;color:#10b981;"><i class="fas fa-check-double"></i></div>حالات الشراكة</h5>
            </div>
            <div class="chart-body">
                <canvas id="statusChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 3: Job Types --}}
    <div class="col-lg-6 col-xl-3">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon" style="background:#fffbeb;color:#f59e0b;"><i class="fas fa-clock"></i></div>فرص العمل (النوع)</h5>
            </div>
            <div class="chart-body">
                <canvas id="jobsChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 4: Top Industries --}}
    <div class="col-lg-6 col-xl-3">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon" style="background:#f5f3ff;color:#8b5cf6;"><i class="fas fa-industry"></i></div>مجالات الشركات</h5>
            </div>
            <div class="chart-body">
                <canvas id="industryChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Partner Companies Table --}}
<div class="modern-table-wrap mb-4">
    <div class="modern-table-header">
        <h5>
            <div class="ch-icon"><i class="fas fa-building"></i></div>
            أحدث الشركات الشريكة
        </h5>
        <div class="d-flex align-items-center gap-2">
            <input type="text" class="search-input" id="tableSearch" placeholder="🔍 بحث في الشركات...">
            <a href="{{ route('partnership.companies') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                عرض الدليل كاملاً
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="companiesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الشركة</th>
                    <th>مجال العمل</th>
                    <th>نوع الشراكة</th>
                    <th>حالة الاعتماد</th>
                    <th>حالة الشراكة</th>
                    <th>تاريخ التسجيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCompanies ?? [] as $company)
                <tr>
                    <td><span style="font-size:.76rem;color:#94a3b8;font-weight:600;">#{{ $loop->iteration }}</span></td>
                    <td>
                        <div class="fw-bold text-dark">{{ $company->name }}</div>
                        <small class="text-muted">{{ $company->email ?? $company->phone ?? '' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size:.78rem;">
                            {{ $company->industry ?? 'عام' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-type">
                            {{ $typeLabels[$company->partnership_type] ?? ($company->partnership_type ?? 'عام') }}
                        </span>
                    </td>
                    <td>
                        @if($company->is_approved)
                            <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size:.72rem;">
                                <i class="fas fa-check me-1"></i>معتمد
                            </span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size:.72rem;">
                                <i class="fas fa-clock me-1"></i>بانتظار الاعتماد
                            </span>
                        @endif
                    </td>
                    <td>
                        @php $st = $company->partnership_status ?? 'under_review'; @endphp
                        @if($st === 'active')
                            <span class="badge-status status-active"><i class="fas fa-circle" style="font-size:.45rem;"></i> نشطة</span>
                        @elseif($st === 'under_review')
                            <span class="badge-status status-review">تحت المراجعة</span>
                        @else
                            <span class="badge-status status-ended">غير نشطة</span>
                        @endif
                    </td>
                    <td><span style="font-size:.82rem;color:#64748b;">{{ $company->created_at ? $company->created_at->format('Y/m/d') : '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        لا توجد شركات شريكة مسجلة بعد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // KPI Counters
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseInt(el.dataset.target) || 0;
        let cur = 0, step = Math.max(1, Math.ceil(target / 40));
        const t = setInterval(() => {
            cur = Math.min(cur + step, target);
            el.textContent = cur.toLocaleString('en-US');
            if (cur >= target) clearInterval(t);
        }, 25);
    });

    const palette = ['#2563eb', '#10b981', '#f59e0b', '#6366f1', '#8b5cf6', '#14b8a6'];

    // Types Chart
    @php
    $typesData = $partnershipStats['byType'] ?? collect();
    $typeChartLabels = $typesData->pluck('partnership_type')->map(fn($t) => $typeLabels[$t] ?? $t)->values();
    $typeChartCounts = $typesData->pluck('count')->values();
    @endphp
    const ctx1 = document.getElementById('typesChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: @json($typeChartLabels),
                datasets: [{
                    data: @json($typeChartCounts),
                    backgroundColor: palette,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '66%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } }
            }
        });
    }

    // Status Chart
    @php
    $statusData = $partnershipStats['byStatus'] ?? collect();
    $statusChartLabels = $statusData->pluck('partnership_status')->map(fn($s) => $statusLabels[$s] ?? $s)->values();
    $statusChartCounts = $statusData->pluck('count')->values();
    @endphp
    const ctx2 = document.getElementById('statusChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: @json($statusChartLabels),
                datasets: [{
                    data: @json($statusChartCounts),
                    backgroundColor: ['#10b981', '#f59e0b', '#64748b', '#ef4444', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '66%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } }
            }
        });
    }

    // Job Types Chart
    @php
    $jobTypeData = $opportunityStats['byType'] ?? collect();
    $jobChartLabels = $jobTypeData->pluck('type')->map(fn($t) => $jobTypeLabels[$t] ?? $t)->values();
    $jobChartCounts = $jobTypeData->pluck('count')->values();
    @endphp
    const ctx3 = document.getElementById('jobsChart');
    if (ctx3) {
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: @json($jobChartLabels),
                datasets: [{
                    label: 'عدد الفرص',
                    data: @json($jobChartCounts),
                    backgroundColor: '#2563eb',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } } }
            }
        });
    }

    // Industry Chart
    @php
    $indData = $partnershipStats['byIndustry'] ?? collect();
    $indChartLabels = $indData->pluck('industry')->values();
    $indChartCounts = $indData->pluck('count')->values();
    @endphp
    const ctx4 = document.getElementById('industryChart');
    if (ctx4) {
        new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: @json($indChartLabels),
                datasets: [{
                    label: 'عدد الشركات',
                    data: @json($indChartCounts),
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } } }
            }
        });
    }

    // Search
    const searchInp = document.getElementById('tableSearch');
    if (searchInp) {
        searchInp.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#companiesTable tbody tr').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
