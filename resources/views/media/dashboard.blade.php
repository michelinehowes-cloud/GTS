@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الميديا والإعلام — المركز الإعلامي')
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

    /* Live Badge Pulses */
    .pulse-live-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #ef4444;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 800;
        animation: pulseTag 2s infinite ease-in-out;
    }
    .live-dot-mini {
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 8px #ef4444;
        animation: blinkMini 1.2s infinite ease-in-out;
    }
    @keyframes blinkMini {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.3; transform: scale(0.7); }
    }
    @keyframes pulseTag {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.3); }
        50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
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

    /* Mini Live Player Preview */
    .mini-live-preview {
        border-radius: 14px;
        overflow: hidden;
        background: #040914;
        aspect-ratio: 16 / 9;
        position: relative;
    }
    .mini-live-preview iframe,
    .mini-live-preview video {
        width: 100%;
        height: 100%;
        border: none;
        object-fit: cover;
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
                    <div class="rounded-circle bg-white bg-opacity-15 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 60px; height: 60px; font-size: 1.8rem;">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-bold mb-0 text-white">المركز الإعلامي واستوديو البث الذكي</h4>
                            @if($broadcastSetting->is_live_now)
                                <span class="pulse-live-tag bg-white text-danger fw-bold"><span class="live-dot-mini"></span> بث مباشر للجمهور (ON AIR)</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1.5 bg-white bg-opacity-15 text-white"><i class="fas fa-circle me-1 opacity-50" style="font-size: 0.5rem;"></i> البث العام متوقف (OFF AIR)</span>
                            @endif
                        </div>
                        <p class="text-white-50 small mb-0">
                            جامعة طرابلس &bull; إدارة التغطيات الميدانية، ربط كاميرات IP، وبث فعاليات يوم التوظيف والتدريب دون استهلاك مساحة السيرفر.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('media.live-studio') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2">
                        <i class="fas fa-satellite-dish"></i>
                        <span>غرفة تحكم البث والكاميرات</span>
                    </a>
                    <a href="{{ route('job-fair.live-stream') }}" target="_blank" class="btn btn-outline-light rounded-pill px-3 py-2">
                        <i class="fas fa-tv me-1"></i> شاشة الجمهور
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
        
        <!-- بطاقة الكاميرات والبث -->
        <div class="col-sm-6 col-lg-3">
            <div class="card bento-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-circle-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-video"></i>
                    </div>
                    @if($broadcastSetting->is_live_now)
                        <span class="badge bg-danger rounded-pill px-2.5 py-1 small fw-bold">ON AIR 🔴</span>
                    @else
                        <span class="badge bg-secondary rounded-pill px-2.5 py-1 small">OFF AIR</span>
                    @endif
                </div>
                <div class="small text-muted fw-bold mb-1">الكاميرات والبث المباشر</div>
                <h3 class="fw-bold text-dark mb-1">{{ $camerasCount }} <span class="fs-6 fw-normal text-muted">كاميرات متصلة</span></h3>
                <div class="small text-muted">
                    <i class="fas fa-signal text-success me-1"></i>{{ $liveCamerasCount }} كاميرات نشطة التغذية
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
                    <span class="badge bg-primary bg-opacity-15 text-primary rounded-pill px-2.5 py-1 fw-bold">{{ $coverageRate }}% إنجاز</span>
                </div>
                <div class="small text-muted fw-bold mb-1">تغطية البرامج التدريبية</div>
                <h3 class="fw-bold text-dark mb-1">{{ $coveredTrainings }} <span class="fs-6 fw-normal text-muted">/ {{ $totalTrainings }} تدريب</span></h3>
                <div class="progress mt-2" style="height: 6px; border-radius: 999px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $coverageRate }}%;" aria-valuenow="{{ $coverageRate }}" aria-valuemin="0" aria-valuemax="100"></div>
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
        
        <!-- نافذة البث المباشر والكاميرا على الهواء حالياً -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-broadcast-tower text-danger me-2"></i>معاينة الكاميرا النشطة للبث المباشر
                    </h6>
                    <a href="{{ route('media.live-studio') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" style="background: #1d4ed8;">
                        <i class="fas fa-sliders-h me-1"></i> فتح غرفة التحكم
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($activeCamera)
                        <div class="mini-live-preview mb-3">
                            @if($activeCamera->stream_type === 'youtube_live')
                                <iframe src="{{ $activeCamera->embed_url }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            @elseif($activeCamera->stream_type === 'hls_m3u8')
                                <video id="dashboardMiniHls" controls autoplay muted>
                                    <source src="{{ $activeCamera->stream_url }}" type="application/x-mpegURL">
                                </video>
                            @else
                                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-white p-3">
                                    <i class="fas fa-video fa-2x mb-2 text-info"></i>
                                    <h6>{{ $activeCamera->title }}</h6>
                                    <small class="text-white-50">{{ $activeCamera->stream_type }}</small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $activeCamera->title }}</h6>
                                <small class="text-muted"><i class="fas fa-map-marker-alt text-warning me-1"></i>{{ $activeCamera->location_tag ?? 'الموقع العام' }}</small>
                            </div>
                            <div>
                                <form action="{{ route('media.live-studio.broadcast.toggle') }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    @if($broadcastSetting->is_live_now)
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                            <i class="fas fa-stop me-1"></i> إيقاف البث الحي
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                            <i class="fas fa-play me-1"></i> إطلاق البث للجمهور 🔴
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-video-slash fa-3x mb-3 opacity-50"></i>
                            <p class="mb-2">لا توجد كاميرا نشطة حالياً</p>
                            <a href="{{ route('media.live-studio') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                + إضافة كاميرا في الاستوديو
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
                    
                    <a href="{{ route('media.live-studio') }}" class="quick-action-item">
                        <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-2 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-satellite-dish"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">استوديو البث وغرفة الكاميرات</div>
                            <small class="text-muted">التحكم في زوايا البث وتبديل الكاميرات على الهواء</small>
                        </div>
                        <i class="fas fa-chevron-left text-muted opacity-50"></i>
                    </a>

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
                                <a href="{{ route('media.trainings.show', $training->id) }}" class="btn btn-sm btn-light border text-primary rounded-2 px-2.5 py-1" title="معاينة التدريب">
                                    <i class="fas fa-eye"></i>
                                </a>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var video = document.getElementById('dashboardMiniHls');
        if (video && video.querySelector('source')) {
            var src = video.querySelector('source').src;
            if (Hls.isSupported() && src.indexOf('.m3u8') !== -1) {
                var hls = new Hls();
                hls.loadSource(src);
                hls.attachMedia(video);
            }
        }
    });
</script>
@endpush
