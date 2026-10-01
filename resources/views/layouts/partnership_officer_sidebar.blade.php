{{-- Partnership Officer Sidebar Navigation --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('partnership.dashboard') ? 'active' : '' }}"
        href="{{ route('partnership.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i>
        لوحة التحكم
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('partnership.companies*') ? 'active' : '' }}"
        href="{{ route('partnership.companies') }}">
        <i class="fas fa-building"></i>
        إدارة الشركات
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('partnership.documents*') ? 'active' : '' }}"
        href="{{ route('partnership.documents') }}">
        <i class="fas fa-file-contract"></i>
        إدارة الوثائق
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('partnership.import-graduates*') ? 'active' : '' }}"
        href="{{ route('partnership.import-graduates') }}">
        <i class="fas fa-file-import"></i>
        استيراد بيانات الخريجين
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('partnership.reports') || request()->routeIs('export.reports.pdf') || request()->routeIs('export.reports.excel') ? 'active' : '' }}"
        href="{{ route('partnership.reports') }}">
        <i class="fas fa-chart-bar"></i>
        التقارير
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-opportunities.index') ? 'active' : '' }}"
        href="{{ route('job-opportunities.index') }}">
        <i class="fas fa-briefcase"></i>
        فرص العمل
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-fair.admin.index') || request()->routeIs('job-fair.admin.show') || request()->routeIs('job-fair.admin.edit') ? 'active' : '' }}"
        href="{{ route('job-fair.admin.index') }}">
        <i class="fas fa-calendar-star"></i>
        المعارض والفعاليات
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('job-fair.admin.visitors*') ? 'active' : '' }}"
        href="{{ route('job-fair.admin.visitors.index', 1) }}">
        <i class="fas fa-id-badge"></i>
        إدارة زوار المعرض
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('partnership.nominations*') ? 'active' : '' }}"
        href="{{ route('partnership.nominations') }}">
        <i class="fas fa-user-check"></i>
        إدارة الترشيحات
    </a>
</li>