{{--
Empty State Component
مكون حالة عدم وجود بيانات

Usage:
@include('components.empty-state', [
'icon' => 'fas fa-inbox',
'title' => 'لا توجد بيانات',
'message' => 'لم يتم العثور على أي سجلات',
'actionText' => 'إضافة جديد', // optional
'actionUrl' => route('some.create'), // optional
])
--}}

<div class="empty-state">
    <div class="empty-state-icon">
        <i class="{{ $icon ?? 'fas fa-inbox' }}"></i>
    </div>
    <h3 class="empty-state-title">{{ $title ?? 'لا توجد بيانات' }}</h3>
    <p class="empty-state-message">{{ $message ?? 'لم يتم العثور على أي سجلات' }}</p>

    @if(isset($actionText) && isset($actionUrl))
        <a href="{{ $actionUrl }}" class="btn btn-primary-modern">
            <i class="fas fa-plus me-2"></i>
            {{ $actionText }}
        </a>
    @endif
</div>