{{-- ══════════════════════════════════════════════════════════════════
     الشريط السفلي الموحد لرعاة وشركاء المعرض (Luxury Glassmorphism Ticker)
     تصميم راقٍ وانسيابي متكامل الهوية مع جامعة طرابلس والشريك الاستراتيجي
     ══════════════════════════════════════════════════════════════════ --}}
@php
    $footerSponsors = (isset($sponsors) && $sponsors->count() > 0) 
        ? $sponsors 
        : (isset($fair) && $fair ? $fair->sponsors()->where('is_active', true)->get() : collect([]));
    
    // في حال عدم وجود رعاة مسجلين، استخدام الشركات المشاركة كبديل حي، أو قائمة تمثيلية
    if ($footerSponsors->isEmpty() && isset($fair) && $fair) {
        $compSponsors = $fair->companies()->with('company')->get()->map(function($fc) {
            return (object)[
                'name' => $fc->company->name ?? 'شركة مشاركة',
                'logo_path' => $fc->company->logo_path ?? $fc->company->logo,
                'website' => $fc->company->website ?? null,
                'tier' => 'partner'
            ];
        });
        if ($compSponsors->isNotEmpty()) {
            $footerSponsors = $compSponsors;
        }
    }

    $hasSponsors = $footerSponsors->isNotEmpty();
    $placeholders = [
        ["name" => "شركة المدار الجديد", "icon" => "fa-broadcast-tower"],
        ["name" => "شركة ليبيانا للهاتف المحمول", "icon" => "fa-signal"],
        ["name" => "ليبيا للاتصالات والتقنية LTT", "icon" => "fa-network-wired"],
        ["name" => "مصرف التجارة والتنمية", "icon" => "fa-university"],
        ["name" => "شركة الواحة للنفط", "icon" => "fa-oil-can"],
        ["name" => "مصرف الجمهورية", "icon" => "fa-landmark"],
        ["name" => "الشركة الليبية للبريد والاتصالات", "icon" => "fa-globe"],
        ["name" => "الشركة العامة للكهرباء", "icon" => "fa-bolt"],
    ];

    $items = $hasSponsors ? $footerSponsors->toArray() : $placeholders;
    // مضاعفة العناصر لضمان حركة شريط لا نهائية وسلسة
    $tickerItems = array_merge((array)$items, (array)$items);
@endphp

