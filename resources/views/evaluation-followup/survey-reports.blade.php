@extends('layouts.app')
@section('title', 'تقارير الاستبيانات المتقدمة')
@section('page-title', 'تقارير الاستبيانات')
@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#0c4a6e 0%,#075985 45%,#0284c7 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(12,74,110,.35)}
.rpt-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.rpt-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.rpt-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.rpt-badge{background:rgba(186,230,253,.2);border:1px solid rgba(186,230,253,.4);color:#bae6fd;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.kpi-card{background:#fff;border-radius:16px;padding:22px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);transition:all .3s;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:16px 16px 0 0}
.kpi-card:hover{transform:translateY(-4px);box-shadow:0 10px 36px rgba(0,0,0,.12)}
.kpi-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info small{font-size:.82rem;color:#64748b;font-weight:500}
.kc-blue{--accent:#0284c7;--icon-bg:#f0f9ff;--icon-color:#0284c7}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-indigo{--accent:#6366f1;--icon-bg:#eef2ff;--icon-color:#6366f1}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.chart-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#f0f9ff;color:#0284c7;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:20px}
.modern-table-wrap{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden}
.modern-table-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.modern-table-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.search-input{padding:8px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:.87rem;outline:none;transition:all .2s;width:220px}
.search-input:focus{border-color:#0284c7;box-shadow:0 0 0 3px rgba(2,132,199,.15)}
.modern-table{width:100%;border-collapse:separate;border-spacing:0}
.modern-table thead tr{background:#f8fafc}
.modern-table thead th{padding:12px 16px;font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;white-space:nowrap}
.modern-table tbody tr{transition:background .15s}
.modern-table tbody tr:hover{background:#f8fafc}
.modern-table tbody td{padding:14px 16px;font-size:.88rem;color:#334155;border-bottom:1px solid #f8fafc;vertical-align:middle}
.modern-table tbody tr:last-child td{border-bottom:none}
.badge-audience{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.badge-status{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.status-active{background:#d1fae5;color:#047857}
.status-inactive{background:#fee2e2;color:#b91c1c}
.action-icon-btn{width:32px;height:32px;border-radius:8px;background:#f0f9ff;color:#0284c7;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s}
.action-icon-btn:hover{background:#e0f2fe;color:#0369a1;transform:scale(1.05)}
</style>
@endpush

@section('content')
@php
$totalSurveys = $stats['total_surveys'] ?? 0;
$activeSurveys = $stats['active_surveys'] ?? 0;
$totalResponses = $stats['total_responses'] ?? 0;
$avgRate = $stats['average_completion_rate'] ?? 0;

$audienceCounts = [];
foreach($surveys as $s) {
    $aud = $s->target_audience ?? 'all';
    $audienceCounts[$aud] = ($audienceCounts[$aud] ?? 0) + 1;
}
$audLabelsMap = [
    'graduates' => 'الخريجين',
    'companies' => 'الشركات',
    'training_coordinators' => 'منسقي التدريب',
    'all' => 'الكل'
];
@endphp

<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير الاستبيانات المتقدمة"
    subtitle="إحصائيات تفصيلية ومؤشرات استجابة الفئات المستهدفة للاستبيانات الميدانية"
    icon="fas fa-poll"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'تقارير الاستبيانات']
    ]"
    badge="التقييم والمتابعة"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير</span>
    </button>
</x-page-hero>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-blue">
            <div class="kpi-icon"><i class="fas fa-poll"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $totalSurveys }}">0</h2>
                <small>إجمالي الاستبيانات</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-green">
            <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $activeSurveys }}">0</h2>
                <small>استبيانات نشطة حالياً</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-indigo">
            <div class="kpi-icon"><i class="fas fa-comments"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $totalResponses }}">0</h2>
                <small>إجمالي الردود المستلمة</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-amber">
            <div class="kpi-icon"><i class="fas fa-chart-pie"></i></div>
            <div class="kpi-info">
                <h2>{{ number_format($avgRate, 1) }}<span style="font-size:1rem;color:#64748b;">%</span></h2>
                <small>متوسط نسبة الإكمال</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon"><i class="fas fa-chart-pie"></i></div>الجمهور المستهدف للاستبيانات</h5>
            </div>
            <div class="chart-body">
                <canvas id="audienceChart" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon"><i class="fas fa-chart-bar"></i></div>أعلى الاستبيانات تفاعلاً (عدد الردود)</h5>
            </div>
            <div class="chart-body">
                <canvas id="responsesChart" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="modern-table-wrap mb-4">
    <div class="modern-table-header">
        <h5>
            <div class="ch-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-list"></i></div>
            قائمة الاستبيانات والنتائج
        </h5>
        <input type="text" class="search-input" id="surveySearch" placeholder="🔍 بحث في الاستبيانات...">
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="surveyTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>عنوان الاستبيان</th>
                    <th>الجمهور المستهدف</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>عدد الردود</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveys as $survey)
                @php
                    $aud = $survey->target_audience ?? 'all';
                    $respCount = $survey->responses ? $survey->responses->count() : 0;
                @endphp
                <tr>
                    <td><span style="font-size:.76rem;color:#94a3b8;font-weight:600;">#{{ $loop->iteration }}</span></td>
                    <td>
                        <div style="font-weight:600;color:#1e293b;">{{ $survey->title }}</div>
                        <small class="text-muted">{{ Str::limit($survey->description ?? '', 50) }}</small>
                    </td>
                    <td>
                        @switch($aud)
                            @case('graduates')
                                <span class="badge-audience" style="background:#eff6ff;color:#1d4ed8;">الخريجين</span>
                                @break
                            @case('companies')
                                <span class="badge-audience" style="background:#ecfdf5;color:#047857;">الشركات</span>
                                @break
                            @case('training_coordinators')
                                <span class="badge-audience" style="background:#f5f3ff;color:#7c3aed;">منسقي التدريب</span>
                                @break
                            @default
                                <span class="badge-audience" style="background:#f1f5f9;color:#475569;">الكل</span>
                        @endswitch
                    </td>
                    <td>
                        @if($survey->is_active)
                            <span class="badge-status status-active"><i class="fas fa-circle" style="font-size:.45rem;"></i> نشط</span>
                        @else
                            <span class="badge-status status-inactive">غير نشط</span>
                        @endif
                    </td>
                    <td><span style="font-size:.82rem;color:#64748b;">{{ $survey->created_at ? $survey->created_at->format('Y-m-d') : '—' }}</span></td>
                    <td>
                        <span class="fw-bold text-primary px-2 py-1 rounded" style="background:#eff6ff;font-size:.85rem;">
                            {{ $respCount }} رد
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('evaluation-followup.surveys.show', $survey->id) }}" class="action-icon-btn" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        لا توجد استبيانات مسجلة
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
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseInt(el.dataset.target) || 0;
        let cur = 0, step = Math.max(1, Math.ceil(target / 40));
        const t = setInterval(() => {
            cur = Math.min(cur + step, target);
            el.textContent = cur.toLocaleString('en-US');
            if (cur >= target) clearInterval(t);
        }, 25);
    });

    // Audience Chart
    const audData = @json($audienceCounts);
    const audLabelsMap = @json($audLabelsMap);
    const audLabels = Object.keys(audData).map(k => audLabelsMap[k] || k);
    const audValues = Object.values(audData);
    const audColors = ['#0284c7', '#10b981', '#6366f1', '#f59e0b'];

    const ctx1 = document.getElementById('audienceChart');
    if (ctx1 && audValues.length > 0) {
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: audLabels,
                datasets: [{
                    data: audValues,
                    backgroundColor: audColors.slice(0, audValues.length),
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '66%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, boxWidth: 10, usePointStyle: true, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Responses Chart (Top 6 surveys by responses)
    @php
    $topSurveys = $surveys->sortByDesc(function($s){ return $s->responses ? $s->responses->count() : 0; })->take(6);
    $topTitles = $topSurveys->pluck('title')->map(function($t){ return \Illuminate\Support\Str::limit($t, 20); })->values();
    $topCounts = $topSurveys->map(function($s){ return $s->responses ? $s->responses->count() : 0; })->values();
    @endphp

    const sTitles = @json($topTitles);
    const sCounts = @json($topCounts);

    const ctx2 = document.getElementById('responsesChart');
    if (ctx2 && sTitles.length > 0) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: sTitles,
                datasets: [{
                    label: 'عدد الردود',
                    data: sCounts,
                    backgroundColor: '#0284c7',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } }
                }
            }
        });
    }

    // Search Filter
    const sInput = document.getElementById('surveySearch');
    if (sInput) {
        sInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#surveyTable tbody tr').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
