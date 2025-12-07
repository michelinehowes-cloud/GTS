{{--
Breadcrumbs Component
مكون التنقل الهرمي

Usage:
@include('components.breadcrumbs', [
'items' => [
['title' => 'الرئيسية', 'url' => route('home')],
['title' => 'لوحة التحكم', 'url' => route('dashboard')],
['title' => 'الصفحة الحالية'], // No URL means active item
]
])
--}}

@if(isset($items) && count($items) > 0)
    <div class="breadcrumb-modern">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($items as $index => $item)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $item['title'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $item['url'] ?? '#' }}">
                                @if($loop->first)
                                    <i class="fas fa-home me-1"></i>
                                @endif
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif