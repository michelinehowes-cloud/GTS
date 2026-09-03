@extends('layouts.app')

@section('title', 'لوحة تحكم التقييم والمتابعة')
@section('page-title', 'لوحة تحكم التقييم والمتابعة')

@push('styles')
<style>
.eval-hero {
    background: linear-gradient(135deg, #1a2a6c 0%, #1e3a8a 40%, #1d4ed8 100%);
    border-radius: 20px;
    padding: 32px 40px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(26,42,108,0.35);
}
.eval-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}
.eval-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 40px;
    width: 200px; height: 200px;
    background: rgba(245,158,11,0.1);
    border-radius: 50%;
}
.eval-hero h1 { font-size: 1.9rem; font-weight: 800; color: #fff; margin: 0; }
.eval-hero p { color: rgba(255,255,255,0.75); margin: 6px 0 0; font-size: 0.95rem; }
.eval-hero .hero-badge {
    background: rgba(245,158,11,0.2);
    border: 1px solid rgba(245,158,11,0.4);
    color: #fbbf24;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 14px;
}
.kpi-card {
    background: #fff;
    border-radius: 18px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 100%;
}
.kpi-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: 18px 18px 0 0;
    background: var(--accent);
}
.kpi-card:hover { transform: translateY(-5px); box-shadow: 0 12px 40px rgba(0,0,0,0.13); }
.kpi-icon {
    width: 60px; height: 60px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
    background: var(--icon-bg); color: var(--icon-color);
}
.kpi-info h2 { font-size: 2.2rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1; }
.kpi-info p { font-size: 0.85rem; color: #64748b; margin: 4px 0 0; font-weight: 500; }
.kpi-card.blue   { --accent:#3b82f6; --icon-bg:#eff6ff; --icon-color:#3b82f6; }
.kpi-card.indigo { --accent:#6366f1; --icon-bg:#eef2ff; --icon-color:#6366f1; }
.kpi-card.green  { --accent:#10b981; --icon-bg:#ecfdf5; --icon-color:#10b981; }
.kpi-card.orange { --accent:#f59e0b; --icon-bg:#fffbeb; --icon-color:#f59e0b; }
.kpi-card.red    { --accent:#ef4444; --icon-bg:#fef2f2; --icon-color:#ef4444; }
.kpi-card.purple { --accent:#8b5cf6; --icon-bg:#f5f3ff; --icon-color:#8b5cf6; }
.kpi-card.teal   { --accent:#14b8a6; --icon-bg:#f0fdfa; --icon-color:#14b8a6; }
.kpi-card.pink   { --accent:#ec4899; --icon-bg:#fdf2f8; --icon-color:#ec4899; }
.chart-card { background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,0.07); border:1px solid rgba(0,0,0,0.06); overflow:hidden; }
.chart-card .chart-header { padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.chart-card .chart-header h5 { font-size:1rem; font-weight:700; color:#1e293b; margin:0; display:flex; align-items:center; gap:10px; }
.chart-card .chart-header .chart-icon { width:36px; height:36px; border-radius:10px; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:0.9rem; }
.chart-card .chart-body { padding:24px; }
.activity-list { list-style:none; padding:0; margin:0; }
.activity-item { display:flex; align-items:center; gap:14px; padding:14px 0; border-bottom:1px solid #f1f5f9; }
.activity-item:last-child { border-bottom:none; }
.activity-avatar { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1rem; font-weight:700; color:#fff; flex-shrink:0; }
.activity-info h6 { font-size:0.9rem; font-weight:600; color:#1e293b; margin:0; }
.activity-info span { font-size:0.78rem; color:#94a3b8; }
.activity-badge { margin-right:auto; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:600; }
.role-row { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f1f5f9; }
.role-row:last-child { border-bottom:none; }
.role-dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
.role-name { font-size:0.9rem; font-weight:500; color:#374151; flex:1; }
.role-bar-wrap { flex:2; height:8px; background:#f1f5f9; border-radius:20px; overflow:hidden; }
.role-bar { height:100%; border-radius:20px; transition:width 1.2s cubic-bezier(.17,.67,.83,.67); }
.role-count { font-size:0.85rem; font-weight:700; color:#1e293b; min-width:30px; text-align:end; }
.quick-link { display:flex; align-items:center; gap:12px; padding:14px 20px; border-radius:14px; background:#f8fafc; border:1px solid #e2e8f0; text-decoration:none; color:#334155; font-size:0.9rem; font-weight:600; transition:all 0.2s; }
.quick-link:hover { background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8; transform:translateX(-3px); }
.quick-link .ql-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:0.9rem; flex-shrink:0; }
</style>
@endpush

@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="لوحة تحكم التقييم والمتابعة"
    subtitle="نظرة شاملة ومؤشرات أداء منظومة التدريب والمتابعة وتقييمات الطلاب والشركات"
    icon="fas fa-chart-line"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('home')],
        ['label' => 'التقييم والمتابعة']
    ]"
    badge="وحدة التقييم والمتابعة"
/>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card blue"><div class="kpi-icon"><i class="fas fa-users"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $usersCount ?? 0 }}">0</h2><p>إجمالي المستخدمين</p></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card indigo"><div class="kpi-icon"><i class="fas fa-building"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $companiesCount ?? 0 }}">0</h2><p>إجمالي الشركات</p></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card green"><div class="kpi-icon"><i class="fas fa-graduation-cap"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $trainingsCount ?? 0 }}">0</h2><p>برامج التدريب</p></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card orange"><div class="kpi-icon"><i class="fas fa-file-alt"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $applicationsCount ?? 0 }}">0</h2><p>طلبات التدريب</p></div></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card red"><div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $pendingApplicationsCount ?? 0 }}">0</h2><p>طلبات قيد الانتظار</p></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card purple"><div class="kpi-icon"><i class="fas fa-user-graduate"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $graduatesCount ?? 0 }}">0</h2><p>الخريجون</p></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card teal"><div class="kpi-icon"><i class="fas fa-handshake"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $partnershipDocumentsCount ?? 0 }}">0</h2><p>وثائق الشراكة</p></div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card pink"><div class="kpi-icon"><i class="fas fa-briefcase"></i></div><div class="kpi-info"><h2 class="counter" data-target="{{ $jobOpportunitiesCount ?? 0 }}">0</h2><p>فرص العمل</p></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="chart-icon"><i class="fas fa-chart-pie"></i></div>توزيع المستخدمين حسب الدور</h5></div>
            <div class="chart-body"><canvas id="rolesChart" style="max-height:260px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="chart-icon"><i class="fas fa-history"></i></div>أحدث المستخدمين المسجلين</h5></div>
            <div class="chart-body" style="padding-top:8px;">
                <ul class="activity-list">
                    @php
                    $avatarColors=['#3b82f6','#8b5cf6','#10b981','#f59e0b','#ef4444','#14b8a6','#ec4899'];
                    $roleLabels=['admin'=>['text'=>'مدير','bg'=>'#eff6ff','color'=>'#1d4ed8'],'graduate'=>['text'=>'خريج','bg'=>'#ecfdf5','color'=>'#059669'],'company'=>['text'=>'شركة','bg'=>'#fef3c7','color'=>'#d97706'],'staff'=>['text'=>'موظف','bg'=>'#f5f3ff','color'=>'#7c3aed'],'training_coordinator'=>['text'=>'منسق','bg'=>'#f0fdfa','color'=>'#0d9488'],'placement_coordinator'=>['text'=>'توظيف','bg'=>'#fdf2f8','color'=>'#be185d']];
                    @endphp
                    @forelse($recentUsers as $i=>$user)
                    @php $bgColor=$avatarColors[$i%count($avatarColors)];$firstLetter=mb_substr($user->name,0,1);$roleInfo=$roleLabels[$user->role]??['text'=>$user->role,'bg'=>'#f1f5f9','color'=>'#475569']; @endphp
                    <li class="activity-item">
                        <div class="activity-avatar" style="background:{{ $bgColor }};">{{ $firstLetter }}</div>
                        <div class="activity-info"><h6>{{ $user->name }}</h6><span>{{ $user->created_at->diffForHumans() }}</span></div>
                        <span class="activity-badge" style="background:{{ $roleInfo['bg'] }};color:{{ $roleInfo['color'] }};">{{ $roleInfo['text'] }}</span>
                    </li>
                    @empty
                    <li class="activity-item"><div class="text-center w-100 py-3 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>لا يوجد مستخدمون حديثون</div></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="chart-icon"><i class="fas fa-users-cog"></i></div>الأدوار بالتفصيل</h5></div>
            <div class="chart-body">
                @php
                $roleColors=['#3b82f6','#8b5cf6','#10b981','#f59e0b','#ef4444','#14b8a6'];
                $roleAr=['admin'=>'مدير النظام','training_coordinator'=>'منسق التدريب','placement_coordinator'=>'منسق التوظيف','graduate'=>'خريج','company'=>'شركة','staff'=>'موظف'];
                $maxRole=collect($usersByRole)->max()?:1;
                @endphp
                @foreach($usersByRole as $role=>$count)
                @php $ci=$loop->index%count($roleColors);$w=round(($count/$maxRole)*100); @endphp
                <div class="role-row">
                    <div class="role-dot" style="background:{{ $roleColors[$ci] }};"></div>
                    <span class="role-name">{{ $roleAr[$role]??$role }}</span>
                    <div class="role-bar-wrap"><div class="role-bar" data-width="{{ $w }}" style="width:0;background:{{ $roleColors[$ci] }};"></div></div>
                    <span class="role-count">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="chart-icon"><i class="fas fa-link"></i></div>روابط سريعة</h5></div>
            <div class="chart-body d-flex flex-column gap-2" style="padding-top:12px;">
                <a href="{{ route('evaluation-followup.surveys.index') }}" class="quick-link"><div class="ql-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fas fa-poll"></i></div>إدارة الاستبيانات</a>
                <a href="{{ route('evaluation-followup.evaluations.index') }}" class="quick-link"><div class="ql-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-star"></i></div>إدارة التقييمات</a>
                <a href="{{ route('evaluation-followup.training-programs') }}" class="quick-link"><div class="ql-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-graduation-cap"></i></div>برامج التدريب</a>
                <a href="{{ route('evaluation-followup.training-reports') }}" class="quick-link"><div class="ql-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-chart-bar"></i></div>تقارير التدريب</a>
                <a href="{{ route('evaluation-followup.performance-reports') }}" class="quick-link"><div class="ql-icon" style="background:#fdf2f8;color:#be185d;"><i class="fas fa-tachometer-alt"></i></div>تقارير الأداء</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-header"><h5><div class="chart-icon"><i class="fas fa-building"></i></div>أحدث الشركات</h5></div>
            <div class="chart-body" style="padding-top:8px;">
                <ul class="activity-list">
                    @forelse($recentCompanies as $i=>$company)
                    @php $bg=$avatarColors[$i%count($avatarColors)]; @endphp
                    <li class="activity-item">
                        <div class="activity-avatar" style="background:{{ $bg }};border-radius:10px;"><i class="fas fa-building" style="font-size:0.9rem;"></i></div>
                        <div class="activity-info"><h6>{{ $company->name }}</h6><span>{{ $company->created_at->diffForHumans() }}</span></div>
                    </li>
                    @empty
                    <li class="activity-item"><div class="text-center w-100 py-3 text-muted"><i class="fas fa-building fa-2x mb-2 d-block"></i>لا توجد شركات حديثة</div></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseInt(el.dataset.target)||0;
        let current=0,step=Math.max(1,Math.ceil(target/50));
        const timer=setInterval(()=>{current=Math.min(current+step,target);el.textContent=current.toLocaleString('en-US');if(current>=target)clearInterval(timer);},25);
    });
    document.querySelectorAll('.role-bar').forEach(bar => {
        setTimeout(()=>{bar.style.width=bar.dataset.width+'%';},400);
    });
    const rolesData = @json($usersByRole??[]);
    const labels=Object.keys(rolesData).map(r=>({'admin':'مدير النظام','training_coordinator':'منسق التدريب','placement_coordinator':'منسق التوظيف','graduate':'خريج','company':'شركة','staff':'موظف'}[r]||r));
    const palette=['#3b82f6','#8b5cf6','#10b981','#f59e0b','#ef4444','#14b8a6','#ec4899'];
    const ctx=document.getElementById('rolesChart');
    if(ctx){new Chart(ctx,{type:'doughnut',data:{labels:labels,datasets:[{data:Object.values(rolesData),backgroundColor:palette,borderWidth:0,hoverOffset:8}]},options:{cutout:'68%',responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{padding:16,boxWidth:12,boxHeight:12,usePointStyle:true,font:{size:12,family:'inherit'}}},tooltip:{callbacks:{label:ctx=>` ${ctx.label}: ${ctx.parsed} مستخدم`}}}}});}
});
</script>
@endpush
