{{-- ══════════════════════════════════════════════════════════════════
     الشريط السفلي الزجاجي الفاخر لرعاة وشركات المعرض (Pure Glassmorphic Logos Ticker)
     تصميم زجاجي شفاف بالكامل يعرض شعارات الشركات مباشرة وبدون أشكال بيضاوية
     ══════════════════════════════════════════════════════════════════ --}}
@php
    use Illuminate\Support\Facades\Storage;

    $tickerList = collect();

    // 1. إضافة الرعاة الرسميين المضافين للمعرض في لوحة التحكم
    $activeSponsors = (isset($sponsors) && $sponsors instanceof \Illuminate\Support\Collection && $sponsors->isNotEmpty())
        ? $sponsors
        : (isset($fair) && $fair ? $fair->sponsors()->where('is_active', true)->orderBy('display_order')->get() : collect());

    foreach ($activeSponsors as $sp) {
        $tickerList->push((object)[
            'name'       => $sp->name,
            'logo_path'  => $sp->logo_path ?? null,
            'website'    => $sp->website ?? null,
            'type'       => 'sponsor',
            'tier_label' => method_exists($sp, 'getTierLabelAttribute') ? $sp->tier_label : ($sp->tier ?? 'جهة راعية'),
        ]);
    }

    // 2. إضافة الشركات المشاركة المؤكدة في المعرض من لوحة التحكم
    $activeCompanies = (isset($companies) && $companies instanceof \Illuminate\Support\Collection && $companies->isNotEmpty())
        ? $companies
        : (isset($fair) && $fair ? $fair->companies()->with('company')->where('status', 'confirmed')->get() : collect());

    foreach ($activeCompanies as $fc) {
        $comp = $fc->company ?? null;
        if ($comp) {
            $tickerList->push((object)[
                'name'         => $comp->name,
                'logo_path'    => $comp->logo_path ?: ($comp->logo ?? null),
                'website'      => $comp->website ?? null,
                'type'         => 'company',
                'booth_number' => $fc->booth_number ?? null,
            ]);
        }
    }

    // إزالة التكرار حسب الاسم
    $tickerList = $tickerList->unique('name');

    // دالة مرنة لحل مسار الشعار
    $resolveLogoUrl = function($path) {
        if (empty($path)) return null;
        $path = trim($path);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }
        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }
        return Storage::url($path);
    };

    $hasDynamicItems = $tickerList->isNotEmpty();

    if ($hasDynamicItems) {
        $rawItems = $tickerList->values()->all();
        // تكرار العناصر لضمان مسار شريط لانهائي وسلس
        $tickerItems = array_merge($rawItems, $rawItems);
        if (count($tickerItems) < 8) {
            $tickerItems = array_merge($tickerItems, $rawItems);
        }
    } else {
        $tickerItems = [];
    }
@endphp

