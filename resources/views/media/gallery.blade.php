@extends('layouts.app')

@section('title', 'معرض الوسائط')

@section('page-title', 'معرض الوسائط')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>معرض الوسائط
                </h5>
                <div>
                    <a href="{{ route('media.upload.form') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-upload me-1"></i>رفع وسائط جديدة
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filters">
                        <i class="fas fa-filter me-1"></i>تصفية
                    </button>
                </div>
            </div>

            <!-- فلاتر البحث -->
            <div class="collapse" id="filters">
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('media.gallery') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">نوع الوسيط</label>
                            <select name="file_type" class="form-select">
                                <option value="">الكل</option>
                                <option value="image" {{ request('file_type') == 'image' ? 'selected' : '' }}>صور</option>
                                <option value="video" {{ request('file_type') == 'video' ? 'selected' : '' }}>فيديوهات</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">التدريب</label>
                            <select name="training_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($trainings as $training)
                                <option value="{{ $training->id }}" {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                    {{ $training->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">مكان العرض</label>
                            <select name="is_welcome_page_media" class="form-select">
                                <option value="">الكل</option>
                                <option value="1" {{ request('is_welcome_page_media') == '1' ? 'selected' : '' }}>واجهة الترحيب</option>
                                <option value="0" {{ request('is_welcome_page_media') == '0' ? 'selected' : '' }}>التدريبات</option>
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
                            <a href="{{ route('media.gallery') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح الفلاتر
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if($media->count() > 0)
                    <div class="row">
                        @foreach($media as $item)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card media-card h-100">
                                <div class="position-relative">
                                    @if($item->file_type === 'image')
                                        <img src="{{ Storage::url($item->file_path) }}" class="card-img-top" alt="{{ $item->caption }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <video class="card-img-top" style="height: 200px; object-fit: cover;" controls>
                                            <source src="{{ Storage::url($item->file_path) }}" type="video/mp4">
                                            متصفحك لا يدعم تشغيل الفيديو
                                        </video>
                                    @endif

                                    <!-- أزرار التحكم -->
                                    <div class="media-overlay">
                                        <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="btn btn-light btn-sm me-1" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-warning btn-sm me-1" onclick="editMedia({{ $item->id }})" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteMedia({{ $item->id }}, '{{ $item->caption ?: 'هذا الوسيط' }}')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- شارة الحالة -->
                                    @if(!$item->is_active)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-secondary">غير نشط</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">
                                        @if($item->caption)
                                            {{ Str::limit($item->caption, 30) }}
                                        @else
                                            وسيط بدون وصف
                                        @endif
                                    </h6>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calendar me-1"></i>{{ $item->created_at->format('d/m/Y') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>{{ $item->uploader->name }}
                                        </small>
                                    </div>

                                    @if($item->training)
                                        <div class="mb-2">
                                            <span class="badge bg-info">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ Str::limit($item->training->title, 20) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="mb-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-home me-1"></i>واجهة الترحيب
                                            </span>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-{{ $item->file_type === 'image' ? 'image' : 'video' }} me-1"></i>
                                            {{ ucfirst($item->file_type) }}
                                        </small>
                                        @if($item->is_welcome_page_media)
                                            <small class="text-warning">
                                                <i class="fas fa-star me-1"></i>ترتيب: {{ $item->display_order }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- التنقل بين الصفحات -->
                    <div class="d-flex justify-content-center">
                        {{ $media->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">لا توجد وسائط</h4>
                        <p class="text-muted mb-4">لم يتم رفع أي وسائط بعد أو لا توجد نتائج تطابق معايير البحث</p>
                        <a href="{{ route('media.upload.form') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>رفع أول وسيط
                        </a>
                    </div>
                @endif
            </div>
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
                    <div class="mb-3" id="displayOrderGroup" style="display: none;">
                        <label class="form-label">ترتيب العرض</label>
                        <input type="number" name="display_order" class="form-control" id="editDisplayOrder" min="1">
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

.badge {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
// تحديد المسار الأساسي
const baseUrl = "{{ url('/') }}";

function editMedia(mediaId) {
    // جلب بيانات الوسيط
    fetch(`${baseUrl}/media/media-item/${mediaId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Network response was not ok: ${response.status} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('editCaption').value = data.caption || '';
            document.getElementById('editIsActive').checked = data.is_active;
            document.getElementById('editDisplayOrder').value = data.display_order || '';

            if (data.is_welcome_page_media) {
                document.getElementById('displayOrderGroup').style.display = 'block';
            } else {
                document.getElementById('displayOrderGroup').style.display = 'none';
            }

            document.getElementById('editMediaForm').action = `${baseUrl}/media/media-item/${mediaId}`;
            new bootstrap.Modal(document.getElementById('editMediaModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في جلب بيانات الوسيط: ' + error.message);
        });
}

function deleteMedia(mediaId, mediaName) {
    if (confirm(`هل أنت متأكد من حذف "${mediaName}"؟`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${baseUrl}/media/media-item/${mediaId}`;

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