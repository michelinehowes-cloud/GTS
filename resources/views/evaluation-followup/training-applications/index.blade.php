@extends('layouts.app')
@section('title', 'طلبات التدريب')
@section('page-title', 'طلبات التدريب')
@push('styles')
<style>
.page-hero{background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 50%,#3b82f6 100%);border-radius:20px;padding:28px 36px;margin-bottom:28px;position:relative;overflow:hidden;box-shadow:0 16px 48px rgba(30,58,138,.35)}
.page-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%}
.page-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin:0}
.page-hero p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:.9rem}
.page-badge{background:rgba(191,219,254,.2);border:1px solid rgba(191,219,254,.4);color:#bfdbfe;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
.search-filter-bar{background:#fff;border-radius:14px;padding:16px 20px;margin-bottom:24px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.search-input{padding:9px 16px;border:1px solid #e2e8f0;border-radius:10px;font-size:.88rem;outline:none;transition:all .2s;min-width:240px}
.search-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
.modern-table-wrap{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden}
.modern-table-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.modern-table-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.modern-table{width:100%;border-collapse:separate;border-spacing:0}
.modern-table thead tr{background:#f8fafc}
.modern-table thead th{padding:12px 16px;font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;white-space:nowrap}
.modern-table tbody tr{transition:background .15s}
.modern-table tbody tr:hover{background:#f8fafc}
.modern-table tbody td{padding:14px 16px;font-size:.88rem;color:#334155;border-bottom:1px solid #f8fafc;vertical-align:middle}
.modern-table tbody tr:last-child td{border-bottom:none}
.empty-state{text-align:center;padding:60px 20px}
.badge-status{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.status-pending{background:#fef3c7;color:#b45309}
.status-accepted{background:#d1fae5;color:#047857}
.status-rejected{background:#fee2e2;color:#b91c1c}
</style>
@endpush

@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="طلبات التدريب"
    subtitle="إدارة ومتابعة طلبات الالتحاق بالبرامج التدريبية ومراجعة حالات القبول والرفض"
    icon="fas fa-file-signature"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'طلبات التدريب']
    ]"
    badge="وحدة التدريب"
>
    <a href="{{ route('evaluation-followup.training-reports') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
        <i class="fas fa-chart-line"></i>
        <span>تقارير التدريب</span>
    </a>
</x-page-hero>

<div class="search-filter-bar">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-search text-muted"></i>
        <input type="text" class="search-input" id="appSearch" placeholder="بحث باسم المتقدم أو اسم التدريب...">
    </div>
    <span class="text-muted small">إجمالي الطلبات: <strong>{{ $applications->total() ?? $applications->count() }}</strong></span>
</div>

<div class="modern-table-wrap mb-4">
    <div class="modern-table-header">
        <h5><i class="fas fa-list text-primary me-2"></i>قائمة المتقدمين للتدريب</h5>
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="applicationsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المتقدم</th>
                    <th>البرنامج التدريبي</th>
                    <th>تاريخ التقديم</th>
                    <th>الحالة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td><span style="font-size:.76rem;color:#94a3b8;font-weight:600;">#{{ $loop->iteration }}</span></td>
                    <td>
                        <div class="fw-bold text-dark">{{ $app->user->name ?? 'مستخدم غير محدد' }}</div>
                        <small class="text-muted">{{ $app->user->email ?? '' }}</small>
                    </td>
                    <td>
                        <span class="fw-semibold text-primary">{{ $app->training->title ?? $app->training->name ?? '—' }}</span>
                    </td>
                    <td>
                        <span style="font-size:.82rem;color:#64748b;">
                            {{ $app->created_at ? $app->created_at->format('Y/m/d H:i') : '—' }}
                        </span>
                    </td>
                    <td>
                        @php $st = $app->status ?? 'pending'; @endphp
                        @if($st === 'accepted' || $st === 'approved')
                            <span class="badge-status status-accepted"><i class="fas fa-check-circle me-1"></i>مقبول</span>
                        @elseif($st === 'rejected')
                            <span class="badge-status status-rejected"><i class="fas fa-times-circle me-1"></i>مرفوض</span>
                        @else
                            <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i>قيد الانتظار</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.82rem;color:#64748b;">{{ Str::limit($app->notes ?? '—', 40) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3 d-block"></i>
                            <h5 class="text-secondary fw-bold mb-2">لا توجد طلبات تدريب مسجلة حالياً</h5>
                            <p class="text-muted">ستظهر هنا طلبات الالتحاق بمجرد قيام الخريجين أو الطلاب بالتقديم.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="d-flex justify-content-center p-3">
        {{ $applications->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('appSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#applicationsTable tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
