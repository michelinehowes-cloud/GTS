@extends('layouts.training-coordinator')

@section('title', 'لوحة تحكم منسق التدريب')
@section('page-title', 'لوحة تحكم منسق التدريب')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>تعديل برنامج التدريب: {{ $training->title }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('training-coordinator.trainings.update', $training->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">اسم البرنامج *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="{{ old('title', $training->title) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">نوع البرنامج *</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="workshop" {{ $training->type == 'workshop' ? 'selected' : '' }}>ورشة عمل</option>
                                    <option value="course" {{ $training->type == 'course' ? 'selected' : '' }}>دورة</option>
                                    <option value="seminar" {{ $training->type == 'seminar' ? 'selected' : '' }}>ندوة</option>
                                    <option value="internship" {{ $training->type == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">وصف البرنامج *</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $training->description) }}</textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="duration" class="form-label">المدة *</label>
                                <input type="text" class="form-control" id="duration" name="duration" 
                                       value="{{ old('duration', $training->duration) }}" placeholder="مثال: 3 أشهر" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">تاريخ البدء *</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ old('start_date', $training->start_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ old('end_date', $training->end_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">المكان *</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="{{ old('location', $training->location) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="seats" class="form-label">عدد المقاعد *</label>
                                <input type="number" class="form-control" id="seats" name="seats" 
                                       value="{{ old('seats', $training->seats) }}" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">الحالة *</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active" {{ $training->status == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ $training->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    <option value="completed" {{ $training->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>حفظ التعديلات
                                </button>
                                <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>إلغاء
                                </a>
                                <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-info">
                                    <i class="fas fa-list me-2"></i>عرض جميع البرامج
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection