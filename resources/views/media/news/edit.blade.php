@extends('layouts.app')

@section('title', 'تعديل الخبر الصحفي | وحدة الإعلام')

@push('styles')
<style>
    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0d3882;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eff6ff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .input-group-modern {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .input-group-modern:focus-within {
        box-shadow: 0 0 0 3px rgba(13, 56, 130, 0.12);
    }
    .input-group-modern .input-group-text {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
        font-size: 0.9rem;
    }
    .input-group-modern .form-control,
    .input-group-modern .form-select {
        border-color: #e2e8f0;
        font-size: 0.9rem;
        padding: 0.65rem 0.85rem;
    }
    .input-group-modern .form-control:focus,
    .input-group-modern .form-select:focus {
        border-color: #3b82f6;
        box-shadow: none;
    }
    .radio-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .radio-card:hover {
        border-color: #93c5fd;
        background: #f8fafc;
    }
    .radio-card.active {
        border-color: #0d3882;
        background: #eff6ff;
    }
    .image-preview-box {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .image-preview-box:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="تعديل الخبر الصحفي"
        subtitle="تحديث محتوى وتفاصيل الخبر الصحفي وفترة العرض وصورة الغلاف"
        icon="fas fa-edit"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'إدارة الأخبار', 'url' => route('media.news.index')],
            ['label' => 'معاينة الخبر', 'url' => route('media.news.show', $news)],
            ['label' => 'تعديل']
        ]"
        secondaryBadge="{{ $news->status_data['label'] }}"
        secondaryBadgeIcon="{{ $news->status_data['icon'] }}"
    >
        <a href="{{ route('media.news.show', $news) }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
            <i class="fas fa-eye"></i>
            <span>معاينة الخبر</span>
        </a>
        <a href="{{ route('media.news.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لقائمة الأخبار</span>
        </a>
    </x-page-hero>

    <!-- بطاقة النموذج العصرية المعتمدة -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="background: #ffffff;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.2rem;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-6">تعديل بيانات الخبر الصحفي</h6>
                            <small class="text-muted">آخر تحديث: {{ $news->updated_at ? $news->updated_at->diffForHumans() : 'غير محدد' }}</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-3 py-1.5 {{ $news->status_data['class'] }}" style="font-size: 0.8rem;">
                        <i class="{{ $news->status_data['icon'] }} me-1"></i>{{ $news->status_data['label'] }}
                    </span>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert">
                            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>يرجى مراجعة وتصحيح الأخطاء التالية:</h6>
                            <ul class="mb-0 ps-3 small" style="line-height: 1.8;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('media.news.update', $news) }}" method="POST" enctype="multipart/form-data" id="editNewsForm">
                        @csrf
                        @method('PUT')

                        <!-- 1. المحتوى والتفاصيل الصحفية -->
                        <div class="form-section-title">
                            <i class="fas fa-align-right"></i>
                            <span>1. البيانات الأساسية ومحتوى الخبر</span>
                        </div>

                        <!-- عنوان الخبر -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold text-dark small mb-1">
                                عنوان الخبر الصحفي <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                <span class="input-group-text border-end-0"><i class="fas fa-heading"></i></span>
                                <input type="text" 
                                       class="form-control border-start-0 @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $news->title) }}" 
                                       placeholder="أدخل عنوان الخبر الصحفي كاملاً..." 
                                       required
                                       style="font-size: 1rem; font-weight: 600;">
                            </div>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- نص ومحتوى الخبر -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="content" class="form-label fw-semibold text-dark small mb-0">
                                    نص وتفاصيل الخبر الصحفي <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-light text-muted border rounded-pill px-2.5 py-0.5 small" id="wordCountBadge">
                                    {{ str_word_count(strip_tags($news->content)) }} كلمة
                                </span>
                            </div>
                            <textarea class="form-control rounded-3 @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="12" 
                                      required
                                      style="font-size: 0.95rem; line-height: 1.8; border-color: #e2e8f0;">{{ old('content', $news->content) }}</textarea>
                            @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- 2. صورة الغلاف والتغطية المصورة -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-image"></i>
                            <span>2. صورة الغلاف الصحفية</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small mb-1">صورة الغلاف الرسمية</label>
                            
                            @if($news->thumbnail_url)
                                <div class="p-3 mb-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="rounded-3 shadow-xs" style="width: 90px; height: 60px; object-fit: cover;">
                                        <div>
                                            <span class="fw-bold text-dark small d-block">الصورة الحالية للخبر</span>
                                            <small class="text-muted">يمكنك الإبقاء عليها أو اختيار صورة بديلة بالأسفل</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small px-2.5 py-1">مرفوعة حالياً</span>
                                </div>
                            @endif

                            <div class="image-preview-box" onclick="document.getElementById('thumbnailInput').click()">
                                <input type="file" name="thumbnail" id="thumbnailInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                <div id="uploadPlaceholder" class="{{ $news->thumbnail_url ? '' : '' }}">
                                    <i class="fas fa-cloud-upload-alt text-primary fa-2x mb-2"></i>
                                    <p class="mb-1 text-dark fw-bold small">انقر لاختيار صورة غلاف جديدة أو اسحبها هنا</p>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">الصيغ المدعومة: JPG, PNG, WEBP — الحد الأقصى 5 ميجابايت (يُفضل أبعاد 16:9)</small>
                                </div>
                                <div id="imagePreviewContainer" class="d-none">
                                    <img id="imagePreview" src="#" alt="معاينة الصورة الجديدة" class="img-fluid rounded-3 shadow-xs" style="max-height: 240px; object-fit: cover;">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="event.stopPropagation(); removeImage();">
                                            <i class="fas fa-trash me-1"></i>إلغاء الصورة الجديدة
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('thumbnail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- 3. آلية النشر وفترة العرض (دورة حياة الخبر) -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-clock"></i>
                            <span>3. سياسة النشر وفترة العرض (دورة حياة الخبر)</span>
                        </div>

                        @php
                            $hasExpires = old('expires_at') || ($news->expires_at && !old('_token'));
                        @endphp

                        <div class="row g-3 mb-3">
                            <!-- تاريخ ووقت بدء النشر -->
                            <div class="col-12 col-md-6">
                                <label for="published_at" class="form-label fw-semibold text-dark small mb-1">تاريخ ووقت النشر للجمهور</label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="far fa-calendar-alt"></i></span>
                                    <input type="datetime-local" 
                                           class="form-control border-start-0 @error('published_at') is-invalid @enderror" 
                                           id="published_at" 
                                           name="published_at" 
                                           value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">تاريخ ظهور الخبر في المنظومة</small>
                            </div>

                            <!-- تحديد آلية انتهاء العرض -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-1">آلية وسياسة انتهاء عرض الخبر</label>
                                <div class="d-flex gap-2">
                                    <div class="radio-card flex-fill text-center {{ !$hasExpires ? 'active' : '' }}" id="cardPermanent" onclick="setExpiryMode('permanent')">
                                        <i class="fas fa-infinity text-primary mb-1 d-block"></i>
                                        <span class="fw-bold small d-block">عرض دائم ومستمر</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">يبقى ظاهراً حتى استبداله</small>
                                    </div>
                                    <div class="radio-card flex-fill text-center {{ $hasExpires ? 'active' : '' }}" id="cardTimed" onclick="setExpiryMode('timed')">
                                        <i class="fas fa-calendar-times text-warning mb-1 d-block"></i>
                                        <span class="fw-bold small d-block">تحديد موعد انتهاء</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">أرشفة تلقائية بعد التاريخ</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- حقل تاريخ انتهاء العرض -->
                        <div class="mb-4 {{ $hasExpires ? '' : 'd-none' }}" id="expiryDateContainer">
                            <div class="p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fef3c7;">
                                <label for="expires_at" class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-hourglass-end text-warning me-1"></i>تاريخ ووقت انتهاء العرض التلقائي
                                </label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0 bg-white"><i class="fas fa-calendar-times text-warning"></i></span>
                                    <input type="datetime-local" 
                                           class="form-control border-start-0 bg-white @error('expires_at') is-invalid @enderror" 
                                           id="expires_at" 
                                           name="expires_at" 
                                           value="{{ old('expires_at', $news->expires_at ? $news->expires_at->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                @error('expires_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                <small class="text-muted mt-1.5 d-block" style="font-size: 0.78rem;">
                                    عند حلول هذا التاريخ والوقت، سيختفي الخبر تلقائياً من الصفحة الرئيسية وقائمة الأخبار النشطة ويتحول لحالة الأرشيف المنتهي دون حذفه.
                                </small>
                            </div>
                        </div>

                        <!-- 4. حالة التفعيل والظهور -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-toggle-on"></i>
                            <span>4. حالة النشر والظهور</span>
                        </div>

                        <div class="p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="form-check form-switch d-flex align-items-center gap-3">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $news->is_active) ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                <label class="form-check-label fw-bold text-dark small" for="is_active">
                                    الخبر نشط ومعروض للجمهور
                                    <span class="text-muted fw-normal d-block" style="font-size: 0.78rem;">يمكنك إيقاف تفعيل الخبر في أي وقت ليتحول لمسودة غير ظاهرة.</span>
                                </label>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات السفلية المعتمدة -->
                        <div class="card-footer bg-light py-3 px-0 d-flex align-items-center justify-content-between border-top">
                            <a href="{{ route('media.news.show', $news) }}" class="btn btn-outline-secondary rounded-3 px-4 fw-semibold">
                                <i class="fas fa-times me-1"></i>إلغاء والعودة
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm d-flex align-items-center gap-2" style="background: #0d3882; border-color: #0d3882;">
                                <i class="fas fa-save"></i>
                                <span>حفظ التعديلات وتحديث الخبر</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // عداد الكلمات المباشر
    const contentArea = document.getElementById('content');
    const wordBadge = document.getElementById('wordCountBadge');
    if (contentArea && wordBadge) {
        contentArea.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/).filter(w => w.length > 0).length;
            wordBadge.textContent = words + ' كلمة';
        });
    }

    // تبديل آلية انتهاء العرض
    function setExpiryMode(mode) {
        const cardPerm = document.getElementById('cardPermanent');
        const cardTimed = document.getElementById('cardTimed');
        const container = document.getElementById('expiryDateContainer');
        const expiresInput = document.getElementById('expires_at');

        if (mode === 'permanent') {
            cardPerm.classList.add('active');
            cardTimed.classList.remove('active');
            container.classList.add('d-none');
            expiresInput.value = '';
        } else {
            cardTimed.classList.add('active');
            cardPerm.classList.remove('active');
            container.classList.remove('d-none');
            if (!expiresInput.value) {
                const d = new Date();
                d.setDate(d.getDate() + 14);
                expiresInput.value = d.toISOString().slice(0, 16);
            }
        }
    }

    // معاينة الصورة المرفوعة
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('uploadPlaceholder').classList.add('d-none');
                document.getElementById('imagePreviewContainer').classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const input = document.getElementById('thumbnailInput');
        input.value = '';
        document.getElementById('imagePreview').src = '#';
        document.getElementById('imagePreviewContainer').classList.add('d-none');
        document.getElementById('uploadPlaceholder').classList.remove('d-none');
    }
</script>
@endpush
@endsection
