<div class="registration-container">
    <div class="registration-card">
        <!-- Header -->
        <div class="card-header position-relative">
            @if(isset($backRoute) || isset($backUrl))
                <div class="d-flex justify-content-end mb-2 position-relative" style="z-index: 2;">
                    <a href="{{ $backRoute ?? $backUrl }}" class="btn btn-sm btn-light bg-white bg-opacity-25 text-white rounded-pill px-3 border-0 shadow-none">
                        <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                    </a>
                </div>
            @endif
            <h2><i class="fas {{ $icon ?? 'fa-edit' }} me-2"></i> {{ $title }}</h2>
            @if(isset($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
        </div>

        <!-- Body -->
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>
