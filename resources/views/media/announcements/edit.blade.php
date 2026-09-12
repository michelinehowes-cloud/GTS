@extends('layouts.app')

@section('title', 'تعديل الإعلان: ' . $announcement->title . ' | وحدة الإعلام')

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
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="تعديل بيانات الإعلان"
        subtitle="تحديث نص الإعلان، فترة الصلاحية ومواعيد النشر أو حالة التفعيل"
        icon="fas fa-edit"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'إدارة الإعلانات', 'url' => route('media.announcements.index')],
            ['label' => 'معاينة الإعلان', 'url' => route('media.announcements.show', $announcement)],
            ['label' => 'تعديل']
        ]"
        secondaryBadge="{{ $announcement->status_data['label'] }}"
        secondaryBadgeIcon="{{ $announcement->status_data['icon'] }}"
    >
        <a href="{{ route('media.announcements.show', $announcement) }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
            <i class="fas fa-eye"></i>
            <span>معاينة الإعلان</span>
        </a>
        <a href="{{ route('media.announcements.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للإعلانات</span>
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
                            <h6 class="mb-0 fw-bold text-dark fs-6">تعديل بيانات وسريان الإعلان</h6>
                            <small class="text-muted">آخر تحديث: {{ $announcement->updated_at ? $announcement->updated_at->diffForHumans() : 'غير محدد' }}</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-3 py-1.5 {{ $announcement->status_data['class'] }}" style="font-size: 0.8rem;">
                        <i class="{{ $announcement->status_data['icon'] }} me-1"></i>{{ $announcement->status_data['label'] }}
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

                    <form action="{{ route('media.announcements.update', $announcement) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- 1. المحتوى والتفاصيل -->
                        <div class="form-section-title">
                            <i class="fas fa-file-alt"></i>
                            <span>1. بيانات ومحتوى الإعلان</span>
                        </div>

                        <!-- عنوان الإعلان -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold text-dark small mb-1">
                                عنوان الإعلان <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                <span class="input-group-text border-end-0"><i class="fas fa-heading"></i></span>
                                <input type="text" 
                                       class="form-control border-start-0 @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $announcement->title) }}" 
                                       placeholder="أدخل عنوان الإعلان..." 
                                       required
                                       style="font-size: 1rem; font-weight: 600;">
                            </div>
                            @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- نص ومحتوى الإعلان -->
                        <div class="mb-4">
                            <label for="content" class="form-label fw-semibold text-dark small mb-1">
                                نص وتفاصيل الإعلان <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control rounded-3 @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="7" 
                                      required
                                      style="font-size: 0.95rem; line-height: 1.8; border-color: #e2e8f0;">{{ old('content', $announcement->content) }}</textarea>
                            @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- رابط خارجي إضافي -->
                        <div class="mb-4">
                            <label for="link" class="form-label fw-semibold text-dark small mb-1">
                                <i class="fas fa-link text-primary me-1"></i>رابط إضافي / زر الإجراء (اختياري)
                            </label>
                            <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                <span class="input-group-text border-end-0"><i class="fas fa-globe"></i></span>
                                <input type="url" 
                                       class="form-control border-start-0 @error('link') is-invalid @enderror" 
                                       id="link" 
                                       name="link" 
                                       value="{{ old('link', $announcement->link) }}" 
                                       placeholder="https://example.com/register">
                            </div>
                            @error('link') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- 2. فترة وسريان الإعلان -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-calendar-alt"></i>
                            <span>2. فترة وسريان عرض الإعلان</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="start_date" class="form-label fw-semibold text-dark small mb-1">
                                    تاريخ بدء النشر <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="far fa-calendar-check text-success"></i></span>
                                    <input type="date" 
                                           class="form-control border-start-0 @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date', $announcement->start_date ? $announcement->start_date->format('Y-m-d') : '') }}" 
                                           required>
                                </div>
                                @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="end_date" class="form-label fw-semibold text-dark small mb-1">
                                    تاريخ انتهاء النشر (أرشفة تلقائية) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="far fa-calendar-times text-danger"></i></span>
                                    <input type="date" 
                                           class="form-control border-start-0 @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date', $announcement->end_date ? $announcement->end_date->format('Y-m-d') : '') }}" 
                                           required>
                                </div>
                                @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- 3. حالة التفعيل والظهور -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-toggle-on"></i>
                            <span>3. حالة النشر والظهور</span>
                        </div>

                        <div class="p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="form-check form-switch d-flex align-items-center gap-3">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                <label class="form-check-label fw-bold text-dark small" for="is_active">
                                    الإعلان نشط ومعروض للجمهور
                                    <span class="text-muted fw-normal d-block" style="font-size: 0.78rem;">يمكنك إيقاف تفعيل الإعلان في أي وقت ليتحول لمسودة.</span>
                                </label>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات السفلية المعتمدة -->
                        <div class="card-footer bg-light py-3 px-0 d-flex align-items-center justify-content-between border-top">
                            <a href="{{ route('media.announcements.show', $announcement) }}" class="btn btn-outline-secondary rounded-3 px-4 fw-semibold">
                                <i class="fas fa-times me-1"></i>إلغاء والعودة
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm d-flex align-items-center gap-2" style="background: #0d3882; border-color: #0d3882;">
                                <i class="fas fa-save"></i>
                                <span>حفظ التعديلات وتحديث الإعلان</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
