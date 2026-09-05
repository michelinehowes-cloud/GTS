@extends('layouts.app')

@section('title', 'غرفة تحكم البث المباشر والكاميرات — المركز الإعلامي')
@section('page-title', 'استوديو البث المباشر والتحكم بالكاميرات')

@push('styles')
<style>
    :root {
        --studio-bg: #070d18;
        --studio-card: #0d172a;
        --studio-border: rgba(255, 255, 255, 0.08);
        --studio-accent: #38bdf8;
        --studio-red: #ef4444;
    }

    .studio-header {
        background: linear-gradient(135deg, #0b1e42 0%, #040914 100%);
        border: 1px solid rgba(56, 189, 248, 0.2);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }

    .live-badge-pulse {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.4);
        color: #fca5a5;
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 800;
        animation: pulseBorder 2s infinite ease-in-out;
    }
    .live-dot-blink {
        width: 10px;
        height: 10px;
        background-color: var(--studio-red);
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 12px var(--studio-red);
        animation: blink 1.2s infinite ease-in-out;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.8); }
    }
    @keyframes pulseBorder {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    .off-air-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(148, 163, 184, 0.12);
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #cbd5e1;
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    /* Program Monitor Stage */
    .program-monitor-card {
        background: #040914;
        border: 1px solid rgba(56, 189, 248, 0.25);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.7);
    }
    .monitor-viewport {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #020611;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .monitor-viewport iframe,
    .monitor-viewport video {
        width: 100%;
        height: 100%;
        border: none;
        object-fit: cover;
    }
    .on-air-tag {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        z-index: 10;
        box-shadow: 0 2px 10px rgba(239, 68, 68, 0.5);
    }

    /* Camera Switcher Deck */
    .camera-deck-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .camera-deck-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.1);
    }
    .camera-deck-card.is-active {
        border: 2px solid #ef4444;
        background: #fffafa;
    }

    .type-badge-yt { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
    .type-badge-hls { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .type-badge-rtsp { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- الهيدر الاستوديو -->
    <div class="studio-header text-white mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.5rem; background: linear-gradient(135deg, #1d4ed8, #0ea5e9);">
                    <i class="fas fa-satellite-dish"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fw-bold mb-0 text-white">استوديو البث وغرفة التحكم بالكاميرات</h4>
                        @if($setting->is_live_now)
                            <span class="live-badge-pulse"><span class="live-dot-blink"></span> على الهواء مباشرة (ON AIR)</span>
                        @else
                            <span class="off-air-badge"><i class="fas fa-circle me-1 opacity-50" style="font-size: 0.5rem;"></i> البث متوقف (OFF AIR)</span>
                        @endif
                    </div>
                    <p class="text-white-50 small mb-0 mt-1">
                        إدارة كاميرات IP، وتغذية البث المباشر (YouTube / HLS / RTSP)، والتبديل الحي بين زوايا التغطية لمعرض التوظيف والفعاليات.
                    </p>
                </div>
            </div>

            <!-- أزرار الإجراءات السريعة -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- زر التبديل الرئيسي للبث -->
                <form action="{{ route('media.live-studio.broadcast.toggle') }}" method="POST" class="d-inline m-0">
                    @csrf
                    @if($setting->is_live_now)
                        <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4 py-2 shadow-sm" onclick="return confirm('هل أنت متأكد من إيقاف البث الحي عن الجمهور؟')">
                            <i class="fas fa-stop-circle me-1"></i> إيقاف البث الحي (OFF AIR)
                        </button>
                    @else
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm" style="background: linear-gradient(135deg, #15803d, #16a34a);">
                            <i class="fas fa-broadcast-tower me-1"></i> إطلاق البث المباشر للجمهور (GO LIVE) 🔴
                        </button>
                    @endif
                </form>

                <!-- زر فتح شاشة المسرح للجمهور -->
                <a href="{{ route('job-fair.live-stream') }}" target="_blank" class="btn btn-outline-info rounded-pill px-3 py-2 text-white">
                    <i class="fas fa-external-link-alt me-1"></i> شاشة الجمهور
                </a>

                <!-- زر إضافة كاميرا -->
                <button type="button" class="btn btn-primary rounded-pill px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addCameraModal" style="background: linear-gradient(135deg, #2563eb, #3b82f6);">
                    <i class="fas fa-video me-1"></i> + إضافة كاميرا / رابط بث
                </button>
            </div>
        </div>
    </div>

    <!-- شبكة الاستوديو الرئيسية -->
    <div class="row g-4 mb-4">
        
        <!-- شاشة المعاينة الحية المخرج (Program Monitor) -->
        <div class="col-lg-8">
            <div class="program-monitor-card">
                <div class="p-3 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25 text-white" style="background: rgba(10, 20, 38, 0.9);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.72rem;">PROGRAM FEED</span>
                        <h6 class="fw-bold mb-0 text-white">
                            <i class="fas fa-video text-info me-1"></i>
                            {{ $activeCamera ? $activeCamera->title : 'لا توجد كاميرا نشطة' }}
                        </h6>
                    </div>
                    <div>
                        @if($activeCamera)
                            <span class="badge rounded-pill px-2.5 py-1 text-white-50" style="background: rgba(255,255,255,0.08); font-size: 0.75rem;">
                                <i class="fas fa-map-marker-alt me-1 text-warning"></i>{{ $activeCamera->location_tag ?? 'الموقع العام' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="monitor-viewport">
                    @if($activeCamera)
                        @if($setting->is_live_now)
                            <div class="on-air-tag">
                                <span class="live-dot-blink" style="background: white; box-shadow: none;"></span> ON AIR
                            </div>
                        @else
                            <div class="on-air-tag" style="background: rgba(100, 116, 139, 0.85); box-shadow: none;">
                                PREVIEW
                            </div>
                        @endif

                        @if($activeCamera->stream_type === 'youtube_live')
                            <iframe src="{{ $activeCamera->embed_url }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        @elseif($activeCamera->stream_type === 'hls_m3u8')
                            <video id="studioPreviewVideo" controls autoplay muted style="width: 100%; height: 100%; object-fit: cover;">
                                <source src="{{ $activeCamera->stream_url }}" type="application/x-mpegURL">
                            </video>
                        @else
                            <div class="text-center text-white-50 p-4">
                                <i class="fas fa-broadcast-tower fa-3x text-info mb-3"></i>
                                <h6 class="text-white">{{ $activeCamera->title }}</h6>
                                <p class="small text-white-50 mb-2">نوع البث: {{ $activeCamera->stream_type }}</p>
                                <a href="{{ $activeCamera->stream_url }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                    <i class="fas fa-external-link-alt me-1"></i> فتح الرابط المباشر
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-video-slash fa-3x mb-3 opacity-50"></i>
                            <h6 class="text-white">لم يتم تعيين كاميرا نشطة بعد</h6>
                            <p class="small text-white-50">قم بإضافة كاميرا أو اختيار إحدى الكاميرات أدناه لوضعها على الهواء.</p>
                        </div>
                    @endif
                </div>

                <!-- شريط تحكم بيانات البث -->
                <div class="p-3 text-white d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(6, 13, 26, 0.95);">
                    <div>
                        <span class="text-white-50 small d-block">عنوان البث المعروض للجمهور:</span>
                        <strong class="text-info">{{ $setting->broadcast_title }}</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editBroadcastModal">
                            <i class="fas fa-pen me-1"></i> تعديل عنوان البث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- لوحة التحكم السريع وتبديل الكاميرات (Video Switcher Panel) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: #f8fafc;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-th-large text-primary me-2"></i>منصة التبديل الفوري (Switcher)
                    </h6>
                    <span class="badge bg-dark rounded-pill px-2.5 py-1">{{ $cameras->count() }} كاميرات</span>
                </div>

                <div class="card-body p-3 overflow-auto" style="max-height: 520px;">
                    <p class="small text-muted mb-3">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        انقر على <strong>«تحويل على الهواء»</strong> لنقل الكاميرا المحددة فوراً إلى شاشة الجمهور الرئيسية.
                    </p>

                    @forelse($cameras as $camera)
                        <div class="camera-deck-card p-3 mb-3 {{ $activeCamera && $activeCamera->id === $camera->id ? 'is-active' : '' }}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 26px; height: 26px; background: {{ $activeCamera && $activeCamera->id === $camera->id ? '#ef4444' : '#64748b' }};">
                                        {{ $camera->display_order ?? $loop->iteration }}
                                    </span>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $camera->title }}</h6>
                                </div>
                                @if($activeCamera && $activeCamera->id === $camera->id)
                                    <span class="badge bg-danger rounded-pill px-2 py-1 small fw-bold"><i class="fas fa-broadcast-tower me-1"></i>على الهواء</span>
                                @endif
                            </div>

                            <div class="small text-muted mb-3 d-flex align-items-center gap-2 flex-wrap">
                                @if($camera->stream_type === 'youtube_live')
                                    <span class="badge rounded-pill type-badge-yt px-2 py-0.5"><i class="fab fa-youtube me-1"></i>YouTube Live</span>
                                @elseif($camera->stream_type === 'hls_m3u8')
                                    <span class="badge rounded-pill type-badge-hls px-2 py-0.5"><i class="fas fa-bolt me-1"></i>HLS Stream</span>
                                @else
                                    <span class="badge rounded-pill type-badge-rtsp px-2 py-0.5"><i class="fas fa-network-wired me-1"></i>IP / RTSP</span>
                                @endif

                                @if($camera->location_tag)
                                    <span class="text-secondary"><i class="fas fa-map-marker-alt me-1 text-warning"></i>{{ $camera->location_tag }}</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2">
                                @if(!$activeCamera || $activeCamera->id !== $camera->id)
                                    <form action="{{ route('media.live-studio.cameras.on-air', $camera->id) }}" method="POST" class="m-0 flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3 fw-bold">
                                            <i class="fas fa-play me-1"></i> تحويل على الهواء
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-3 fw-bold disabled">
                                        <i class="fas fa-check-circle me-1"></i> تبث للجمهور حالياً
                                    </button>
                                @endif

                                <form action="{{ route('media.live-studio.cameras.destroy', $camera->id) }}" method="POST" class="m-0" onsubmit="return confirm('هل أنت متأكد من حذف هذه الكاميرا؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger rounded-3" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-video-slash fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">لا توجد كاميرات مسجلة حتى الآن.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- جدول الكاميرات وروابط البث المتصلة -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fas fa-list text-primary me-2"></i>دليل وتفاصيل كاميرات التغطية المتصلة
            </h6>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCameraModal">
                <i class="fas fa-plus me-1"></i> إضافة كاميرا
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc;">
                    <tr class="text-secondary small fw-bold">
                        <th class="py-3 px-3 text-center" style="width: 60px;">#</th>
                        <th class="py-3">اسم الكاميرا والزاوية</th>
                        <th class="py-3">نوع البث / البروتوكول</th>
                        <th class="py-3">موقع الكاميرا الميداني</th>
                        <th class="py-3">رابط البث التغذوي</th>
                        <th class="py-3 text-center">الحالة</th>
                        <th class="py-3 text-center" style="width: 140px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cameras as $camera)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $camera->display_order ?? $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $camera->title }}</div>
                                @if($camera->description)
                                    <small class="text-muted">{{ Str::limit($camera->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($camera->stream_type === 'youtube_live')
                                    <span class="badge type-badge-yt rounded-pill px-2.5 py-1">YouTube Live</span>
                                @elseif($camera->stream_type === 'hls_m3u8')
                                    <span class="badge type-badge-hls rounded-pill px-2.5 py-1">HLS Stream</span>
                                @else
                                    <span class="badge type-badge-rtsp rounded-pill px-2.5 py-1">{{ $camera->stream_type }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary small"><i class="fas fa-map-marker-alt text-warning me-1"></i>{{ $camera->location_tag ?? 'غير محدد' }}</span>
                            </td>
                            <td>
                                <code class="small text-truncate d-inline-block" style="max-width: 250px;">{{ $camera->stream_url }}</code>
                            </td>
                            <td class="text-center">
                                @if($activeCamera && $activeCamera->id === $camera->id)
                                    <span class="badge bg-danger rounded-pill px-2.5 py-1"><i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>على الهواء</span>
                                @elseif($camera->is_live)
                                    <span class="badge bg-success rounded-pill px-2.5 py-1">متصلة وجاهزة</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-2.5 py-1">متوقفة</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    @if(!$activeCamera || $activeCamera->id !== $camera->id)
                                        <form action="{{ route('media.live-studio.cameras.on-air', $camera->id) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1" title="بث على الهواء فوراً">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('media.live-studio.cameras.toggle', $camera->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-2 px-2 py-1" title="تبديل التشغيل">
                                            <i class="fas {{ $camera->is_live ? 'fa-pause text-warning' : 'fa-play text-success' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('media.live-studio.cameras.destroy', $camera->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('هل أنت متأكد من حذف هذه الكاميرا؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-2 px-2 py-1" title="حذف">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- نافذة إضافة كاميرا / بث خارجي جديدة -->
<div class="modal fade" id="addCameraModal" tabindex="-1" aria-labelledby="addCameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('media.live-studio.cameras.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3 rounded-top-4" style="background: linear-gradient(135deg, #1d4ed8, #0ea5e9) !important;">
                    <h5 class="modal-title fw-bold" id="addCameraModalLabel">
                        <i class="fas fa-video me-2"></i>إضافة كاميرا IP أو رابط بث حي
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">اسم الكاميرا أو الزاوية <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: كاميرا المسرح الرئيسي، كاميرا بهو الشركات" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">الموقع الميداني للكاميرا</label>
                        <input type="text" name="location_tag" class="form-control rounded-3" placeholder="مثال: قاعة المؤتمرات المركزية، مدرج تقنية المعلومات">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">نوع البث / البروتوكول <span class="text-danger">*</span></label>
                        <select name="stream_type" class="form-select rounded-3" required>
                            <option value="youtube_live">🔴 YouTube Live (رابط فيديو أو معرف يوتيوب مباشر)</option>
                            <option value="hls_m3u8">⚡ HLS Stream (.m3u8 رابط بث شبكي)</option>
                            <option value="rtsp_ip">📡 IP Camera (RTSP / WebRTC)</option>
                            <option value="iframe_embed">🌐 Tضمين خارجي (Embed / Iframe)</option>
                            <option value="external_url">🔗 رابط بث خارجي (Zoom / Meet / Live Link)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">رابط البث أو الكاميرا <span class="text-danger">*</span></label>
                        <input type="text" name="stream_url" class="form-control font-monospace rounded-3" placeholder="https://www.youtube.com/watch?v=... أو رابط m3u8" required>
                        <small class="text-muted mt-1 d-block">لا يتم استهلاك أي مساحة على السيرفر، حيث يتم جلب البث سحابياً ومباشراً.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">وصف التغطية (اختياري)</label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="وصف موجز لما تغطيه هذه الكاميرا..."></textarea>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="isPrimaryCheck">
                        <label class="form-check-label fw-bold small text-dark" for="isPrimaryCheck">
                            تعيين هذه الكاميرا مباشرة على الهواء للجمهور (ON AIR)
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-3 border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: #1d4ed8;">
                        <i class="fas fa-check-circle me-1"></i> حفظ وإضافة الكاميرا
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نافذة تعديل بيانات البث العام -->
<div class="modal fade" id="editBroadcastModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('media.live-studio.broadcast.update') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-sliders-h me-2"></i>تعديل بيانات البث المعروض للجمهور
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">عنوان البث الرئيسي</label>
                        <input type="text" name="broadcast_title" class="form-control rounded-3" value="{{ $setting->broadcast_title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">وصف الفعالية المنقولة</label>
                        <textarea name="broadcast_description" class="form-control rounded-3" rows="3">{{ $setting->broadcast_description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">عداد المشاهدين الافتراضي</label>
                        <input type="number" name="viewers_count" class="form-control rounded-3" value="{{ $setting->viewers_count }}">
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-3 border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var video = document.getElementById('studioPreviewVideo');
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
