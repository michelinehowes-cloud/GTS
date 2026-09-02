@extends('layouts.app')

@section('title', 'استيراد بيانات الخريجين')

@php
    $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : 'career-guidance';
@endphp

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($prefix . '.dashboard')],
            ['label' => 'بيانات الخريجين', 'url' => route($prefix . '.graduates')],
            ['label' => 'استيراد من Excel', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-file-import me-2"></i>استيراد بيانات الخريجين
            </h2>
            <div class="text-muted small mt-1">رفع واستيراد قاعدة بيانات الخريجين دفعة واحدة عبر ملف Excel أو CSV</div>
        </div>
        <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> العودة للخريجين
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>يرجى تصحيح الأخطاء التالية:</h6>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('import_errors'))
                <div class="alert alert-warning alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                    <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>أخطاء أثناء الاستيراد:</h6>
                    <ul class="mb-0 mt-2">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-upload me-2"></i>رفع ملف البيانات
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route($prefix . '.import.graduates') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="excel_file" class="form-label-modern">ملف Excel أو CSV <span class="text-danger">*</span></label>
                            <input type="file" class="form-control-modern @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text mt-2 text-muted">
                                يرجى التأكد من أن الملف بصيغة XLSX, XLS, أو CSV ومطابق لترتيب الأعمدة في النموذج المعتمد.
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="fw-bold text-dark d-block"><i class="fas fa-file-excel text-success me-1"></i> نموذج الاستيراد المعتمد</span>
                                <small class="text-muted">قم بتحميل النموذج لملء بيانات الخريجين بالترتيب المطلوب</small>
                            </div>
                            <a href="{{ route($prefix . '.download.template') }}" class="btn btn-outline-info-modern">
                                <i class="fas fa-download me-1"></i> تحميل النموذج
                            </a>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary-modern px-5 py-2">
                                <i class="fas fa-upload me-2"></i> بدء استيراد البيانات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


