@extends('layouts.app')
@section('title', 'التقارير والإحصائيات المتقدمة')
@section('page-title', 'التقارير والإحصائيات')
@push('styles')
<style>
.stat-hero{background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#1e3a8a 100%);border-radius:20px;padding:32px 40px;margin-bottom:32px;position:relative;overflow:hidden;box-shadow:0 20px 60px rgba(15,23,42,.35)}
.stat-hero::before{content:'';position:absolute;top:-60px;right:-60px;width:240px;height:240px;background:rgba(255,255,255,.04);border-radius:50%}
.stat-hero h1{font-size:1.8rem;font-weight:800;color:#fff;margin:0}
.stat-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.95rem}
.stat-badge{background:rgba(147,197,253,.2);border:1px solid rgba(147,197,253,.4);color:#93c5fd;padding:5px 14px;border-radius:20px;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:14px}
.kpi-card{background:#fff;border-radius:18px;padding:24px;display:flex;align-items:center;gap:20px;box-shadow:0 4px 24px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);transition:all .3s ease;height:100%;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--accent);border-radius:18px 18px 0 0}
.kpi-card:hover{transform:translateY(-5px);box-shadow:0 12px 40px rgba(0,0,0,.12)}
.kpi-icon{width:60px;height:60px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;background:var(--icon-bg);color:var(--icon-color)}
.kpi-info h2{font-size:2.2rem;font-weight:800;color:#1e293b;margin:0;line-height:1}
.kpi-info p{font-size:.85rem;color:#64748b;margin:4px 0 0;font-weight:500}
.kc-blue{--accent:#3b82f6;--icon-bg:#eff6ff;--icon-color:#3b82f6}
.kc-green{--accent:#10b981;--icon-bg:#ecfdf5;--icon-color:#10b981}
.kc-indigo{--accent:#6366f1;--icon-bg:#eef2ff;--icon-color:#6366f1}
.kc-amber{--accent:#f59e0b;--icon-bg:#fffbeb;--icon-color:#f59e0b}
.kc-purple{--accent:#8b5cf6;--icon-bg:#f5f3ff;--icon-color:#8b5cf6}
.kc-teal{--accent:#14b8a6;--icon-bg:#f0fdfa;--icon-color:#14b8a6}
.hub-card{background:#fff;border-radius:18px;padding:24px;box-shadow:0 4px 24px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);transition:all .3s ease;height:100%;display:flex;flex-direction:column}
.hub-card:hover{transform:translateY(-4px);box-shadow:0 12px 36px rgba(0,0,0,.12);border-color:#bfdbfe}
.hub-icon{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:16px}
.hub-title{font-size:1.1rem;font-weight:700;color:#1e293b;margin-bottom:8px}
.hub-desc{font-size:.85rem;color:#64748b;margin-bottom:20px;flex:1}
.hub-link{font-weight:600;font-size:.88rem;display:inline-flex;align-items:center;gap:6px;text-decoration:none;transition:all .2s}
.hub-link:hover{gap:10px}
</style>
@endpush

@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="منظومة التقارير والإحصائيات المتقدمة"
    subtitle="لوحة مركزية متكاملة لجميع المؤشرات القياسية، أداء البرامج التدريبية، ونسب التوظيف والتقييم"
    icon="fas fa-chart-line"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'التقارير والإحصائيات']
    ]"
    badge="مركز المؤشرات والتحليلات"
>
    <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-print"></i>
        <span>طباعة التقرير العام</span>
    </button>
</x-page-hero>

{{-- KPI Stats Grid --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-blue">
            <div class="kpi-icon"><i class="fas fa-graduation-cap"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $stats['trainings_count'] ?? 0 }}">0</h2>
                <p>برامج التدريب</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-green">
            <div class="kpi-icon"><i class="fas fa-file-signature"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $stats['applications_count'] ?? 0 }}">0</h2>
                <p>طلبات الالتحاق</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-indigo">
            <div class="kpi-icon"><i class="fas fa-star"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $stats['evaluations_count'] ?? 0 }}">0</h2>
                <p>التقييمات المسجلة</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-purple">
            <div class="kpi-icon"><i class="fas fa-poll"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $stats['surveys_count'] ?? 0 }}">0</h2>
                <p>الاستبيانات</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-amber">
            <div class="kpi-icon"><i class="fas fa-handshake"></i></div>
            <div class="kpi-info">
                <h2 class="counter" data-target="{{ $stats['partnerships_count'] ?? 0 }}">0</h2>
                <p>وثائق الشراكة</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card kc-teal">
            <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
            <div class="kpi-info">
                <h2>{{ number_format($stats['avg_evaluation_score'] ?? 0, 1) }}<span style="font-size:1rem;color:#64748b;">/5</span></h2>
                <p>متوسط التقييم العام</p>
            </div>
        </div>
    </div>
</div>

{{-- Reporting Modules Hub --}}
<h4 class="fw-bold text-dark mb-3"><i class="fas fa-layer-group text-primary me-2"></i>وحدات التقارير المتخصصة</h4>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="hub-card">
            <div class="hub-icon" style="background:#ecfdf5;color:#10b981;"><i class="fas fa-graduation-cap"></i></div>
            <h5 class="hub-title">تقارير التدريب والبرامج</h5>
            <p class="hub-desc">تحليل شامل لحالات التدريبات، نسب الإشغال، توزيع الشركات، ومعدلات قبول الطلبات.</p>
            <a href="{{ route('evaluation-followup.training-reports') }}" class="hub-link text-success">
                فتح تقارير التدريب <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="hub-card">
            <div class="hub-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-tachometer-alt"></i></div>
            <h5 class="hub-title">تقارير الأداء المتقدمة</h5>
            <p class="hub-desc">مؤشرات الأداء العامة، قياس مستويات المتدربين، والمتفوقين ومجالات التحسين المقترحة.</p>
            <a href="{{ route('evaluation-followup.performance-reports') }}" class="hub-link text-warning">
                فتح تقارير الأداء <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="hub-card">
            <div class="hub-icon" style="background:#eef2ff;color:#6366f1;"><i class="fas fa-clipboard-check"></i></div>
            <h5 class="hub-title">تقارير التقييمات الشاملة</h5>
            <p class="hub-desc">متابعة دقيقة لتقييمات المتدربين والمدربين والتجهيزات ومعدلات الرضا العامة.</p>
            <a href="{{ route('evaluation-followup.evaluation-reports') }}" class="hub-link text-indigo" style="color:#6366f1;">
                فتح تقارير التقييمات <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="hub-card">
            <div class="hub-icon" style="background:#f0f9ff;color:#0284c7;"><i class="fas fa-poll"></i></div>
            <h5 class="hub-title">تقارير الاستبيانات الميدانية</h5>
            <p class="hub-desc">تحليل استجابات الجمهور المستهدف (خريجين، شركات، منسقين) ونسب إكمال الاستبيانات.</p>
            <a href="{{ route('evaluation-followup.survey-reports') }}" class="hub-link text-primary">
                فتح تقارير الاستبيانات <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="hub-card">
            <div class="hub-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-handshake"></i></div>
            <h5 class="hub-title">تقارير الشراكات والتوظيف</h5>
            <p class="hub-desc">إحصاءات اتفاقيات التعاون مع الشركات والمؤسسات، والوظائف المتاحة للخريجين.</p>
            <a href="{{ route('evaluation-followup.partnership-employment-reports') }}" class="hub-link text-primary">
                فتح تقارير الشراكات <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="hub-card">
            <div class="hub-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-compass"></i></div>
            <h5 class="hub-title">تقارير الإرشاد المهني</h5>
            <p class="hub-desc">مؤشرات التوجيه الوظيفي، نسب التحاق الخريجين بسوق العمل وفرص التطوير المهني.</p>
            <a href="{{ route('evaluation-followup.career-guidance-advanced-reports') }}" class="hub-link" style="color:#7c3aed;">
                فتح تقارير الإرشاد المهني <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
});
</script>
@endpush
