@extends('layouts.app')

@section('title', 'مكتبة قوالب الاستبيانات')
@section('page-title', 'مكتبة قوالب الاستبيانات')

@push('styles')
<style>
.template-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.25s ease;
    overflow: hidden;
}
.template-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
}
.template-header {
    padding: 20px 22px 14px;
    border-bottom: 1px solid #f1f5f9;
}
.template-body {
    padding: 18px 22px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.template-footer {
    padding: 16px 22px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.cat-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.76rem;
    font-weight: 700;
}
.cat-training { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.cat-employment { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.cat-events { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.cat-general { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }

.filter-btn {
    padding: 8px 18px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.88rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.filter-btn:hover {
    background: #f8fafc;
    color: #1e293b;
}
.filter-btn.active {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}
.questions-preview-box {
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px 14px;
    margin-top: 14px;
    border: 1px solid #f1f5f9;
}
.preview-q-item {
    font-size: 0.82rem;
    color: #475569;
    padding: 4px 0;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.preview-q-item i {
    font-size: 0.75rem;
    margin-top: 4px;
    color: #94a3b8;
}
.type-badge {
    font-size: 0.7rem;
    padding: 2px 7px;
    border-radius: 6px;
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<!-- الشريط العلوي الموحد -->
<x-page-hero
    title="مكتبة قوالب الاستبيانات"
    subtitle="قوالب جاهزة ومعدة مسبقاً لقياس أثر التدريب، التوظيف والشراكات، والفعاليات ومعارض التوظيف، مع إمكانية حفظ وإعادة استخدام قوالبك الخاصة"
    icon="fas fa-layer-group"
    :breadcrumbs="[
        ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
        ['label' => 'إدارة الاستبيانات', 'url' => route('evaluation-followup.surveys.index')],
        ['label' => 'مكتبة القوالب']
    ]"
    badge="التقييم والمتابعة"
>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('evaluation-followup.surveys.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>إنشاء استبيان مخصص</span>
        </a>
        <a href="{{ route('evaluation-followup.surveys.index') }}" class="btn btn-light py-2.5 px-3.5 rounded-3 shadow-sm text-primary fw-bold">
            <i class="fas fa-arrow-right ms-1"></i>
            <span>الاستبيانات النشطة</span>
        </a>
    </div>
</x-page-hero>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- تصنيفات الفلترة السريعة -->
<div class="d-flex align-items-center flex-wrap gap-2 mb-4">
    <a href="{{ route('evaluation-followup.surveys.templates.index') }}" class="filter-btn {{ !$category ? 'active' : '' }}">
        <i class="fas fa-border-all"></i>
        <span>كافة القوالب ({{ $stats['total'] }})</span>
    </a>
    <a href="{{ route('evaluation-followup.surveys.templates.index', ['category' => 'training']) }}" class="filter-btn {{ $category === 'training' ? 'active' : '' }}">
        <i class="fas fa-graduation-cap text-success"></i>
        <span>التدريب وورش العمل ({{ $stats['training'] }})</span>
    </a>
    <a href="{{ route('evaluation-followup.surveys.templates.index', ['category' => 'employment']) }}" class="filter-btn {{ $category === 'employment' ? 'active' : '' }}">
        <i class="fas fa-briefcase text-primary"></i>
        <span>التوظيف والشراكات ({{ $stats['employment'] }})</span>
    </a>
    <a href="{{ route('evaluation-followup.surveys.templates.index', ['category' => 'events']) }}" class="filter-btn {{ $category === 'events' ? 'active' : '' }}">
        <i class="fas fa-calendar-star text-warning"></i>
        <span>المعارض والفعاليات ({{ $stats['events'] }})</span>
    </a>
    <a href="{{ route('evaluation-followup.surveys.templates.index', ['category' => 'custom']) }}" class="filter-btn {{ $category === 'custom' ? 'active' : '' }}">
        <i class="fas fa-bookmark text-purple"></i>
        <span>قوالبي المحفوظة ({{ $stats['custom'] }})</span>
    </a>
</div>

<!-- شبكة بطاقات القوالب -->
<div class="row g-4">
    @forelse($templates as $template)
    <div class="col-lg-4 col-md-6">
        <div class="template-card">
            <div class="template-header">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    @php
                        $catClass = match($template->category) {
                            'training' => 'cat-training',
                            'employment', 'partners' => 'cat-employment',
                            'events', 'internal', 'visitors' => 'cat-events',
                            default => 'cat-general'
                        };
                        $catIcon = match($template->category) {
                            'training' => 'fa-graduation-cap',
                            'employment', 'partners' => 'fa-briefcase',
                            'events', 'internal', 'visitors' => 'fa-calendar-alt',
                            default => 'fa-poll'
                        };
                    @endphp
                    <span class="cat-badge {{ $catClass }}">
                        <i class="fas {{ $catIcon }}"></i>
                        {{ $template->category_label }}
                    </span>

                    <span class="badge bg-light text-muted border">
                        <i class="fas fa-question-circle ms-1 text-primary"></i>
                        {{ count($template->questions ?? []) }} أسئلة
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem; line-height: 1.4;">
                    {{ $template->title }}
                </h5>
                @if($template->is_system)
                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.7rem;">
                        <i class="fas fa-shield-alt ms-1"></i> معتمد من المنظومة
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">
                        <i class="fas fa-user ms-1"></i> قالب محفوظ بواسطة: {{ $template->creator->name ?? 'مستخدم' }}
                    </span>
                @endif
            </div>

            <div class="template-body">
                <p class="text-muted small mb-2" style="line-height: 1.5;">
                    {{ Str::limit($template->description, 110) }}
                </p>

                <div class="mb-2">
                    <span class="text-muted" style="font-size: 0.76rem;">الجمهور المستهدف:</span>
                    <span class="badge bg-info-subtle text-info fw-bold" style="font-size: 0.75rem;">
                        <i class="fas fa-users ms-1"></i> {{ $template->audience_label }}
                    </span>
                </div>

                <!-- مقتطف من أسئلة القالب -->
                <div class="questions-preview-box">
                    <div class="fw-bold text-dark mb-1" style="font-size: 0.78rem;">
                        <i class="fas fa-list-ul ms-1 text-muted"></i> محاور ونماذج الأسئلة:
                    </div>
                    @php $previewQuestions = array_slice($template->questions ?? [], 0, 3); @endphp
                    @foreach($previewQuestions as $pq)
                    <div class="preview-q-item">
                        <i class="fas fa-circle-dot"></i>
                        <span class="text-truncate" title="{{ $pq['question'] ?? '' }}">{{ $pq['question'] ?? '' }}</span>
                    </div>
                    @endforeach
                    @if(count($template->questions ?? []) > 3)
                        <div class="text-muted text-center pt-1" style="font-size: 0.74rem;">
                            + {{ count($template->questions) - 3 }} أسئلة إضافية...
                        </div>
                    @endif
                </div>
            </div>

            <div class="template-footer">
                <a href="{{ route('evaluation-followup.surveys.create', ['template_id' => $template->id]) }}"
                   class="btn btn-primary-modern flex-grow-1 py-2 px-3 rounded-pill fw-bold text-center d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-magic"></i>
                    <span>استخدم هذا القالب</span>
                </a>

                <button type="button"
                        class="btn btn-outline-secondary rounded-circle"
                        style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;"
                        title="معاينة الأسئلة كاملة"
                        onclick="previewTemplate({{ $template->id }})">
                    <i class="fas fa-eye"></i>
                </button>

                @if(!$template->is_system)
                <form action="{{ route('evaluation-followup.surveys.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا القالب؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-circle" style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;" title="حذف القالب">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <i class="fas fa-layer-group fa-3x text-muted mb-3 d-block"></i>
            <h5 class="fw-bold text-dark">لا توجد قوالب في هذا التصنيف حالياً</h5>
            <p class="text-muted">يمكنك اختيار تصنيف آخر أو حفظ أي استبيان كقالب جديد للاستخدام لاحقاً.</p>
            <a href="{{ route('evaluation-followup.surveys.templates.index') }}" class="btn btn-primary rounded-pill px-4">
                عرض كافة القوالب
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Modal معاينة تفاصيل وأسئلة القالب -->
<div class="modal fade" id="templatePreviewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-poll-h"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="previewModalTitle">معاينة القالب</h5>
                        <small class="text-muted" id="previewModalSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert bg-light border p-3 rounded-3 mb-4">
                    <p class="mb-1 text-muted" id="previewModalDesc"></p>
                    <div class="d-flex gap-2 mt-2" id="previewModalBadges"></div>
                </div>

                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-list-check ms-1 text-primary"></i> قائمة الأسئلة بالتفصيل:
                </h6>

                <div id="previewQuestionsList" class="d-flex flex-column gap-3">
                    <!-- تتولد الأسئلة هنا عبر JavaScript -->
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" id="previewUseTemplateBtn" class="btn btn-primary-modern rounded-pill px-4 fw-bold">
                    <i class="fas fa-magic ms-1"></i> استخدام هذا القالب لإنشاء استبيان
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewTemplate(templateId) {
    const modalEl = document.getElementById('templatePreviewModal');
    const modal = new bootstrap.Modal(modalEl);

    document.getElementById('previewModalTitle').textContent = 'جارٍ تحميل القالب...';
    document.getElementById('previewQuestionsList').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>';

    modal.show();

    fetch(`{{ url('/evaluation-followup/surveys/templates') }}/${templateId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) throw new Error('فشل تحميل القالب');
        const t = data.template;

        document.getElementById('previewModalTitle').textContent = t.title;
        document.getElementById('previewModalSubtitle').textContent = `التصنيف: ${t.category_label} | الجمهور: ${t.audience_label}`;
        document.getElementById('previewModalDesc').textContent = t.description || 'لا يوجد وصف إضافي للقالب.';
        document.getElementById('previewUseTemplateBtn').href = t.create_url;

        let badgesHtml = `
            <span class="badge bg-primary-subtle text-primary">${t.category_label}</span>
            <span class="badge bg-info-subtle text-info">${t.audience_label}</span>
            <span class="badge bg-secondary-subtle text-secondary">${t.questions_count} أسئلة</span>
        `;
        document.getElementById('previewModalBadges').innerHTML = badgesHtml;

        const typeLabels = {
            'text': 'نص قصير',
            'textarea': 'نص مفصل',
            'rating': 'تقييم نجوم (1-5)',
            'radio': 'اختيار واحد',
            'checkbox': 'اختيار متعدد',
            'select': 'قائمة منسدلة',
            'date': 'تاريخ',
            'email': 'بريد إلكتروني',
            'number': 'رقم'
        };

        let listHtml = '';
        (t.questions || []).forEach((q, idx) => {
            let optionsHtml = '';
            if (q.options && q.options.length > 0) {
                optionsHtml = '<div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1.5">';
                q.options.forEach(opt => {
                    optionsHtml += `<span class="badge bg-white text-dark border px-2 py-1">${opt}</span>`;
                });
                optionsHtml += '</div>';
            }

            listHtml += `
                <div class="card border rounded-3 p-3 bg-white shadow-none">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                        <div class="fw-bold text-dark">
                            <span class="badge bg-primary me-1">${idx + 1}</span>
                            ${q.question}
                            ${q.required ? '<span class="text-danger">*</span>' : ''}
                        </div>
                        <span class="badge bg-light text-dark border">${typeLabels[q.type] || q.type}</span>
                    </div>
                    ${q.description ? `<small class="text-muted mb-2 d-block">${q.description}</small>` : ''}
                    ${optionsHtml}
                </div>
            `;
        });

        document.getElementById('previewQuestionsList').innerHTML = listHtml;
    })
    .catch(err => {
        console.error(err);
        document.getElementById('previewQuestionsList').innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء تحميل بيانات القالب.</div>';
    });
}
</script>
@endpush
