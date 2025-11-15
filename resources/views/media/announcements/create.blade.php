@extends('layouts.app')

@section('title', 'إضافة إعلان جديد')

@section('page-title', 'إضافة إعلان جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة إعلان جديد
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('media.announcements.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">عنوان الإعلان <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">محتوى الإعلان <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">رابط (اختياري)</label>
                        <input type="url" class="form-control @error('link') is-invalid @enderror"
                               id="link" name="link" value="{{ old('link') }}"
                               placeholder="https://example.com">
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">أضف رابطاً اختيارياً للإعلان</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل الإعلان
                            </label>
                        </div>
                        <div class="form-text">حدد لتفعيل عرض الإعلان على الصفحة الرئيسية</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('media.announcements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>العودة
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>حفظ الإعلان
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-label {
    font-weight: 600;
}

.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // التحقق من صحة التواريخ
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate > endDate) {
            endDateInput.setCustomValidity('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
        } else {
            endDateInput.setCustomValidity('');
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
});
</script>
@endpush
