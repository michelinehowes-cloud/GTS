@extends('layouts.app')

@section('title', 'مشروع سنة 2026 - نموذج المهام وتنظيم الفرق العاملة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with University Branding -->
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 58px; height: 58px;">
                        <i class="fas fa-tasks text-primary fs-3"></i>
                    </div>
                    <div>
                        <div class="badge bg-warning text-dark mb-1 fw-bold">جامعة طرابلس - مكتب تدريب الخريجين</div>
                        <h4 class="mb-0 fw-bold text-white">مشروع سنة 2026 | نموذج تنظيم ومتابعة المهام</h4>
                        <small class="text-white-50">إطار تنظيم ومتابعة مهام الموظفين والفرق العاملة على التجهيز لمعرض التوظيف 2026</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('job-fair.tasks.print', request()->query()) }}" target="_blank" class="btn btn-light shadow-sm text-primary fw-bold">
                        <i class="fas fa-print me-1"></i> طباعة النموذج الرسمي
                    </a>
                    <button type="button" class="btn btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="fas fa-plus-circle me-1"></i> إضافة مهمة جديدة
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="card-body bg-light border-bottom p-4">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-white rounded-3 shadow-sm border-start border-primary border-4">
                        <small class="text-muted d-block mb-1">إجمالي المهام المجدولة</small>
                        <h3 class="mb-0 fw-bold text-dark">{{ $totalTasks }}</h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-white rounded-3 shadow-sm border-start border-success border-4">
                        <small class="text-muted d-block mb-1">المهام المكتملة</small>
                        <h3 class="mb-0 fw-bold text-success">{{ $completedTasks }}</h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-white rounded-3 shadow-sm border-start border-info border-4">
                        <small class="text-muted d-block mb-1">قيد التنفيذ والمتابعة</small>
                        <h3 class="mb-0 fw-bold text-info">{{ $inProgressTasks }}</h3>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-white rounded-3 shadow-sm border-start border-warning border-4">
                        <small class="text-muted d-block mb-1">نسبة الإنجاز العامة</small>
                        <h3 class="mb-0 fw-bold text-warning">{{ $completionRate }}%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters & Task Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" action="{{ route('job-fair.tasks.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="team_name" class="form-select border-2" onchange="this.form.submit()">
                        <option value="">-- جميع الفرق واللجان --</option>
                        @foreach($teams as $t)
                            <option value="{{ $t }}" {{ request('team_name') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="user_id" class="form-select border-2" onchange="this.form.submit()">
                        <option value="">-- تصفية حسب الموظف --</option>
                        @foreach($staffUsers as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select border-2" onchange="this.form.submit()">
                        <option value="">-- جميع الحالات --</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>متأخرة</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار البدء</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('job-fair.tasks.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo me-1"></i> إعادة ضبط
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">الموظف المكلف</th>
                            <th>الفريق / اللجنة</th>
                            <th>المهمة</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ التسليم</th>
                            <th>التقييم</th>
                            <th>الحالة</th>
                            <th>الملاحظات</th>
                            <th class="text-center pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $task->user->name ?? 'غير محدد' }}</div>
                                    <small class="text-muted">{{ $task->user->email ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">
                                        {{ $task->team_name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark text-wrap" style="max-width: 280px;">
                                        {{ $task->task_description }}
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark font-monospace">{{ $task->start_date?->format('Y/m/d') }}</span></td>
                                <td><span class="badge bg-light text-dark font-monospace">{{ $task->due_date?->format('Y/m/d') }}</span></td>
                                <td>
                                    @if($task->evaluation_score)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            {{ $task->evaluation_score }}
                                        </span>
                                    @else
                                        <span class="text-muted small">قيد التقييم</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $task->status_badge }}">{{ $task->status_label }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $task->notes ?? '-' }}</small>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $task->id }}" title="تعديل وتقييم المهمة">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('job-fair.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('job-fair.tasks.update', $task) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-edit me-2"></i> تحديث وتقييم المهمة</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">المهمة</label>
                                                    <textarea name="task_description" class="form-control" rows="3" required>{{ $task->task_description }}</textarea>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label fw-bold">تاريخ البدء</label>
                                                        <input type="date" name="start_date" class="form-control" value="{{ $task->start_date?->format('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label fw-bold">تاريخ التسليم</label>
                                                        <input type="date" name="due_date" class="form-control" value="{{ $task->due_date?->format('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label fw-bold">الحالة</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ والمتابعة</option>
                                                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة بنجاح</option>
                                                            <option value="delayed" {{ $task->status == 'delayed' ? 'selected' : '' }}>متأخرة عن الجدول</option>
                                                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>بانتظار البدء</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label fw-bold">التقييم</label>
                                                        <input type="text" name="evaluation_score" class="form-control" value="{{ $task->evaluation_score }}" placeholder="مثال: 5/5 أو ممتاز أو 95%">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">الملاحظات</label>
                                                    <textarea name="notes" class="form-control" rows="2">{{ $task->notes }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary px-4">حفظ التعديلات</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    لا توجد مهام مسجلة حالياً ضمن هذا النطاق. اضغط على "إضافة مهمة جديدة" لبدء جدولة مهام الفرق.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($tasks->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('job-fair.tasks.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h5 class="modal-title fs-6 fw-bold">
                        <i class="fas fa-calendar-plus me-2"></i> إضافة مهمة إلى نموذج مشروع سنة 2026
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم الموظف المكلف <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select border-2" required>
                                <option value="">-- اختر الموظف من القائمة --</option>
                                @foreach($staffUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">التابع لفريق <span class="text-danger">*</span></label>
                            <select name="team_name" class="form-select border-2" required>
                                @foreach($teams as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">وصف المهمة بدقة <span class="text-danger">*</span></label>
                        <textarea name="task_description" class="form-control border-2" rows="3" placeholder="أدخل تفاصيل ومخرجات المهمة المطلوبة بدقة..." required></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">تاريخ البدء <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control border-2" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">تاريخ التسليم <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control border-2" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">حالة المهمة</label>
                            <select name="status" class="form-select border-2" required>
                                <option value="in_progress" selected>قيد التنفيذ والمتابعة</option>
                                <option value="completed">مكتملة</option>
                                <option value="delayed">متأخرة</option>
                                <option value="pending">بانتظار البدء</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">التقييم (إن وُجد مسبقاً)</label>
                            <input type="text" name="evaluation_score" class="form-control border-2" placeholder="مثال: 5/5 أو ممتاز">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الملاحظات</label>
                            <input type="text" name="notes" class="form-control border-2" placeholder="ملاحظات وتوجيهات خاصة بالمهمة">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> حفظ وتثبيت المهمة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
