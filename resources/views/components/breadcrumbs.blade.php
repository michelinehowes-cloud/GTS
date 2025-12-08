{{--
Breadcrumbs Component
مكون التنقل الهرمي

Usage:
@include('components.breadcrumbs', [
'items' => [
['label' => 'الرئيسية', 'url' => route('home')],
['label' => 'لوحة التحكم', 'url' => route('dashboard')],
['label' => 'الصفحة الحالية', 'active' => true], // active means current page
]
])
--}}

@if(isset($items) && count($items) > 0)
    <div class="breadcrumb-modern mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($items as $index => $item)
                    @php
                        $label = $item['label'] ?? $item['title'] ?? '';
                        $isActive = $item['active'] ?? $loop->last;
                    @endphp

                    @if($isActive)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $label }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $item['url'] ?? '#' }}">
                                @if($loop->first)
                                    <i class="fas fa-home me-1"></i>
                                @endif
                                {{ $label }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif