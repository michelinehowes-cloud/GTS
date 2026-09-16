@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الميديا والإعلام — وحدة الإعلام')
@section('page-title', 'لوحة تحكم مسؤول الميديا والإعلام')

@push('styles')
<style>
    /* Bento Grid and Studio Cards */
    .bento-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .bento-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -6px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }

    .stat-circle-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    /* Quick Action Buttons */
    .quick-action-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.1rem;
        text-decoration: none;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
    }
    .quick-action-item:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        transform: translateX(-4px);
        color: #1d4ed8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- الشريط الأزرق الملكي الموحد المعتمد في المنظومة -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #0d3882 0%, #1e40af 50%, #0284c7 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px; font-size: 1.8rem; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff;">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-bold mb-0 text-white">وحدة الإعلام والتغطيات الصحفية</h4>
                            <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 23, 42, 0.55); color: #f1f5f9; border: 1px solid rgba(255, 255, 255, 0.25); font-size: 0.8rem; font-weight: 600;">
                                <i class="fas fa-check-circle text-success me-1"></i> بوابة التوثيق والنشر
                            </span>
                        </div>
                        <p class="text-white-50 small mb-0">
                            جامعة طرابلس &bull; إدارة التغطيات الميدانية، تحرير ونشر الأخبار والبيانات الصحفية، ومتابعة إحصائيات المنصة الرسمية.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('media.news.create') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2">
                        <i class="fas fa-pen-nib"></i>
                        <span>تحرير خبر جديد</span>
                    </a>
                    <a href="{{ route('media.coverage-calendar') }}" class="btn btn-outline-light rounded-pill px-3 py-2">
                        <i class="fas fa-calendar-alt me-1"></i> تقويم التغطيات
                    </a>
                    <a href="{{ route('job-fair.public') }}" target="_blank" class="btn btn-outline-light rounded-pill px-3 py-2">
                        <i class="fas fa-globe me-1"></i> صفحة المعرض
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- شبكة Bento Grid الإحصائية الحديثة -->
    <div class="row g-3 mb-4">
        
        <!-- بطاقة التقارير والبيانات الصحفية -->
        <div class="col-sm-6 col-lg-3">
            <div class="card bento-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-circle-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <a href="{{ route('media.reports.coverage') }}" class="btn btn-sm btn-outline-info rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">استعراض &larr;</a>
                </div>
                <div class="small text-muted fw-bold mb-1">تقارير التغطية والبيانات</div>
                <h3 class="fw-bold text-dark mb-1">{{ $reportsCount }} <span class="fs-6 fw-normal text-muted">تقرير وبيان</span></h3>
                <div class="small text-muted">
                    <a href="{{ route('media.reports.coverage') }}" class="text-decoration-none text-info fw-bold">البيانات الصحفية والتقارير &larr;</a>
                </div>
            </div>
        </div>

        <!-- بطاقة تغطية البرامج التدريبية -->
        <div class="col-sm-6 col-lg-3">
            <div class="card bento-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-circle-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <a href="{{ route('media.reports.coverage') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">تقارير التغطية الصحفية &larr;</a>
                </div>
                <div class="small text-muted fw-bold mb-1">تغطية البرامج التدريبية</div>
                <h3 class="fw-bold text-dark mb-1">{{ $coveredTrainings }} <span class="fs-6 fw-normal text-muted">/ {{ $totalTrainings }} تدريب</span></h3>
                <div class="progress my-2" style="height: 6px; border-radius: 999px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $coverageRate }}%;" aria-valuenow="{{ $coverageRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="small text-muted mt-1 d-flex align-items-center justify-content-between">
                    <a href="{{ route('media.reports.coverage') }}" class="text-decoration-none text-primary fw-bold">إدارة التغطيات &larr;</a>
                    <span class="badge rounded-pill fw-bold" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 0.72rem; border: 1px solid rgba(13, 56, 130, 0.2);">{{ $coverageRate }}% إنجاز</span>
                </div>
            </div>
        </div>

        <!-- بطاقة الأخبار النشطة -->
        <div class="col-sm-6 col-lg-3">
            <div class="card bento-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-circle-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <a href="{{ route('media.news.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">+ نشر خبر</a>
                </div>
                <div class="small text-muted fw-bold mb-1">الأخبار والبيانات الصحفية</div>
                <h3 class="fw-bold text-dark mb-1">{{ $activeNews }} <span class="fs-6 fw-normal text-muted">خبر معتمد</span></h3>
                <div class="small text-muted">
                    <a href="{{ route('media.news.index') }}" class="text-decoration-none text-success fw-bold">إدارة الأخبار &larr;</a>
                </div>
            </div>
        </div>

        <!-- بطاقة الإعلانات النشطة -->
        <div class="col-sm-6 col-lg-3">
            <div class="card bento-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-circle-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <a href="{{ route('media.announcements.create') }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-0.5 text-dark" style="font-size: 0.72rem;">+ نشر إعلان</a>
                </div>
                <div class="small text-muted fw-bold mb-1">الإعلانات والتعميمات</div>
                <h3 class="fw-bold text-dark mb-1">{{ $activeAnnouncements }} <span class="fs-6 fw-normal text-muted">إعلان نشط</span></h3>
                <div class="small text-muted">
                    <a href="{{ route('media.announcements.index') }}" class="text-decoration-none text-warning fw-bold">إدارة الإعلانات &larr;</a>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">
        
        <!-- أحدث الأخبار والتغطيات الصحفية -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-newspaper text-primary me-2"></i>أحدث الأخبار والتغطيات الصحفية
                    </h6>
                    <a href="{{ route('media.news.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="fas fa-list me-1"></i> إدارة الأخبار
                    </a>
                </div>
                <div class="card-body p-3">
                    @if(isset($recentNews) && $recentNews->count() > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($recentNews as $news)
                                <div class="p-3 rounded-3 border bg-light bg-opacity-50 d-flex align-items-start justify-content-between gap-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 flex-shrink-0" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">{{ $news->title }}</h6>
                                            <div class="d-flex align-items-center gap-2 text-muted small flex-wrap">
                                                <span><i class="far fa-calendar-alt me-1"></i>{{ $news->published_at ? $news->published_at->format('Y-m-d') : $news->created_at->format('Y-m-d') }}</span>
                                                <span>&bull;</span>
                                                <span class="badge {{ $news->is_active ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 text-{{ $news->is_active ? 'success' : 'secondary' }} rounded-pill px-2">
                                                    {{ $news->is_active ? 'منشور' : 'مسودة' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('media.news.edit', $news) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            تعديل
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-newspaper fa-3x mb-3 opacity-50"></i>
                            <p class="mb-2">لا توجد أخبار منشورة حالياً</p>
                            <a href="{{ route('media.news.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                + تحرير أول خبر صحفي
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- منصة الإجراءات السريعة (Quick Action Dock) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-bolt text-warning me-2"></i>منصة الوصول والإجراءات السريعة
                    </h6>
                </div>
                <div class="card-body p-3 d-flex flex-column gap-2">

                    <a href="{{ route('media.coverage-calendar') }}" class="quick-action-item">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">تقويم وجدول التغطيات الإعلامية</div>
                            <small class="text-muted">متابعة الفعاليات وجدولة مهام التصوير والتوثيق</small>
                        </div>
                        <i class="fas fa-chevron-left text-muted opacity-50"></i>
                    </a>

                    <a href="{{ route('media.news.create') }}" class="quick-action-item">
                        <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-pen-nib"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">تحرير ونشر خبر صحفي جديد</div>
                            <small class="text-muted">نشر البيانات الرسمية وتغطيات الأنشطة الجامعية</small>
                        </div>
                        <i class="fas fa-chevron-left text-muted opacity-50"></i>
                    </a>

                    <a href="{{ route('media.reports.coverage') }}" class="quick-action-item">
                        <div class="rounded-3 bg-warning bg-opacity-15 text-dark p-2 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-feather-alt text-warning-emphasis"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">تقارير التغطية والبيانات الصحفية</div>
                            <small class="text-muted">صياغة البيانات الرسمية واستخراج تقارير التوثيق الميداني</small>
                        </div>
                        <i class="fas fa-chevron-left text-muted opacity-50"></i>
                    </a>

                    <a href="{{ route('media.platform-stats') }}" class="quick-action-item">
                        <div class="rounded-3 bg-purple bg-opacity-10 p-2 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">إحصائيات المنصة والصفحة الرئيسية</div>
                            <small class="text-muted">التحكم في الأرقام المعروضة للجمهور (تلقائي حقيقي أو معتمد)</small>
                        </div>
                        <i class="fas fa-chevron-left text-muted opacity-50"></i>
                    </a>

                    <a href="{{ route('job-fair.public') }}" target="_blank" class="quick-action-item">
                        <div class="rounded-3 bg-info bg-opacity-10 text-info p-2 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">الصفحة الزرقاء لمعرض التوظيف 2026</div>
                            <small class="text-muted">معاينة واجهة المعرض العامة للزوار والشركات</small>
                        </div>
                        <i class="fas fa-external-link-alt text-muted opacity-50"></i>
                    </a>

                </div>
            </div>
        </div>

    </div>

    <!-- قائمة التدريبات القادمة وحالة تغطيتها الإعلامية -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-calendar-check text-primary me-2"></i>التدريبات والفعاليات القادمة وحالة التغطية
            </h6>
            <a href="{{ route('media.coverage-calendar') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                عرض الجدول الكامل &larr;
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr class="text-secondary small fw-bold">
                        <th class="py-3 px-3">البرنامج التدريبي</th>
                        <th class="py-3">الموقع / القاعة</th>
                        <th class="py-3 text-center">تاريخ الانطلاق</th>
                        <th class="py-3 text-center">تاريخ الختام</th>
                        <th class="py-3 text-center">حالة التغطية</th>
                        <th class="py-3 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingTrainings as $training)
                        <tr>
                            <td class="px-3">
                                <div class="fw-bold text-dark">{{ $training->title }}</div>
                                <small class="text-muted">{{ $training->instructor_name ?? ($training->trainer->name ?? 'غير محدد') }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1">
                                    <i class="fas fa-map-marker-alt text-warning me-1"></i>{{ $training->location ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td class="text-center font-monospace small text-primary fw-bold">
                                {{ $training->start_date ? $training->start_date->format('Y-m-d') : '—' }}
                            </td>
                            <td class="text-center font-monospace small text-dark">
                                {{ $training->end_date ? $training->end_date->format('Y-m-d') : '—' }}
                            </td>
                            <td class="text-center">
                                @if($training->media_coverage_status === 'covered')
                                    <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2.5 py-1 fw-bold">
                                        <i class="fas fa-check-circle me-1"></i>تمت التغطية
                                    </span>
                                @elseif($training->media_coverage_status === 'pending')
                                    <span class="badge bg-warning bg-opacity-15 text-warning rounded-pill px-2.5 py-1 fw-bold text-dark">
                                        <i class="fas fa-clock me-1"></i>بانتظار التغطية
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-15 text-secondary rounded-pill px-2.5 py-1 fw-bold">
                                        غير مطلوبة
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('media.reports.coverage.edit', $training->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-2 px-2 py-1" title="كتابة وصياغة التقرير الصحفي">
                                        <i class="fas fa-feather-alt"></i>
                                    </a>
                                    <a href="{{ route('media.reports.coverage.show', $training->id) }}" class="btn btn-sm btn-light border text-primary rounded-2 px-2 py-1" title="عرض التقرير الرسمي">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-2 opacity-50"></i>
                                <p class="small mb-0">لا توجد تدريبات قادمة مجدولة حالياً.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


