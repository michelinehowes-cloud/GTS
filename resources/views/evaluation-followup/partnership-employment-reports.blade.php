@extends('layouts.app')
@section('title', 'تقارير الشراكات والتوظيف المتقدمة')
@section('page-title', 'تقارير الشراكات والتوظيف')
@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#1d4ed8 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(15,23,42,.4)}
.rpt-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.04);border-radius:50%}
.rpt-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.rpt-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.rpt-badge{background:rgba(147,197,253,.2);border:1px solid rgba(147,197,253,.4);color:#93c5fd;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.kpi-card{background:#fff;border-radius:16px;padding:22px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);transition:all .3s;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:16px 16px 0 0}
.kpi-card:hover{transform:translateY(-4px);box-shadow:0 10px 36px rgba(0,0,0,.12)}
.kpi-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info small{font-size:.82rem;color:#64748b;font-weight:500}
.kc-blue{--accent:#3b82f6;--icon-bg:#eff6ff;--icon-color:#3b82f6}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.kc-indigo{--accent:#6366f1;--icon-bg:#eef2ff;--icon-color:#6366f1}
.kc-teal{--accent:#14b8a6;--icon-bg:#f0fdfa;--icon-color:#14b8a6}
.kc-pink{--accent:#ec4899;--icon-bg:#fdf2f8;--icon-color:#ec4899}
.chart-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:20px}
.partner-row{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f1f5f9}
.partner-row:last-child{border-bottom:none}
.partner-icon{width:42px;height:42px;border-radius:12px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
.partner-info h6{font-size:.88rem;font-weight:600;color:#1e293b;margin:0}
.partner-info small{font-size:.76rem;color:#94a3b8}
.status-dot{width:10px;height:10px;border-radius:50%;background:var(--dc);flex-shrink:0}
.dot-active{--dc:#10b981}
.dot-expired{--dc:#ef4444}
.dot-pending{--dc:#f59e0b}
</style>
@endpush

@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير الشراكات والتوظيف"
    subtitle="تحليل شامل لبيانات الشراكات واتفاقيات التعاون وفرص التوظيف المتاحة للخريجين"
    icon="fas fa-handshake"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'تقارير الشراكات والتوظيف']
    ]"
    badge="التقييم والمتابعة"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير</span>
    </button>
</x-page-hero>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-2"><div class="kpi-card kc-blue"><div class="kpi-icon"><i class="fas fa-handshake"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $totalPartnershipsCount??0 }}">0</h2><small>إجمالي الشراكات</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-green"><div class="kpi-icon"><i class="fas fa-check-circle"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $activePartnershipsCount??0 }}">0</h2><small>شراكات نشطة</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-amber"><div class="kpi-icon"><i class="fas fa-clock"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $expiredPartnershipsCount??0 }}">0</h2><small>منتهية الصلاحية</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-indigo"><div class="kpi-icon"><i class="fas fa-building"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $totalCompaniesCount??0 }}">0</h2><small>إجمالي الشركات</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-teal"><div class="kpi-icon"><i class="fas fa-briefcase"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $totalJobOpportunitiesCount??0 }}">0</h2><small>فرص العمل</small></div></div></div>
    <div class="col-6 col-md-2"><div class="kpi-card kc-pink"><div class="kpi-icon"><i class="fas fa-file-signature"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $documentsCount??0 }}">0</h2><small>وثائق الشراكة</small></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-pie"></i></div>حالات الشراكات</h5></div>
            <div class="chart-body"><canvas id="partnershipStatusChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-tags"></i></div>أنواع الشراكات</h5></div>
            <div class="chart-body"><canvas id="partnershipTypesChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-briefcase"></i></div>فرص العمل حسب النوع</h5></div>
            <div class="chart-body"><canvas id="jobTypesChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-line"></i></div>اتجاه الشراكات الشهري</h5></div>
            <div class="chart-body"><canvas id="partnershipTrendChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-chart-bar"></i></div>الشركات الأكثر شراكةً</h5></div>
            <div class="chart-body"><canvas id="topCompaniesChart" style="max-height:240px;"></canvas></div>
        </div>
    </div>
</div>

@if(!empty($recentPartnerships))
<div class="chart-card mb-4">
    <div class="chart-header"><h5><div class="ch-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-history"></i></div>أحدث الشراكات</h5></div>
    <div class="chart-body">
        @foreach($recentPartnerships as $p)
        <div class="partner-row">
            <div class="partner-icon"><i class="fas fa-handshake"></i></div>
            <div class="partner-info flex-1">
                <h6>{{ $p->title??($p->company->name??'—') }}</h6>
                <small>{{ $p->start_date??'' }} — {{ $p->end_date??'' }}</small>
            </div>
            @php $st=$p->status??'active'; $dotClass=$st==='active'?'dot-active':($st==='expired'?'dot-expired':'dot-pending'); @endphp
            <div class="status-dot {{ $dotClass }}"></div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.querySelectorAll('.counter').forEach(el=>{const t=parseInt(el.dataset.target)||0;let c=0,s=Math.max(1,Math.ceil(t/50));const ti=setInterval(()=>{c=Math.min(c+s,t);el.textContent=c.toLocaleString('en-US');if(c>=t)clearInterval(ti);},25);});
const palette=['#3b82f6','#10b981','#f59e0b','#6366f1','#ef4444','#14b8a6','#ec4899'];
function mkChart(id,type,labels,data,colors,opts={}){const ctx=document.getElementById(id);if(!ctx)return;new Chart(ctx,{type,data:{labels,datasets:[{data,backgroundColor:type==='line'?undefined:colors,borderColor:type==='line'?colors[0]:undefined,borderWidth:type==='bar'?0:2,fill:type==='line'?{target:'origin',above:'rgba(59,130,246,.08)'}:false,tension:.4,pointRadius:4,pointBackgroundColor:type==='line'?colors[0]:undefined,...(type==='bar'?{borderRadius:6,borderSkipped:false}:{})}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:type!=='bar',position:'bottom',labels:{padding:14,boxWidth:10,usePointStyle:true,font:{size:11}}}},scales:type!=='bar'&&type!=='line'?{}:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#f1f5f9'}}}},...opts});}
mkChart('partnershipStatusChart','doughnut',
    ['نشطة','منتهية','معلقة'],
    [{{ $activePartnershipsCount??0 }},{{ $expiredPartnershipsCount??0 }},{{ ($totalPartnershipsCount??0)-($activePartnershipsCount??0)-($expiredPartnershipsCount??0) }}],
    ['#10b981','#ef4444','#f59e0b']);
@php
    $ptLabels = $charts['partnership_types']['labels'] ?? ['تدريب', 'توظيف', 'بحث', 'أخرى'];
    $ptData = $charts['partnership_types']['data'] ?? [0, 0, 0, 0];
    $jtLabels = $charts['job_types']['labels'] ?? ['دوام كامل', 'دوام جزئي', 'عن بعد', 'تعاقد'];
    $jtData = $charts['job_types']['data'] ?? [0, 0, 0, 0];
@endphp
mkChart('partnershipTypesChart','doughnut',
    @json($ptLabels),
    @json($ptData),
    palette);
mkChart('jobTypesChart','doughnut',
    @json($jtLabels),
    @json($jtData),
    ['#3b82f6','#8b5cf6','#14b8a6','#f59e0b']);
mkChart('partnershipTrendChart','line',
    @json($charts['monthly_trend']['labels']??[]),
    @json($charts['monthly_trend']['data']??[]),
    ['#3b82f6']);
mkChart('topCompaniesChart','bar',
    @json($charts['top_companies']['labels']??[]),
    @json($charts['top_companies']['data']??[]),
    palette);
</script>
@endpush
