<div class="registration-container">
    <div class="registration-card">
        <!-- Header -->
        <div class="card-header">
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
