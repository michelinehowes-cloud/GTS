@props([
    'theme' => 'light',
    'size' => 'flexible',
    'action' => null,
])

@if(config('security.turnstile.enabled', true))
    @php
        $siteKey = config('security.turnstile.site_key');
        $funcSuffix = 'ts_' . substr(md5($action . '_' . uniqid()), 0, 8);
    @endphp

    <div class="turnstile-wrapper my-2.5 d-flex flex-column align-items-center justify-content-center" id="wrap-{{ $funcSuffix }}">
        <div id="widget-{{ $funcSuffix }}"
             class="cf-turnstile" 
             data-sitekey="{{ $siteKey }}" 
             data-theme="{{ $theme }}" 
             data-size="{{ $size }}"
             @if($action) data-action="{{ $action }}" @endif>
        </div>
        @error('cf-turnstile-response')
            <div class="text-danger small mt-1 fw-bold text-center">
                <i class="fas fa-shield-alt me-1"></i>{{ $message }}
            </div>
        @enderror
        @error('security')
            <div class="text-danger small mt-1 fw-bold text-center">
                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>

    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce
@endif
