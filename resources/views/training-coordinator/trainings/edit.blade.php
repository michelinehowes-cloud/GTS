@extends('layouts.app')

@section('title', 'تعديل برنامج تدريب')
@section('page-title', 'تعديل برنامج تدريب')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>تعديل برنامج التدريب: {{ $training->title }}</h3>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('training-coordinator.trainings.update', $training->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">اسم البرنامج *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                        name="title" value="{{ old('title', $training->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">نوع البرنامج *</label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type"
                                        required>
                                        <option value="">اختر النوع</option>
                                        <option value="workshop" {{ old('type', $training->type) == 'workshop' ? 'selected' : '' }}>ورشة عمل
                                        </option>
                                        <option value="course" {{ old('type', $training->type) == 'course' ? 'selected' : '' }}>دورة</option>
                                        <option value="seminar" {{ old('type', $training->type) == 'seminar' ? 'selected' : '' }}>ندوة</option>
                                        <option value="internship" {{ old('type', $training->type) == 'internship' ? 'selected' : '' }}>تدريب
                                            عملي</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">الفئة *</label>
                                    <select class="form-control @error('category') is-invalid @enderror" id="category"
                                        name="category" required>
                                        <option value="">اختر الفئة</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category', $training->category) == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="company_id" class="form-label">الشركة</label>
                                    <select class="form-control @error('company_id') is-invalid @enderror" id="company_id"
                                        name="company_id">
                                        <option value="">اختر الشركة</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id', $training->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">الحالة *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="">اختر الحالة</option>
                                        <option value="active" {{ old('status', $training->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ old('status', $training->status) == 'inactive' ? 'selected' : '' }}>غير نشط
                                        </option>
                                        <option value="completed" {{ old('status', $training->status) == 'completed' ? 'selected' : '' }}>مكتمل
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="instructor_name" class="form-label">اسم المدرب *</label>
                                    <input type="text" class="form-control @error('instructor_name') is-invalid @enderror"
                                        id="instructor_name" name="instructor_name"
                                        value="{{ old('instructor_name', $training->instructor_name) }}"
                                        list="trainers-list" autocomplete="off" required>
                                    <datalist id="trainers-list">
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->name }}" data-id="{{ $trainer->id }}">
                                                {{ $trainer->specialization }}</option>
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" id="trainer_id" name="trainer_id"
                                        value="{{ old('trainer_id', $training->trainer_id) }}">
                                    @error('instructor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">اكتب اسم المدرب أو ابدأ بالكتابة لاختيار مدرب من
                                        القائمة</small>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">وصف البرنامج *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="3"
                                        required>{{ old('description', $training->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="duration" class="form-label">المدة *</label>
                                    <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                        id="duration" name="duration" value="{{ old('duration', $training->duration) }}"
                                        placeholder="مثال: 3 أشهر" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label">تاريخ البدء *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date"
                                        value="{{ old('start_date', $training->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date"
                                        value="{{ old('end_date', $training->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">المكان *</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                        id="location" name="location" value="{{ old('location', $training->location) }}"
                                        required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="seats" class="form-label">عدد المقاعد *</label>
                                    <input type="number" class="form-control @error('seats') is-invalid @enderror"
                                        id="seats" name="seats" value="{{ old('seats', $training->seats) }}" min="1"
                                        required>
                                    @error('seats')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>حفظ التعديلات
                                    </button>
                                    <a href="{{ route('training-coordinator.trainings.show', $training->id) }}"
                                        class="btn btn-secondary">
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

@push('scripts')
    <script>
        // بيانات المدربين
        const trainers = @json($trainers->map(function ($trainer) {
            return [
                'id' => $trainer->id,
                'name' => $trainer->name,
                'specialization' => $trainer->specialization
            ];
        }));

        const instructorInput = document.getElementById('instructor_name');
        const trainerIdInput = document.getElementById('trainer_id');

        // عند تغيير قيمة حقل اسم المدرب
        instructorInput.addEventListener('input', function () {
            const inputValue = this.value.trim();

            // البحث عن المدرب في القائمة
            const foundTrainer = trainers.find(trainer =>
                trainer.name.toLowerCase() === inputValue.toLowerCase()
            );

            // إذا وجد المدرب، احفظ ID
            if (foundTrainer) {
                trainerIdInput.value = foundTrainer.id;
            } else {
                trainerIdInput.value = '';
            }
        });

        // عند اختيار من datalist
        instructorInput.addEventListener('change', function () {
            const inputValue = this.value.trim();

            const foundTrainer = trainers.find(trainer =>
                trainer.name.toLowerCase() === inputValue.toLowerCase()
            );

            if (foundTrainer) {
                trainerIdInput.value = foundTrainer.id;
            }
        });
    </script>
@endpush