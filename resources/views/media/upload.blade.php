@extends('layouts.app')

@section('title', 'رفع وسائط جديدة')

@section('page-title', 'رفع وسائط جديدة')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload me-2"></i>رفع وسائط جديدة
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('media.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <!-- اختيار الملفات -->
                    <div class="mb-4">
                        <label class="form-label">اختر الملفات <span class="text-danger">*</span></label>
                        <div class="upload-area border border-2 border-dashed rounded p-4 text-center" id="uploadArea">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">اسحب الملفات هنا أو انقر للاختيار</h5>
                                <p class="text-muted small">يدعم: صور (JPG, PNG) وفيديوهات (MP4, MOV) - حد أقصى 50 ميجابايت للملف الواحد</p>
                                <input type="file" name="files[]" id="files" multiple class="d-none" accept="image/*,video/*">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('files').click()">
                                    <i class="fas fa-folder-open me-1"></i>اختيار الملفات
                                </button>
                            </div>
                        </div>

                        <!-- قائمة الملفات المختارة -->
                        <div id="fileList" class="mt-3" style="display: none;">
                            <h6>الملفات المختارة:</h6>
                            <div id="selectedFiles" class="list-group"></div>
                        </div>
                    </div>

                    <!-- ربط بالتدريب -->
                    <div class="mb-4">
                        <label class="form-label">ربط بالتدريب (اختياري)</label>
                        <select name="training_id" class="form-select">
                            <option value="">غير مرتبط بتدريب محدد</option>
                            @foreach($trainings as $training)
                            <option value="{{ $training->id }}">
                                {{ $training->title }} - {{ $training->start_date->format('d/m/Y') }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">إذا لم تختر تدريباً، سيتم اعتبار الوسائط للاستخدام العام</div>
                    </div>

                    <!-- خيارات العرض -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_welcome_page_media" value="1" class="form-check-input" id="welcomePageMedia">
                            <label class="form-check-label" for="welcomePageMedia">
                                عرض في واجهة الترحيب (Carousel/Gallery)
                            </label>
                        </div>
                        <div class="form-text">حدد هذا الخيار إذا كنت تريد عرض الصور في الصفحة الرئيسية</div>
                    </div>

                    <!-- الوصف -->
                    <div class="mb-4">
                        <label class="form-label">وصف الوسائط (اختياري)</label>
                        <input type="text" name="caption" class="form-control" placeholder="أدخل وصفاً مختصراً للوسائط">
                        <div class="form-text">سيتم تطبيق هذا الوصف على جميع الملفات المرفوعة</div>
                    </div>

                    <!-- أزرار التحكم -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('media.gallery') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>العودة للمعرض
                        </a>
                        <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                            <i class="fas fa-upload me-1"></i>رفع الوسائط
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- إرشادات الاستخدام -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>إرشادات الاستخدام
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>الصور المدعومة:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i>JPG, JPEG</li>
                            <li><i class="fas fa-check text-success me-1"></i>PNG</li>
                            <li><i class="fas fa-check text-success me-1"></i>GIF</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>الفيديوهات المدعومة:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i>MP4</li>
                            <li><i class="fas fa-check text-success me-1"></i>MOV</li>
                            <li><i class="fas fa-check text-success me-1"></i>AVI</li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>نصائح:</strong>
                    <ul class="mb-0 mt-2">
                        <li>استخدم صور عالية الجودة للحصول على أفضل عرض</li>
                        <li>الفيديوهات الكبيرة قد تستغرق وقتاً أطول في الرفع</li>
                        <li>يمكنك رفع عدة ملفات في نفس الوقت</li>
                        <li>الوسائط المرتبطة بواجهة الترحيب ستظهر في المعرض الرئيسي</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.upload-area {
    min-height: 200px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.upload-area:hover {
    background-color: #e9ecef;
    border-color: #0d6efd !important;
}

.upload-area.dragover {
    background-color: #e3f2fd;
    border-color: #2196f3 !important;
}

.file-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    margin-bottom: 5px;
    background-color: #fff;
}

.file-item .file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    margin-left: 10px;
}

.file-item .file-info {
    flex: 1;
}

.file-item .file-size {
    font-size: 0.8rem;
    color: #6c757d;
}

.file-item .remove-file {
    color: #dc3545;
    cursor: pointer;
    padding: 5px;
}

.file-item .remove-file:hover {
    color: #b02a37;
}

.progress {
    height: 6px;
    margin-top: 10px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('files');
    const fileList = document.getElementById('fileList');
    const selectedFiles = document.getElementById('selectedFiles');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadForm = document.getElementById('uploadForm');

    let selectedFileList = [];

    // منع السلوك الافتراضي للـ drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    // تمييز منطقة الرفع عند السحب
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    // معالجة إسقاط الملفات
    uploadArea.addEventListener('drop', handleDrop, false);

    // معالجة اختيار الملفات
    fileInput.addEventListener('change', handleFileSelect, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        uploadArea.classList.add('dragover');
    }

    function unhighlight() {
        uploadArea.classList.remove('dragover');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        [...files].forEach(file => {
            if (validateFile(file)) {
                selectedFileList.push(file);
            }
        });
        updateFileList();
    }

    function validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime', 'video/avi'];
        const maxSize = 50 * 1024 * 1024; // 50MB

        if (!allowedTypes.includes(file.type)) {
            alert(`نوع الملف غير مدعوم: ${file.name}`);
            return false;
        }

        if (file.size > maxSize) {
            alert(`حجم الملف كبير جداً: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
            return false;
        }

        return true;
    }

    function updateFileList() {
        selectedFiles.innerHTML = '';

        if (selectedFileList.length > 0) {
            fileList.style.display = 'block';
            uploadBtn.disabled = false;

            selectedFileList.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';

                const iconClass = file.type.startsWith('image/') ? 'fas fa-image text-primary' : 'fas fa-video text-warning';

                fileItem.innerHTML = `
                    <div class="file-icon bg-light">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="file-info">
                        <div class="fw-bold">${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)}</div>
                    </div>
                    <div class="remove-file" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </div>
                `;

                selectedFiles.appendChild(fileItem);
            });
        } else {
            fileList.style.display = 'none';
            uploadBtn.disabled = true;
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // إضافة الملفات المختارة للنموذج قبل الإرسال
    uploadForm.addEventListener('submit', function(e) {
        // مسح الملفات القديمة من input
        const existingInputs = uploadForm.querySelectorAll('input[name="files[]"]');
        existingInputs.forEach(input => input.remove());

        // إضافة الملفات المختارة
        selectedFileList.forEach(file => {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'files[]';
            fileInput.style.display = 'none';

            // إنشاء FileList جديد
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            uploadForm.appendChild(fileInput);
        });
    });
});

// إزالة ملف من القائمة
function removeFile(index) {
    selectedFileList.splice(index, 1);
    updateFileList();
}
</script>
@endpush
