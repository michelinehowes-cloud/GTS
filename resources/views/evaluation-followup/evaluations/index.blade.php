@extends('layouts.app')
@section('title', 'إدارة التقييمات')
@section('page-title', 'إدارة التقييمات')
@push('styles')
<style>
.page-hero{background:linear-gradient(135deg,#1e1b4b 0%,#3730a3 50%,#4338ca 100%);border-radius:18px;padding:26px 32px;margin-bottom:24px;position:relative;overflow:hidden;box-shadow:0 14px 40px rgba(30,27,75,.35)}
.page-hero::before{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,.05);border-radius:50%}
.page-hero h1{font-size:1.6rem;font-weight:800;color:#fff;margin:0}
.page-hero p{color:rgba(255,255,255,.75);margin:5px 0 0;font-size:.88rem}
.page-badge{background:rgba(196,181,253,.2);border:1px solid rgba(196,181,253,.4);color:#c4b5fd;padding:4px 12px;border-radius:16px;font-size:.75rem;font-weight:600;display:inline-flex;align-items:center;gap:5px;margin-bottom:10px}
.action-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:12px;font-weight:600;font-size:.9rem;text-decoration:none;transition:all .2s;border:none;cursor:pointer}
.btn-primary-modern{background:linear-gradient(135deg,#4338ca,#3730a3);color:#fff;box-shadow:0 4px 14px rgba(67,56,202,.35)}
.btn-primary-modern:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(67,56,202,.45);color:#fff}
.filter-bar{background:#fff;border-radius:14px;padding:16px 20px;margin-bottom:20px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.06);display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.filter-select,.filter-input{padding:8px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:.85rem;outline:none;transition:all .2s;background:#fff;color:#334155}
.filter-select:focus,.filter-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.clear-btn{padding:8px 16px;border-radius:10px;background:#f1f5f9;border:none;font-size:.85rem;color:#64748b;cursor:pointer;transition:all .2s;font-weight:500}
.clear-btn:hover{background:#e2e8f0}
.modern-table-wrap{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden}
.modern-table-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.modern-table-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.th-icon{width:30px;height:30px;border-radius:8px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:.8rem}
.search-input{padding:8px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:.87rem;outline:none;transition:all .2s;width:220px}
.search-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.modern-table{width:100%;border-collapse:separate;border-spacing:0}
.modern-table thead tr{background:#f8fafc}
.modern-table thead th{padding:12px 16px;font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;white-space:nowrap}
.modern-table tbody tr{transition:background .15s}
.modern-table tbody tr:hover{background:#f8fafc}
.modern-table tbody td{padding:14px 16px;font-size:.88rem;color:#334155;border-bottom:1px solid #f8fafc;vertical-align:middle}
.modern-table tbody tr:last-child td{border-bottom:none}
.type-badge{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600;display:inline-flex;align-items:center;gap:4px}
.type-perf{background:#eff6ff;color:#1d4ed8}
.type-train{background:#ecfdf5;color:#047857}
.type-emp{background:#fef3c7;color:#b45309}
.type-comp{background:#f5f3ff;color:#7c3aed}
.badge-status{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.status-done{background:#d1fae5;color:#047857}
.status-draft{background:#fef3c7;color:#b45309}
.score-chip{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:.78rem;font-weight:700}
.score-high{background:#d1fae5;color:#047857}
.score-med{background:#fef3c7;color:#b45309}
.score-low{background:#fee2e2;color:#b91c1c}
.tbl-actions{display:flex;align-items:center;gap:6px}
.tbl-btn{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.82rem;text-decoration:none;border:none;cursor:pointer;transition:all .2s}
.tbl-btn.view{background:#f5f3ff;color:#7c3aed}
.tbl-btn.view:hover{background:#ede9fe;color:#5b21b6}
.tbl-btn.edit{background:#fffbeb;color:#d97706}
.tbl-btn.edit:hover{background:#fef3c7;color:#b45309}
.tbl-btn.del{background:#fef2f2;color:#ef4444}
.tbl-btn.del:hover{background:#fee2e2;color:#b91c1c}
.empty-state{text-align:center;padding:60px 20px}
.empty-state i{font-size:3rem;color:#cbd5e1;margin-bottom:16px}
.empty-state h5{font-size:1.1rem;font-weight:700;color:#334155;margin:0 0 6px}
.empty-state p{font-size:.9rem;color:#94a3b8;margin:0 0 20px}
</style>
@endpush
@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="إدارة التقييمات"
    subtitle="إدارة ومتابعة تقييمات الأداء والتدريب والتوظيف عبر كافة مسارات المنظومة"
    icon="fas fa-star"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'إدارة التقييمات']
    ]"
    badge="التقييم والمتابعة"
>
    <a href="{{ route('evaluation-followup.evaluations.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i>
        <span>إضافة تقييم جديد</span>
    </a>
</x-page-hero>

<div class="filter-bar">
    <select class="filter-select" id="typeFilter">
        <option value="">جميع الأنواع</option>
        <option value="performance">تقييم الأداء</option>
        <option value="training">تقييم التدريب</option>
        <option value="employment">تقييم التوظيف</option>
        <option value="company">تقييم الشركة</option>
    </select>
    <select class="filter-select" id="statusFilter">
        <option value="">جميع الحالات</option>
        <option value="draft">مسودة</option>
        <option value="completed">مكتمل</option>
    </select>
    <input type="date" class="filter-input" id="dateFilter">
    <button class="clear-btn" onclick="clearFilters()"><i class="fas fa-times me-1"></i>مسح</button>
    <div class="me-auto"></div>
    <input type="text" class="search-input" id="evalSearch" placeholder="🔍 بحث...">
</div>

<div class="modern-table-wrap">
    <div class="modern-table-header">
        <h5><div class="th-icon"><i class="fas fa-star"></i></div>قائمة التقييمات</h5>
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="evaluationsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نوع التقييم</th>
                    <th>التدريب</th>
                    <th>المقيِّم</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>متوسط الدرجات</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $evaluation)
                @php
                    $allScores = [];
                    foreach(['facilities_evaluation','content_evaluation','trainer_evaluation','organization_evaluation','impact_evaluation','employment_evaluation'] as $field){
                        if($evaluation->$field) $allScores=array_merge($allScores,array_filter((array)$evaluation->$field,'is_numeric'));
                    }
                    $avgScore=count($allScores)>0?array_sum($allScores)/count($allScores):0;
                    $typeMap=['performance'=>['لون'=>'type-perf','نص'=>'تقييم الأداء','أيقونة'=>'fa-tachometer-alt'],'training'=>['لون'=>'type-train','نص'=>'تقييم التدريب','أيقونة'=>'fa-graduation-cap'],'employment'=>['لون'=>'type-emp','نص'=>'تقييم التوظيف','أيقونة'=>'fa-briefcase'],'company'=>['لون'=>'type-comp','نص'=>'تقييم الشركة','أيقونة'=>'fa-building']];
                    $typeInfo=$typeMap[$evaluation->type]??['لون'=>'type-comp','نص'=>$evaluation->type,'أيقونة'=>'fa-star'];
                @endphp
                <tr>
                    <td><span style="font-size:.76rem;color:#94a3b8;font-weight:600;">{{ $loop->iteration }}</span></td>
                    <td><span class="type-badge {{ $typeInfo['لون'] }}"><i class="fas {{ $typeInfo['أيقونة'] }}"></i>{{ $typeInfo['نص'] }}</span></td>
                    <td><span style="font-size:.85rem;font-weight:500;">{{ $evaluation->training->title ?? '-' }}</span></td>
                    <td><span style="font-size:.85rem;">{{ $evaluation->evaluator->name ?? '-' }}</span></td>
                    <td><span style="font-size:.82rem;color:#64748b;">{{ $evaluation->evaluation_date ? \Carbon\Carbon::parse($evaluation->evaluation_date)->format('Y/m/d') : '-' }}</span></td>
                    <td><span class="badge-status {{ $evaluation->status=='completed'?'status-done':'status-draft' }}">{{ $evaluation->status=='completed'?'مكتمل':'مسودة' }}</span></td>
                    <td>
                        @if($avgScore>0)
                        <span class="score-chip {{ $avgScore>=4?'score-high':($avgScore>=3?'score-med':'score-low') }}">
                            <i class="fas fa-star" style="font-size:.65rem;"></i>{{ number_format($avgScore,1) }}/5
                        </span>
                        @else<span style="color:#cbd5e1;font-size:.85rem;">—</span>@endif
                    </td>
                    <td>
                        <div class="tbl-actions">
                            <a href="{{ route('evaluation-followup.evaluations.show', $evaluation) }}" class="tbl-btn view" title="عرض"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('evaluation-followup.evaluations.edit', $evaluation) }}" class="tbl-btn edit" title="تعديل"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('evaluation-followup.evaluations.destroy', $evaluation) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذا التقييم؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="tbl-btn del" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-star d-block"></i>
                        <h5>لا توجد تقييمات بعد</h5>
                        <p>ابدأ بإنشاء تقييم جديد لقياس مستوى الأداء والتدريب</p>
                        <a href="{{ route('evaluation-followup.evaluations.create') }}" class="action-btn btn-primary-modern">
                            <i class="fas fa-plus"></i>إضافة تقييم
                        </a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($evaluations->hasPages())
    <div class="d-flex justify-content-center p-4">{{ $evaluations->links() }}</div>
    @endif
</div>
@endsection
@push('scripts')
<script>
const typeFilter=document.getElementById('typeFilter');
const statusFilter=document.getElementById('statusFilter');
const dateFilter=document.getElementById('dateFilter');
const searchInput=document.getElementById('evalSearch');
function applyFilters(){
    const type=typeFilter.value,status=statusFilter.value,date=dateFilter.value,q=searchInput.value.toLowerCase();
    const types={'performance':'تقييم الأداء','training':'تقييم التدريب','employment':'تقييم التوظيف','company':'تقييم الشركة'};
    const statuses={'draft':'مسودة','completed':'مكتمل'};
    document.querySelectorAll('#evaluationsTable tbody tr').forEach(row=>{
        if(row.cells.length<8){row.style.display='';return;}
        const rowType=row.cells[1].textContent.trim();
        const rowStatus=row.cells[5].textContent.trim();
        const rowDate=row.cells[4].textContent.trim();
        const rowText=row.textContent.toLowerCase();
        let show=true;
        if(type&&!rowType.includes(types[type]||type))show=false;
        if(status&&!rowStatus.includes(statuses[status]||status))show=false;
        if(date&&!rowDate.includes(date.replace(/-/g,'/')))show=false;
        if(q&&!rowText.includes(q))show=false;
        row.style.display=show?'':'none';
    });
}
[typeFilter,statusFilter,dateFilter,searchInput].forEach(el=>el?.addEventListener('input',applyFilters));
window.clearFilters=function(){typeFilter.value='';statusFilter.value='';dateFilter.value='';searchInput.value='';applyFilters();};
</script>
@endpush
