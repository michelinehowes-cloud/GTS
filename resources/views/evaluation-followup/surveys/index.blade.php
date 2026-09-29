@extends('layouts.app')
@section('title', 'إدارة الاستبيانات')
@section('page-title', 'إدارة الاستبيانات')
@push('styles')
<style>
.page-hero{background:linear-gradient(135deg,#0c4a6e 0%,#0369a1 50%,#0284c7 100%);border-radius:18px;padding:26px 32px;margin-bottom:24px;position:relative;overflow:hidden;box-shadow:0 14px 40px rgba(12,74,110,.3)}
.page-hero::before{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,.05);border-radius:50%}
.page-hero h1{font-size:1.6rem;font-weight:800;color:#fff;margin:0}
.page-hero p{color:rgba(255,255,255,.75);margin:5px 0 0;font-size:.88rem}
.page-badge{background:rgba(186,230,253,.2);border:1px solid rgba(186,230,253,.4);color:#bae6fd;padding:4px 12px;border-radius:16px;font-size:.75rem;font-weight:600;display:inline-flex;align-items:center;gap:5px;margin-bottom:10px}
.action-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:12px;font-weight:600;font-size:.9rem;text-decoration:none;transition:all .2s;border:none;cursor:pointer}
.btn-primary-modern{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 4px 14px rgba(37,99,235,.35)}
.btn-primary-modern:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(37,99,235,.45);color:#fff}
.modern-table-wrap{background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);overflow:hidden}
.modern-table-header{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.modern-table-header h5{font-size:.95rem;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
.th-icon{width:30px;height:30px;border-radius:8px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:.8rem}
.search-input{padding:8px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:.87rem;outline:none;transition:all .2s;width:220px}
.search-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.modern-table{width:100%;border-collapse:separate;border-spacing:0}
.modern-table thead tr{background:#f8fafc}
.modern-table thead th{padding:12px 16px;font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;white-space:nowrap}
.modern-table tbody tr{transition:background .15s}
.modern-table tbody tr:hover{background:#f8fafc}
.modern-table tbody td{padding:14px 16px;font-size:.88rem;color:#334155;border-bottom:1px solid #f8fafc;vertical-align:middle}
.modern-table tbody tr:last-child td{border-bottom:none}
.survey-title{font-weight:600;color:#1e293b}
.survey-desc{font-size:.76rem;color:#94a3b8;margin-top:2px}
.badge-audience{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600;display:inline-flex;align-items:center;gap:4px}
.aud-all{background:#eff6ff;color:#1d4ed8}
.aud-grad{background:#ecfdf5;color:#047857}
.aud-comp{background:#fef3c7;color:#b45309}
.aud-coord{background:#f5f3ff;color:#7c3aed}
.badge-status{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:600}
.status-active{background:#d1fae5;color:#047857}
.status-inactive{background:#f1f5f9;color:#64748b}
.status-scheduled{background:#fef3c7;color:#b45309}
.tbl-actions{display:flex;align-items:center;gap:6px}
.tbl-btn{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.82rem;text-decoration:none;border:none;cursor:pointer;transition:all .2s}
.tbl-btn.view{background:#eff6ff;color:#3b82f6}
.tbl-btn.view:hover{background:#dbeafe;color:#1d4ed8}
.tbl-btn.edit{background:#fffbeb;color:#d97706}
.tbl-btn.edit:hover{background:#fef3c7;color:#b45309}
.tbl-btn.del{background:#fef2f2;color:#ef4444}
.tbl-btn.del:hover{background:#fee2e2;color:#b91c1c}
.empty-state{text-align:center;padding:60px 20px}
.empty-state i{font-size:3rem;color:#cbd5e1;margin-bottom:16px}
.empty-state h5{font-size:1.1rem;font-weight:700;color:#334155;margin:0 0 6px}
.empty-state p{font-size:.9rem;color:#94a3b8;margin:0 0 20px}
.resp-count{display:inline-flex;align-items:center;gap:4px;background:#eff6ff;color:#3b82f6;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:700}
.alert-modern{border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:12px;margin-bottom:16px;border:1px solid transparent}
.alert-modern.success{background:#ecfdf5;border-color:#a7f3d0;color:#047857}
.alert-modern.error{background:#fef2f2;border-color:#fecaca;color:#b91c1c}
</style>
@endpush
@section('content')
<!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
<x-page-hero
    title="إدارة الاستبيانات"
    subtitle="إنشاء وإدارة استبيانات التقييم والمتابعة وقياس رضا الخريجين والشركات"
    icon="fas fa-poll"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'إدارة الاستبيانات']
    ]"
    badge="التقييم والمتابعة"
>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('evaluation-followup.survey-reports') }}" class="btn btn-outline-light py-2.5 px-3.5 rounded-3 shadow-sm fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-chart-pie"></i>
            <span>التقارير والإحصائيات</span>
        </a>
        <a href="{{ route('evaluation-followup.surveys.templates.index') }}" class="btn btn-light py-2.5 px-3.5 rounded-3 shadow-sm text-primary fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-layer-group text-warning"></i>
            <span>مكتبة القوالب</span>
        </a>
        <a href="{{ route('evaluation-followup.surveys.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>إضافة استبيان جديد</span>
        </a>
    </div>
</x-page-hero>

@if(session('success'))
<div class="alert-modern success"><i class="fas fa-check-circle fa-lg"></i><span>{{ session('success') }}</span></div>
@endif
@if(session('error'))
<div class="alert-modern error"><i class="fas fa-exclamation-circle fa-lg"></i><span>{{ session('error') }}</span></div>
@endif

<div class="modern-table-wrap">
    <div class="modern-table-header">
        <h5><div class="th-icon"><i class="fas fa-poll"></i></div>قائمة الاستبيانات ({{ $surveys->total() ?? $surveys->count() }})</h5>
        <input type="text" class="search-input" id="surveySearch" placeholder="🔍 بحث في الاستبيانات...">
    </div>
    <div class="table-responsive">
        <table class="modern-table" id="surveysTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>عنوان الاستبيان</th>
                    <th>الجمهور المستهدف</th>
                    <th>تاريخ البداية</th>
                    <th>تاريخ النهاية</th>
                    <th>الحالة</th>
                    <th>الردود</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveys as $survey)
                <tr>
                    <td><span style="font-size:.78rem;color:#94a3b8;font-weight:600;">{{ $loop->iteration }}</span></td>
                    <td>
                        <div class="survey-title">{{ $survey->title }}</div>
                        <div class="survey-desc">{{ Str::limit($survey->description, 50) }}</div>
                    </td>
                    <td>
                        @php $aud=$survey->target_audience; @endphp
                        <span class="badge-audience {{ $aud=='all'?'aud-all':($aud=='graduates'?'aud-grad':($aud=='companies'?'aud-comp':'aud-coord')) }}">
                            <i class="fas fa-{{ $aud=='all'?'users':($aud=='graduates'?'user-graduate':($aud=='companies'?'building':'chalkboard-teacher')) }}"></i>
                            {{ $aud=='all'?'الكل':($aud=='graduates'?'الخريجين':($aud=='companies'?'الشركات':'منسقو التدريب')) }}
                        </span>
                    </td>
                    <td><span style="font-size:.82rem;">{{ $survey->start_date->format('Y-m-d') }}</span></td>
                    <td><span style="font-size:.82rem;">{{ $survey->end_date->format('Y-m-d') }}</span></td>
                    <td>
                        @if($survey->isActive())
                        <span class="badge-status status-active"><i class="fas fa-circle" style="font-size:.5rem;"></i> نشط</span>
                        @elseif($survey->is_active)
                        <span class="badge-status status-scheduled"><i class="fas fa-clock" style="font-size:.65rem;"></i> مجدول</span>
                        @else
                        <span class="badge-status status-inactive">غير نشط</span>
                        @endif
                    </td>
                    <td><span class="resp-count"><i class="fas fa-comment-alt"></i>{{ $survey->responses_count ?? 0 }}</span></td>
                    <td>
                        <div class="tbl-actions">
                            <a href="{{ route('evaluation-followup.surveys.show', $survey) }}" class="tbl-btn view" title="التقرير والتحليل الإحصائي"><i class="fas fa-chart-pie"></i></a>
                            <a href="{{ route('evaluation-followup.surveys.export-responses', $survey) }}" class="tbl-btn" style="background:#ecfdf5;color:#059669;" title="تصدير النتائج إلى Excel"><i class="fas fa-file-excel"></i></a>
                            <button type="button" class="tbl-btn" style="background:#fdf4ff;color:#a855f7;" title="حفظ كقالب جاهز" onclick="openSaveAsTemplateModal({{ $survey->id }}, '{{ addslashes($survey->title) }}', '{{ addslashes($survey->description ?? '') }}', '{{ $survey->type ?? 'general' }}')">
                                <i class="fas fa-bookmark"></i>
                            </button>
                            <a href="{{ route('evaluation-followup.surveys.edit', $survey) }}" class="tbl-btn edit" title="تعديل"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('evaluation-followup.surveys.destroy', $survey) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذا الاستبيان؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="tbl-btn del" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-poll d-block"></i>
                        <h5>لا توجد استبيانات بعد</h5>
                        <p>ابدأ بإنشاء أول استبيان لجمع البيانات والتقييمات</p>
                        <a href="{{ route('evaluation-followup.surveys.create') }}" class="action-btn btn-primary-modern">
                            <i class="fas fa-plus"></i>إضافة أول استبيان
                        </a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($surveys->hasPages())
    <div class="d-flex justify-content-center p-4">{{ $surveys->links() }}</div>
    @endif
</div>

<!-- Modal حفظ الاستبيان كقالب -->
<div class="modal fade" id="saveAsTemplateModal" tabindex="-1" aria-labelledby="saveTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="saveAsTemplateForm" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-purple text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #8b5cf6;">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0">حفظ الاستبيان كقالب دائم</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        سيتم حفظ كافة أسئلة هذا الاستبيان وخياراته في مكتبة القوالب لتتمكن من إعادة استخدامها بضغطة زر واحدة في أي وقت.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">اسم القالب الجديد <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="template_title" id="modal_template_title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">تصنيف القالب <span class="text-danger">*</span></label>
                        <select class="form-select" name="template_category" id="modal_template_category" required>
                            <option value="training">🎓 التدريب وورش العمل</option>
                            <option value="employment">💼 التوظيف والشراكات</option>
                            <option value="events">🎪 المعارض والفعاليات</option>
                            <option value="general">📋 قالب عام</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">وصف مختصر للقالب</label>
                        <textarea class="form-control" name="template_description" id="modal_template_description" rows="2" placeholder="أدخل وصفاً يوضح الغرض من هذا القالب..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: #8b5cf6; border-color: #8b5cf6;">
                        <i class="fas fa-save me-1"></i> حفظ في مكتبة القوالب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('surveySearch')?.addEventListener('input', function(){
    const q=this.value.toLowerCase();
    document.querySelectorAll('#surveysTable tbody tr').forEach(r=>{
        r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
    });
});

function openSaveAsTemplateModal(surveyId, title, description, type) {
    const form = document.getElementById('saveAsTemplateForm');
    form.action = `{{ url('/evaluation-followup/surveys') }}/${surveyId}/save-template`;

    document.getElementById('modal_template_title').value = title + ' (قالب)';
    document.getElementById('modal_template_description').value = description || '';

    let category = 'general';
    if (type === 'training') category = 'training';
    else if (type === 'job_opportunity') category = 'employment';
    else if (type === 'job_fair') category = 'events';

    document.getElementById('modal_template_category').value = category;

    const modal = new bootstrap.Modal(document.getElementById('saveAsTemplateModal'));
    modal.show();
}
</script>
@endpush