<style>
    /* ══════════════════════════════════════════════════════════════════
       الشريط السفلي العصري للرعاة (Luxury Sponsors Marquee Strip)
       ══════════════════════════════════════════════════════════════════ */
    .lux-sponsors-strip {
        background: linear-gradient(90deg, #021226 0%, #062b54 35%, #052447 70%, #021226 100%) !important;
        position: relative;
        z-index: 100;
        overflow: hidden;
        width: 100%;
        max-width: 100vw;
        border-top: 1.5px solid transparent;
        border-image: linear-gradient(90deg, transparent 0%, rgba(56, 189, 248, 0.6) 20%, #eeca3e 50%, rgba(56, 189, 248, 0.6) 80%, transparent 100%) 1;
        box-shadow: 0 -8px 30px rgba(2, 18, 38, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .lux-strip-inner {
        height: 64px;
        width: 100%;
        display: flex;
        align-items: center;
    }

    /* الكتلة الثابتة لليمين: الشعار والهوية الرسمية */
    .lux-strip-brand {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 0 20px 0 24px;
        flex-shrink: 0;
        position: relative;
        z-index: 5;
        height: 100%;
        background: linear-gradient(90deg, rgba(4, 28, 56, 0.98) 0%, rgba(3, 40, 80, 0.88) 85%, rgba(3, 40, 80, 0) 100%);
        border-left: 1px solid rgba(56, 189, 248, 0.25);
    }

    .lux-strip-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(238, 202, 62, 0.12);
        border: 1px solid rgba(238, 202, 62, 0.35);
        color: #fef08a;
        font-size: 0.76rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        white-space: nowrap;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .lux-strip-logos {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lux-strip-logo-img {
        height: 32px;
        width: auto;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        transition: transform 0.2s ease, filter 0.2s ease;
    }
    .lux-strip-logo-img:hover {
        transform: scale(1.05);
        filter: drop-shadow(0 4px 8px rgba(255,255,255,0.2));
    }

    .lux-strip-divider {
        width: 1px;
        height: 24px;
        background: linear-gradient(to bottom, transparent, rgba(56, 189, 248, 0.4), transparent);
    }

    /* مسار الشريط المتحرك مع قناع التلاشي الجانبي */
    .lux-ticker-container {
        overflow: hidden;
        width: 100%;
        min-width: 0;
        flex-grow: 1;
        position: relative;
        mask-image: linear-gradient(to right, transparent 0%, black 4%, black 96%, transparent 100%);
        -webkit-mask-image: linear-gradient(to right, transparent 0%, black 4%, black 96%, transparent 100%);
    }

    .lux-ticker-track {
        display: flex;
        align-items: center;
        gap: 1.1rem;
        width: max-content;
        animation: luxTickerScroll 36s linear infinite;
        padding: 8px 0;
    }
    .lux-ticker-track:hover {
        animation-play-state: paused;
    }

    @keyframes luxTickerScroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* بطاقة الراعي الزجاجية داخل الشريط */
    .lux-sponsor-card {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 50rem;
        padding: 5px 16px 5px 8px;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.12);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        white-space: nowrap;
    }

    .lux-sponsor-card.is-clickable {
        cursor: pointer;
    }
    .lux-sponsor-card:hover {
        background: rgba(255, 255, 255, 0.16);
        border-color: #38bdf8;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(56, 189, 248, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .lux-sponsor-avatar {
        width: 36px;
        height: 36px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        padding: 4px;
        position: relative;
    }

    .lux-sponsor-avatar img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .lux-sponsor-fallback {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #0284c7, #03488a);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .lux-sponsor-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.95);
        letter-spacing: 0.2px;
        transition: color 0.2s ease;
    }
    .lux-sponsor-card:hover .lux-sponsor-name {
        color: #38bdf8;
    }

    .lux-ticker-separator {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(56, 189, 248, 0.4);
        flex-shrink: 0;
    }

    /* التجاوب للشاشات الصغيرة */
    @media (max-width: 991.98px) {
        .lux-strip-inner { height: 56px; }
        .lux-strip-brand { padding: 0 14px 0 16px; gap: 10px; }
        .lux-strip-badge { font-size: 0.7rem; padding: 3px 9px; }
        .lux-strip-logo-img { height: 26px; }
        .lux-sponsor-card { padding: 4px 12px 4px 6px; gap: 8px; }
        .lux-sponsor-avatar { width: 30px; height: 30px; }
        .lux-sponsor-name { font-size: 0.75rem; }
    }

    @media (max-width: 575.98px) {
        .lux-strip-inner { height: 50px; }
        .lux-strip-brand { padding: 0 10px; }
        .lux-strip-badge span { display: none; }
        .lux-strip-badge { padding: 4px 8px; font-size: 0.8rem; }
        .lux-strip-logos { gap: 8px; }
        .lux-strip-logo-img { height: 22px; }
    }
</style>

<div class="lux-sponsors-strip" role="region" aria-label="شريط رعاة وشركاء المعرض">
    <div class="lux-strip-inner">
        <!-- الكتلة الثابتة لليمين: الشعار والهوية الرسمية -->
        <div class="lux-strip-brand">
            <div class="lux-strip-badge" title="رعاة وشركاء معرض التوظيف 2026">
                <i class="fas fa-crown text-warning"></i>
                <span>رعاة المعرض</span>
            </div>

            <div class="lux-strip-logos">
                <!-- شعار مكتب تدريب وتوظيف الخريجين -->
                <img src="{{ asset('images/logo.jpg') }}"
                     alt="مكتب تدريب الخريجين"
                     class="lux-strip-logo-img rounded-circle"
                     title="مكتب تدريب الخريجين — جامعة طرابلس"
                     onerror="this.src='{{ asset('images/uni_logo_white.png') }}'">

                <div class="lux-strip-divider"></div>

                <!-- شعار الشريك الاستراتيجي الواحة لتنظيم المعارض -->
                <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}"
                     alt="شركة الواحة لتنظيم المعارض والمؤتمرات"
                     class="lux-strip-logo-img"
                     title="شركة الواحة لتنظيم المعارض والمؤتمرات — الشريك والراعي الاستراتيجي"
                     onerror="this.src='{{ asset('images/wahaexpo_logo.png') }}'">
            </div>
        </div>

        <!-- المسار التفاعلي المتحرك (Scrolling Ticker Track) -->
        <div class="lux-ticker-container">
            <div class="lux-ticker-track">
                @foreach($tickerItems as $item)
                    @php
                        $sp = (object)$item;
                        $spName = $sp->name ?? 'شريك المعرض';
                        $spLogo = !empty($sp->logo_path) ? Storage::url($sp->logo_path) : null;
                        $spUrl = $sp->website ?? null;
                    @endphp
                    <div class="lux-sponsor-card {{ $spUrl ? 'is-clickable' : '' }}"
                         @if($spUrl) onclick="window.open('{{ $spUrl }}', '_blank', 'noopener,noreferrer');" @endif
                         title="{{ $spName }}">
                        <div class="lux-sponsor-avatar">
                            @if($spLogo)
                                <img src="{{ $spLogo }}" 
                                     alt="{{ $spName }}" 
                                     loading="lazy"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div class="lux-sponsor-fallback" style="display:none;">
                                    <span>{{ mb_substr($spName, 0, 1) }}</span>
                                </div>
                            @elseif(isset($sp->icon))
                                <div class="lux-sponsor-fallback">
                                    <i class="fas {{ $sp->icon }}"></i>
                                </div>
                            @else
                                <div class="lux-sponsor-fallback">
                                    <span>{{ mb_substr($spName, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <span class="lux-sponsor-name">{{ $spName }}</span>
                    </div>
                    <div class="lux-ticker-separator"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
