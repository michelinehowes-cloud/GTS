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
                <i class="fas fa-graduation-cap me-1 text-warning"></i>منصة الخريجين
            </span>
            <button type="button" class="btn btn-sm btn-light bg-white bg-opacity-25 text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 36px; height: 36px;" onclick="window.toggleSidebarFunc ? window.toggleSidebarFunc() : null">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="row align-items-center g-4">
            <!-- User / Page Info -->
            <div class="col-12 col-lg-7">
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
                <div class="col-12 col-lg-5 text-lg-start">
                    <div class="d-flex gap-2 gap-md-3 justify-content-between justify-content-lg-end flex-wrap flex-sm-nowrap">
                        {{ $slot }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
