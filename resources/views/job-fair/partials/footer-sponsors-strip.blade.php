{{-- ══════════════════════════════════════════════════════════════════
     الشريط السفلي الزجاجي الفاخر لرعاة وشركات المعرض (Pure Glassmorphic Logos Ticker)
     تصميم زجاجي شفاف بالكامل يعرض شعارات الشركات مباشرة وبدون أشكال بيضاوية
     ══════════════════════════════════════════════════════════════════ --}}
@php
    use Illuminate\Support\Facades\Storage;

    $tickerList = collect();

    // 1. إضافة الرعاة الرسميين المضافين للمعرض في لوحة التحكم (المفعلين فقط)
    $activeSponsors = collect();
    try {
        if (isset($sponsors) && $sponsors instanceof \Illuminate\Support\Collection) {
            $activeSponsors = $sponsors->filter(function($sp) {
                return !isset($sp->is_active) || (bool)$sp->is_active;
            });
        } elseif (isset($fair) && $fair && method_exists($fair, 'sponsors')) {
            $activeSponsors = $fair->sponsors()->where('is_active', true)->orderBy('display_order')->get();
        }
    } catch (\Throwable $e) {}

    foreach ($activeSponsors as $sp) {
        $tickerList->push((object)[
            'name'       => $sp->name,
            'logo_path'  => $sp->logo_path ?? null,
            'website'    => $sp->website ?? null,
            'type'       => 'sponsor',
            'tier_label' => method_exists($sp, 'getTierLabelAttribute') ? $sp->tier_label : ($sp->tier ?? 'جهة راعية'),
        ]);
    }

    // 2. إضافة الشركات المشاركة المؤكدة في المعرض من لوحة التحكم (المؤكدة فقط)
    $activeCompanies = collect();
    try {
        if (isset($companies) && $companies instanceof \Illuminate\Support\Collection) {
            $activeCompanies = $companies->filter(function($fc) {
                return !isset($fc->status) || $fc->status === 'confirmed';
            });
        } elseif (isset($fair) && $fair && method_exists($fair, 'companies')) {
            $activeCompanies = $fair->companies()->with('company')->where('status', 'confirmed')->get();
        }
    } catch (\Throwable $e) {}

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
       الشريط السفلي للرعاة والشركات — انسيابي وبدون شريط واضح (Seamless Logo Ticker)
       مطابق للصورة تماماً: يظهر الشعار والاسم معاً مباشرة على لون الصفحة بدون إطار أو شريط واضح
       ══════════════════════════════════════════════════════════════════ */
    .lux-glass-strip {
        background: #043c73 !important;
        background-color: #043c73 !important;
        position: relative;
        z-index: 100;
        overflow: hidden;
        width: 100%;
        max-width: 100vw;
        border: none !important;
        box-shadow: none !important;
        padding: 22px 0 28px;
    }

    .lux-glass-strip::before,
    .lux-glass-strip::after {
        display: none !important;
    }

    .lux-strip-inner {
        height: 48px;
        width: 100%;
        display: flex;
        align-items: center;
        position: relative;
    }

    /* العلامة الجانبية — انسيابية وخفيفة وبدون أي حدود أو صناديق بارزة */
    .lux-strip-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 20px 0 16px;
        flex-shrink: 0;
        position: relative;
        z-index: 5;
        height: 100%;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .lux-strip-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(238, 202, 62, 0.12);
        border: 1px solid rgba(238, 202, 62, 0.25);
        color: #f6d860;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        white-space: nowrap;
        letter-spacing: 0.3px;
    }

    .lux-strip-logos {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .lux-strip-logo-img {
        height: 26px;
        width: auto;
        object-fit: contain;
        opacity: 0.88;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        transition: all 0.25s ease;
    }
    .lux-strip-logo-img:hover {
        opacity: 1;
        transform: scale(1.06);
    }

    .lux-strip-divider {
        width: 1px;
        height: 18px;
        background: rgba(255, 255, 255, 0.18);
    }

    /* مسار الشريط المتحرك مع قناع تلاشي جانبي متدرج وناعم */
    .lux-ticker-container {
        overflow: hidden;
        width: 100%;
        min-width: 0;
        flex-grow: 1;
        position: relative;
        mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
        -webkit-mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
    }

    .lux-ticker-track {
        display: flex;
        align-items: center;
        width: max-content;
        animation: luxLogosScroll 38s linear infinite;
        padding: 4px 0;
    }
    .lux-ticker-track:hover {
        animation-play-state: paused;
    }

    @keyframes luxLogosScroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* ── عنصر الشعار + الاسم (انسيابي بدون أي صندوق أو إطار مثل الصورة المرفقة) ── */
    .lux-seamless-item {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 4px 14px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        cursor: default;
        opacity: 0.88;
    }

    .lux-seamless-item.is-clickable {
        cursor: pointer;
    }

    .lux-seamless-item:hover {
        opacity: 1;
        transform: translateY(-2px);
    }

    /* صورة الشعار داخل العنصر بدون خلفية أو صندوق */
    .lux-seamless-logo {
        height: 28px;
        max-width: 90px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.35));
        transition: transform 0.25s ease;
        user-select: none;
    }

    .lux-seamless-item:hover .lux-seamless-logo {
        transform: scale(1.08);
    }

    /* اسم الشركة أو الراعي المرافق للشعار مباشرة */
    .lux-seamless-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.02em;
        white-space: nowrap;
        user-select: none;
        transition: color 0.2s ease;
    }

    .lux-seamless-item:hover .lux-seamless-name {
        color: #38bdf8;
    }

    /* أيقونة احتياطية في حال لم تكن هناك صورة شعار */
    .lux-seamless-icon {
        font-size: 1.1rem;
        color: #38bdf8;
        opacity: 0.9;
        transition: transform 0.25s ease;
    }
    .lux-seamless-item:hover .lux-seamless-icon {
        transform: scale(1.15);
        color: #f6d860;
    }

    /* فاصل نقطي ناعم جداً بين الشعارات */
    .lux-seamless-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        flex-shrink: 0;
        margin: 0 14px;
    }

    /* التجاوب */
    @media (max-width: 991.98px) {
        .lux-glass-strip { padding: 18px 0 22px; }
        .lux-strip-inner { height: 44px; }
        .lux-strip-brand { padding: 0 12px; gap: 8px; }
        .lux-strip-badge { font-size: 0.7rem; padding: 3px 8px; }
        .lux-strip-logo-img { height: 22px; }
        .lux-seamless-logo { height: 24px; max-width: 75px; }
        .lux-seamless-name { font-size: 0.85rem; }
        .lux-seamless-dot { margin: 0 10px; }
    }

    @media (max-width: 575.98px) {
        .lux-glass-strip { padding: 14px 0 18px; }
        .lux-strip-inner { height: 40px; }
        .lux-strip-brand { padding: 0 8px; }
        .lux-strip-badge span { display: none; }
        .lux-strip-badge { padding: 3px 6px; }
        .lux-strip-logo-img { height: 20px; }
        .lux-seamless-logo { height: 20px; max-width: 60px; }
        .lux-seamless-name { font-size: 0.8rem; }
        .lux-seamless-dot { margin: 0 8px; }
    }
