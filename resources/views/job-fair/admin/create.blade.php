@extends('layouts.app')

@section('title', 'إنشاء معرض توظيف جديد')

@section('content')
<div class="container py-4" style="max-width: 800px">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('job-fair.admin.index') }}" class="btn btn-light rounded-circle" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0" style="color: #0A1628">إنشاء معرض توظيف</h2>
            <small class="text-muted">أدخل تفاصيل المعرض الجديد</small>
        </div>
    </div>

    <form action="{{ route('job-fair.admin.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- بيانات أساسية -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent p-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-info-circle me-2" style="color: #3B82F6"></i>
                    المعلومات الأساسية
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">عنوان المعرض *</label>
                        <input type="text" name="title" class="form-control rounded-3 @error('title') is-invalid @enderror"
                               value="{{ old('title', 'معرض التوظيف 2026') }}" required
                               placeholder="مثال: معرض التوظيف السنوي 2026">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">العنوان الفرعي</label>
                        <input type="text" name="subtitle" class="form-control rounded-3"
                               value="{{ old('subtitle') }}"
                               placeholder="مثال: نحو مستقبل مهني أفضل">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">تاريخ المعرض *</label>
                        <input type="date" name="event_date" class="form-control rounded-3 @error('event_date') is-invalid @enderror"
                               value="{{ old('event_date') }}" required>
                        @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">وقت البداية</label>
                        <input type="time" name="start_time" class="form-control rounded-3"
                               value="{{ old('start_time', '09:00') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">وقت النهاية</label>
                        <input type="time" name="end_time" class="form-control rounded-3"
                               value="{{ old('end_time', '16:00') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">مكان الانعقاد *</label>
                        <input type="text" name="location" class="form-control rounded-3 @error('location') is-invalid @enderror"
                               value="{{ old('location') }}" required
                               placeholder="مثال: القاعة الكبرى — كلية الهندسة — جامعة طرابلس">
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">وصف المعرض</label>
                        <textarea name="description" class="form-control rounded-3" rows="4"
                                  placeholder="اكتب تفاصيل المعرض، أهدافه، وما يمكن للخريجين توقعه...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- التسجيل -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent p-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-users me-2" style="color: #10B981"></i>
                    إعدادات التسجيل
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">أقصى عدد خريجين</label>
                        <input type="number" name="max_graduates" class="form-control rounded-3"
                               value="{{ old('max_graduates') }}" placeholder="اتركه فارغاً للتسجيل المفتوح" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">موعد انتهاء التسجيل</label>
                        <input type="datetime-local" name="registration_deadline" class="form-control rounded-3"
                               value="{{ old('registration_deadline') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="registration_open" id="regOpen"
                                   value="1" {{ old('registration_open', '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="regOpen">
                                التسجيل مفتوح للخريجين
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الحالة -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent p-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-toggle-on me-2" style="color: #F59E0B"></i>
                    حالة المعرض
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="status" class="form-select rounded-3">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة (غير مرئي للعموم)</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور (مرئي للجميع)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">صورة البانر</label>
                        <input type="file" name="banner_image" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">ملاحظات داخلية</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2"
                                  placeholder="ملاحظات للإدارة فقط...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- الشركات المشاركة -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent p-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-building me-2" style="color: #6366F1"></i>
                    الشركات المشاركة
                    <small class="text-muted fw-normal">(يمكن الإضافة لاحقاً)</small>
                </h5>
            </div>
            <div class="card-body p-4">
                <div id="companies-container"></div>
                <button type="button" class="btn btn-outline-primary rounded-pill btn-sm" onclick="addCompanyRow()">
                    <i class="fas fa-plus me-2"></i>إضافة شركة
                </button>
            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end">
            <a href="{{ route('job-fair.admin.index') }}" class="btn btn-light rounded-pill px-4">إلغاء</a>
            <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold text-dark">
                <i class="fas fa-save me-2"></i>إنشاء المعرض
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let companyIndex = 0;
const companies = @json($companies->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));

function addCompanyRow() {
    const container = document.getElementById('companies-container');
    const div = document.createElement('div');
    div.className = 'row g-2 mb-3 align-items-center border rounded-3 p-3';
    div.innerHTML = `
        <div class="col-md-4">
            <label class="form-label small fw-semibold">الشركة</label>
            <select name="companies[${companyIndex}][company_id]" class="form-select form-select-sm rounded-3">
                <option value="">-- اختر شركة --</option>
                ${companies.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">رقم الجناح</label>
            <input type="text" name="companies[${companyIndex}][booth_number]" class="form-control form-control-sm rounded-3" placeholder="A1">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">عدد الوظائف المتاحة</label>
            <input type="number" name="companies[${companyIndex}][available_positions]" class="form-control form-control-sm rounded-3" placeholder="5" min="0">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.row').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    companyIndex++;
}
</script>
@endpush
