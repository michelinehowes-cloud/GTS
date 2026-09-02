@extends('layouts.app')

@section('title', 'إضافة برنامج تدريب')
@section('page-title', 'إضافة برنامج تدريب')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/premium-forms.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'برامج التدريب', 'url' => route('training-coordinator.trainings')],
            ['label' => 'إضافة برنامج جديد', 'active' => true],
        ]
    ])

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fs-4 text-danger"></i>
                <div>
                    <strong>يوجد بعض الأخطاء في النموذج:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-bento-form title="إضافة برنامج تدريبي جديد" subtitle="أدخل تفاصيل البرنامج أو الدورة" icon="fa-chalkboard-teacher">
        <form action="{{ route('training-coordinator.trainings.store') }}" method="POST" class="premium-form">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <input type="text" class="form-control premium-input @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title') }}" placeholder=" " required>
                        <label for="title"><i class="fas fa-heading me-2"></i>اسم البرنامج *</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <select class="form-select premium-input @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="" disabled selected>اختر النوع</option>
                            <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>ورشة عمل</option>
                            <option value="course" {{ old('type') == 'course' ? 'selected' : '' }}>دورة</option>
                            <option value="seminar" {{ old('type') == 'seminar' ? 'selected' : '' }}>ندوة</option>
                            <option value="internship" {{ old('type') == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                        </select>
                        <label for="type"><i class="fas fa-tag me-2"></i>نوع البرنامج *</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <select class="form-select premium-input @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="" disabled selected>اختر الفئة</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                        <label for="category"><i class="fas fa-layer-group me-2"></i>الفئة *</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <select class="form-select premium-input @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                            <option value="" disabled selected>اختر الشركة المنظمة</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <label for="company_id"><i class="fas fa-building me-2"></i>الشركة المنظمة</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <select class="form-select premium-input @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="" disabled selected>اختر الحالة</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        </select>
                        <label for="status"><i class="fas fa-chart-line me-2"></i>الحالة *</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <input type="text" class="form-control premium-input @error('instructor_name') is-invalid @enderror"
                            id="instructor_name" name="instructor_name" value="{{ old('instructor_name') }}"
                            list="trainers-list" autocomplete="off" placeholder=" " required>
                        <label for="instructor_name"><i class="fas fa-chalkboard-teacher me-2"></i>اسم المدرب *</label>
                        <datalist id="trainers-list">
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->name }}" data-id="{{ $trainer->id }}">{{ $trainer->specialization }}</option>
                            @endforeach
                        </datalist>
                        <input type="hidden" id="trainer_id" name="trainer_id" value="{{ old('trainer_id') }}">
                    </div>
                    <small class="text-muted d-block mt-2 px-2"><i class="fas fa-info-circle me-1"></i>اكتب اسم المدرب أو ابدأ بالكتابة للاختيار من القائمة</small>
                </div>

                <div class="col-12">
                    <div class="form-floating-custom">
                        <textarea class="form-control premium-input @error('description') is-invalid @enderror"
                            id="description" name="description" style="height: 120px;" placeholder=" " required>{{ old('description') }}</textarea>
                        <label for="description"><i class="fas fa-align-right me-2"></i>وصف البرنامج *</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating-custom">
                        <input type="text" class="form-control premium-input @error('duration') is-invalid @enderror"
                            id="duration" name="duration" value="{{ old('duration') }}" placeholder=" " required>
                        <label for="duration"><i class="fas fa-clock me-2"></i>المدة (مثال: 3 أشهر) *</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating-custom">
                        <input type="date" class="form-control premium-input @error('start_date') is-invalid @enderror"
                            id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        <label for="start_date"><i class="fas fa-calendar-alt me-2"></i>تاريخ البدء *</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating-custom">
                        <input type="date" class="form-control premium-input @error('end_date') is-invalid @enderror"
                            id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        <label for="end_date"><i class="fas fa-calendar-check me-2"></i>تاريخ الانتهاء *</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <input type="text" class="form-control premium-input @error('location') is-invalid @enderror"
                            id="location" name="location" value="{{ old('location') }}" placeholder=" " required>
                        <label for="location"><i class="fas fa-map-marker-alt me-2"></i>المكان *</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating-custom">
                        <input type="number" class="form-control premium-input @error('seats') is-invalid @enderror"
                            id="seats" name="seats" value="{{ old('seats') }}" min="1" placeholder=" " required>
                        <label for="seats"><i class="fas fa-chair me-2"></i>عدد المقاعد *</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end mt-5 pt-3 border-top border-light">
                <a href="{{ route('training-coordinator.trainings') }}" class="btn-bento-outline px-4">
                    <i class="fas fa-times me-2"></i>إلغاء
                </a>
                <button type="submit" class="btn-bento px-4">
                    <i class="fas fa-save me-2"></i>حفظ البرنامج
                </button>
            </div>
        </form>
    </x-bento-form>
</div>
@endsection

@push('scripts')
<script>
    // بيانات المدربين
    const trainers = @json($trainers->map(function($trainer) {
        return [
            'id' => $trainer->id,
            'name' => $trainer->name,
            'specialization' => $trainer->specialization
        ];
    }));

    const instructorInput = document.getElementById('instructor_name');
    const trainerIdInput = document.getElementById('trainer_id');

    // عند تغيير قيمة حقل اسم المدرب
    instructorInput.addEventListener('input', function() {
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
    instructorInput.addEventListener('change', function() {
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