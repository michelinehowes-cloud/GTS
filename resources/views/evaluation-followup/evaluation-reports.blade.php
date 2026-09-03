@extends('layouts.app')
@section('title', 'تقارير التقييمات الشاملة')
@section('page-title', 'تقارير التقييمات')
@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#1e1b4b 0%,#312e81 40%,#4338ca 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(30,27,75,.35)}
.rpt-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.rpt-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.rpt-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.rpt-badge{background:rgba(196,181,253,.2);border:1px solid rgba(196,181,253,.4);color:#c4b5fd;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.kpi-card{background:#fff;border-radius:16px;padding:22px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);transition:all .3s;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:16px 16px 0 0}
.kpi-card:hover{transform:translateY(-4px);box-shadow:0 10px 36px rgba(0,0,0,.12)}
.kpi-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info small{font-size:.82rem;color:#64748b;font-weight:500}
.kc-indigo{--accent:#6366f1;--icon-bg:#eef2ff;--icon-color:#6366f1}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.kc-purple{--accent:#8b5cf6;--icon-bg:#f5f3ff;--icon-color:#8b5cf6}
.chart-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#eef2ff;color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:20px}
.type-progress-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9}
.type-progress-row:last-child{border-bottom:none}
.type-name{font-size:.86rem;font-weight:600;color:#334155;flex:1}
.type-bar-wrap{flex:3;height:8px;background:#f1f5f9;border-radius:20px;overflow:hidden}
.type-bar{height:100%;border-radius:20px;transition:width 1.2s cubic-bezier(.17,.67,.83,.67)}
.type-count{font-size:.82rem;font-weight:700;color:#1e293b;min-width:32px;text-align:end}
.modern-table-wrap{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden}
.modern-table-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.modern-table-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.search-input{padding:8px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:.87rem;outline:none;transition:all .2s;width:220px}
.search-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.modern-table{width:100%;border-collapse:separate;border-spacing:0}
.modern-table thead tr{background:#f8fafc}
.modern-table thead th{padding:12px 16px;font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;white-space:nowrap}
.modern-table tbody tr{transition:background .15s}
.modern-table tbody tr:hover{background:#f8fafc}
.modern-table tbody td{padding:14px 16px;font-size:.88rem;color:#334155;border-bottom:1px solid #f8fafc;vertical-align:middle}
.modern-table tbody tr:last-child td{border-bottom:none}
.score-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:.78rem;font-weight:700}
.score-high{background:#d1fae5;color:#047857}
.score-med{background:#fef3c7;color:#b45309}
.score-low{background:#fee2e2;color:#b91c1c}
.badge-type{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
</style>
@endpush

@section('content')
@php
$totalEvals = $stats['total_evaluations'] ?? 0;
$avgScore = $stats['average_scores'] ?? 0;
$evalsByType = $stats['evaluations_by_type'] ?? [];
$typeLabels = [
    'performance' => 'تقييم الأداء',
    'training' => 'تقييم التدريب',
    'employment' => 'تقييم التوظيف',
    'company' => 'تقييم الشركة',
];
$palette = ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6'];
$maxTypeCount = !empty($evalsByType) ? max(array_values(is_array($evalsByType) ? $evalsByType : $evalsByType->toArray())) : 1;
@endphp

<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير التقييمات الشاملة"
    subtitle="نظرة تحليلية متقدمة على نتائج التقييمات ومعدلات الأداء ومسارات التدريب"
    icon="fas fa-clipboard-check"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'تقارير التقييمات']
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
        <div class="kpi-card kc-indigo">
            <div class="kpi-icon"><i class="fas fa-clipboard-check"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $totalEvals }}">0</h2>
                <small>إجمالي التقييمات</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-green">
            <div class="kpi-icon"><i class="fas fa-star"></i></div>
            <div class="kpi-info">
                <h2>{{ number_format($avgScore, 2) }}<span style="font-size:1rem;color:#64748b;"> / 5</span></h2>
                <small>متوسط الدرجة العامة</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-amber">
            <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ count($evalsByType) }}">0</h2>
                <small>مسارات التقييم المعتمدة</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card kc-purple">
            <div class="kpi-icon"><i class="fas fa-history"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ ($evaluations ?? false) ? $evaluations->count() : 0 }}">0</h2>
                <small>تقييمات حديثة مسجلة</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon"><i class="fas fa-chart-pie"></i></div>توزيع التقييمات حسب المسار</h5>
            </div>
            <div class="chart-body">
                <canvas id="evalTypesChart" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-header">
                <h5><div class="ch-icon"><i class="fas fa-align-right"></i></div>إحصاء المسارات بالتفصيل</h5>
            </div>
            <div class="chart-body">
                @forelse($evalsByType as $type => $count)
                @php
                    $ci = $loop->index % count($palette);
                    $color = $palette[$ci];
                    $w = $maxTypeCount > 0 ? round(($count / $maxTypeCount) * 100) : 0;
                    $lbl = $typeLabels[$type] ?? ucfirst($type);
                @endphp
                <div class="type-progress-row">
                    <span class="type-name">{{ $lbl }}</span>
                    <div class="type-bar-wrap">
                        <div class="type-bar" data-width="{{ $w }}" style="width:0;background:{{ $color }};"></div>
                    </div>
                    <span class="type-count">{{ $count }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>لا توجد تقييمات مصنفة</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modern-table-wrap mb-4">
    <div class="modern-table-header">
        <h5>
            <div class="ch-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-list"></i></div>
            أحدث التقييمات المسجلة
        </h5>
        <input type="text" class="search-input" id="evalSearch" placeholder="🔍 بحث في التقييمات...">
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="evalReportsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>النوع</th>
                    <th>المقيِّم</th>
                    <th>المستفيد / التدريب</th>
                    <th>الدرجة</th>
                    <th>الملاحظات</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations ?? [] as $evaluation)
                @php
                    $sc = $evaluation->average_score ?? 0;
                @endphp
                <tr>
                    <td><span style="font-size:.76rem;color:#94a3b8;font-weight:600;">#{{ $evaluation->id }}</span></td>
                    <td>
                        <span class="badge-type" style="background:#eff6ff;color:#1d4ed8;">
                            {{ $typeLabels[$evaluation->type] ?? ucfirst($evaluation->type ?? 'عام') }}
                        </span>
                    </td>
                    <td><span class="fw-semibold">{{ $evaluation->evaluator->name ?? 'غير محدد' }}</span></td>
                    <td>
                        <div>
                            <div style="font-weight:600;color:#1e293b;">{{ $evaluation->user->name ?? $evaluation->training->title ?? $evaluation->training->name ?? '—' }}</div>
                            @if(isset($evaluation->training) && isset($evaluation->user))
                            <small class="text-muted">{{ $evaluation->training->title ?? $evaluation->training->name ?? '' }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($sc > 0)
                        <span class="score-badge {{ $sc >= 4 ? 'score-high' : ($sc >= 3 ? 'score-med' : 'score-low') }}">
                            <i class="fas fa-star" style="font-size:.65rem;"></i> {{ number_format($sc, 1) }} / 5
                        </span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><span style="font-size:.82rem;color:#64748b;">{{ Str::limit($evaluation->comments ?? 'لا توجد ملاحظات', 45) }}</span></td>
                    <td><span style="font-size:.82rem;color:#64748b;">{{ $evaluation->evaluation_date ? \Carbon\Carbon::parse($evaluation->evaluation_date)->format('Y-m-d') : '—' }}</span></td>
                    <td>
                        @if(($evaluation->status ?? '') == 'completed')
                        <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size:.72rem;">مكتمل</span>
                        @else
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size:.72rem;">مسودة</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        لا توجد تقييمات حديثة مسجلة
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

    document.querySelectorAll('.type-bar').forEach(b => {
        setTimeout(() => { b.style.width = b.dataset.width + '%'; }, 300);
    });

    // Chart
    const typesData = @json($evalsByType ?? []);
    const labelsMap = {
        'performance': 'تقييم الأداء',
        'training': 'تقييم التدريب',
        'employment': 'تقييم التوظيف',
        'company': 'تقييم الشركة'
    };
    const labels = Object.keys(typesData).map(k => labelsMap[k] || k);
    const dataValues = Object.values(typesData);
    const colors = ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6'];

    const ctx = document.getElementById('evalTypesChart');
    if (ctx && dataValues.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors.slice(0, dataValues.length),
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

    // Search filter
    const sInput = document.getElementById('evalSearch');
    if (sInput) {
        sInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#evalReportsTable tbody tr').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