<style>
    /* ══════════════════════════════════════════════════════════════════
       الشريط السفلي الزجاجي الشفاف (Pure Glassmorphic Logos Strip)
       ══════════════════════════════════════════════════════════════════ */
    .lux-glass-strip {
        background: linear-gradient(180deg, #03386b 0%, #033363 50%, #03284f 100%) !important;
        position: relative;
        z-index: 100;
        overflow: hidden;
        width: 100%;
        max-width: 100vw;
        border-top: 1px solid rgba(56, 189, 248, 0.3) !important;
        border-bottom: 1px solid rgba(2, 20, 45, 0.8) !important;
        box-shadow: 0 -10px 30px rgba(3, 40, 79, 0.5) !important;
    }

    /* خط مضيء علوي متناغم مع الهوية */
    .lux-glass-strip::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1.5px;
        background: linear-gradient(90deg, transparent 0%, rgba(56, 189, 248, 0.5) 25%, rgba(238, 202, 62, 0.6) 50%, rgba(56, 189, 248, 0.5) 75%, transparent 100%);
        pointer-events: none;
        z-index: 10;
    }

    .lux-strip-inner {
        height: 60px;
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
        background: linear-gradient(90deg, #04427c 0%, #03386b 100%);
        border-left: 1px solid rgba(56, 189, 248, 0.25);
        box-shadow: -10px 0 20px rgba(2, 20, 45, 0.45);
    }

    .lux-strip-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(238, 202, 62, 0.14);
        border: 1px solid rgba(238, 202, 62, 0.4);
        color: #f6d860;
        font-size: 0.76rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        white-space: nowrap;
        letter-spacing: 0.3px;
        box-shadow: 0 0 10px rgba(238, 202, 62, 0.15);
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
        transform: scale(1.06);
        filter: drop-shadow(0 4px 8px rgba(255,255,255,0.3));
    }

    .lux-strip-divider {
        width: 1px;
        height: 24px;
        background: linear-gradient(to bottom, transparent, rgba(56, 189, 248, 0.45), transparent);
    }

    /* مسار الشريط المتحرك مع قناع التلاشي الجانبي الناعم */
    .lux-ticker-container {
        overflow: hidden;
        width: 100%;
        min-width: 0;
        flex-grow: 1;
        position: relative;
        mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
        -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
    }

    .lux-ticker-track {
        display: flex;
        align-items: center;
        width: max-content;
        animation: luxLogosScroll 34s linear infinite;
        padding: 6px 0;
    }
    .lux-ticker-track:hover {
        animation-play-state: paused;
    }

    @keyframes luxLogosScroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* ── شعار الشركة الخالص (بدون أي شكل بيضاوي أو خلفية مقيدة) ── */
    .lux-logo-pure-item {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 4px 12px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        cursor: default;
    }

    .lux-logo-pure-item.is-clickable {
        cursor: pointer;
    }

    /* حاوية الشعار للشركة أو الراعي */
    .lux-logo-card {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        min-height: 40px;
    }

    .lux-logo-card:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(56, 189, 248, 0.45);
        box-shadow: 0 4px 16px rgba(56, 189, 248, 0.25);
        transform: translateY(-1px);
    }

    /* صورة الشعار المباشرة */
    .lux-pure-img {
        height: 34px;
        max-width: 130px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
    }

    .lux-logo-card:hover .lux-pure-img {
        transform: scale(1.08);
    }

    /* بطاقة نصية أنيقة إذا لم يكن للشركة أو الراعي صورة شعار بعد */
    .lux-pure-text-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.92);
        font-size: 0.92rem;
        font-weight: 700;
        white-space: nowrap;
        user-select: none;
        transition: all 0.22s ease;
    }

    .lux-pure-text-wrap:hover {
        background: rgba(56, 189, 248, 0.12);
        border-color: rgba(56, 189, 248, 0.35);
        color: #38bdf8;
    }

    /* شريحة الإعلان عندما لا توجد شركات بعد في لوحة التحكم */
    .lux-empty-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 14px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px dashed rgba(255, 255, 255, 0.15);
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.86rem;
        font-weight: 600;
        white-space: nowrap;
        user-select: none;
    }

    /* الفاصل النقطي المضيء بين الشعارات */
    .lux-ticker-separator {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #38bdf8;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.7);
        flex-shrink: 0;
        margin: 0 1.25rem;
        opacity: 0.6;
    }

    /* التجاوب للشاشات المتوسطة والصغيرة */
    @media (max-width: 991.98px) {
        .lux-strip-inner { height: 56px; }
        .lux-strip-brand { padding: 0 14px 0 16px; gap: 10px; }
        .lux-strip-badge { font-size: 0.7rem; padding: 3px 9px; }
        .lux-strip-logo-img { height: 26px; }
        .lux-logo-card { min-height: 34px; padding: 2px 10px; }
        .lux-pure-img { height: 28px; max-width: 105px; }
        .lux-pure-text-wrap { font-size: 0.82rem; padding: 4px 10px; }
        .lux-empty-chip { font-size: 0.78rem; }
        .lux-ticker-separator { margin: 0 1rem; }
    }

    @media (max-width: 575.98px) {
        .lux-strip-inner { height: 50px; }
        .lux-strip-brand { padding: 0 10px; }
        .lux-strip-badge span { display: none; }
        .lux-strip-badge { padding: 4px 8px; font-size: 0.8rem; }
        .lux-strip-logos { gap: 8px; }
        .lux-strip-logo-img { height: 22px; }
        .lux-logo-card { min-height: 30px; padding: 2px 8px; }
        .lux-pure-img { height: 24px; max-width: 85px; }
        .lux-pure-text-wrap { font-size: 0.76rem; }
        .lux-empty-chip { font-size: 0.74rem; }
        .lux-ticker-separator { margin: 0 0.75rem; }
    }
