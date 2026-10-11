{{-- ══════════════════════════════════════════════════════════════════
     الشريط السفلي الزجاجي الفاخر لرعاة وشركات المعرض (Pure Glassmorphic Logos Ticker)
     تصميم زجاجي شفاف بالكامل يعرض شعارات الشركات مباشرة وبدون أشكال بيضاوية
     ══════════════════════════════════════════════════════════════════ --}}
@php
    $tickerList = collect();

    // 1. إضافة الرعاة الرسميين
    if (isset($sponsors) && $sponsors->isNotEmpty()) {
        foreach ($sponsors as $sp) {
            $tickerList->push((object)[
                'name' => $sp->name,
                'logo_path' => $sp->logo_path ?? null,
                'website' => $sp->website ?? null,
            ]);
        }
    } elseif (isset($fair) && $fair) {
        foreach ($fair->sponsors()->where('is_active', true)->get() as $sp) {
            $tickerList->push((object)[
                'name' => $sp->name,
                'logo_path' => $sp->logo_path ?? null,
                'website' => $sp->website ?? null,
            ]);
        }
    }

    // 2. إضافة الشركات المشاركة في المعرض
    if (isset($companies) && $companies->isNotEmpty()) {
        foreach ($companies as $fc) {
            $comp = $fc->company ?? null;
            if ($comp) {
                $tickerList->push((object)[
                    'name' => $comp->name,
                    'logo_path' => $comp->logo_path ?: $comp->logo,
                    'website' => $comp->website ?? null,
                ]);
            }
        }
    } elseif (isset($fair) && $fair) {
        foreach ($fair->companies()->with('company')->get() as $fc) {
            $comp = $fc->company ?? null;
            if ($comp) {
                $tickerList->push((object)[
                    'name' => $comp->name,
                    'logo_path' => $comp->logo_path ?: $comp->logo,
                    'website' => $comp->website ?? null,
                ]);
            }
        }
    }

    // إزالة التكرار بالاسم
    $tickerList = $tickerList->unique('name');

    // قائمة افتراضية عند عدم وجود شركات
    if ($tickerList->isEmpty()) {
        $tickerList = collect([
            (object)['name' => 'شركة المدار الجديد', 'logo_path' => null, 'website' => null],
            (object)['name' => 'شركة ليبيانا للهاتف المحمول', 'logo_path' => null, 'website' => null],
            (object)['name' => 'ليبيا للاتصالات والتقنية LTT', 'logo_path' => null, 'website' => null],
            (object)['name' => 'مصرف التجارة والتنمية', 'logo_path' => null, 'website' => null],
            (object)['name' => 'شركة الواحة للنفط', 'logo_path' => null, 'website' => null],
            (object)['name' => 'مصرف الجمهورية', 'logo_path' => null, 'website' => null],
        ]);
    }

    $rawItems = $tickerList->all();
    // تكرار العناصر لضمان مسار شريط لانهائي وسلس
    $tickerItems = array_merge($rawItems, $rawItems);
@endphp

<style>
    /* ══════════════════════════════════════════════════════════════════
       الشريط السفلي الزجاجي الشفاف (Pure Glassmorphic Logos Strip)
       ══════════════════════════════════════════════════════════════════ */
    .lux-glass-strip {
        background: rgba(3, 16, 36, 0.58) !important;
        backdrop-filter: blur(22px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(22px) saturate(190%) !important;
        position: relative;
        z-index: 100;
        overflow: hidden;
        width: 100%;
        max-width: 100vw;
        border-top: 1px solid rgba(255, 255, 255, 0.16) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 -10px 35px rgba(0, 0, 0, 0.45), 
                    inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    }

    /* خط مضيء علوي فائق الدقة */
    .lux-glass-strip::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1.5px;
        background: linear-gradient(90deg, transparent 0%, rgba(56, 189, 248, 0.7) 20%, #eeca3e 50%, rgba(56, 189, 248, 0.7) 80%, transparent 100%);
        pointer-events: none;
        z-index: 10;
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
        background: rgba(3, 18, 38, 0.72);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-left: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: -8px 0 25px rgba(0, 0, 0, 0.3);
    }

    .lux-strip-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(238, 202, 62, 0.12);
        border: 1px solid rgba(238, 202, 62, 0.4);
        color: #fef08a;
        font-size: 0.76rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        white-space: nowrap;
        letter-spacing: 0.3px;
        box-shadow: 0 0 12px rgba(238, 202, 62, 0.15);
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

    /* صورة الشعار المباشرة */
    .lux-pure-img {
        height: 38px;
        max-width: 130px;
        object-fit: contain;
        filter: brightness(1) drop-shadow(0 2px 6px rgba(0, 0, 0, 0.5));
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), filter 0.25s ease;
        user-select: none;
    }

    .lux-logo-pure-item:hover .lux-pure-img {
        transform: scale(1.15) translateY(-1px);
        filter: brightness(1.25) drop-shadow(0 4px 14px rgba(56, 189, 248, 0.5));
    }

    /* نص الشعار النقي (بديل الشعار إذا كانت الصورة مفقودة أو نصية فقط) */
    .lux-pure-text {
        font-size: 0.96rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.92);
        letter-spacing: 0.3px;
        white-space: nowrap;
        transition: color 0.22s ease, transform 0.22s ease;
        user-select: none;
    }

    .lux-logo-pure-item:hover .lux-pure-text {
        color: #38bdf8;
        transform: scale(1.08);
        text-shadow: 0 0 10px rgba(56, 189, 248, 0.4);
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
        .lux-pure-img { height: 32px; max-width: 105px; }
        .lux-pure-text { font-size: 0.86rem; }
        .lux-ticker-separator { margin: 0 1rem; }
    }

    @media (max-width: 575.98px) {
        .lux-strip-inner { height: 50px; }
        .lux-strip-brand { padding: 0 10px; }
        .lux-strip-badge span { display: none; }
        .lux-strip-badge { padding: 4px 8px; font-size: 0.8rem; }
        .lux-strip-logos { gap: 8px; }
        .lux-strip-logo-img { height: 22px; }
        .lux-pure-img { height: 28px; max-width: 90px; }
        .lux-pure-text { font-size: 0.8rem; }
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

        <!-- المسار التفاعلي المتحرك (Scrolling Ticker Track) - شعارات خالصة فقط -->
        <div class="lux-ticker-container">
            <div class="lux-ticker-track">
                @foreach($tickerItems as $item)
                    @php
                        $sp = (object)$item;
                        $spName = $sp->name ?? 'شريك المعرض';
                        $spLogo = !empty($sp->logo_path) ? Storage::url($sp->logo_path) : null;
                        $spUrl = $sp->website ?? null;
                    @endphp
                    <div class="lux-logo-pure-item {{ $spUrl ? 'is-clickable' : '' }}"
                         @if($spUrl) onclick="window.open('{{ $spUrl }}', '_blank', 'noopener,noreferrer');" @endif
                         title="{{ $spName }}">
                        @if($spLogo)
                            <img src="{{ $spLogo }}" 
                                 alt="{{ $spName }}" 
                                 class="lux-pure-img"
                                 loading="lazy"
                                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                            <span class="lux-pure-text" style="display:none;">{{ $spName }}</span>
                        @else
                            <span class="lux-pure-text">{{ $spName }}</span>
                        @endif
                    </div>
                    <div class="lux-ticker-separator"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
