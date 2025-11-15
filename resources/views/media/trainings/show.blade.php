@extends('layouts.app')

@section('title', 'تفاصيل التدريب: ' . $training->title)

@section('page-title', 'تفاصيل التدريب: ' . $training->title)

@section('content')
<div class="row">
    <!-- معلومات التدريب الأساسية -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>معلومات التدريب
                </h5>
                <div>
                    <button class="btn btn-sm btn-warning" onclick="updateCoverageStatus({{ $training->id }}, '{{ $training->media_coverage_status }}')">
                        <i class="fas fa-edit me-1"></i>تحديث حالة التغطية
                    </button>
                    <a href="{{ route('media.trainings.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>العودة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="text-primary mb-3">{{ $training->title }}</h4>
                        <p class="text-muted mb-3">{{ $training->description }}</p>

                        <div class="mb-2">
                            <strong>النوع:</strong> {{ $training->getTypeArabicAttribute() }}
                        </div>
                        <div class="mb-2">
                            <strong>المدة:</strong> {{ $training->duration }}
                        </div>
                        <div class="mb-2">
                            <strong>عدد المقاعد:</strong> {{ $training->seats }}
                        </div>
                        <div class="mb-2">
                            <strong>الحالة:</strong>
                            <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }}">
                                {{ $training->getStatusArabicAttribute() }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>تاريخ البدء:</strong> {{ $training->start_date->format('d/m/Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>تاريخ الانتهاء:</strong> {{ $training->end_date->format('d/m/Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>الموقع:</strong> {{ $training->location }}
                        </div>
                        <div class="mb-2">
                            <strong>المنسق:</strong>
                            @if($training->coordinator)
                                {{ $training->coordinator->name }}
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>حالة التغطية الإعلامية:</strong>
                            <span class="badge bg-{{ $training->media_coverage_status == 'covered' ? 'success' : ($training->media_coverage_status == 'pending' ? 'warning' : 'secondary') }} badge-lg">
                                {{ $training->getMediaCoverageStatusText() }}
                            </span>
                        </div>
                        @if($training->company)
                        <div class="mb-2">
                            <strong>الشركة:</strong> {{ $training->company->name }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الوسائط -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>إحصائيات الوسائط
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stat-circle bg-primary text-white mb-2">
                            <i class="fas fa-images fa-2x"></i>
                        </div>
                        <h4 class="mb-1">{{ $training->media->where('file_type', 'image')->count() }}</h4>
                        <small class="text-muted">صور</small>
                    </div>
                    <div class="col-6">
                        <div class="stat-circle bg-success text-white mb-2">
                            <i class="fas fa-video fa-2x"></i>
                        </div>
                        <h4 class="mb-1">{{ $training->media->where('file_type', 'video')->count() }}</h4>
                        <small class="text-muted">فيديوهات</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h5 class="text-primary">{{ $training->media->count() }}</h5>
                    <small class="text-muted">إجمالي الوسائط</small>
                </div>
                <div class="mt-3">
                    <a href="{{ route('media.upload.form') }}?training_id={{ $training->id }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-upload me-1"></i>رفع وسائط جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- الوسائط المرتبطة بالتدريب -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>الوسائط المرتبطة بالتدريب
                </h5>
                @if($training->media->count() > 0)
                <a href="{{ route('media.reports.coverage', $training) }}" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-file-alt me-1"></i>إنشاء تقرير تغطية
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($training->media->count() > 0)
                    <div class="row">
                        @foreach($training->media as $media)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card media-card h-100">
                                <div class="position-relative">
                                    @if($media->file_type === 'image')
                                        <img src="{{ Storage::url($media->file_path) }}" class="card-img-top" alt="{{ $media->caption }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <video class="card-img-top" style="height: 200px; object-fit: cover;" controls>
                                            <source src="{{ Storage::url($media->file_path) }}" type="video/mp4">
                                            متصفحك لا يدعم تشغيل الفيديو
                                        </video>
                                    @endif

                                    <!-- أزرار التحكم -->
                                    <div class="media-overlay">
                                        <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="btn btn-light btn-sm me-1" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-warning btn-sm me-1" onclick="editMedia({{ $media->id }})" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteMedia({{ $media->id }}, '{{ $media->caption ?: 'هذا الوسيط' }}')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- شارة الحالة -->
                                    @if(!$media->is_active)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-secondary">غير نشط</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">
                                        @if($media->caption)
                                            {{ Str::limit($media->caption, 30) }}
                                        @else
                                            وسيط بدون وصف
                                        @endif
                                    </h6>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calendar me-1"></i>{{ $media->created_at->format('d/m/Y') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>{{ $media->uploader->name }}
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-{{ $media->file_type === 'image' ? 'image' : 'video' }} me-1"></i>
                                            {{ ucfirst($media->file_type) }}
                                        </small>
                                        @if($media->is_welcome_page_media)
                                            <small class="text-warning">
                                                <i class="fas fa-star me-1"></i>واجهة الترحيب
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">لا توجد وسائط مرتبطة بهذا التدريب</h4>
                        <p class="text-muted mb-4">ابدأ برفع الصور والفيديوهات لتوثيق التدريب</p>
                        <a href="{{ route('media.upload.form') }}?training_id={{ $training->id }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>رفع أول وسيط
                        </a>
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

<!-- نافذة تعديل الوسيط -->
<div class="modal fade" id="editMediaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل الوسيط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMediaForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <input type="text" name="caption" class="form-control" id="editCaption">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="editIsActive">
                            <label class="form-check-label" for="editIsActive">نشط</label>
                        </div>
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

@push('styles')
<style>
.media-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.media-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 25px rgba(0,0,0,0.15);
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.media-card:hover .media-overlay {
    opacity: 1;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.media-card:hover .card-img-top {
    transform: scale(1.05);
}

.stat-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
function updateCoverageStatus(trainingId, currentStatus) {
    document.getElementById('coverageStatusSelect').value = currentStatus;
    document.getElementById('coverageStatusForm').action = `/media/trainings/${trainingId}/coverage-status`;

    new bootstrap.Modal(document.getElementById('coverageStatusModal')).show();
}

function editMedia(mediaId) {
    // جلب بيانات الوسيط
    fetch(`/media/media/${mediaId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editCaption').value = data.caption || '';
            document.getElementById('editIsActive').checked = data.is_active;

            document.getElementById('editMediaForm').action = `/media/media/${mediaId}`;
            new bootstrap.Modal(document.getElementById('editMediaModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في جلب بيانات الوسيط');
        });
}

function deleteMedia(mediaId, mediaName) {
    if (confirm(`هل أنت متأكد من حذف "${mediaName}"؟`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/media/media/${mediaId}`;

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfField);

        document.body.appendChild(form);
        form.submit();
    }
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