</style>

<div class="lux-glass-strip" role="region" aria-label="شريط رعاة وشركات المعرض">
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
                @if($hasDynamicItems)
                    @foreach($tickerItems as $item)
                        @php
                            $sp = (object)$item;
                            $spName = $sp->name ?? 'شريك المعرض';
                            $spLogo = !empty($sp->logo_path) ? $resolveLogoUrl($sp->logo_path) : null;
                            $spUrl = $sp->website ?? null;
                            $isSponsor = ($sp->type ?? '') === 'sponsor';
                        @endphp
                        <div class="lux-logo-pure-item {{ $spUrl ? 'is-clickable' : '' }}"
                             @if($spUrl) onclick="window.open('{{ $spUrl }}', '_blank', 'noopener,noreferrer');" @endif
                             title="{{ $spName }} ({{ $sp->tier_label ?? ($isSponsor ? 'جهة راعية' : 'شركة مشاركة') }})">
                            @if($spLogo)
                                <div class="lux-logo-card">
                                    <img src="{{ $spLogo }}" 
                                         alt="{{ $spName }}" 
                                         class="lux-pure-img"
                                         loading="lazy"
                                         onerror="this.parentElement.style.display='none'; if(this.closest('.lux-logo-pure-item').querySelector('.lux-pure-text-wrap')) this.closest('.lux-logo-pure-item').querySelector('.lux-pure-text-wrap').style.display='inline-flex';">
                                </div>
                                <div class="lux-pure-text-wrap" style="display:none;">
                                    <i class="fas {{ $isSponsor ? 'fa-crown text-warning' : 'fa-building text-info' }}"></i>
                                    <span>{{ $spName }}</span>
                                </div>
                            @else
                                <div class="lux-pure-text-wrap">
                                    <i class="fas {{ $isSponsor ? 'fa-crown text-warning' : 'fa-building text-info' }}"></i>
                                    <span>{{ $spName }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="lux-ticker-separator"></div>
                    @endforeach
                @else
                    {{-- عرض شريط إعلامي أنيق عندما لا تكون هناك جهات مضافة بعد في لوحة التحكم --}}
                    <div class="lux-empty-chip">
                        <i class="fas fa-crown text-warning"></i>
                        <span>باب الرعاية والمشاركة في المعرض متاح لكبرى الشركات والمؤسسات</span>
                    </div>
                    <div class="lux-ticker-separator"></div>
                    <div class="lux-empty-chip">
                        <i class="fas fa-handshake text-info"></i>
                        <span>تضاف شعارات الرعاة والشركات فور اعتمادها في لوحة تحكم المعرض</span>
                    </div>
                    <div class="lux-ticker-separator"></div>
                    <div class="lux-empty-chip">
                        <i class="fas fa-university text-warning"></i>
                        <span>جامعة طرابلس — تنظيم بالتعاون مع شركة الواحة للمعارض والمؤتمرات</span>
                    </div>
                    <div class="lux-ticker-separator"></div>
                    <div class="lux-empty-chip">
                        <i class="fas fa-crown text-warning"></i>
                        <span>باب الرعاية والمشاركة في المعرض متاح لكبرى الشركات والمؤسسات</span>
                    </div>
                    <div class="lux-ticker-separator"></div>
                    <div class="lux-empty-chip">
                        <i class="fas fa-handshake text-info"></i>
                        <span>تضاف شعارات الرعاة والشركات فور اعتمادها في لوحة تحكم المعرض</span>
                    </div>
                    <div class="lux-ticker-separator"></div>
                @endif
            </div>
        </div>
    </div>
</div>
