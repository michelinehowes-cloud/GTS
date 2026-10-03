@extends('layouts.app')

@section('title', 'إضافة تقرير تغطية إعلامية جديد | وحدة الإعلام')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- Hero Header -->
    <x-page-hero
        title="إضافة وتوثيق تقرير تغطية إعلامية"
        description="صياغة البيان الصحفي الرسمي وتوثيق وقائع البرامج التدريبية وروابط التغطية الرقمية"
        icon="fas fa-file-invoice"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'تقارير التغطية', 'url' => route('media.reports.coverage')],
            ['label' => 'إضافة تقرير تغطية جديد']
        ]"
        secondaryBadge="توثيق معتمد"
        secondaryBadgeIcon="fas fa-check-circle"
    >
        <a href="{{ route('media.reports.coverage') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center gap-2" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لتقارير التغطية</span>
        </a>
        <a href="{{ route('media.dashboard') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center gap-2" style="font-size: 0.88rem;">
            <i class="fas fa-th-large"></i>
            <span>لوحة الميديا</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <span class="fw-bold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle fs-5"></i>
                <span class="fw-bold">يرجى تصحيح الأخطاء التالية:</span>
            </div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('media.reports.coverage.store') }}" method="POST" id="createCoverageReportForm">
        @csrf

        <div class="row g-4">
            <!-- العمود الرئيسي: اختيار البرنامج ومحتوى التقرير (8 أعمدة) -->
            <div class="col-lg-8">

                <!-- بطاقة اختيار البرنامج التدريبي المستهدف -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #0d3882;">
                                <i class="fas fa-graduation-cap" style="font-size: 0.85rem;"></i>
                            </span>
                            <span>البرنامج التدريبي المستهدف <span class="text-danger">*</span></span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">
                            اختر البرنامج التدريبي المراد صياغة تقرير التغطية الإعلامية والبيان الصحفي الخاص به:
                        </p>

                        <!-- حقل البحث والتصفية للبرامج -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" id="trainingFilterInput" class="form-control bg-light border-start-0 ps-0" placeholder="اكتب للبحث باسم البرنامج أو الشركة أو القاعة...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <select name="training_id" id="targetTrainingSelect" class="form-select bg-light rounded-3 p-3 @error('training_id') is-invalid @enderror" size="6" style="height: 190px;" required>
                                @php
                                    $pendingGroup = $allTrainings->filter(function($t) {
                                        return $t->media_coverage_status === 'pending' || is_null($t->media_coverage_status);
                                    });
                                    $otherGroup = $allTrainings->filter(function($t) {
                                        return $t->media_coverage_status !== 'pending' && !is_null($t->media_coverage_status);
                                    });
                                @endphp

                                @if($pendingGroup->isNotEmpty())
                                    <optgroup label="⭐ برامج بانتظار التغطية الإعلامية (أولوية التوثيق)">
                                        @foreach($pendingGroup as $t)
                                            <option value="{{ $t->id }}"
                                                    {{ (old('training_id') == $t->id || request('training_id') == $t->id) ? 'selected' : '' }}
                                                    data-title="{{ $t->title }}"
                                                    data-location="{{ $t->location ?: 'جامعة طرابلس' }}"
                                                    data-date="{{ $t->start_date ? $t->start_date->format('Y-m-d') : 'غير محدد' }}"
                                                    data-company="{{ $t->company ? $t->company->name : ($t->coordinator ? $t->coordinator->name : 'مكتب تدريب الخريجين') }}"
                                                    data-type="{{ $t->type_arabic ?? 'تدريب' }}"
                                                    data-status="⏳ بانتظار التغطية">
                                                {{ $t->title }} — ({{ $t->company ? $t->company->name : 'جامعة طرابلس' }} | {{ $t->start_date ? $t->start_date->format('Y-m-d') : '--' }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif

                                @if($otherGroup->isNotEmpty())
                                    <optgroup label="📌 باقي البرامج والفعاليات المسجلة">
                                        @foreach($otherGroup as $t)
                                            <option value="{{ $t->id }}"
                                                    {{ (old('training_id') == $t->id || request('training_id') == $t->id) ? 'selected' : '' }}
                                                    data-title="{{ $t->title }}"
                                                    data-location="{{ $t->location ?: 'جامعة طرابلس' }}"
                                                    data-date="{{ $t->start_date ? $t->start_date->format('Y-m-d') : 'غير محدد' }}"
                                                    data-company="{{ $t->company ? $t->company->name : ($t->coordinator ? $t->coordinator->name : 'مكتب تدريب الخريجين') }}"
                                                    data-type="{{ $t->type_arabic ?? 'تدريب' }}"
                                                    data-status="{{ $t->getMediaCoverageStatusText() }}">
                                                {{ $t->title }} — ({{ $t->company ? $t->company->name : 'جامعة طرابلس' }} | {{ $t->start_date ? $t->start_date->format('Y-m-d') : '--' }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('training_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- بطاقة معاينة البرنامج التدريبي المختار -->
                        <div id="selectedTrainingCard" class="card border rounded-3 p-3 bg-light d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span id="cardTypeBadge" class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-2.5 py-1 fw-bold"></span>
                                <span id="cardStatusBadge" class="badge rounded-pill bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-2.5 py-1 fw-bold"></span>
                            </div>
                            <h6 id="cardTitle" class="fw-bold text-dark mb-1"></h6>
                            <div class="d-flex align-items-center gap-3 text-muted small flex-wrap mt-2">
                                <span><i class="fas fa-building text-primary me-1"></i><span id="cardCompany"></span></span>
                                <span><i class="fas fa-map-marker-alt text-danger me-1"></i><span id="cardLocation"></span></span>
                                <span><i class="far fa-calendar-alt text-primary me-1"></i><span id="cardDate"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة البيان الصحفي الرسمي -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #0d3882;">
                                <i class="fas fa-newspaper" style="font-size: 0.85rem;"></i>
                            </span>
                            <span>البيان الصحفي الرسمي المعتمد</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="generatePressTemplate()">
                            <i class="fas fa-magic me-1"></i>توليد مسودة رسمية
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">
                            الصياغة الصحفية الرسمية المعتمدة للنشر في الموقع الإلكتروني لجامعة طرابلس والصحف والصفحات الرسمية لوزارة التعليم العالي.
                        </p>
                        <div class="form-group mb-0">
                            <textarea
                                name="media_press_release"
                                id="media_press_release"
                                rows="8"
                                class="form-control rounded-3 p-3 @error('media_press_release') is-invalid @enderror"
                                style="font-size: 0.95rem; line-height: 1.7; border-color: #cbd5e1;"
                                placeholder="اكتب هنا نص البيان الصحفي الرسمي... أو اضغط 'توليد مسودة رسمية' أعلاه لصياغة مسودة تلقائية مستندة لبيانات البرنامج التدريبي."
                            >{{ old('media_press_release') }}</textarea>
                            @error('media_press_release')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- بطاقة وقائع وتفاصيل التغطية الميدانية -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #059669;">
                                <i class="fas fa-file-signature" style="font-size: 0.85rem;"></i>
                            </span>
                            <span>تفاصيل ووقائع التغطية الميدانية</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">
                            ملخص التغطية الصحفية الميدانية: مجريات الجلسات، التفاعل مع الخريجين، ورش العمل المصاحبة، والكلمات الرسمية.
                        </p>
                        <div class="form-group mb-0">
                            <textarea
                                name="media_coverage_summary"
                                id="media_coverage_summary"
                                rows="5"
                                class="form-control rounded-3 p-3 @error('media_coverage_summary') is-invalid @enderror"
                                style="font-size: 0.92rem; line-height: 1.6; border-color: #cbd5e1;"
                                placeholder="دوّن هنا ملخصاً لأبرز ما تم توثيقه وتغطيته إعلامياً خلال أيام التدريب..."
                            >{{ old('media_coverage_summary') }}</textarea>
                            @error('media_coverage_summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- بطاقة التوصيات والملاحظات الفنية للمكتب الإعلامي -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #b45309;">
                                <i class="fas fa-lightbulb" style="font-size: 0.85rem;"></i>
                            </span>
                            <span>الملاحظات الفنية وتوصيات الفريق الإعلامي</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">
                            ملاحظات حول إضاءة القاعة، جودة الصوتيات، التفاعل الرقمي، وتوصيات لتحسين التغطية للفعاليات اللاحقة.
                        </p>
                        <div class="form-group mb-0">
                            <textarea
                                name="media_coverage_notes"
                                id="media_coverage_notes"
                                rows="4"
                                class="form-control rounded-3 p-3 @error('media_coverage_notes') is-invalid @enderror"
                                style="font-size: 0.92rem; line-height: 1.6; border-color: #cbd5e1;"
                                placeholder="ملاحظات وتوصيات الفريق الإعلامي والمصورين..."
                            >{{ old('media_coverage_notes') }}</textarea>
                            @error('media_coverage_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- العمود الجانبي: إعدادات وفريق التغطية والروابط (4 أعمدة) -->
            <div class="col-lg-4">

                <!-- بطاقة اعتماد وحفظ التقرير -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fas fa-save text-primary me-2"></i>اعتماد وحفظ التقرير
                        </h6>

                        <!-- حالة التغطية -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">
                                حالة التغطية الإعلامية <span class="text-danger">*</span>
                            </label>
                            <select name="media_coverage_status" class="form-select rounded-3 p-2.5 @error('media_coverage_status') is-invalid @enderror" required>
                                <option value="covered" {{ old('media_coverage_status', 'covered') === 'covered' ? 'selected' : '' }}>
                                    ✅ تمت التغطية والتوثيق (معتمد)
                                </option>
                                <option value="pending" {{ old('media_coverage_status') === 'pending' ? 'selected' : '' }}>
                                    ⏳ قيد الإعداد / بانتظار التغطية
                                </option>
                                <option value="not_required" {{ old('media_coverage_status') === 'not_required' ? 'selected' : '' }}>
                                    ⚪ غير مطلوبة
                                </option>
                            </select>
                            @error('media_coverage_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- تاريخ إنجاز التغطية -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">
                                تاريخ إنجاز التغطية الإعلامية
                            </label>
                            <input
                                type="date"
                                name="media_coverage_date"
                                class="form-control rounded-3 p-2.5 @error('media_coverage_date') is-invalid @enderror"
                                value="{{ old('media_coverage_date', now()->format('Y-m-d')) }}"
                            >
                            @error('media_coverage_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.75rem;">تاريخ إعداد أو تصوير التغطية</small>
                        </div>

                        <!-- أزرار الإجراء -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #1d4ed8 0%, #0d3882 100%);">
                                <i class="fas fa-check-circle"></i>
                                <span>حفظ واعتماد التقرير الصحفي</span>
                            </button>
                            <a href="{{ route('media.reports.coverage') }}" class="btn btn-outline-secondary py-2 rounded-3 text-center small">
                                إلغاء والعودة
                            </a>
                        </div>
                    </div>
                </div>

                <!-- بطاقة فريق العمل الإعلامي -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-users-cog text-primary"></i>
                            <span>فريق التغطية والمصورين</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            أسماء الكادر الإعلامي والمصورين
                        </label>
                        <input
                            type="text"
                            name="media_team_members"
                            class="form-control rounded-3 p-2.5 @error('media_team_members') is-invalid @enderror"
                            placeholder="مثال: أ. محمد سالم (محرر)، م. أحمد كمال (تصوير فوتوغرافي)"
                            value="{{ old('media_team_members') }}"
                        >
                        @error('media_team_members')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            يتم إدراج هذه الأسماء رسمياً في ترويسة التقرير المطبوع.
                        </small>
                    </div>
                </div>

                <!-- بطاقة روابط النشر والتغطية الخارجية -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-share-alt text-primary"></i>
                            <span>روابط النشر والسوشيال ميديا</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-2">
                            أضف روابط المنشورات الخارجية (فيسبوك، لينكدإن، يوتيوب، مجلد صور Google Drive أو Dropbox).
                        </p>
                        <textarea
                            name="media_coverage_links"
                            rows="4"
                            class="form-control rounded-3 p-2.5 font-monospace @error('media_coverage_links') is-invalid @enderror"
                            style="font-size: 0.85rem; direction: ltr;"
                            placeholder="https://facebook.com/post/...&#10;https://drive.google.com/drive/folders/...&#10;https://youtube.com/watch?v=..."
                        >{{ old('media_coverage_links') }}</textarea>
                        @error('media_coverage_links')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            اكتب رابطاً واحداً في كل سطر.
                        </small>
                    </div>
                </div>

                <!-- بطاقة إرشادات التوثيق السحابي والخارجي -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-cloud text-primary"></i>
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">سياسة التوثيق السحابي</h6>
                        </div>
                        <p class="text-muted small mb-2" style="font-size: 0.8rem; line-height: 1.5;">
                            يتم حفظ ومشاركة ألبومات الصور عالية الدقة والفيديوهات عبر الخدمات السحابية الخارجية لضمان الجودة العالية دون استهلاك مساحة السيرفر:
                        </p>
                        <ul class="text-muted small ps-3 mb-0" style="font-size: 0.78rem; line-height: 1.6;">
                            <li>Google Drive أو OneDrive للصور الأصلية</li>
                            <li>YouTube أو Vimeo للتسجيلات المرئية</li>
                            <li>منشورات المنصات الرسمية لجامعة طرابلس</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterInput = document.getElementById('trainingFilterInput');
    const select = document.getElementById('targetTrainingSelect');
    const card = document.getElementById('selectedTrainingCard');
    const cardTitle = document.getElementById('cardTitle');
    const cardCompany = document.getElementById('cardCompany');
    const cardLocation = document.getElementById('cardLocation');
    const cardDate = document.getElementById('cardDate');
    const cardTypeBadge = document.getElementById('cardTypeBadge');
    const cardStatusBadge = document.getElementById('cardStatusBadge');

    function updateCard() {
        if (!select) return;
        const selected = select.options[select.selectedIndex];
        if (selected && selected.value) {
            cardTitle.textContent = selected.dataset.title || '';
            cardCompany.textContent = selected.dataset.company || 'غير محدد';
            cardLocation.textContent = selected.dataset.location || 'جامعة طرابلس';
            cardDate.textContent = selected.dataset.date || '—';
            cardTypeBadge.textContent = selected.dataset.type || 'تدريب';
            cardStatusBadge.textContent = selected.dataset.status || '';

            card.classList.remove('d-none');
        } else {
            card.classList.add('d-none');
        }
    }

    if (filterInput && select) {
        filterInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const options = select.querySelectorAll('option');
            options.forEach(opt => {
                const text = (opt.textContent + ' ' + (opt.dataset.company || '') + ' ' + (opt.dataset.location || '')).toLowerCase();
                opt.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    if (select) {
        select.addEventListener('change', updateCard);
        updateCard();
    }
});

function generatePressTemplate() {
    const select = document.getElementById('targetTrainingSelect');
    const textarea = document.getElementById('media_press_release');

    if (!select || !select.value) {
        alert('يرجى أولاً اختيار البرنامج التدريبي المستهدف لتوليد البيان الصحفي المخصص له.');
        select.focus();
        return;
    }

    if (textarea.value.trim() !== '') {
        if (!confirm('هل ترغب في استبدال النص الحالي بنموذج البيان الصحفي المعتمد؟')) {
            return;
        }
    }

    const selected = select.options[select.selectedIndex];
    const title = selected.dataset.title || 'البرنامج التدريبي';
    const location = selected.dataset.location || 'قاعات جامعة طرابلس';
    const org = selected.dataset.company || 'مكتب تدريب الخريجين بجامعة طرابلس';
    const date = selected.dataset.date || new Date().toISOString().slice(0, 10);

    const template = `بيان صحفي: اختتام فعاليات البرنامج التدريبي "${title}" بجامعة طرابلس

طرابلس — ${date}
في إطار حرص جامعة طرابلس ومكتب تدريب الخريجين على تأهيل الكفاءات الوطنية وربط مخرجات التعليم العالي بمتطلبات سوق العمل، اختتمت بنجاح فعاليات البرنامج التدريبي التخصصي بعنوان "${title}" والذي انعقد في ${location} بالتعاون مع ${org}.

وشهد البرنامج مشاركة نخبة من الخريجين والمهتمين، حيث تلقوا تدريبات مكثفة على مدار عدة أيام ركزت على المهارات العملية والتطبيقات الميدانية الحديثة بإشراف مدربين وخبراء معتمدين.

وأكد القائمون على البرنامج أن هذه الخطوة تأتي استكمالاً للخطة الاستراتيجية للجامعة لدعم التمكين المهني للشباب وفتح آفاق واعدة أمامهم في سوق العمل المحلي والدولي.

انتهى البيان.`;

    textarea.value = template;
    textarea.focus();
}
</script>
@endpush
@endsection
