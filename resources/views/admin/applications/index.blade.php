@extends('layouts.app')

@section('title', 'إدارة طلبات التدريب')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'إدارة طلبات التدريب', 'active' => true],
        ]
    ])

    <!-- الإحصائيات -->
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

    <div class="row">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-list me-2"></i>قائمة طلبات التدريب
                    </h5>
                    <button type="button" class="btn btn-success-modern btn-sm d-none" id="bulk-approve-btn" onclick="submitBulkApprove()">
                        <i class="fas fa-check-double me-2"></i>قبول المحدد (<span id="selected-count">0</span>)
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($applications->count() > 0)
                        <!-- نموذج مخفي لقبول المحدد -->
                        <form id="bulk-approve-form" action="{{ route('admin.applications.bulk-approve') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        
                           <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 border-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th class="py-3 border-0">#</th>
                                        <th class="py-3 border-0">الخريج</th>
                                        <th class="py-3 border-0">برنامج التدريب</th>
                                        <th class="py-3 border-0">منسق التدريب</th>
                                        <th class="py-3 border-0">تاريخ التقديم</th>
                                        <th class="py-3 border-0">الحالة</th>
                                        <th class="py-3 border-0 text-end">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                    <tr>
                                        <td>
                                            @if($application->status == 'pending')
                                            <div class="form-check">
                                                <input class="form-check-input app-checkbox" type="checkbox" name="application_ids[]" value="{{ $application->id }}">
                                            </div>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    <span class="fw-bold">{{ strtoupper(substr($application->user->name ?? 'U', 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $application->user->name ?? 'غير محدد' }}</div>
                                                    <small class="text-muted">{{ $application->user->email ?? 'لا يوجد بريد' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $application->training->title ?? 'غير محدد' }}</div>
                                            <small class="text-muted">
                                                @if($application->training)
                                                    @switch($application->training->type)
                                                        @case('workshop') <i class="fas fa-hammer me-1"></i>ورشة عمل @break
                                                        @case('course') <i class="fas fa-book me-1"></i>دورة @break
                                                        @case('seminar') <i class="fas fa-users me-1"></i>ندوة @break
                                                        @case('internship') <i class="fas fa-briefcase me-1"></i>تدريب عملي @break
                                                        @default {{ $application->training->type }}
                                                    @endswitch
                                                @else
                                                    نوع غير محدد
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <div class="text-dark">{{ $application->training->coordinator->name ?? 'غير محدد' }}</div>
                                        </td>
                                        <td class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $application->applied_at ? $application->applied_at->format('Y-m-d') : 'غير محدد' }}
                                        </td>
                                        <td>
                                            @if($application->status == 'pending')
                                                <span class="badge bg-light text-warning text-warning px-3 py-2 rounded-pill">
                                                    <i class="fas fa-clock me-1 small"></i>قيد المراجعة
                                                </span>
                                            @elseif($application->status == 'approved')
                                                <span class="badge bg-light text-success text-success px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1 small"></i>مقبول
                                                </span>
                                            @else
                                                <span class="badge bg-light text-danger text-danger px-3 py-2 rounded-pill">
                                                    <i class="fas fa-times-circle me-1 small"></i>مرفوض
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <!-- زر الموافقة -->
                                                @if($application->status == 'pending')
                                                <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success-modern" title="موافقة">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif

                                                <!-- زر الرفض -->
                                                @if($application->status == 'pending')
                                                <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger-modern ms-1" title="رفض">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endif

                                                <!-- زر إعادة التعيين -->
                                                @if($application->status != 'pending')
                                                <form action="{{ route('admin.applications.pending', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning-modern" title="إعادة للمراجعة">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>
                                                @endif

                                                <!-- زر حذف الطلب -->
                                                <form action="{{ route('applications.destroy', $application->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary-modern" 
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')"
                                                            title="حذف الطلب">
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
                                <p class="text-muted">سيظهر هنا جميع طلبات الخريجين لبرامج التدريب</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.app-checkbox');
        const bulkBtn = document.getElementById('bulk-approve-btn');
        const countSpan = document.getElementById('selected-count');

        function updateBulkButton() {
            const checkedCount = document.querySelectorAll('.app-checkbox:checked').length;
            countSpan.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkBtn.classList.remove('d-none');
            } else {
                bulkBtn.classList.add('d-none');
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
                updateBulkButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkButton);
        });
    });

    function submitBulkApprove() {
        const checked = document.querySelectorAll('.app-checkbox:checked');
        if (checked.length === 0) return;
        
        if (confirm('هل أنت متأكد من قبول جميع الطلبات المحددة؟')) {
            const form = document.getElementById('bulk-approve-form');
            // تفريغ أي مدخلات سابقة (في حال تم الإلغاء والمحاولة مرة أخرى)
            form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());
            
            // إضافة المدخلات المحددة
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
</script>
@endsection