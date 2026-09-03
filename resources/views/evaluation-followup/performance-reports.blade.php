@extends('layouts.app')
@section('title', 'تقارير الأداء')
@section('page-title', 'تقارير الأداء')
@push('styles')
<style>
.rpt-hero{background:linear-gradient(135deg,#7c2d12 0%,#c2410c 50%,#ea580c 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(124,45,18,.3)}
.rpt-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.rpt-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.rpt-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.rpt-badge{background:rgba(254,215,170,.2);border:1px solid rgba(254,215,170,.4);color:#fed7aa;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.kpi-card{background:#fff;border-radius:16px;padding:22px;display:flex;align-items:center;gap:18px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);transition:all .3s;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:16px 16px 0 0}
.kpi-card:hover{transform:translateY(-4px);box-shadow:0 10px 36px rgba(0,0,0,.12)}
.kpi-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info small{font-size:.82rem;color:#64748b;font-weight:500}
.kc-orange{--accent:#ea580c;--icon-bg:#fff7ed;--icon-color:#ea580c}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-blue{--accent:#3b82f6;--icon-bg:#eff6ff;--icon-color:#3b82f6}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.chart-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden;height:100%}
.chart-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.chart-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.ch-icon{width:32px;height:32px;border-radius:9px;background:#fff7ed;color:#ea580c;display:flex;align-items:center;justify-content:center;font-size:.82rem}
.chart-body{padding:20px}
.gauge-wrap{display:flex;flex-direction:column;align-items:center;padding:16px 0}
.gauge-svg{width:210px;height:120px}
.gauge-val{font-size:2.4rem;font-weight:800;color:#1e293b;text-align:center;margin-top:-8px;line-height:1}
.gauge-lbl{font-size:.85rem;color:#64748b;text-align:center;margin-top:4px}
.star-score{display:flex;gap:3px;justify-content:center;margin-top:8px}
.star-score i{color:#fbbf24;font-size:1.1rem}
.star-score i.empty{color:#e2e8f0}
.perf-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9}
.perf-row:last-child{border-bottom:none}
.perf-label{font-size:.85rem;color:#374151;flex:1}
.perf-bar-wrap{flex:3;height:10px;background:#f1f5f9;border-radius:20px;overflow:hidden}
.perf-bar{height:100%;border-radius:20px;transition:width 1.2s cubic-bezier(.17,.67,.83,.67)}
.perf-count{font-size:.82rem;font-weight:700;color:#1e293b;min-width:28px;text-align:end}
.eval-row{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f1f5f9}
.eval-row:last-child{border-bottom:none}
.eval-avatar{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:.95rem;font-weight:700;color:#fff;flex-shrink:0}
.eval-info h6{font-size:.88rem;font-weight:600;color:#1e293b;margin:0}
.eval-info small{font-size:.76rem;color:#94a3b8}
.score-badge{padding:4px 10px;border-radius:20px;font-size:.75rem;font-weight:700;margin-right:auto}
.sb-high{background:#d1fae5;color:#047857}
.sb-med{background:#fef3c7;color:#b45309}
.sb-low{background:#fee2e2;color:#b91c1c}
.area-card{background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 18px;margin-bottom:10px}
.area-card h6{font-size:.88rem;font-weight:700;color:#7c2d12;margin:0 0 4px}
.area-card p{font-size:.8rem;color:#92400e;margin:0}
</style>
@endpush
@section('content')
@php
$dist = $performanceStats['performance_distribution'] ?? [];
$excellent = $dist['excellent'] ?? 0;
$good = $dist['good'] ?? 0;
$average = $dist['average'] ?? 0;
$belowAverage = $dist['below_average'] ?? 0;
$poor = $dist['poor'] ?? 0;
$total = $performanceStats['total_performance_evaluations'] ?? 0;
$avgScore = $performanceStats['average_performance_score'] ?? 0;
$topPerformers = $performanceStats['top_performers'] ?? collect();
$improvAreas = $performanceStats['areas_for_improvement'] ?? [];
$avatarColors = ['#ea580c','#3b82f6','#10b981','#8b5cf6','#f59e0b','#ef4444','#14b8a6'];
@endphp
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="تقارير الأداء التدريبي"
    subtitle="تحليل شامل لمستويات الأداء وتقييمات المتدربين ومنصة المتفوقين"
    icon="fas fa-tachometer-alt"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'تقارير الأداء']
    ]"
    badge="التقييم والمتابعة"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير</span>
    </button>
</x-page-hero>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kc-orange"><div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $total }}">0</h2><small>إجمالي التقييمات</small></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kc-green"><div class="kpi-icon"><i class="fas fa-chart-line"></i></div><div class="kpi-info"><h2>{{ number_format($avgScore,1) }}<span style="font-size:.9rem;font-weight:500;color:#64748b;">/5</span></h2><small>متوسط الأداء</small></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kc-blue"><div class="kpi-icon"><i class="fas fa-star"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $excellent }}">0</h2><small>أداء ممتاز (4.5+)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kc-amber"><div class="kpi-icon"><i class="fas fa-exclamation-circle"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $poor }}">0</h2><small>يحتاج تحسين (&lt;1.5)</small></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-tachometer-alt"></i></div>متوسط الأداء العام</h5></div>
            <div class="chart-body">
                <div class="gauge-wrap">
                    <svg class="gauge-svg" viewBox="0 0 200 120">
                        <path d="M 20 110 A 90 90 0 0 1 180 110" fill="none" stroke="#f1f5f9" stroke-width="18" stroke-linecap="round"/>
                        <path id="gaugeArc" d="M 20 110 A 90 90 0 0 1 180 110" fill="none" stroke="url(#perfGrad)" stroke-width="18" stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="283"/>
                        <defs><linearGradient id="perfGrad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#ea580c"/><stop offset="100%" stop-color="#f59e0b"/></linearGradient></defs>
                    </svg>
                    <div class="gauge-val">{{ number_format($avgScore,1) }}</div>
                    <div class="gauge-lbl">من 5.0 — متوسط الأداء</div>
                </div>
                <div class="star-score">
                    @for($s=1;$s<=5;$s++)<i class="fas fa-star {{ $s<=round($avgScore)?'':'empty' }}"></i>@endfor
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-chart-pie"></i></div>توزيع مستويات الأداء</h5></div>
            <div class="chart-body"><canvas id="performanceLevelChart" style="max-height:220px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header"><h5><div class="ch-icon"><i class="fas fa-bars"></i></div>توزيع الدرجات</h5></div>
            <div class="chart-body">
                @php
                $levels=[['ممتاز (4.5-5)',$excellent,'#10b981'],['جيد (3.5-4.4)',$good,'#3b82f6'],['متوسط (2.5-3.4)',$average,'#f59e0b'],['مقبول (1.5-2.4)',$belowAverage,'#ea580c'],['ضعيف (<1.5)',$poor,'#ef4444']];
                $maxL=max(array_column($levels,1))?:1;
                @endphp
                @foreach($levels as [$lbl,$cnt,$clr])
                <div class="perf-row">
                    <span class="perf-label">{{ $lbl }}</span>
                    <div class="perf-bar-wrap"><div class="perf-bar" data-width="{{ round(($cnt/$maxL)*100) }}" style="width:0;background:{{ $clr }};"></div></div>
                    <span class="perf-count">{{ $cnt }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-medal"></i></div>أعلى المتدربين أداءً</h5></div>
            <div class="chart-body">
                @forelse($topPerformers as $i=>$eval)
                @php $bg=$avatarColors[$i%count($avatarColors)];$name=$eval->user->name??$eval->evaluator->name??'مجهول';$score=$eval->average_score??0; @endphp
                <div class="eval-row">
                    <div class="eval-avatar" style="background:{{ $bg }};">{{ $i+1 }}</div>
                    <div class="eval-info">
                        <h6>{{ $name }}</h6>
                        <small>{{ $eval->training->title??'—' }}</small>
                    </div>
                    <span class="score-badge {{ $score>=4?'sb-high':($score>=3?'sb-med':'sb-low') }}"><i class="fas fa-star" style="font-size:.65rem;"></i> {{ number_format($score,1) }}/5</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>لا توجد بيانات كافية</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="ch-icon" style="background:#fef2f2;color:#b91c1c;"><i class="fas fa-exclamation-triangle"></i></div>مجالات تحتاج تحسين</h5></div>
            <div class="chart-body">
                @forelse($improvAreas as $area)
                <div class="area-card">
                    <h6><i class="fas fa-exclamation-circle me-2"></i>{{ $area['area'] }}</h6>
                    <p>{{ $area['recommendation'] }} — متوسط: {{ $area['average_score'] }}/5</p>
                </div>
                @empty
                <div class="text-center py-4" style="color:#10b981;"><i class="fas fa-check-circle fa-3x mb-3 d-block"></i><p style="font-weight:600;">ممتاز! لا توجد مجالات تحتاج تحسين عاجل</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.querySelectorAll('.counter').forEach(el=>{const t=parseInt(el.dataset.target)||0;let c=0,s=Math.max(1,Math.ceil(t/50));const ti=setInterval(()=>{c=Math.min(c+s,t);el.textContent=c.toLocaleString('en-US');if(c>=t)clearInterval(ti);},25);});
document.querySelectorAll('.perf-bar').forEach(b=>{setTimeout(()=>{b.style.width=b.dataset.width+'%';},400);});
const avgScore={{ $avgScore??0 }};
const arc=document.getElementById('gaugeArc');
if(arc){const offset=283-(avgScore/5)*283;setTimeout(()=>{arc.style.transition='stroke-dashoffset 1.5s cubic-bezier(.17,.67,.83,.67)';arc.style.strokeDashoffset=offset;},300);}
const ctx=document.getElementById('performanceLevelChart');
if(ctx)new Chart(ctx,{type:'doughnut',data:{labels:['ممتاز','جيد','متوسط','مقبول','ضعيف'],datasets:[{data:[{{ $excellent }},{{ $good }},{{ $average }},{{ $belowAverage }},{{ $poor }}],backgroundColor:['#10b981','#3b82f6','#f59e0b','#ea580c','#ef4444'],borderWidth:0,hoverOffset:8}]},options:{cutout:'65%',responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{padding:14,boxWidth:10,usePointStyle:true,font:{size:11}}}}}});
</script>
@endpush
