@extends('layouts.app')

@section('title', 'تعديل وثيقة شراكة')

@section('content')
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="تعديل وثيقة شراكة"
        subtitle="تحديث بيانات الوثيقة، مدة السريان، أو استبدال الملف الرقمي المرفق"
        icon="fas fa-edit"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الشراكات والتوظيف', 'url' => route('partnership.dashboard')],
            ['label' => 'وثائق الشراكة', 'url' => route('partnership.documents')],
            ['label' => 'تعديل وثيقة']
        ]"
        badge="تحديث الاتفاقيات"
    >
        <a href="{{ route('partnership.documents') }}" class="btn btn-light bg-white text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لقائمة الوثائق</span>
        </a>
    </x-page-hero>

    <!-- بطاقة النموذج الحديثة -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark fs-6">تحديث بيانات: {{ $document->document_name }}</h6>
                        <small class="text-muted">يمكنك تعديل أي من تفاصيل الوثيقة أو استبدال الملف الحالي</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>يرجى تصحيح الأخطاء التالية:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('partnership.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 g-md-4">
                            <!-- الشركة الشريكة -->
                            <div class="col-md-6">
                                <label for="company_id" class="form-label fw-bold text-dark">
                                    <i class="fas fa-building text-primary me-1"></i>الشركة الشريكة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select py-2 rounded-3 border-secondary-subtle" id="company_id" name="company_id" required>
                                    <option value="">-- اختر الشركة --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $document->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} {{ $company->industry ? "({$company->industry})" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- نوع الوثيقة -->
                            <div class="col-md-6">
                                <label for="document_type" class="form-label fw-bold text-dark">
                                    <i class="fas fa-tag text-primary me-1"></i>نوع الوثيقة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select py-2 rounded-3 border-secondary-subtle" id="document_type" name="document_type" required>
                                    <option value="">-- اختر نوع الوثيقة --</option>
                                    <option value="mou" {{ old('document_type', $document->document_type) == 'mou' ? 'selected' : '' }}>مذكرة تفاهم (MOU)</option>
                                    <option value="contract" {{ old('document_type', $document->document_type) == 'contract' ? 'selected' : '' }}>عقد شراكة وتوظيف</option>
                                    <option value="agreement" {{ old('document_type', $document->document_type) == 'agreement' ? 'selected' : '' }}>اتفاقية تعاون مشترك</option>
                                    <option value="amendment" {{ old('document_type', $document->document_type) == 'amendment' ? 'selected' : '' }}>ملحق تعديل اتفاقية</option>
                                    <option value="renewal" {{ old('document_type', $document->document_type) == 'renewal' ? 'selected' : '' }}>تجديد عقد أو شراكة</option>
                                    <option value="termination" {{ old('document_type', $document->document_type) == 'termination' ? 'selected' : '' }}>إنهاء شراكة</option>
                                    <option value="other" {{ old('document_type', $document->document_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                            </div>

                            <!-- اسم الوثيقة -->
                            <div class="col-12">
                                <label for="document_name" class="form-label fw-bold text-dark">
                                    <i class="fas fa-heading text-primary me-1"></i>اسم / عنوان الوثيقة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control py-2 rounded-3 border-secondary-subtle" id="document_name" name="document_name" value="{{ old('document_name', $document->document_name) }}" required>
                            </div>

                            <!-- ملف الوثيقة -->
                            <div class="col-12">
                                <label for="document_file" class="form-label fw-bold text-dark">
                                    <i class="fas fa-file-pdf text-danger me-1"></i>ملف الوثيقة
                                </label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <input type="file" class="form-control rounded-3" id="document_file" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                        <div class="text-muted small">
                                            <i class="fas fa-info-circle text-primary me-1"></i>
                                            <span>اتركه فارغاً للإبقاء على الملف الحالي، أو اختر ملفاً جديداً لاستبداله</span>
                                        </div>
                                        @if($document->file_path)
                                            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2 d-inline-flex align-items-center gap-1">
                                                <i class="fas fa-external-link-alt"></i>
                                                <span>معاينة الملف الحالي ({{ $document->file_name ?? 'تحميل' }})</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- تاريخ الوثيقة -->
                            <div class="col-md-4">
                                <label for="document_date" class="form-label fw-bold text-dark">
                                    <i class="fas fa-calendar-day text-primary me-1"></i>تاريخ توقيع الوثيقة
                                </label>
                                <input type="date" class="form-control py-2 rounded-3 border-secondary-subtle font-monospace" id="document_date" name="document_date" value="{{ old('document_date', $document->document_date ? $document->document_date->format('Y-m-d') : '') }}">
                            </div>

                            <!-- تاريخ السريان -->
                            <div class="col-md-4">
                                <label for="effective_date" class="form-label fw-bold text-dark">
                                    <i class="fas fa-play-circle text-success me-1"></i>تاريخ سريان الاتفاقية
                                </label>
                                <input type="date" class="form-control py-2 rounded-3 border-secondary-subtle font-monospace" id="effective_date" name="effective_date" value="{{ old('effective_date', $document->effective_date ? $document->effective_date->format('Y-m-d') : '') }}">
                            </div>

                            <!-- تاريخ الانتهاء -->
                            <div class="col-md-4">
                                <label for="expiry_date" class="form-label fw-bold text-dark">
                                    <i class="fas fa-flag-checkered text-danger me-1"></i>تاريخ انتهاء الصلاحية
                                </label>
                                <input type="date" class="form-control py-2 rounded-3 border-secondary-subtle font-monospace" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '') }}">
                            </div>

                            <!-- الوصف والملاحظات -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-bold text-dark">
                                    <i class="fas fa-align-right text-primary me-1"></i>ملخص أو بنود الاتفاقية
                                </label>
                                <textarea class="form-control rounded-3 border-secondary-subtle" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <!-- أزرار الإجراءات -->
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <a href="{{ route('partnership.documents') }}" class="btn btn-light border px-4 py-2 rounded-3 text-muted">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary-modern px-5 py-2 rounded-3 fw-bold d-flex align-items-center gap-1.5 shadow-sm">
                                <i class="fas fa-save"></i>
                                <span>حفظ التعديلات</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
