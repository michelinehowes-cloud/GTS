@extends('layouts.app')

@section('title', 'تعديل بيانات الشركة')

@section('page-title', 'تعديل بيانات الشركة')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الشركات', 'url' => route('admin.companies')],
            ['label' => $company->name, 'active' => true],
        ]
    ])

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-edit me-2"></i>تعديل بيانات الشركة: <span class="text-dark">{{ $company->name }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger-emphasis mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- اسم الشركة -->
                            <div class="col-md-6">
                                <label for="name" class="form-label-modern">اسم الشركة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $company->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control-modern border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $company->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label-modern">رقم الهاتف <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $company->phone) }}" required>
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- المجال الصناعي -->
                            <div class="col-md-6">
                                <label for="industry" class="form-label-modern">المجال الصناعي <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-industry text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('industry') is-invalid @enderror" 
                                           id="industry" name="industry" value="{{ old('industry', $company->industry) }}" required>
                                </div>
                                @error('industry')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- العنوان -->
                            <div class="col-12">
                                <label for="address" class="form-label-modern">العنوان <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $company->address) }}" required>
                                </div>
                                @error('address')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label-modern">وصف الشركة</label>
                                <textarea class="form-control-modern @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description', $company->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.companies') }}" class="btn btn-secondary-modern">
                                <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                            </a>
                            <button type="submit" class="btn btn-primary-modern px-4">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection