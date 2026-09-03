@extends('layouts.app')
@section('title', 'إدارة برامج التدريب')
@section('page-title', 'برامج التدريب')
@push('styles')
<style>
.page-hero{background:linear-gradient(135deg,#064e3b 0%,#047857 50%,#059669 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(6,78,59,.35)}
.page-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.page-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.page-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.page-badge{background:rgba(167,243,208,.2);border:1px solid rgba(167,243,208,.4);color:#a7f3d0;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.action-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:12px;font-weight:600;font-size:.9rem;text-decoration:none;transition:all .2s;border:none;cursor:pointer}
.btn-emerald{background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,.35)}
.btn-emerald:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(16,185,129,.45);color:#fff}
.search-filter-bar{background:#fff;border-radius:14px;padding:16px 20px;margin-bottom:24px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.search-input{padding:9px 16px;border:1px solid #e2e8f0;border-radius:10px;font-size:.88rem;outline:none;transition:all .2s;min-width:260px}
.search-input:focus{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.15)}
.training-card{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);overflow:hidden;transition:all .3s ease;height:100%;display:flex;flex-direction:column}
.training-card:hover{transform:translateY(-5px);box-shadow:0 12px 36px rgba(0,0,0,.12)}
.card-banner{padding:16px 20px;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center}
.card-content{padding:20px;flex:1;display:flex;flex-direction:column}
.program-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0 0 8px;line-height:1.4}
.program-desc{font-size:.84rem;color:#64748b;margin-bottom:16px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.meta-row{display:flex;align-items:center;gap:8px;font-size:.8rem;color:#64748b;margin-bottom:8px}
.meta-row i{color:#10b981;width:16px;text-align:center}
.card-footer-action{padding:14px 20px;background:#f8fafc;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center}
.badge-status{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.empty-state{text-align:center;padding:60px 20px;background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06)}
</style>
@endpush

@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="إدارة برامج التدريب"
    subtitle="متابعة واستعراض الخطط التدريبية والبرامج التأهيلية للطلبة والخريجين"
    icon="fas fa-graduation-cap"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'برامج التدريب']
    ]"
    badge="وحدة التدريب"
>
    <a href="{{ route('evaluation-followup.training-programs.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i>
        <span>إضافة برنامج تدريبي</span>
    </a>
</x-page-hero>

<div class="search-filter-bar">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-search text-muted"></i>
        <input type="text" class="search-input" id="programSearch" placeholder="بحث باسم البرنامج، المدرب، أو المكان...">
    </div>
    <span class="text-muted small">إجمالي البرامج: <strong>{{ $trainings->total() ?? $trainings->count() }}</strong></span>
</div>

<div class="row g-4 mb-4" id="programsGrid">
    @forelse($trainings as $training)
    <div class="col-md-6 col-lg-4 program-item">
        <div class="training-card">
            <div class="card-banner">
                <span class="badge bg-light text-dark fw-bold border">
                    <i class="fas fa-users me-1 text-primary"></i>{{ $training->capacity ?? 'غير محدد' }} مقعد
                </span>
                @php
                    $isOngoing = $training->start_date && $training->end_date && now()->between($training->start_date, $training->end_date);
                    $isPast = $training->end_date && now()->gt($training->end_date);
                @endphp
                @if($isOngoing)
                    <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size:.72rem;">جارٍ الآن</span>
                @elseif($isPast)
                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size:.72rem;">منتهٍ</span>
                @else
                    <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size:.72rem;">قادم</span>
                @endif
            </div>
            <div class="card-content">
                <h5 class="program-title">{{ $training->title ?? $training->name }}</h5>
                <p class="program-desc">{{ $training->description ?? 'لا يوجد وصف متاح لهذا البرنامج التدريبي.' }}</p>
                
                <div class="meta-row">
                    <i class="fas fa-calendar-alt"></i>
                    <span>
                        {{ $training->start_date ? \Carbon\Carbon::parse($training->start_date)->format('Y/m/d') : 'تاريخ غير محدد' }}
                        @if($training->end_date)
                            — {{ \Carbon\Carbon::parse($training->end_date)->format('Y/m/d') }}
                        @endif
                    </span>
                </div>
                <div class="meta-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $training->location ?? 'غير محدد' }}</span>
                </div>
            </div>
            <div class="card-footer-action">
                <span class="text-muted small">
                    <i class="fas fa-file-signature text-muted me-1"></i>الطلبات: <strong>{{ $training->applications_count ?? ($training->applications ? $training->applications->count() : 0) }}</strong>
                </span>
                <a href="{{ route('evaluation-followup.training-reports') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="fas fa-chart-bar me-1"></i>التقارير
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state">
            <i class="fas fa-graduation-cap fa-3x text-muted mb-3 d-block"></i>
            <h5 class="text-secondary fw-bold mb-2">لا توجد برامج تدريبية مسجلة حالياً</h5>
            <p class="text-muted mb-4">يمكنك البدء بإضافة أول برنامج تدريبي للمتابعة والتقييم.</p>
            <a href="{{ route('evaluation-followup.training-programs.create') }}" class="action-btn btn-emerald">
                <i class="fas fa-plus"></i>إضافة برنامج الآن
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($trainings->hasPages())
<div class="d-flex justify-content-center p-3">
    {{ $trainings->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('programSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.program-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
