{{--
Stat Card Component
بطاقة إحصائية قابلة لإعادة الاستخدام

Usage:
@include('components.stat-card', [
'title' => 'عنوان البطاقة',
'value' => '100',
'icon' => 'fas fa-users',
'color' => 'primary', // primary, success, warning, danger, info
'trend' => '+10%', // optional
'trendDirection' => 'up', // up or down
'link' => route('some.route'), // optional
])
--}}

@php
    $colorClasses = [
        'primary' => 'bg-primary',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'danger' => 'bg-danger',
        'info' => 'bg-info',
    ];

    $bgClass = $colorClasses[$color ?? 'primary'] ?? 'bg-primary';
@endphp

<div class="card-stat">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="card-stat-icon {{ $bgClass }}">
            <i class="{{ $icon ?? 'fas fa-chart-line' }}"></i>
        </div>

        @isset($trend)
            <div class="badge badge-modern badge-{{ $trendDirection === 'up' ? 'success' : 'danger' }}">
                <i class="fas fa-arrow-{{ $trendDirection === 'up' ? 'up' : 'down' }} me-1"></i>
                {{ $trend }}
            </div>
        @endisset
    </div>

    <div class="card-stat-value">{{ $value ?? '0' }}</div>
    <div class="card-stat-label">{{ $title ?? 'عنوان البطاقة' }}</div>

    @isset($link)
        <a href="{{ $link }}" class="stretched-link"></a>
    @endisset
</div>