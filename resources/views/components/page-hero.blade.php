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


        <!-- Mobile Header Top Bar (Brand + Menu Trigger) -->
        <div class="d-flex justify-content-between align-items-center w-100 mb-3 d-md-none">
            <span class="badge rounded-pill px-3 py-1.5 small" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; font-size: 0.75rem; backdrop-filter: blur(6px);">
                <i class="fas fa-graduation-cap me-1 text-warning"></i>{{ config('app.name', 'منصة الخريجين') }}
            </span>
            <div class="d-flex align-items-center gap-2">
                @auth
                <!-- زر تبديل الوضع الليلي للموبايل -->
                <button type="button" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none dark-mode-trigger" onclick="toggleDarkMode(event)" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" title="الوضع الليلي / النهاري">
                    <i class="fas fa-moon"></i>
                </button>

                @if(auth()->user()->role === 'admin')
                <!-- زر إدارة المساعد الذكي الصغير للموبايل -->
                <a href="{{ route('admin.settings.ai') }}" class="btn btn-sm text-warning rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" title="إدارة المساعد الذكي">
                    <i class="fas fa-robot"></i>
                </a>
                @endif

                <!-- زر الرسائل المنزلق بالموبايل -->
                <button type="button" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 position-relative shadow-none" onclick="openMessagesDrawer()" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" title="الرسائل">
                    <i class="fas fa-envelope"></i>
                    @php
                        $unreadMsgs = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    @if($unreadMsgs > 0)
                        <span class="position-absolute bg-danger rounded-circle border border-2 border-white" style="width: 10px; height: 10px; top: 0px; right: 0px;"></span>
                    @endif
                </button>

                <!-- زر الإشعارات المنزلق بالموبايل -->
                <button type="button" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 position-relative shadow-none" onclick="openNotificationsDrawer()" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" title="الإشعارات">
                    <i class="fas fa-bell"></i>
                    @php
                        $unreadNotifs = auth()->user()->unreadNotifications->count() ?? 0;
                    @endphp
                    @if($unreadNotifs > 0)
                        <span class="position-absolute bg-danger rounded-circle border border-2 border-white" style="width: 10px; height: 10px; top: 0px; right: 0px;"></span>
                    @endif
                </button>
                @endauth

                <!-- زر القائمة الجانبية للموبايل -->
                <button type="button" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" onclick="window.toggleSidebarFunc ? window.toggleSidebarFunc() : null">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
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
            <div class="col-12 col-xl-6 col-lg-6 text-lg-start">
                <div class="d-flex gap-2 justify-content-start justify-content-lg-end flex-wrap align-items-center">
                    @auth
                    <!-- Desktop Notifications, Messages, Dark Mode & Admin AI Management -->
                    <div class="d-none d-md-flex gap-2 align-items-center me-3">
                        <button class="btn text-white border-0 px-3 py-2 rounded-pill fw-bold" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" onclick="openMessagesDrawer()">
                            <i class="fas fa-envelope me-1"></i>الرسائل
                            @php
                                $unreadMsgsDesktop = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                            @endphp
                            @if($unreadMsgsDesktop > 0)
                                <span class="badge bg-danger ms-1 rounded-pill">{{ $unreadMsgsDesktop }}</span>
                            @endif
                        </button>
                        <button class="btn text-white border-0 px-3 py-2 rounded-pill fw-bold" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" onclick="openNotificationsDrawer()">
                            <i class="fas fa-bell me-1"></i>الإشعارات
                            @php
                                $unreadNotifsDesktop = \App\Models\Notification::forUser(auth()->id())->unread()->count();
                            @endphp
                            @if($unreadNotifsDesktop > 0)
                                <span class="badge bg-danger ms-1 rounded-pill">{{ $unreadNotifsDesktop }}</span>
                            @endif
                        </button>

                        <!-- زر تبديل الوضع الليلي (Dark Mode) الدائري الأنيق -->
                        <button type="button" class="btn text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none dark-mode-trigger" onclick="toggleDarkMode(event)" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); transition: transform 0.2s ease;" title="الوضع الليلي / النهاري" data-bs-toggle="tooltip">
                            <i class="fas fa-moon"></i>
                        </button>

                        @if(auth()->user()->role === 'admin')
                        <!-- زر إدارة المساعد الذكي الصغير (Icon Only) -->
                        <a href="{{ route('admin.settings.ai') }}" class="btn text-warning rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); transition: transform 0.2s ease;" title="إدارة المساعد الذكي" data-bs-toggle="tooltip">
                            <i class="fas fa-robot"></i>
                        </a>
                        @endif
                    </div>
                    @endauth

                    @if(isset($slot) && trim($slot) !== '')
                        {{ $slot }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
