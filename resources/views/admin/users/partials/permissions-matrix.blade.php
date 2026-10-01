{{-- مصفوفة إدارة الصلاحيات التفاعلية (RBAC Bento Grid) --}}
<div class="permissions-matrix-wrapper mt-4">
    <!-- Header with Live Counter and Search -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-3 bg-white rounded-4 shadow-sm border mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="fas fa-layer-group text-primary me-2"></i> مصفوفة الصلاحيات المخصصة الشاملة
            </h5>
            <p class="text-muted small mb-0">
                حدد بدقة الوظائف التي يحق لهذا الموظف الوصول إليها. لن يتمكن الموظف من عرض أو تعديل أي قسم لم يتم اختياره هنا.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fs-6 fw-bold">
                <i class="fas fa-check-circle me-1"></i>
                المحدد: <span id="selected_perms_counter">{{ isset($userPermissionIds) ? count($userPermissionIds) : 0 }}</span> / <span id="total_perms_counter">{{ collect($groupedPermissions)->sum(fn($g) => count($g['items'] ?? $g['permissions'] ?? [])) }}</span>
            </span>
        </div>
    </div>

    <!-- Quick Role Presets (قوالب جاهزة بنقرة واحدة) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-dark">
                    <i class="fas fa-magic text-warning me-1"></i> قوالب وظيفية سريعة (بنقرة واحدة لتسهيل التعيين):
                </span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btn_select_all_perms">
                        <i class="fas fa-check-double me-1"></i> تحديد الكل
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btn_deselect_all_perms">
                        <i class="fas fa-times me-1"></i> إلغاء التحديد
                    </button>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 preset-btn" data-preset="training">
                    🎓 منسق تدريب
                </button>
                <button type="button" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 preset-btn" data-preset="partnerships">
                    🏢 مسؤول شركات وتوظيف
                </button>
                <button type="button" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 preset-btn" data-preset="career">
                    🧭 أخصائي إرشاد مهني
                </button>
                <button type="button" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 preset-btn" data-preset="job_fair">
                    🎪 مشرف معرض التوظيف 2026
                </button>
                <button type="button" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 preset-btn" data-preset="evaluations">
                    📊 مسؤول تقييم ومتابعة
                </button>
                <button type="button" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 preset-btn" data-preset="media">
                    📢 مسؤول إعلام ونشر
                </button>
            </div>
        </div>
    </div>

    <!-- Permissions Filter Bar -->
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="filter_permissions_input" class="form-control border-start-0 rounded-end-pill" placeholder="ابحث باسم الصلاحية أو الوصف لتصفية الخيارات فورياً...">
        </div>
    </div>

    <!-- Modules Bento Grid -->
    <div class="row g-3" id="permissions_bento_grid">
        @foreach($groupedPermissions as $moduleKey => $module)
        <div class="col-md-6 permission-module-col" data-module="{{ $moduleKey }}">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden module-card">
                <!-- Module Header -->
                <div class="card-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" 
                             style="width: 36px; height: 36px; background-color: {{ $module['meta']['color'] }}15; color: {{ $module['meta']['color'] }};">
                            <i class="{{ $module['meta']['icon'] }}"></i>
                        </div>
                        @php
                            $moduleItems = $module['items'] ?? $module['permissions'] ?? collect();
                        @endphp
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $module['meta']['label'] }}</h6>
                            <small class="text-muted">{{ count($moduleItems) }} صلاحيات متاحة</small>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 select-module-all-btn" 
                                data-target-module="{{ $moduleKey }}" style="font-size: 0.75rem;">
                            تحديد القسم
                        </button>
                    </div>
                </div>

                <!-- Module Permissions List -->
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2">
                        @foreach($moduleItems as $perm)
                            @php
                                $isChecked = isset($userPermissionIds) && in_array($perm->id, $userPermissionIds);
                            @endphp
                            <label class="permission-item-label p-2 rounded-3 border d-flex align-items-start gap-3 cursor-pointer transition-all {{ $isChecked ? 'bg-primary bg-opacity-10 border-primary' : 'bg-white' }}"
                                   for="perm_{{ $perm->id }}" 
                                   data-perm-id="{{ $perm->id }}"
                                   data-perm-name="{{ $perm->name }}"
                                   data-perm-module="{{ $perm->module }}">
                                <div class="form-check form-switch m-0 pt-1">
                                    <input class="form-check-input permission-checkbox" 
                                           type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $perm->id }}" 
                                           id="perm_{{ $perm->id }}"
                                           data-name="{{ $perm->name }}"
                                           data-module="{{ $perm->module }}"
                                           {{ $isChecked ? 'checked' : '' }}>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark perm-display-title">{{ $perm->display_name }}</span>
                                        <code class="text-muted small" style="font-size: 0.7rem;">{{ $perm->name }}</code>
                                    </div>
                                    @if($perm->description)
                                        <p class="text-muted small mb-0 perm-desc-text" style="font-size: 0.78rem;">
                                            {{ $perm->description }}
                                        </p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.transition-all { transition: all 0.2s ease-in-out; }
