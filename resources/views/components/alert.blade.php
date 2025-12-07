{{--
Alert Component
مكون التنبيهات المحسن

Usage:
@include('components.alert', [
'type' => 'success', // success, warning, danger, info
'title' => 'عنوان التنبيه', // optional
'message' => 'رسالة التنبيه',
'dismissible' => true, // optional, default false
])
--}}

@php
    $icons = [
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-times-circle',
        'info' => 'fas fa-info-circle',
    ];

    $icon = $icons[$type ?? 'info'] ?? 'fas fa-info-circle';
    $isDismissible = $dismissible ?? false;
@endphp

<div class="alert-modern alert-{{ $type ?? 'info' }}-modern {{ $isDismissible ? 'alert-dismissible fade show' : '' }}"
    role="alert">
    <div class="alert-modern-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="alert-modern-content">
        @isset($title)
            <div class="alert-modern-title">{{ $title }}</div>
        @endisset
        <div class="alert-modern-message">{{ $message ?? '' }}</div>
    </div>
    @if($isDismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>