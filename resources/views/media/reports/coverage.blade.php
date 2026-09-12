@extends('layouts.app')

@section('title', 'تقرير التغطية الإعلامية: ' . $training->title)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- Hero Header (شاشة فقط - يختفي عند الطباعة) -->
    <div class="d-print-none mb-4">
        <x-page-hero
            title="تقرير التغطية الإعلامية والتوثيق الصحفي"
            description="التقرير الفني والصحفي المعتمد لتوثيق وقائع البرنامج التدريبي والمواد الإعلامية المصاحبة"
            icon="fas fa-file-invoice"
            :breadcrumbs="[
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
                ['label' => 'تقارير التغطية', 'url' => route('media.reports.coverage')],
                ['label' => \Illuminate\Support\Str::limit($training->title, 30)]
            ]"
            secondaryBadge="{{ $training->getMediaCoverageStatusText() }}"
            secondaryBadgeIcon="fas fa-check-circle"
        >
            <a href="{{ route('media.reports.coverage.edit', $training) }}" class="btn btn-warning text-dark fw-bold py-2 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                <i class="fas fa-feather-alt"></i>
                <span>تحرير وكتابة التقرير</span>
            </a>
            <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                <i class="fas fa-print"></i>
                <span>طباعة التقرير الرسمي</span>
            </button>
            <a href="{{ route('media.reports.coverage') }}" class="btn btn-outline-light text-white fw-bold py-2 px-3 rounded-3 d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                <i class="fas fa-arrow-right"></i>
                <span>العودة</span>
            </a>
        </x-page-hero>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 d-print-none" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <span class="fw-bold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ورقة التقرير الرسمية المعتمدة (قابلة للطباعة والعرض المتميز) -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden report-sheet" style="background: #ffffff;">
        <div class="card-body p-4 p-md-5">

            <!-- الترويسة الرسمية لجامعة طرابلس -->
            <div class="official-header text-center pb-4 mb-4 border-bottom position-relative">
                <div class="row align-items-center">
                    <div class="col-4 text-start">
                        <p class="mb-0 text-muted small fw-bold">دولة ليبيا</p>
                        <p class="mb-0 text-muted small fw-bold">وزارة التعليم العالي والبحث العلمي</p>
                        <p class="mb-0 text-dark fw-bold" style="font-size: 0.95rem;">جامعة طرابلس</p>
                        <p class="mb-0 text-primary small fw-semibold">مكتب تدريب الخريجين</p>
                    </div>
                    <div class="col-4 text-center">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center border shadow-xs overflow-hidden" style="width: 82px; height: 82px; background: #ffffff;">
                            <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.src='{{ asset('storage/logo.jpg') }}'">
                        </div>
                        <h5 class="fw-bold text-dark mt-2 mb-0" style="letter-spacing: 0.5px;">وحدة الإعلام</h5>
                    </div>
                    <div class="col-4 text-end">
                        <p class="mb-0 text-muted small"><strong>رقم التقرير:</strong> <span class="font-monospace">TR-{{ str_pad($training->id, 5, '0', STR_PAD_LEFT) }}</span></p>
                        <p class="mb-0 text-muted small"><strong>تاريخ الإعداد:</strong> {{ $training->media_coverage_date ? $training->media_coverage_date->format('Y/m/d') : now()->format('Y/m/d') }}</p>
                        <p class="mb-0 text-muted small"><strong>حالة التغطية:</strong> 
                            <span class="badge bg-{{ $training->media_coverage_status === 'covered' ? 'success' : ($training->media_coverage_status === 'pending' ? 'warning text-dark' : 'secondary') }}">
                                {{ $training->getMediaCoverageStatusText() }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <h3 class="fw-bold text-dark mb-1" style="color: #0d3882 !important;">تقرير التغطية والتوثيق الإعلامي الميداني</h3>
                    <p class="text-muted small mb-0">لبرنامج: <strong class="text-dark">{{ $training->title }}</strong></p>
                </div>
            </div>

            <!-- بطاقة ملخص التدريب والبيانات الأساسية -->
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    <span>بيانات المحطة التدريبية</span>
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="border-color: #e2e8f0; font-size: 0.9rem;">
                        <tbody>
                            <tr>
                                <th class="bg-light text-secondary" style="width: 18%;">عنوان البرنامج</th>
                                <td colspan="3"><strong class="text-dark">{{ $training->title }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary">الجهة المنفذة</th>
                                <td>{{ $training->company ? $training->company->name : 'مكتب تدريب الخريجين' }}</td>
                                <th class="bg-light text-secondary" style="width: 18%;">المدرب / المحاضر</th>
                                <td>{{ $training->instructor_name ?? ($training->trainer->name ?? 'غير محدد') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary">تاريخ الانطلاق</th>
                                <td>{{ $training->start_date ? $training->start_date->format('d/m/Y') : '—' }}</td>
                                <th class="bg-light text-secondary">تاريخ الختام</th>
                                <td>{{ $training->end_date ? $training->end_date->format('d/m/Y') : '—' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary">مكان الانعقاد</th>
                                <td>{{ $training->location ?: 'قاعات جامعة طرابلس' }}</td>
                                <th class="bg-light text-secondary">نوع التدريب</th>
                                <td>{{ $training->type_arabic }} ({{ $training->duration }})</td>
                            </tr>
                            @if($training->coordinator)
                            <tr>
                                <th class="bg-light text-secondary">المنسق الإداري</th>
                                <td colspan="3">{{ $training->coordinator->name }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- بطاقة البيان الصحفي الرسمي (مع زر نسخ) -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-newspaper text-primary"></i>
                        <span>البيان الصحفي الرسمي المعتمد للنشر</span>
                    </h6>
                    @if($training->media_press_release)
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-print-none" onclick="copyPressRelease()">
                            <i class="fas fa-copy me-1"></i>نسخ النص
                        </button>
                    @endif
                </div>
                <div class="p-4 rounded-4 border position-relative" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                    @if($training->media_press_release)
                        <div class="press-release-content text-dark" id="pressReleaseContent" style="font-size: 0.95rem; line-height: 1.8; white-space: pre-line;">{{ $training->media_press_release }}</div>
                    @else
                        <div class="text-center py-4 text-muted d-print-none">
                            <i class="fas fa-feather-alt fa-2x mb-2 opacity-50"></i>
                            <p class="mb-2">لم تتم كتابة نص البيان الصحفي الرسمي بعد لهذا البرنامج.</p>
                            <a href="{{ route('media.reports.coverage.edit', $training) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="fas fa-pen me-1"></i>كتابة وصياغة البيان الصحفي الآن
                            </a>
                        </div>
                        <p class="d-none d-print-block text-muted small mb-0">لم يتم إرفاق صياغة للبيان الصحفي.</p>
                    @endif
                </div>
            </div>

            <!-- بطاقة تفاصيل ووقائع التغطية الميدانية -->
            @if($training->media_coverage_summary)
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-file-signature text-success"></i>
                    <span>وقائع وتفاصيل التغطية الميدانية</span>
                </h6>
                <div class="p-4 rounded-4 border" style="background: #ffffff; border-color: #e2e8f0; font-size: 0.92rem; line-height: 1.7; white-space: pre-line;">
                    {{ $training->media_coverage_summary }}
                </div>
            </div>
            @endif

            <!-- شبكة معلومات التغطية والفريق والروابط (Bento) -->
            <div class="row g-3 mb-4">
                <!-- فريق العمل الإعلامي -->
                <div class="col-md-6">
                    <div class="card h-100 border rounded-4 p-3.5" style="border-color: #e2e8f0; background: #ffffff;">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-camera text-primary"></i>
                            <span>كادر وفريق التغطية الإعلامية</span>
                        </h6>
                        <p class="text-dark mb-0 fw-semibold" style="font-size: 0.9rem;">
                            {{ $training->media_team_members ?: 'لم يتم تحديد أسماء الفريق' }}
                        </p>
                    </div>
                </div>

                <!-- تاريخ واعتماد التغطية -->
                <div class="col-md-6">
                    <div class="card h-100 border rounded-4 p-3.5" style="border-color: #e2e8f0; background: #ffffff;">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <span>تاريخ واعتماد التغطية</span>
                        </h6>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-dark fw-bold" style="font-size: 0.9rem;">
                                {{ $training->media_coverage_date ? $training->media_coverage_date->format('Y/m/d') : 'تاريخ غير محدد' }}
                            </span>
                            <span class="badge bg-{{ $training->media_coverage_status === 'covered' ? 'success' : ($training->media_coverage_status === 'pending' ? 'warning text-dark' : 'secondary') }} rounded-pill px-3 py-1.5 fw-bold">
                                {{ $training->getMediaCoverageStatusText() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- روابط النشر والتغطية الخارجية والتخزين السحابي -->
                @if($training->media_coverage_links)
                <div class="col-12">
                    <div class="card border rounded-4 p-3.5" style="border-color: #e2e8f0; background: #ffffff;">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-cloud text-primary"></i>
                            <span>روابط التغطية السحابية، مشاركة الملفات، والنشر الخارجي</span>
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(explode("\n", str_replace("\r", "", $training->media_coverage_links)) as $link)
                                @php $cleanLink = trim($link); @endphp
                                @if(!empty($cleanLink))
                                    <a href="{{ $cleanLink }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 text-primary d-inline-flex align-items-center gap-1.5" style="direction: ltr; font-size: 0.8rem;">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>{{ \Illuminate\Support\Str::limit($cleanLink, 50) }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- الملاحظات والتوصيات -->
                @if($training->media_coverage_notes)
                <div class="col-12">
                    <div class="card border rounded-4 p-3.5" style="border-color: #e2e8f0; background: #fffbeb;">
                        <h6 class="fw-bold text-warning-emphasis mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <span>ملاحظات وتوصيات الفريق الإعلامي</span>
                        </h6>
                        <p class="text-secondary small mb-0" style="line-height: 1.6; white-space: pre-line;">
                            {{ $training->media_coverage_notes }}
                        </p>
                    </div>
                </div>
                @endif
            </div>

            <!-- تذييل وتوقيعات الاعتماد الرسمي (للطباعة) -->
            <div class="official-footer pt-4 mt-4 border-top">
                <div class="row text-center">
                    <div class="col-4">
                        <p class="mb-1 text-muted small fw-bold">مسؤول التغطية الإعلامية</p>
                        <p class="mb-4 text-dark fw-bold">{{ auth()->user()->name }}</p>
                        <p class="text-muted small">التوقيع: ..........................</p>
                    </div>
                    <div class="col-4">
                        <p class="mb-1 text-muted small fw-bold">رئيس وحدة التوثيق والإعلام</p>
                        <p class="mb-4 text-dark fw-bold">وحدة الإعلام — مكتب تدريب الخريجين</p>
                        <p class="text-muted small">التوقيع: ..........................</p>
                    </div>
                    <div class="col-4">
                        <p class="mb-1 text-muted small fw-bold">يعتمد، مدير مكتب تدريب الخريجين</p>
                        <p class="mb-4 text-dark fw-bold">جامعة طرابلس</p>
                        <p class="text-muted small">الخاتم الرسمي: [ &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ]</p>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted" style="font-size: 0.72rem;">
                    تم استخراج هذا التقرير آلياً عبر منظومة متابعة وتدريب الخريجين — جامعة طرابلس بتاريخ {{ now()->format('Y/m/d H:i') }}
                </div>
            </div>

        </div>
    </div>

</div>

@push('styles')
<style>
@media print {
    /* إخفاء عناصر التنقل والأزرار */
    nav, .navbar, .sidebar, .d-print-none, .btn, footer, .page-hero-container {
        display: none !important;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 11pt !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .container-fluid {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .report-sheet {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .report-sheet .card-body {
        padding: 0 !important;
    }

    .official-header {
        border-bottom: 2px double #0d3882 !important;
    }

    .table {
        border-color: #000000 !important;
    }

    .table th {
        background-color: #f1f5f9 !important;
        color: #000000 !important;
    }

    .badge {
        border: 1px solid #000000 !important;
        color: #000000 !important;
        background: transparent !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function copyPressRelease() {
    const el = document.getElementById('pressReleaseContent');
    if (!el) return;

    navigator.clipboard.writeText(el.innerText).then(() => {
        alert('تم نسخ نص البيان الصحفي الرسمي إلى الحافظة بنجاح.');
    }).catch(err => {
        console.error('فشل النسخ:', err);
    });
}
</script>
@endpush
@endsection
