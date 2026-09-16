@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'fas fa-graduation-cap',
    'badge' => null,
    'badgeIcon' => 'fas fa-check-circle',
    'badgeClass' => 'bg-white bg-opacity-20 text-white border border-white border-opacity-25',
    'secondaryBadge' => null,
    'secondaryBadgeUrl' => null,
    'secondaryBadgeIcon' => null,
    'breadcrumbs' => [],
])

<div class="card border-0 rounded-4 shadow mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%); color: white; box-shadow: 0 10px 30px rgba(13, 71, 161, 0.18) !important;">
    <!-- Ambient decorative glow -->
    <div class="position-absolute end-0 top-0 w-100 h-100 opacity-25 pointer-events-none" style="background: radial-gradient(circle at 90% 10%, rgba(255,255,255,0.3) 0%, transparent 60%);"></div>

    <div class="p-4 p-lg-5 position-relative" style="z-index: 2;">

        <!-- زر الإشعارات - في الزاوية العلوية اليسرى للبانر -->
        @auth
        <div class="position-absolute top-0 start-0 mt-3 ms-3" style="z-index: 10;">
            <div class="dropdown">
                <a class="position-relative d-flex align-items-center justify-content-center text-white"
                    href="#" id="notificationsDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-auto-close="outside"
                    data-bs-container="body"
                    title="مركز الإشعارات"
                    style="width:44px; height:44px; border-radius:12px;
                           background:rgba(255,255,255,0.18);
                           border:1.5px solid rgba(255,255,255,0.35);
                           backdrop-filter:blur(8px);
                           box-shadow:0 4px 16px rgba(0,0,0,0.12);
                           text-decoration:none;
                           transition:all 0.2s ease;">
                    <i class="fas fa-bell" style="font-size:1.15rem;"></i>
                    <span class="position-absolute badge rounded-pill bg-danger"
                        id="notification-badge"
                        style="font-size:0.6rem; min-width:18px; height:18px;
                               padding:2px 5px; top:-5px; right:-5px; left:auto;
                               border:2px solid rgba(255,255,255,0.8);
                               {{ isset($unreadNotificationsCount) && $unreadNotificationsCount > 0 ? '' : 'display:none;' }}">
                        {{ $unreadNotificationsCount ?? 0 }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-start shadow-lg border-0 p-0"
                    aria-labelledby="notificationsDropdown"
                    style="width:360px; max-width:92vw; border-radius:14px; overflow:hidden; z-index:1060; margin-top:8px;">
                    <li class="p-3 border-bottom d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg,#0d3882,#1565c0);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-bell text-white"></i>
                            <span class="fw-bold text-white fs-6">مركز الإشعارات</span>
                            <span class="badge bg-white text-primary rounded-pill px-2"
                                id="notification-header-count" style="font-size:0.72rem; display:none;">0</span>
                        </div>
                        <button id="markAllNotificationsReadBtn" type="button"
                            class="btn btn-sm btn-link text-decoration-none text-white p-0 fw-semibold opacity-75"
                            style="font-size:0.78rem;">
                            <i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء
                        </button>
                    </li>
                    <div id="notifications-list" style="max-height:380px; overflow-y:auto; background:#fff;">
                        <li class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div>
                            <span>جاري التحميل...</span>
                        </li>
                    </div>
                    <li class="p-2 bg-light border-top d-flex justify-content-between align-items-center">
                        <a class="text-primary fw-bold text-decoration-none small py-1 px-2"
                            href="{{ route('notifications.index') }}">
                            <i class="fas fa-list me-1"></i> عرض كافة الإشعارات
                        </a>
                        <button id="clearReadNotificationsBtn" type="button"
                            class="btn btn-sm text-muted py-1 px-2"
                            style="font-size:0.75rem;" title="مسح الإشعارات المقروءة">
                            <i class="fas fa-trash-alt text-danger me-1"></i> تنظيف المقروء
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        @endauth

        <!-- Mobile Header Top Bar (Brand + Menu Trigger) -->
        <div class="d-flex justify-content-between align-items-center w-100 mb-3 d-md-none">
            <span class="badge rounded-pill px-3 py-1.5 small" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; font-size: 0.75rem; backdrop-filter: blur(6px);">
                <i class="fas fa-graduation-cap me-1 text-warning"></i>منصة الخريجين
            </span>
            <button type="button" class="btn btn-sm btn-light bg-white bg-opacity-25 text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 36px; height: 36px;" onclick="window.toggleSidebarFunc ? window.toggleSidebarFunc() : null">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="row align-items-center g-4">
            <!-- User / Page Info -->
            <div class="col-12 col-xl-6 col-lg-6">
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow flex-shrink-0" style="width: 64px; height: 64px; font-size: 1.75rem; font-weight: 700; border: 3px solid rgba(255,255,255,0.4);">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-white fs-4 fs-md-3">{{ $title }}</h2>
                        @if($subtitle)
                            <p class="text-white text-opacity-75 small mb-2" style="font-size: 0.85rem;">
                                {{ $subtitle }}
                            </p>
                        @endif

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if(!empty($breadcrumbs))
                                @foreach($breadcrumbs as $b)
                                    @if(!$loop->last && isset($b['url']))
                                        <a href="{{ $b['url'] }}" class="badge rounded-pill px-3 py-1.5 small text-decoration-none" style="background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; font-size: 0.75rem;">
                                            {{ $b['label'] }}
                                        </a>
                                        <span class="text-white-50 small">/</span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-1.5 small" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; font-size: 0.75rem;">
                                            {{ $b['label'] }}
                                        </span>
                                    @endif
                                @endforeach
                            @elseif($badge)
                                <span class="badge rounded-pill px-3 py-1.5 small fw-bold" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; font-size: 0.78rem; backdrop-filter: blur(6px);">
                                    @if($badgeIcon)<i class="{{ $badgeIcon }} me-1 text-warning"></i>@endif{{ $badge }}
                                </span>
                            @endif

                            @if($secondaryBadge)
                                @if($secondaryBadgeUrl)
                                    <a href="{{ $secondaryBadgeUrl }}" class="badge rounded-pill px-2.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 text-decoration-none" style="background: #fbbf24; color: #1e293b; border: 1px solid #f59e0b; box-shadow: none !important; font-size: 0.75rem;">
                                        @if($secondaryBadgeIcon)<i class="{{ $secondaryBadgeIcon }} text-dark"></i>@endif
                                        <span>{{ $secondaryBadge }}</span>
                                        <i class="fas fa-arrow-left ms-0.5" style="font-size: 0.65rem;"></i>
                                    </a>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="background: #fbbf24; color: #1e293b; border: 1px solid #f59e0b; box-shadow: none !important; font-size: 0.75rem;">
                                        @if($secondaryBadgeIcon)<i class="{{ $secondaryBadgeIcon }} me-1 text-dark"></i>@endif{{ $secondaryBadge }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Slot -->
            @if(isset($slot) && trim($slot) !== '')
                <div class="col-12 col-xl-6 col-lg-6 text-lg-start">
                    <div class="d-flex gap-2 justify-content-start justify-content-lg-end flex-wrap align-items-center">
                        {{ $slot }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
