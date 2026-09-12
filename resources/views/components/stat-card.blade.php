@php
    $colClass = $col ?? 'col-6 col-md-4 col-xl-3';
    if (!str_contains($colClass, 'mb-')) {
        $colClass .= ' mb-2 mb-md-3';
    }
@endphp

<div class="{{ $colClass }}">
    <div class="stat-card-clean stat-{{ $color ?? 'primary' }} h-100">
        <div class="d-flex justify-content-between align-items-start">
            <div class="icon-wrapper">
                <i class="{{ $icon ?? 'fas fa-circle' }}"></i>
            </div>
            @isset($badge)
                <span class="badge {{ $badgeClass ?? 'bg-light text-dark' }} rounded-pill">{{ $badge }}</span>
            @endisset
        </div>
        <h2>{{ $value ?? '0' }}</h2>
        <p title="{{ $title ?? '' }}">{{ $title ?? 'العنوان' }}</p>

        @isset($description)
            <small class="text-muted text-truncate d-none d-sm-block mt-1" style="font-size: 0.75rem;">{{ $description }}</small>
        @endisset

        @isset($link)
            <a href="{{ $link }}" class="stretched-link"></a>
        @endisset
    </div>
</div>