{{-- قائمة الخيارات والإعدادات الموحدة لكافة الصفحات الإدارية للمعرض --}}
<div class="dropdown d-inline-block position-relative">
    <button class="fair-btn-action fair-btn-glass dropdown-toggle" type="button" id="optionsDropdownBtn-{{ $fair->id }}" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="تعديل وتصدير وضبط إعدادات المعرض">
        <i class="fas fa-sliders-h"></i>
        <span>خيارات وإعدادات</span>
    </button>
    <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2" aria-labelledby="optionsDropdownBtn-{{ $fair->id }}" style="min-width: 235px; z-index: 1075; right: 0; left: auto; top: 100%; margin-top: 6px;">
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center gap-2" href="{{ route('job-fair.admin.edit', $fair->id) }}">
                <i class="fas fa-edit text-primary"></i>
                <span>تعديل بيانات المعرض</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center gap-2" href="{{ route('job-fair.admin.export', $fair->id) }}">
                <i class="fas fa-file-excel text-success"></i>
                <span>تصدير بيانات (Excel)</span>
            </a>
        </li>
        <li class="dropdown-divider my-1"></li>
        <li class="dropdown-header small text-muted fw-bold pb-1 pt-1"><i class="fas fa-flag me-1"></i>تغيير حالة المعرض:</li>

        @php
            $statusIcons = [
                'draft' => ['color' => 'text-secondary', 'icon' => 'fas fa-file-alt'],
                'published' => ['color' => 'text-success', 'icon' => 'fas fa-check-circle'],
                'ongoing' => ['color' => 'text-warning', 'icon' => 'fas fa-play-circle'],
                'completed' => ['color' => 'text-primary', 'icon' => 'fas fa-flag-checkered']
            ];
        @endphp
        @foreach(['draft'=>'مسودة','published'=>'منشور ومتاح','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
        <li>
            <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="status" value="{{ $val }}">
                <button type="submit" class="dropdown-item rounded-2 py-1.5 px-3 small d-flex align-items-center justify-content-between {{ $fair->status == $val ? 'active fw-bold' : '' }}">
                    <span class="d-flex align-items-center gap-2">
                        <i class="{{ $statusIcons[$val]['icon'] ?? 'fas fa-circle' }} {{ $fair->status == $val ? 'text-white' : ($statusIcons[$val]['color'] ?? '') }}"></i>
                        <span>{{ $label }}</span>
                    </span>
                    @if($fair->status == $val)
                        <i class="fas fa-check text-white ms-2"></i>
                    @endif
                </button>
            </form>
        </li>
        @endforeach

        <li class="dropdown-divider my-1"></li>
        <li>
            <form action="{{ route('job-fair.admin.reset-attendance', $fair->id) }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="dropdown-item rounded-2 py-1.5 px-3 small text-danger d-flex align-items-center gap-2" onclick="return confirm('تحذير: هل أنت متأكد من رغبتك في إعادة تهيئة سجلات الحضور بالكامل؟')" title="إعادة تعيين حضور المعرض">
                    <i class="fas fa-undo"></i>
                    <span>تصفير سجلات الحضور</span>
                </button>
            </form>
        </li>
    </ul>
</div>
