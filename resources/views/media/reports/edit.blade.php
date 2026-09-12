@extends('layouts.app')

@section('title', 'كتابة وتحرير تقرير التغطية الإعلامية — ' . $training->title)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- Hero Header -->
    <x-page-hero
        title="كتابة وتحرير تقرير التغطية الإعلامية"
        description="صياغة البيان الصحفي الرسمي وتوثيق وقائع الفعالية وفريق التغطية وروابط النشر"
        icon="fas fa-feather-alt"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'تقارير التغطية', 'url' => route('media.reports.coverage')],
            ['label' => 'تحرير تقرير: ' . \Illuminate\Support\Str::limit($training->title, 25)]
        ]"
        secondaryBadge="{{ $training->type_arabic }}"
        secondaryBadgeIcon="fas fa-tag"
    >
        <a href="{{ route('media.reports.coverage.show', $training) }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.88rem;">
            <i class="fas fa-eye"></i>
            <span>معاينة التقرير الرسمي</span>
        </a>
        <a href="{{ route('media.reports.coverage') }}" class="btn btn-outline-light text-white fw-bold py-2 px-3 rounded-3 d-flex align-items-center gap-2" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للتقارير</span>
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

    <form action="{{ route('media.reports.coverage.update', $training) }}" method="POST" id="coverageReportForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- العمود الرئيسي: محتوى التقرير والصياغة الصحفية (8 أعمدة) -->
            <div class="col-lg-8">
                
                <!-- بطاقة ملخص التدريب المرجعي -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-1 mb-2 fw-semibold">
                                    <i class="fas fa-bookmark me-1"></i>{{ $training->type_arabic }}
                                </span>
                                <h4 class="fw-bold text-dark mb-1">{{ $training->title }}</h4>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $training->location ?: 'المقر الرئيسي للجامعة' }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar-alt text-primary me-1"></i>
                                    {{ $training->start_date ? $training->start_date->format('Y/m/d') : '—' }} 
                                    إلى 
                                    {{ $training->end_date ? $training->end_date->format('Y/m/d') : '—' }}
                                </p>
                            </div>
                            <span class="badge rounded-pill bg-{{ $training->status === 'active' ? 'success' : ($training->status === 'completed' ? 'info' : 'secondary') }} px-3 py-2">
                                {{ $training->status_arabic }}
                            </span>
                        </div>
                        @if($training->description)
                            <div class="p-3 rounded-3 bg-light text-secondary small border">
                                <strong>وصف البرنامج الميداني:</strong> {{ $training->description }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- بطاقة البيان الصحفي الرسمي -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #0d3882;">
                                <i class="fas fa-newspaper" style="font-size: 0.85rem;"></i>
                            </span>
                            <span>البيان الصحفي الرسمي المعتمد</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="insertPressTemplate()">
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
                                placeholder="اكتب هنا نص البيان الصحفي الرسمي...
مثال: طرابلس — اختتم مكتب تدريب الخريجين بجامعة طرابلس فعاليات البرنامج التدريبي التخصصي بعنوان ({{ $training->title }})..."
                            >{{ old('media_press_release', $training->media_press_release) }}</textarea>
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
                                rows="6"
                                class="form-control rounded-3 p-3 @error('media_coverage_summary') is-invalid @enderror"
                                style="font-size: 0.92rem; line-height: 1.6; border-color: #cbd5e1;"
                                placeholder="دوّن هنا ملخصاً لأبرز ما تم توثيقه وتغطيته إعلامياً خلال أيام التدريب..."
                            >{{ old('media_coverage_summary', $training->media_coverage_summary) }}</textarea>
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
                            >{{ old('media_coverage_notes', $training->media_coverage_notes) }}</textarea>
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
                                <option value="covered" {{ old('media_coverage_status', $training->media_coverage_status) === 'covered' ? 'selected' : '' }}>
                                    ✅ تمت التغطية والتوثيق (معتمد)
                                </option>
                                <option value="pending" {{ old('media_coverage_status', $training->media_coverage_status) === 'pending' || !$training->media_coverage_status ? 'selected' : '' }}>
                                    ⏳ قيد الإعداد / بانتظار التغطية
                                </option>
                                <option value="not_required" {{ old('media_coverage_status', $training->media_coverage_status) === 'not_required' ? 'selected' : '' }}>
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
                                value="{{ old('media_coverage_date', $training->media_coverage_date ? $training->media_coverage_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
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
                                <span>حفظ التقرير والبيان الصحفي</span>
                            </button>
                            <a href="{{ route('media.reports.coverage.show', $training) }}" class="btn btn-outline-secondary py-2 rounded-3 text-center small">
                                معاينة دون حفظ
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
                            value="{{ old('media_team_members', $training->media_team_members) }}"
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
                        >{{ old('media_coverage_links', $training->media_coverage_links) }}</textarea>
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
function insertPressTemplate() {
    const textarea = document.getElementById('media_press_release');
    if (textarea.value.trim() !== '') {
        if (!confirm('هل ترغب في استبدال النص الحالي بنموذج البيان الصحفي المعتمد؟')) {
            return;
        }
    }

    const title = @json($training->title);
    const location = @json($training->location ?: 'قاعات جامعة طرابلس');
    const org = @json($training->company ? $training->company->name : 'مكتب تدريب الخريجين بجامعة طرابلس');
    const date = @json($training->start_date ? $training->start_date->format('Y/m/d') : now()->format('Y/m/d'));

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
