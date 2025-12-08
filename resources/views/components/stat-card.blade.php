{{--
Stat Card Component
بطاقة إحصائية بتصميم بسيط ونظيف (بدون خلفية للأيقونة)

Usage:
@include('components.stat-card', [
'title' => 'عنوان البطاقة',
'value' => '100',
'icon' => 'fas fa-users',
'color' => 'primary', // primary, success, warning, danger, info
'col' => 'col-md-3'
])
--}}

@if(isset($col))
    <div class="{{ $col }}">
@endif

    <div class="card border-0 shadow-sm h-100 p-3 stat-card-clean overflow-hidden position-relative">
        <div class="d-flex align-items-center justify-content-between position-relative z-1">
            <div class="content-side">
                <h2 class="mb-0 fw-bold text-dark" style="font-size: 2rem;">{{ $value ?? '0' }}</h2>
                <p class="text-muted mb-0 small fw-bold mt-1" style="font-size: 0.9rem;">{{ $title ?? 'العنوان' }}</p>
            </div>
            <div class="icon-side">
                {{-- أيقونة كبيرة ملونة وبدون خلفية --}}
                <i class="{{ $icon ?? 'fas fa-circle' }} fa-3x text-{{ $color ?? 'primary' }}" style="opacity: 0.8;"></i>
            </div>
        </div>
        
        {{-- تأثير جمالي في الخلفية --}}
        <i class="{{ $icon ?? 'fas fa-circle' }} position-absolute text-{{ $color ?? 'primary' }}" 
           style="bottom: -20px; left: -20px; font-size: 8rem; opacity: 0.05; transform: rotate(15deg); z-index: 0;"></i>

        @isset($link)
            <a href="{{ $link }}" class="stretched-link"></a>
        @endisset
    </div>

    @if(isset($col))
        </div>
    @endif

<style>
.stat-card-clean {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 16px;
    background: #fff;
}
.stat-card-clean:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}
</style>