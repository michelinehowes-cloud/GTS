{{-- Graduate Sidebar Navigation --}}
<div class="sidebar-heading">
    الرئيسية
</div>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('graduate.dashboard') ? 'active' : '' }}"
        href="{{ route('graduate.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>لوحة التحكم</span>
    </a>
</li>

<hr class="sidebar-divider">

<div class="sidebar-heading">
    التدريب والتوظيف
</div>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('graduate.trainings*') ? 'active' : '' }}"
        href="{{ route('graduate.trainings') }}">
        <i class="fas fa-fw fa-graduation-cap"></i>
        <span>برامج التدريب</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-opportunities.index') ? 'active' : '' }}"
        href="{{ route('job-opportunities.index') }}">
        <i class="fas fa-fw fa-briefcase"></i>
        <span>فرص العمل</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-fair.*') ? 'active' : '' }}"
        href="{{ route('job-fair.public') }}" style="{{ request()->routeIs('job-fair.*') ? '' : 'position:relative' }}">
        <i class="fas fa-fw fa-store"></i>
        <span>
            معرض التوظيف 2026
            <span class="badge ms-1 py-1 px-2 rounded-pill" style="background: linear-gradient(135deg,#F59E0B,#F97316);font-size:0.62rem;vertical-align:middle">جديد</span>
        </span>
    </a>
</li>

<hr class="sidebar-divider">

<div class="sidebar-heading">
    الملف الشخصي
</div>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('graduate.profile') ? 'active' : '' }}"
        href="{{ route('graduate.profile') }}">
        <i class="fas fa-fw fa-user"></i>
        <span>بياناتي</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}"
        href="{{ route('notifications.index') }}">
        <i class="fas fa-fw fa-bell"></i>
        <span>الإشعارات</span>
    </a>
</li>