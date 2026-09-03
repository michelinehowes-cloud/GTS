@extends('layouts.app')

@section('title', 'تعديل المعرض - ' . $fair->title)

@section('content')
<div class="container py-4" style="max-width: 800px">
    <x-bento-form title="تعديل المعرض" subtitle="تعديل تفاصيل المعرض: {{ $fair->title }}" icon="fa-calendar-alt" :backRoute="route('job-fair.admin.show', $fair->id)">
        <form action="{{ route('job-fair.admin.update', $fair->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- المعلومات الأساسية -->
            <div class="form-section mb-4">
                <h5 class="section-title">
                    <i class="fas fa-info-circle me-2" style="color: #3B82F6"></i>
                    المعلومات الأساسية
                </h5>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">عنوان المعرض *</label>
                            <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror"
                                   value="{{ old('title', $fair->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">العنوان الفرعي</label>
                            <input type="text" name="subtitle" class="form-control rounded-3"
                                   value="{{ old('subtitle', $fair->subtitle) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">تاريخ المعرض *</label>
                            <input type="date" name="event_date" class="form-control rounded-3 @error('event_date') is-invalid @enderror"
                                   value="{{ old('event_date', $fair->event_date->format('Y-m-d')) }}" required>
                            @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">وقت البداية</label>
                            <input type="time" name="start_time" class="form-control rounded-3"
                                   value="{{ old('start_time', $fair->start_time ? substr($fair->start_time, 0, 5) : '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">وقت النهاية</label>
                            <input type="time" name="end_time" class="form-control rounded-3"
                                   value="{{ old('end_time', $fair->end_time ? substr($fair->end_time, 0, 5) : '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">مكان الانعقاد *</label>
                            <input type="text" name="location" class="form-control rounded-3 @error('location') is-invalid @enderror"
                                   value="{{ old('location', $fair->location) }}" required>
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">وصف المعرض</label>
                            <textarea name="description" class="form-control rounded-3" rows="4">{{ old('description', $fair->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إعدادات التسجيل -->
            <div class="form-section mb-4">
                <h5 class="section-title">
                    <i class="fas fa-users me-2" style="color: #10B981"></i>
                    إعدادات التسجيل
                </h5>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">أقصى عدد خريجين</label>
                            <input type="number" name="max_graduates" class="form-control rounded-3"
                                   value="{{ old('max_graduates', $fair->max_graduates) }}" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">موعد انتهاء التسجيل</label>
                            <input type="datetime-local" name="registration_deadline" class="form-control rounded-3"
                                   value="{{ old('registration_deadline', $fair->registration_deadline?->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="registration_open" id="regOpen"
                                       value="1" {{ old('registration_open', $fair->registration_open) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="regOpen">
                                    التسجيل مفتوح للخريجين
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الحالة -->
            <div class="form-section mb-4">
                <h5 class="section-title">
                    <i class="fas fa-toggle-on me-2" style="color: #eeca3e"></i>
                    حالة المعرض
                </h5>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select rounded-3">
                                @foreach(['draft'=>'مسودة','published'=>'منشور','ongoing'=>'جارٍ الآن','completed'=>'منتهي','cancelled'=>'ملغي'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $fair->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">صورة البانر</label>
                            <input type="file" name="banner_image" class="form-control rounded-3" accept="image/*">
                            @if($fair->banner_image)
                            <small class="text-muted">الصورة الحالية: {{ basename($fair->banner_image) }}</small>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">ملاحظات داخلية</label>
                            <textarea name="notes" class="form-control rounded-3" rows="2">{{ old('notes', $fair->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end mt-4">
                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-light rounded-pill px-4">إلغاء</a>
                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold text-dark">
                    <i class="fas fa-save me-2"></i>حفظ التعديلات
                </button>
            </div>
        </form>
    </x-bento-form>
</div>
@endsection
