@extends('layouts.app')

@section('title', 'إضافة تدريب جديد')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'التدريبات', 'url' => route('admin.trainings')],
                ['label' => 'إضافة تدريب جديد', 'active' => true],
            ]
        ])

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card-modern">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-plus-circle me-2"></i>إضافة تدريب جديد
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('trainings.store') }}" method="POST">
                            @csrf

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label-modern">عنوان التدريب <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-heading text-muted"></i></span>
                                        <input type="text" name="title" id="title" class="form-control-modern border-start-0 ps-0" value="{{ old('title') }}" required>
                                    </div>
                                    @error('title')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="category" class="form-label-modern">الفئة <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-tags text-muted"></i></span>
                                        <input type="text" name="category" id="category" class="form-control-modern border-start-0 ps-0" value="{{ old('category') }}" required>
                                    </div>
                                    @error('category')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label-modern">الوصف <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control-modern" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger fw-bold">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="duration" class="form-label-modern">المدة (أيام) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-clock text-muted"></i></span>
                                        <input type="number" name="duration" id="duration" class="form-control-modern border-start-0 ps-0" value="{{ old('duration') }}" required>
                                    </div>
                                    @error('duration')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="cost" class="form-label-modern">التكلفة <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">د.ل</span>
                                        <input type="number" step="0.01" name="cost" id="cost" class="form-control-modern border-start-0 ps-0" value="{{ old('cost') }}" required>
                                    </div>
                                    @error('cost')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="max_participants" class="form-label-modern">الحد الأقصى للمشاركين <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-users text-muted"></i></span>
                                        <input type="number" name="max_participants" id="max_participants" class="form-control-modern border-start-0 ps-0" value="{{ old('max_participants') }}" required>
                                    </div>
                                    @error('max_participants')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label-modern">تاريخ البداية <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control-modern" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label-modern">تاريخ النهاية <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control-modern" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label-modern">الموقع <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" name="location" id="location" class="form-control-modern border-start-0 ps-0" value="{{ old('location') }}" required>
                                </div>
                                @error('location')
                                    <small class="text-danger fw-bold">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="instructor_name" class="form-label-modern">اسم المدرب <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                        <input type="text" name="instructor_name" id="instructor_name" class="form-control-modern border-start-0 ps-0" value="{{ old('instructor_name') }}" required>
                                    </div>
                                    @error('instructor_name')
                                        <small class="text-danger fw-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="instructor_qualifications" class="form-label-modern">مؤهلات المدرب</label>
                                    <textarea name="instructor_qualifications" id="instructor_qualifications" class="form-control-modern" rows="1">{{ old('instructor_qualifications') }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label-modern">المتطلبات</label>
                                <textarea name="requirements" id="requirements" class="form-control-modern" rows="2">{{ old('requirements') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="objectives" class="form-label-modern">الأهداف</label>
                                <textarea name="objectives" id="objectives" class="form-control-modern" rows="2">{{ old('objectives') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.trainings') }}" class="btn btn-secondary-modern">
                                    <i class="fas fa-times me-1"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary-modern px-4">
                                    <i class="fas fa-save me-1"></i> حفظ التدريب
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection