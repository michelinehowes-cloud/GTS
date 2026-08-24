{{-- Company Sidebar Navigation --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}" 
       href="{{ route('company.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i>
        لوحة التحكم
    </a>
</li>

{{-- Assuming a company can manage their job opportunities --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-opportunities.index') ? 'active' : '' }}" 
       href="{{ route('job-opportunities.index') }}">
        <i class="fas fa-briefcase"></i>
        فرص العمل
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('company.job-fairs*') ? 'active' : '' }}" 
       href="{{ route('company.job-fairs.index') }}">
        <i class="fas fa-store"></i>
        معارض التوظيف
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-opportunities.create') ? 'active' : '' }}" 
       href="{{ route('job-opportunities.create') }}">
        <i class="fas fa-plus-circle"></i>
        إضافة فرصة عمل
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" 
       href="{{ route('messages.index') }}">
        <i class="fas fa-envelope"></i>
        <span>
            رسائل المعرض
            @php
                $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            @if($unreadMessages > 0)
                <span class="badge bg-danger ms-1">{{ $unreadMessages }}</span>
            @endif
        </span>
    </a>
</li>
