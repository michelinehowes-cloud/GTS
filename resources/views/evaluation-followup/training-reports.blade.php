@extends('layouts.app')
@section('title', 'تقارير التدريب - تقييم ومتابعة')
@section('page-title', 'تقارير التدريب')
@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#065f46 0%,#047857 50%,#059669 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(6,95,70,.3)}
.rpt-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.rpt-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.rpt-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.rpt-badge{background:rgba(167,243,208,.2);border:1px solid rgba(167,243,208,.4);color:#6ee7b7;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.kpi-card{background:#fff;border-radius:16px;padding:22px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);transition:all .3s;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:16px 16px 0 0}
.kpi-card:hover{transform:translateY(-4px);box-shadow:0 10px 36px rgba(0,0,0,.12)}
.kpi-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info small{font-size:.82rem;color:#64748b;font-weight:500}
.kpi-info .trend{font-size:.75rem;font-weight:600;margin-top:2px}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-blue{--accent:#3b82f6;--icon-bg:#eff6ff;--icon-color:#3b82f6}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.kc-red{--accent:#ef4444;--icon-bg:#fef2f2;--icon-color:#ef4444}
.kc-purple{--accent:#8b5cf6;--icon-bg:#f5f3ff;--icon-color:#8b5cf6}
.kc-teal{--accent:#14b8a6;--icon-bg:#f0fdfa;--icon-color:#14b8a6}
.chart-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:20px}
.occ-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9}
.occ-row:last-child{border-bottom:none}
.occ-name{font-size:.85rem;color:#374151;flex:2;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.occ-bar-wrap{flex:3;height:8px;background:#f1f5f9;border-radius:20px;overflow:hidden}
.occ-bar{height:100%;border-radius:20px;background:linear-gradient(90deg,#3b82f6,#8b5cf6);transition:width 1.3s cubic-bezier(.17,.67,.83,.67)}
.occ-pct{font-size:.8rem;font-weight:700;color:#1e293b;min-width:36px;text-align:end}
.insight-card{border-radius:12px;padding:16px 20px;display:flex;align-items:flex-start;gap:14px;margin-bottom:12px}
.insight-card.info{background:#eff6ff;border:1px solid #bfdbfe}
.insight-card.success{background:#ecfdf5;border:1px solid #a7f3d0}
.insight-card.warning{background:#fffbeb;border:1px solid #fde68a}
.insight-card.danger{background:#fef2f2;border:1px solid #fecaca}
.insight-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.9rem}
.insight-card.info .insight-icon{background:#dbeafe;color:#1d4ed8}
.insight-card.success .insight-icon{background:#d1fae5;color:#047857}
.insight-card.warning .insight-icon{background:#fef3c7;color:#b45309}
.insight-card.danger .insight-icon{background:#fee2e2;color:#b91c1c}
.insight-text h6{font-size:.88rem;font-weight:700;color:#1e293b;margin:0 0 3px}
.insight-text p{font-size:.8rem;color:#64748b;margin:0}
.popular-row{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f1f5f9}
.popular-row:last-child{border-bottom:none}
.pop-rank{width:28px;height:28px;border-radius:8px;background:#1a2a6c;color:#fff;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pop-rank.gold{background:linear-gradient(135deg,#f59e0b,#d97706)}
.pop-rank.silver{background:linear-gradient(135deg,#94a3b8,#64748b)}
.pop-rank.bronze{background:linear-gradient(135deg,#cd7c4b,#92400e)}
.pop-name{flex:1;font-size:.88rem;font-weight:600;color:#1e293b}
.pop-meta{font-size:.75rem;color:#64748b}
.pop-badge{padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:#ecfdf5;color:#047857}
.analytics-stat{text-align:center;padding:20px;border-radius:14px;border:1px solid #e2e8f0;background:#f8fafc}
.analytics-stat .val{font-size:2rem;font-weight:800;color:#1e293b;line-height:1}
.analytics-stat .lbl{font-size:.82rem;color:#64748b;margin-top:4px}
</style>
@endpush
@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير التدريب"
    subtitle="إحصائيات وتحليلات شاملة لبرامج التدريب، نسب الإشغال، والطلبات المسجلة"
    icon="fas fa-graduation-cap"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'تقارير التدريب']
    ]"
    badge="وحدة التدريب"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير</span>
    </button>
</x-page-hero>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kc-green"><div class="kpi-icon"><i class="fas fa-graduation-cap"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $basicStats['totalTrainings']??0 }}">0</h2><small>إجمالي برامج التدريب</small></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kc-blue"><div class="kpi-icon"><i class="fas fa-play-circle"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $basicStats['activeTrainings']??0 }}">0</h2><small>برامج نشطة</small></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kc-amber"><div class="kpi-icon"><i class="fas fa-file-alt"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $basicStats['totalApplications']??0 }}">0</h2><small>إجمالي الطلبات</small></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kc-red"><div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $basicStats['pendingApplications']??0 }}">0</h2><small>طلبات معلقة</small></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-pie"></i></div>أنواع التدريبات</h5></div>
            <div class="chart-body"><canvas id="trainingTypesChart" style="max-height:270px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-bar"></i></div>حالات التدريبات</h5></div>
            <div class="chart-body"><canvas id="trainingStatusChart" style="max-height:270px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-area"></i></div>حالات طلبات التدريب</h5></div>
            <div class="chart-body"><canvas id="applicationStatusChart" style="max-height:250px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#f0fdfa;color:#0d9488;"><i class="fas fa-chart-line"></i></div>الاتجاهات الشهرية</h5></div>
            <div class="chart-body"><canvas id="monthlyTrainingsChart" style="max-height:250px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#fdf2f8;color:#be185d;"><i class="fas fa-building"></i></div>توزيع الشركات</h5></div>
            <div class="chart-body"><canvas id="companyDistributionChart" style="max-height:250px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-percent"></i></div>معدلات الإشغال</h5></div>
            <div class="chart-body">
                @forelse($advancedCharts['occupancy_rates']??[] as $rate)
                <div class="occ-row">
                    <span class="occ-name" title="{{ $rate['training'] }}">{{ Str::limit($rate['training'],30) }}</span>
                    <div class="occ-bar-wrap"><div class="occ-bar" data-width="{{ min(round($rate['rate']),100) }}" style="width:0;"></div></div>
                    <span class="occ-pct">{{ round($rate['rate'],1) }}%</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>لا توجد بيانات</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-trophy"></i></div>أكثر التدريبات طلباً</h5></div>
            <div class="chart-body">
                @forelse($advancedAnalytics['popular_trainings']??[] as $i=>$training)
                @php $rankClass=$i==0?'gold':($i==1?'silver':($i==2?'bronze':'')); @endphp
                <div class="popular-row">
                    <div class="pop-rank {{ $rankClass }}">{{ $i+1 }}</div>
                    <div>
                        <div class="pop-name">{{ $training['name'] }}</div>
                        <div class="pop-meta">طلبات: {{ $training['applications'] }}</div>
                    </div>
                    <span class="pop-badge">{{ $training['occupancy_rate'] }}% إشغال</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>لا توجد بيانات</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-analytics"></i></div>تحليلات إضافية</h5></div>
            <div class="chart-body">
                <div class="row g-2">
                    <div class="col-12"><div class="analytics-stat"><div class="val">{{ $advancedAnalytics['approval_rate']??0 }}%</div><div class="lbl">معدل القبول</div></div></div>
                    <div class="col-12"><div class="analytics-stat"><div class="val">{{ $advancedAnalytics['avg_applicants']??0 }}</div><div class="lbl">متوسط المتقدمين / تدريب</div></div></div>
                    <div class="col-12"><div class="analytics-stat"><div class="val">{{ $advancedAnalytics['overall_occupancy']??0 }}%</div><div class="lbl">نسبة الإشغال الإجمالية</div></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($advancedAnalytics['insights']))
<div class="chart-card mb-4">
    <div class="chart-header"><h5><div class="ch-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-lightbulb"></i></div>استنتاجات وتوصيات</h5></div>
    <div class="chart-body">
        @foreach($advancedAnalytics['insights'] as $insight)
        <div class="insight-card {{ $insight['type'] }}">
            <div class="insight-icon"><i class="fas fa-{{ $insight['icon'] }}"></i></div>
            <div class="insight-text"><h6>{{ $insight['title'] }}</h6><p>{{ $insight['description'] }}</p></div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const palette=['#3b82f6','#8b5cf6','#10b981','#f59e0b','#ef4444','#14b8a6','#ec4899','#6366f1'];
document.querySelectorAll('.counter').forEach(el=>{const t=parseInt(el.dataset.target)||0;let c=0,s=Math.max(1,Math.ceil(t/50));const ti=setInterval(()=>{c=Math.min(c+s,t);el.textContent=c.toLocaleString('en-US');if(c>=t)clearInterval(ti);},25);});
document.querySelectorAll('.occ-bar').forEach(b=>{setTimeout(()=>{b.style.width=b.dataset.width+'%';},400);});
function makeChart(id,type,labels,data,colors,opts={}){const ctx=document.getElementById(id);if(!ctx)return;new Chart(ctx,{type,data:{labels,datasets:[{data,backgroundColor:colors||palette,borderColor:type==='line'?colors[0]||'#3b82f6':undefined,borderWidth:type==='bar'?0:2,fill:type==='line'?{target:'origin',above:'rgba(59,130,246,.1)'}:false,tension:.4,pointRadius:4,pointBackgroundColor:type==='line'?colors[0]||'#3b82f6':undefined,...(type==='bar'?{borderRadius:6,borderSkipped:false}:{})}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:type!=='bar',position:'bottom',labels:{padding:14,boxWidth:10,usePointStyle:true,font:{size:11}}},tooltip:{mode:'index',intersect:false}},scales:type!=='bar'&&type!=='line'?{}:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#f1f5f9'}}}},...opts});}
makeChart('trainingTypesChart','doughnut',@json($advancedCharts['training_types']['labels']??[]),@json($advancedCharts['training_types']['data']??[]),@json($advancedCharts['training_types']['colors']??[]));
makeChart('trainingStatusChart','bar',@json($advancedCharts['training_status']['labels']??[]),@json($advancedCharts['training_status']['data']??[]),palette);
makeChart('applicationStatusChart','doughnut',@json($advancedCharts['application_status']['labels']??[]),@json($advancedCharts['application_status']['data']??[]),@json($advancedCharts['application_status']['colors']??[]));
makeChart('monthlyTrainingsChart','line',@json($advancedCharts['monthly_trends']['labels']??[]),@json($advancedCharts['monthly_trends']['data']??[]),['#8b5cf6']);
makeChart('companyDistributionChart','bar',@json($advancedCharts['company_distribution']['labels']??[]),@json($advancedCharts['company_distribution']['data']??[]),palette);
</script>
@endpush
