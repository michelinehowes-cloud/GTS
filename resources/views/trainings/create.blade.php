{{-- ملف: resources/views/trainings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'إضافة تدريب جديد')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>إضافة تدريب جديد</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('trainings.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">عنوان التدريب</label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">الفئة</label>
                                    <input type="text" name="category" id="category" class="form-control" value="{{ old('category') }}" required>
                                    @error('category')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">الوصف</label>
                            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration">المدة (أيام)</label>
                                    <input type="number" name="duration" id="duration" class="form-control" value="{{ old('duration') }}" required>
                                    @error('duration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cost">التكلفة</label>
                                    <input type="number" step="0.01" name="cost" id="cost" class="form-control" value="{{ old('cost') }}" required>
                                    @error('cost')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_participants">الحد الأقصى للمشاركين</label>
                                    <input type="number" name="max_participants" id="max_participants" class="form-control" value="{{ old('max_participants') }}" required>
                                    @error('max_participants')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">تاريخ البداية</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">تاريخ النهاية</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location">الموقع</label>
                            <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" required>
                            @error('location')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="instructor_name">اسم المدرب</label>
                            <input type="text" name="instructor_name" id="instructor_name" class="form-control" value="{{ old('instructor_name') }}" required>
                            @error('instructor_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="instructor_qualifications">مؤهلات المدرب</label>
                            <textarea name="instructor_qualifications" id="instructor_qualifications" class="form-control" rows="2">{{ old('instructor_qualifications') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="requirements">المتطلبات</label>
                            <textarea name="requirements" id="requirements" class="form-control" rows="2">{{ old('requirements') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="objectives">الأهداف</label>
                            <textarea name="objectives" id="objectives" class="form-control" rows="2">{{ old('objectives') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">حفظ</button>
                        <a href="{{ route('trainings.index') }}" class="btn btn-secondary">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection