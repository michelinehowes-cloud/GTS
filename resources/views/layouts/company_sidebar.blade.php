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
    <a class="nav-link {{ request()->routeIs('job-opportunities.create') ? 'active' : '' }}" 
       href="{{ route('job-opportunities.create') }}">
        <i class="fas fa-plus-circle"></i>
        إضافة فرصة عمل
    </a>
</li>
