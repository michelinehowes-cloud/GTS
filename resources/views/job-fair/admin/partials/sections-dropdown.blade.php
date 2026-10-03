{{-- قائمة أقسام المعرض الموحدة لكافة الصفحات الإدارية --}}
<div class="dropdown d-inline-block position-relative">
    <button class="fair-btn-action fair-btn-glass dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="الوصول السريع لكافة أقسام وإدارات المعرض">
        <i class="fas fa-th-large text-warning"></i>
        <span>أقسام المعرض</span>
    </button>
    <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2" style="min-width: 235px; z-index: 1075; right: 0; left: auto; top: 100%; margin-top: 6px;">
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between {{ Route::currentRouteName() === 'job-fair.admin.projects.index' ? 'active fw-bold' : '' }}" href="{{ route('job-fair.admin.projects.index', $fair->id) }}">
                <span class="d-flex align-items-center gap-2">
                    <i class="fas fa-lightbulb text-warning"></i>
                    <span>مشاريع التخرج</span>
                </span>
                <span class="badge {{ Route::currentRouteName() === 'job-fair.admin.projects.index' ? 'bg-light text-primary' : 'bg-light text-dark' }} border">{{ $fair->projects->count() }}</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between {{ Route::currentRouteName() === 'job-fair.admin.events.index' ? 'active fw-bold' : '' }}" href="{{ route('job-fair.admin.events.index', $fair->id) }}">
                <span class="d-flex align-items-center gap-2">
                    <i class="fas fa-graduation-cap text-primary"></i>
                    <span>البرنامج العلمي</span>
                </span>
                <span class="badge {{ Route::currentRouteName() === 'job-fair.admin.events.index' ? 'bg-light text-primary' : 'bg-light text-dark' }} border">{{ $fair->events->count() }}</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between {{ Route::currentRouteName() === 'job-fair.admin.visitors.index' ? 'active fw-bold' : '' }}" href="{{ route('job-fair.admin.visitors.index', $fair->id) }}">
                <span class="d-flex align-items-center gap-2">
                    <i class="fas fa-id-badge text-success"></i>
                    <span>إدارة الزوار</span>
                </span>
                <span class="badge {{ Route::currentRouteName() === 'job-fair.admin.visitors.index' ? 'bg-light text-primary' : 'bg-light text-dark' }} border">{{ $fair->visitors->count() }}</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between" href="{{ route('job-fair.admin.show', $fair->id) }}#sponsorsSection">
                <span class="d-flex align-items-center gap-2">
                    <i class="fas fa-crown text-warning"></i>
                    <span>الرعاة والداعمون</span>
                </span>
                <span class="badge bg-light text-dark border">{{ $fair->sponsors->count() }}</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center gap-2" href="{{ route('job-fair.admin.show', $fair->id) }}#brandIdentitySection">
                <i class="fas fa-palette text-info"></i>
                <span>الهوية البصرية للأصول</span>
            </a>
        </li>
        <li class="dropdown-divider my-1"></li>
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center gap-2 text-primary fw-semibold" href="{{ route('job-fair.admin.show', $fair->id) }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة التحكم الرئيسية للمعرض</span>
            </a>
        </li>
    </ul>
</div>
