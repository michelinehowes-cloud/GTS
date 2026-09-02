@extends('layouts.app')

@section('title', 'إدارة طلبات التدريب')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم منسق التدريب', 'url' => route('training-coordinator.dashboard')],
            ['label' => 'إدارة طلبات التدريب', 'active' => true],
        ]
    ])

    <!-- رأس الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-clipboard-list me-2"></i>إدارة طلبات التدريب
            </h2>
            <div class="text-muted small mt-1">مراجعة ومعالجة طلبات انضمام الخريجين للبرامج والدورات التدريبية مع إمكانية القبول أو الرفض أو الإلغاء الجماعي</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-outline-primary-modern">
                <i class="fas fa-graduation-cap me-1"></i> البرامج التدريبية
            </a>
        </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'إجمالي الطلبات',
            'value' => $applications->count(),
            'icon' => 'fas fa-clipboard-list',
            'color' => 'primary',
            'description' => 'جميع الطلبات المسجلة'
        ])
        
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'قيد المراجعة',
            'value' => $applications->where('status', 'pending')->count(),
            'icon' => 'fas fa-clock',
            'color' => 'warning',
            'description' => 'بانتظار اتخاذ إجراء'
        ])

        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'تمت الموافقة',
            'value' => $applications->where('status', 'approved')->count(),
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'description' => 'الطلبات المقبولة'
        ])

        @include('components.stat-card', [
            'col' => 'col-xl-3 col-md-6 mb-4',
            'title' => 'مرفوضة',
            'value' => $applications->where('status', 'rejected')->count(),
            'icon' => 'fas fa-times-circle',
            'color' => 'danger',
            'description' => 'الطلبات المرفوضة'
        ])
    </div>

    <!-- جدول الطلبات -->
    <div class="row">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-list me-2"></i>قائمة طلبات التدريب المقدمة
                        </h5>
                    </div>
                    
                    <!-- شريط الإجراءات الجماعية والبحث -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- أزرار الإجراءات على المحدد -->
                        <div id="bulk-actions-toolbar" class="d-none d-flex align-items-center gap-2 bg-light p-1 px-2 rounded-pill border">
                            <span class="small fw-bold text-dark me-1">المحدد (<span id="selected-count" class="text-primary">0</span>):</span>
                            
                            <button type="button" class="btn btn-success-modern btn-sm py-1 px-3" onclick="submitBulk('approve')" title="قبول الطلبات المحددة">
                                <i class="fas fa-check me-1"></i>قبول
                            </button>
                            
                            <button type="button" class="btn btn-danger-modern btn-sm py-1 px-3" onclick="submitBulk('reject')" title="رفض الطلبات المحددة">
                                <i class="fas fa-times me-1"></i>رفض
                            </button>

                            <button type="button" class="btn btn-outline-danger-modern btn-sm py-1 px-3" onclick="submitBulk('delete')" title="إلغاء وحذف الطلبات المحددة">
                                <i class="fas fa-trash me-1"></i>إلغاء وحذف
                            </button>
                        </div>

                        <!-- قبول/رفض الكل المعلق بنقرة واحدة -->
                        @if($applications->where('status', 'pending')->count() > 0)
                        <div class="dropdown">
                            <button class="btn btn-outline-primary-modern btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bolt me-1"></i>إجراءات سريعة للكل
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <button class="dropdown-item text-success" type="button" onclick="actionAllPending('approve')">
                                        <i class="fas fa-check-double me-2"></i>قبول جميع الطلبات المعلقة ({{ $applications->where('status', 'pending')->count() }})
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" type="button" onclick="actionAllPending('reject')">
                                        <i class="fas fa-ban me-2"></i>رفض جميع الطلبات المعلقة ({{ $applications->where('status', 'pending')->count() }})
                                    </button>
                                </li>
                            </ul>
                        </div>
                        @endif
                        
                        <div class="input-group input-group-sm" style="max-width: 240px;">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="app-search" class="form-control bg-light border-0" placeholder="بحث بالخريج أو البرنامج...">
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($applications->count() > 0)
                        <!-- نماذج الإجراءات الجماعية المخفية -->
                        <form id="bulk-action-form" method="POST" class="d-none">
                            @csrf
                        </form>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="applications-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-3 border-0" style="width: 40px;">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" id="select-all" title="تحديد الكل">
                                            </div>
                                        </th>
                                        <th class="py-3 border-0" style="width: 50px;">#</th>
                                        <th class="py-3 border-0">الخريج / المتقدم</th>
                                        <th class="py-3 border-0">البرنامج التدريبي</th>
                                        <th class="py-3 border-0">تاريخ التقديم</th>
                                        <th class="py-3 border-0 text-center">حالة الطلب</th>
                                        <th class="py-3 border-0 text-center">الحضور</th>
                                        <th class="py-3 border-0 text-center" style="min-width: 170px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                    <tr class="app-row" data-name="{{ $application->user->name ?? '' }}" data-training="{{ $application->training->title ?? '' }}" data-status="{{ $application->status }}">
                                        <td class="px-3">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input app-checkbox" type="checkbox" name="application_ids[]" value="{{ $application->id }}" data-status="{{ $application->status }}">
                                            </div>
                                        </td>
                                        <td class="fw-bold text-muted small">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 38px; height: 38px;">
                                                    {{ mb_substr($application->user->name ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('graduate.profile.public', $application->user->id ?? 0) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                        {{ $application->user->name ?? 'غير محدد' }}
                                                    </a>
                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                        {{ $application->user->email ?? 'لا يوجد بريد' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($application->training)
                                                <a href="{{ route('training-coordinator.trainings.show', $application->training->id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                    {{ $application->training->title }}
                                                </a>
                                                <small class="text-muted">
                                                    <span class="badge bg-light text-primary border rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                                        {{ $application->training->type_arabic ?? 'تدريب' }}
                                                    </span>
                                                </small>
                                            @else
                                                <span class="text-muted">برنامج محذوف</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $application->applied_at ? $application->applied_at->format('Y-m-d') : $application->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="text-center">
                                            @if($application->status == 'pending')
                                                <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">
                                                    <i class="fas fa-clock me-1 small"></i>قيد المراجعة
                                                </span>
                                            @elseif($application->status == 'approved')
                                                <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">
                                                    <i class="fas fa-check-circle me-1 small"></i>مقبول
                                                </span>
                                            @else
                                                <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">
                                                    <i class="fas fa-times-circle me-1 small"></i>مرفوض
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($application->status == 'approved' && $application->training)
                                                @php
                                                    $userAttCount = \App\Models\TrainingAttendance::where('training_id', $application->training_id)
                                                        ->where('user_id', $application->user_id)
                                                        ->where('status', 'present')
                                                        ->count();
                                                    $totalDays = $application->training->total_days_count;
                                                @endphp
                                                <a href="{{ route('training-coordinator.trainings.attendance', $application->training_id) }}" class="badge bg-light text-primary border border-primary text-decoration-none rounded-pill px-2 py-1 small">
                                                    <i class="fas fa-clipboard-check me-1"></i>{{ $userAttCount }} / {{ $totalDays }} أيام
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- زر القبول -->
                                                @if($application->status == 'pending')
                                                <form action="{{ route('training-coordinator.applications.approve', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="applications">
                                                    <button type="submit" class="btn btn-outline-success-modern" title="قبول الطلب">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif

                                                <!-- زر الرفض -->
                                                @if($application->status == 'pending')
                                                <form action="{{ route('training-coordinator.applications.reject', $application->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="applications">
                                                    <button type="submit" class="btn btn-outline-danger-modern" title="رفض الطلب">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endif

                                                <!-- زر إعادة التعيين لقيد المراجعة -->
                                                @if($application->status != 'pending')
                                                <form action="{{ route('training-coordinator.applications.pending', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="applications">
                                                    <button type="submit" class="btn btn-outline-warning-modern" title="إعادة للمراجعة">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>
                                                @endif

                                                <!-- زر عرض ملف الخريج -->
                                                @if($application->user)
                                                <a href="{{ route('graduate.profile.public', $application->user->id) }}" target="_blank" class="btn btn-outline-primary-modern ms-1" title="عرض ملف الخريج">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                                @endif

                                                <!-- زر إلغاء وحذف الطلب -->
                                                <form action="{{ route('training-coordinator.applications.destroy', $application->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('هل أنت متأكد من إلغاء وحذف هذا الطلب نهائياً؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger-modern" title="إلغاء وحذف الطلب">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-inbox fa-4x text-muted mb-3 opacity-50"></i>
                                <h4 class="text-muted fw-bold">لا توجد طلبات تدريب حالياً</h4>
                                <p class="text-muted small">ستظهر هنا جميع طلبات الخريجين الراغبين في الانضمام للبرامج التدريبية</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const routes = {
        approve: "{{ route('training-coordinator.applications.bulk-approve') }}",
        reject: "{{ route('training-coordinator.applications.bulk-reject') }}",
        delete: "{{ route('training-coordinator.applications.bulk-delete') }}"
    };

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.app-checkbox');
        const toolbar = document.getElementById('bulk-actions-toolbar');
        const countSpan = document.getElementById('selected-count');

        function updateBulkToolbar() {
            const checkedBoxes = document.querySelectorAll('.app-checkbox:checked');
            const checkedCount = checkedBoxes.length;
            if (countSpan) countSpan.textContent = checkedCount;
            if (toolbar) {
                if (checkedCount > 0) {
                    toolbar.classList.remove('d-none');
                } else {
                    toolbar.classList.add('d-none');
                }
            }
            if (selectAll && checkboxes.length > 0) {
                selectAll.checked = checkedCount === checkboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateBulkToolbar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkToolbar);
        });

        // بحث وتصفية فورية
        document.getElementById('app-search')?.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.app-row').forEach(row => {
                const name = (row.getAttribute('data-name') || '').toLowerCase();
                const training = (row.getAttribute('data-training') || '').toLowerCase();
                row.style.display = (name.includes(q) || training.includes(q)) ? '' : 'none';
            });
        });
    });

    function submitBulk(actionType) {
        const checked = document.querySelectorAll('.app-checkbox:checked');
        if (checked.length === 0) return;
        
        let confirmMsg = '';
        if (actionType === 'approve') confirmMsg = `هل أنت متأكد من قبول ${checked.length} طلب(ات) محددة؟`;
        else if (actionType === 'reject') confirmMsg = `هل أنت متأكد من رفض ${checked.length} طلب(ات) محددة؟`;
        else if (actionType === 'delete') confirmMsg = `هل أنت متأكد من إلغاء وحذف ${checked.length} طلب(ات) محددة نهائياً؟`;

        if (confirm(confirmMsg)) {
            const form = document.getElementById('bulk-action-form');
            form.action = routes[actionType];
            form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());
            
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'application_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            
            form.submit();
        }
    }

    function actionAllPending(actionType) {
        const pendingCheckboxes = document.querySelectorAll('.app-checkbox[data-status="pending"]');
        if (pendingCheckboxes.length === 0) {
            alert('لا توجد طلبات معلقة لاتخاذ إجراء عليها حالياً.');
            return;
        }

        let confirmMsg = actionType === 'approve' 
            ? `هل أنت متأكد من قبول جميع الطلبات المعلقة (${pendingCheckboxes.length} طلب)؟`
            : `هل أنت متأكد من رفض جميع الطلبات المعلقة (${pendingCheckboxes.length} طلب)؟`;

        if (confirm(confirmMsg)) {
            const form = document.getElementById('bulk-action-form');
            form.action = routes[actionType];
            form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());
            
            pendingCheckboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'application_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            
            form.submit();
        }
    }
</script>
@endpush