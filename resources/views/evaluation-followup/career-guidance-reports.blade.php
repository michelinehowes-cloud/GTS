@extends('layouts.app')
@section('title', 'تقارير الإرشاد المهني')
@section('page-title', 'تقارير الإرشاد المهني')
@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 50%,#7c3aed 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(76,29,149,.3)}
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
.kc-purple{--accent:#8b5cf6;--icon-bg:#f5f3ff;--icon-color:#8b5cf6}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.kc-blue{--accent:#3b82f6;--icon-bg:#eff6ff;--icon-color:#3b82f6}
.kc-teal{--accent:#14b8a6;--icon-bg:#f0fdfa;--icon-color:#14b8a6}
.kc-pink{--accent:#ec4899;--icon-bg:#fdf2f8;--icon-color:#ec4899}
.chart-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:20px}
/* Gauge CSS */
.gauge-wrap{display:flex;flex-direction:column;align-items:center;padding:20px 0}
.gauge-svg{width:200px;height:120px}
.gauge-val{font-size:2.4rem;font-weight:800;color:#1e293b;text-align:center;margin-top:-10px;line-height:1}
.gauge-lbl{font-size:.85rem;color:#64748b;text-align:center;margin-top:4px}
/* Graduates list */
.grad-row{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f1f5f9}
.grad-row:last-child{border-bottom:none}
.grad-avatar{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#fff;flex-shrink:0;background:var(--gc)}
.grad-info h6{font-size:.88rem;font-weight:600;color:#1e293b;margin:0}
.grad-info span{font-size:.76rem;color:#94a3b8}
.status-badge{padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.status-emp{background:#d1fae5;color:#047857}
.status-seek{background:#fef3c7;color:#b45309}
</style>
@endpush

@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير الإرشاد المهني"
    subtitle="إحصائيات التوظيف والترشيحات والفرص المهنية للخريجين ومتابعة مسارات العمل"
    icon="fas fa-compass"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'تقارير الإرشاد المهني']
    ]"
    badge="التقييم والمتابعة"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير</span>
    </button>
</x-page-hero>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-2"><div class="kpi-card kc-purple"><div class="kpi-icon"><i class="fas fa-user-graduate"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $stats['totalGraduates']??0 }}">0</h2><small>إجمالي الخريجين</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-green"><div class="kpi-icon"><i class="fas fa-briefcase"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $stats['employedGraduates']??0 }}">0</h2><small>موظفون</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-amber"><div class="kpi-icon"><i class="fas fa-search"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $stats['seekingOpportunities']??0 }}">0</h2><small>باحثون</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-blue"><div class="kpi-icon"><i class="fas fa-star"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $stats['activeNominations']??0 }}">0</h2><small>ترشيحات نشطة</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-teal"><div class="kpi-icon"><i class="fas fa-check-circle"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $stats['availableOpportunities']??0 }}">0</h2><small>فرص متاحة</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-pink"><div class="kpi-icon"><i class="fas fa-trophy"></i></div><div class="kpi-info"><h2>{{ $stats['successRate']??0 }}%</h2><small>نجاح الترشيح</small></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-tachometer-alt"></i></div>معدل التوظيف</h5></div>
            <div class="chart-body">
                <div class="gauge-wrap">
                    <svg class="gauge-svg" viewBox="0 0 200 120">
                        <path d="M 20 110 A 90 90 0 0 1 180 110" fill="none" stroke="#f1f5f9" stroke-width="18" stroke-linecap="round"/>
                        <path id="gaugeArc" d="M 20 110 A 90 90 0 0 1 180 110" fill="none" stroke="url(#gaugeGrad)" stroke-width="18" stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="283"/>
                        <defs>
                            <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#10b981"/>
                                <stop offset="100%" stop-color="#3b82f6"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="gauge-val">{{ $stats['employmentRate']??0 }}%</div>
                    <div class="gauge-lbl">معدل توظيف الخريجين</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-pie"></i></div>توزيع حالة الخريجين</h5></div>
            <div class="chart-body"><canvas id="graduatesStatusChart" style="max-height:220px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-bar"></i></div>فرص العمل حسب القطاع</h5></div>
            <div class="chart-body"><canvas id="sectorChart" style="max-height:220px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-line"></i></div>اتجاه التوظيف الشهري</h5></div>
            <div class="chart-body"><canvas id="employmentTrendChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-radar"></i></div>مستوى الترشيحات حسب التخصص</h5></div>
            <div class="chart-body"><canvas id="nominationsChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.querySelectorAll('.counter').forEach(el=>{const t=parseInt(el.dataset.target)||0;let c=0,s=Math.max(1,Math.ceil(t/50));const ti=setInterval(()=>{c=Math.min(c+s,t);el.textContent=c.toLocaleString('en-US');if(c>=t)clearInterval(ti);},25);});
// Gauge animation
const empRate = {{ $stats['employmentRate']??0 }};
const arc = document.getElementById('gaugeArc');
if(arc){
    const total=283;
    const offset=total-(empRate/100)*total;
    setTimeout(()=>{arc.style.transition='stroke-dashoffset 1.5s cubic-bezier(.17,.67,.83,.67)';arc.style.strokeDashoffset=offset;},300);
}
const palette=['#8b5cf6','#10b981','#f59e0b','#3b82f6','#ef4444','#14b8a6'];
function mkChart(id,type,labels,data,colors,opts={}){const ctx=document.getElementById(id);if(!ctx)return;new Chart(ctx,{type,data:{labels,datasets:[{data,backgroundColor:type==='line'?undefined:colors,borderColor:type==='line'?colors[0]:undefined,borderWidth:type==='bar'?0:2,fill:type==='line'?{target:'origin',above:'rgba(139,92,246,.08)'}:false,tension:.4,pointRadius:4,pointBackgroundColor:type==='line'?colors[0]:undefined,...(type==='bar'?{borderRadius:6,borderSkipped:false}:{})}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:type!=='bar',position:'bottom',labels:{padding:14,boxWidth:10,usePointStyle:true,font:{size:11}}}},scales:type!=='bar'&&type!=='line'?{}:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#f1f5f9'}}}},...opts});}
mkChart('graduatesStatusChart','doughnut',
    ['موظفون','باحثون عن عمل','مكملون دراستهم','أخرى'],
    [{{ $stats['employedGraduates']??0 }},{{ $stats['seekingOpportunities']??0 }},0,0],
    ['#10b981','#f59e0b','#3b82f6','#94a3b8']);
@php
    $sectorLabels = $charts['opportunities_by_sector']['labels'] ?? ['قطاع خاص', 'حكومي', 'أكاديمي', 'غير ربحي'];
    $sectorData = $charts['opportunities_by_sector']['data'] ?? [0, 0, 0, 0];
@endphp
mkChart('sectorChart','bar',
    @json($sectorLabels),
    @json($sectorData),
    palette);
mkChart('employmentTrendChart','line',
    @json($charts['employment_trend']['labels']??[]),
    @json($charts['employment_trend']['data']??[]),
    ['#8b5cf6']);
mkChart('nominationsChart','bar',
    @json($charts['nominations_by_specialty']['labels']??[]),
    @json($charts['nominations_by_specialty']['data']??[]),
    palette);
</script>
@endpush
