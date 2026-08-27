@if(isset($col))
    <div class="{{ $col }}">
@endif

    <div class="stat-card-clean stat-{{ $color ?? 'primary' }}">
        <div class="icon-wrapper">
            <i class="{{ $icon ?? 'fas fa-circle' }}"></i>
        </div>
        <h2>{{ $value ?? '0' }}</h2>
        <p>{{ $title ?? 'العنوان' }}</p>

        @isset($link)
            <a href="{{ $link }}" class="stretched-link"></a>
        @endisset
    </div>

@if(isset($col))
    </div>
@endif