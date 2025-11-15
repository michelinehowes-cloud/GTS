{{-- Graduate Sidebar Navigation --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('graduate.dashboard') ? 'active' : '' }}" 
       href="{{ route('graduate.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i>
        لوحة التحكم
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('graduate.profile') ? 'active' : '' }}" 
       href="{{ route('graduate.profile') }}">
        <i class="fas fa-user"></i>
        الملف الشخصي
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('graduate.trainings*') ? 'active' : '' }}" 
       href="{{ route('graduate.trainings') }}">
        <i class="fas fa-graduation-cap"></i>
        برامج التدريب المتاحة
    </a>
</li>
