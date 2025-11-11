@extends('layouts.app')

@section('title', 'تعديل وثيقة شراكة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل وثيقة شراكة</h1>
        <a href="{{ route('partnership.documents') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-1"></i> العودة لوثائق الشراكة
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">نموذج تعديل وثيقة</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('partnership.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="company_id" class="form-label">الشركة الشريكة <span class="text-danger">*</span></label>
                    <select class="form-control" id="company_id" name="company_id" required>
                        <option value="">اختر شركة</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $document->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="document_name" class="form-label">اسم الوثيقة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="document_name" name="document_name" value="{{ old('document_name', $document->document_name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="document_type" class="form-label">نوع الوثيقة <span class="text-danger">*</span></label>
                    <select class="form-control" id="document_type" name="document_type" required>
                        <option value="">اختر نوع الوثيقة</option>
                        <option value="mou" {{ old('document_type', $document->document_type) == 'mou' ? 'selected' : '' }}>مذكرة تفاهم (MOU)</option>
                        <option value="contract" {{ old('document_type', $document->document_type) == 'contract' ? 'selected' : '' }}>عقد</option>
                        <option value="agreement" {{ old('document_type', $document->document_type) == 'agreement' ? 'selected' : '' }}>اتفاقية</option>
                        <option value="amendment" {{ old('document_type', $document->document_type) == 'amendment' ? 'selected' : '' }}>تعديل</option>
                        <option value="renewal" {{ old('document_type', $document->document_type) == 'renewal' ? 'selected' : '' }}>تجديد</option>
                        <option value="termination" {{ old('document_type', $document->document_type) == 'termination' ? 'selected' : '' }}>إنهاء</option>
                        <option value="other" {{ old('document_type', $document->document_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="document_file" class="form-label">ملف الوثيقة (اتركه فارغاً للإبقاء على الملف الحالي)</label>
                    <input type="file" class="form-control" id="document_file" name="document_file">
                    <small class="form-text text-muted">الصيغ المدعومة: PDF, DOC, DOCX, JPG, JPEG, PNG (الحد الأقصى 10MB)</small>
                    @if($document->file_path)
                        <p class="mt-2">الملف الحالي: <a href="{{ Storage::url($document->file_path) }}" target="_blank">{{ $document->file_name }}</a></p>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="document_date" class="form-label">تاريخ الوثيقة</label>
                    <input type="date" class="form-control" id="document_date" name="document_date" value="{{ old('document_date', $document->document_date ? $document->document_date->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-3">
                    <label for="effective_date" class="form-label">تاريخ السريان</label>
                    <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{ old('effective_date', $document->effective_date ? $document->effective_date->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-3">
                    <label for="expiry_date" class="form-label">تاريخ الانتهاء</label>
                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> تحديث الوثيقة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
