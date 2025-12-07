{{--
Loading Component
مكون التحميل

Usage:
@include('components.loading', [
'message' => 'جاري التحميل...', // optional
'overlay' => true, // optional, creates full-screen overlay
])
--}}

@if(isset($overlay) && $overlay)
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="text-white mt-3">{{ $message ?? 'جاري التحميل...' }}</p>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <div class="loading-spinner mx-auto"></div>
        <p class="mt-3 text-muted">{{ $message ?? 'جاري التحميل...' }}</p>
    </div>
@endif