@extends('layouts.app')

@section('title', 'إضافة خبر جديد')

@section('page-title', 'إضافة خبر جديد')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة خبر جديد
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('media.news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- العنوان -->
                    <div class="mb-3">
                        <label class="form-label">عنوان الخبر <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- المحتوى -->
                    <div class="mb-3">
                        <label class="form-label">محتوى الخبر <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="10" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">يمكنك استخدام HTML للتنسيق</div>
                    </div>

                    <!-- الصورة المصغرة -->
                    <div class="mb-3">
                        <label class="form-label">صورة مصغرة (اختياري)</label>
                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">الصورة ستظهر مع الخبر في القائمة والصفحة الرئيسية</div>
                    </div>

                    <!-- تاريخ النشر -->
                    <div class="mb-3">
                        <label class="form-label">تاريخ النشر</label>
                        <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">اتركه فارغاً للنشر الفوري</div>
                    </div>

                    <!-- الحالة -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                نشط (عرض الخبر للزوار)
                            </label>
                        </div>
                    </div>

                    <!-- أزرار التحكم -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('media.news.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>العودة للأخبار
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>حفظ الخبر
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- معاينة المحتوى -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>معاينة المحتوى
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>نصائح لكتابة الأخبار:</strong>
                    <ul class="mb-0 mt-2">
                        <li>استخدم عناوين واضحة وجذابة</li>
                        <li>اكتب مقدمة قصيرة تلخص الخبر في الفقرات الأولى</li>
                        <li>استخدم صور عالية الجودة للصور المصغرة</li>
                        <li>تأكد من دقة المعلومات وصحة الإملاء</li>
                        <li>يمكنك استخدام HTML للتنسيق (عناوين فرعية، قوائم، روابط)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// معاينة HTML في الوقت الفعلي (اختياري)
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.querySelector('textarea[name="content"]');

    if (contentTextarea) {
        // يمكن إضافة معاينة HTML هنا إذا لزم الأمر
        contentTextarea.addEventListener('input', function() {
            // يمكن إضافة منطق معاينة المحتوى
        });
    }
});
</script>
@endpush