.permission-item-label:hover {
    background-color: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
.permission-item-label.bg-primary:hover {
    background-color: rgba(37, 99, 235, 0.12) !important;
}
.form-check-input:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}
.btn-white {
    background-color: #ffffff;
    color: #1e293b;
}
.btn-white:hover {
    background-color: #f1f5f9;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const counter = document.getElementById('selected_perms_counter');
    const filterInput = document.getElementById('filter_permissions_input');

    // تحديث العداد والمظهر
    function updateCounterAndStyles() {
        let count = 0;
        checkboxes.forEach(cb => {
            const parentLabel = cb.closest('.permission-item-label');
            if (cb.checked) {
                count++;
                parentLabel.classList.add('bg-primary', 'bg-opacity-10', 'border-primary');
                parentLabel.classList.remove('bg-white');
            } else {
                parentLabel.classList.remove('bg-primary', 'bg-opacity-10', 'border-primary');
                parentLabel.classList.add('bg-white');
            }
        });
        if (counter) counter.textContent = count;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCounterAndStyles);
    });

    // تحديد الكل
    document.getElementById('btn_select_all_perms')?.addEventListener('click', function () {
        checkboxes.forEach(cb => cb.checked = true);
        updateCounterAndStyles();
    });

    // إلغاء تحديد الكل
    document.getElementById('btn_deselect_all_perms')?.addEventListener('click', function () {
        checkboxes.forEach(cb => cb.checked = false);
        updateCounterAndStyles();
    });

    // تحديد قسم كامل
    document.querySelectorAll('.select-module-all-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const modKey = this.getAttribute('data-target-module');
            const modCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${modKey}"]`);
            const allChecked = Array.from(modCheckboxes).every(cb => cb.checked);
            
            modCheckboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'تحديد القسم' : 'إلغاء تحديد';
            updateCounterAndStyles();
        });
    });

    // القوالب السريعة (Role Presets)
    const presets = {
        training: [
            'trainings.view', 'trainings.create', 'trainings.edit', 
            'trainings.applications', 'trainings.attendance', 'trainings.trainers', 'reports.view'
        ],
        partnerships: [
            'companies.view', 'companies.create', 'companies.edit', 
            'jobs.view', 'jobs.manage', 'partnerships.documents', 'reports.view'
        ],
        career: [
            'graduates.view', 'graduates.create', 'graduates.edit', 
            'graduates.approve', 'graduates.import_export', 'nominations.manage', 'reports.view'
        ],
        job_fair: [
            'job_fair.view', 'job_fair.create', 'job_fair.edit', 'job_fair.delete',
            'job_fair.manage', 'job_fair.events', 'job_fair.projects', 'job_fair.sponsors',
            'job_fair.registrations', 'job_fair.attendance', 'job_fair.live', 'job_fair.visitors',
            'companies.view', 'jobs.view', 'nominations.manage', 'reports.view'
        ],
        evaluations: [
            'surveys.manage', 'evaluations.manage', 'reports.view'
        ],
        media: [
            'media.manage', 'news.manage'
        ]
    };

    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const presetKey = this.getAttribute('data-preset');
            const allowedNames = presets[presetKey] || [];
            
            // Uncheck all first
            checkboxes.forEach(cb => {
                const name = cb.getAttribute('data-name');
                cb.checked = allowedNames.includes(name);
            });
            updateCounterAndStyles();
        });
    });

    // البحث والتصفية السريعة
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.permission-item-label').forEach(label => {
                const text = label.textContent.toLowerCase();
                if (text.includes(term)) {
                    label.style.display = 'flex';
                } else {
                    label.style.display = 'none';
                }
            });

            // إخفاء الأعمدة التي أصبحت فارغة
            document.querySelectorAll('.permission-module-col').forEach(col => {
                const visibleItems = col.querySelectorAll('.permission-item-label:not([style*="display: none"])');
                col.style.display = visibleItems.length === 0 ? 'none' : 'block';
            });
        });
    }

    // تشغيل التنسيق الأولي
    updateCounterAndStyles();
});
</script>