</style>

<div class="lux-glass-strip" role="region" aria-label="شريط رعاة وشركات المعرض">
    <div class="lux-strip-inner">
        <!-- الكتلة الثابتة لليمين: الشعار والهوية الرسمية بانسيابية تامة وبدون أي صندوق -->
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
                        <div class="lux-seamless-item {{ $spUrl ? 'is-clickable' : '' }}"
                             @if($spUrl) onclick="window.open('{{ $spUrl }}', '_blank', 'noopener,noreferrer');" @endif
                             title="{{ $spName }} ({{ $sp->tier_label ?? ($isSponsor ? 'جهة راعية' : 'شركة مشاركة') }})">
                            @if($spLogo)
                                <img src="{{ $spLogo }}" 
                                     alt="{{ $spName }}" 
                                     class="lux-seamless-logo"
                                     loading="lazy"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                <i class="fas {{ $isSponsor ? 'fa-crown text-warning' : 'fa-building text-info' }} lux-seamless-icon" style="display:none;"></i>
                                <span class="lux-seamless-name">{{ $spName }}</span>
                                @if($isSponsor)
                                    <i class="fas fa-crown text-warning" style="font-size: 0.72rem; opacity: 0.85;" title="جهة راعية"></i>
                                @endif
                            @else
                                <i class="fas {{ $isSponsor ? 'fa-crown text-warning' : 'fa-building text-info' }} lux-seamless-icon"></i>
                                <span class="lux-seamless-name">{{ $spName }}</span>
                            @endif
                        </div>
                        <div class="lux-seamless-dot"></div>
                    @endforeach
                @else
                    {{-- عرض شريط إعلامي أنيق عندما لا تكون هناك جهات مضافة بعد في لوحة التحكم --}}
                    <div class="lux-seamless-item">
                        <i class="fas fa-crown text-warning lux-seamless-icon"></i>
                        <span class="lux-seamless-name">باب الرعاية والمشاركة متاح لكبرى الشركات والمؤسسات</span>
                    </div>
                    <div class="lux-seamless-dot"></div>
                    <div class="lux-seamless-item">
                        <i class="fas fa-handshake text-info lux-seamless-icon"></i>
                        <span class="lux-seamless-name">تضاف شعارات الرعاة والشركات فور اعتمادها</span>
                    </div>
                    <div class="lux-seamless-dot"></div>
                    <div class="lux-seamless-item">
                        <i class="fas fa-university text-warning lux-seamless-icon"></i>
                        <span class="lux-seamless-name">جامعة طرابلس — تنظيم بالتعاون مع شركة الواحة للمعارض</span>
                    </div>
                    <div class="lux-seamless-dot"></div>
                @endif
            </div>
        </div>
    </div>
</div>
