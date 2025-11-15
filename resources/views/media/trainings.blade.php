@extends('layouts.app')

@section('title', 'إدارة التدريبات')

@section('page-title', 'إدارة التدريبات')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>إدارة التدريبات
                </h5>
                <div>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filters">
                        <i class="fas fa-filter me-1"></i>تصفية
                    </button>
                </div>
            </div>

            <!-- فلاتر البحث -->
            <div class="collapse" id="filters">
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('media.trainings.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" class="form-control" placeholder="ابحث عن تدريب..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">حالة التغطية</label>
                            <select name="media_coverage_status" class="form-select">
                                <option value="">الكل</option>
                                <option value="pending" {{ request('media_coverage_status') == 'pending' ? 'selected' : '' }}>تحت التغطية</option>
                                <option value="covered" {{ request('media_coverage_status') == 'covered' ? 'selected' : '' }}>تمت التغطية</option>
                                <option value="not_required" {{ request('media_coverage_status') == 'not_required' ? 'selected' : '' }}>لا يتطلب تغطية</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('media.trainings.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح الفلاتر
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if($trainings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>التدريب</th>
                                    <th>التاريخ</th>
                                    <th>الموقع</th>
                                    <th>المنسق</th>
                                    <th>حالة التغطية</th>
                                    <th>عدد الوسائط</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trainings as $training)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $training->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div>{{ $training->start_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">إلى {{ $training->end_date->format('d/m/Y') }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $training->location }}</td>
                                    <td>
                                        @if($training->coordinator)
                                            {{ $training->coordinator->name }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $training->media_coverage_status == 'covered' ? 'success' : ($training->media_coverage_status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ $training->getMediaCoverageStatusText() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $training->media->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('media.trainings.show', $training) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-warning" onclick="updateCoverageStatus({{ $training->id }}, '{{ $training->media_coverage_status }}')" title="تحديث حالة التغطية">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- التنقل بين الصفحات -->
                    <div class="d-flex justify-content-center">
                        {{ $trainings->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">لا توجد تدريبات</h4>
                        <p class="text-muted">لم يتم العثور على أي تدريبات تطابق معايير البحث</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- نافذة تحديث حالة التغطية -->
<div class="modal fade" id="coverageStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث حالة التغطية الإعلامية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="coverageStatusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">حالة التغطية</label>
                        <select name="media_coverage_status" class="form-select" id="coverageStatusSelect" required>
                            <option value="pending">تحت التغطية</option>
                            <option value="covered">تمت التغطية</option>
                            <option value="not_required">لا يتطلب تغطية</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateCoverageStatus(trainingId, currentStatus) {
    document.getElementById('coverageStatusSelect').value = currentStatus;
    document.getElementById('coverageStatusForm').action = `/media/trainings/${trainingId}/coverage-status`;

    new bootstrap.Modal(document.getElementById('coverageStatusModal')).show();
}

// إضافة تأكيد CSRF token
document.addEventListener('DOMContentLoaded', function() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
});
</script>
@endpush
