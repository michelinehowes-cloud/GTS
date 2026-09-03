@extends('layouts.app')

@section('title', 'إضافة برنامج تدريبي جديد')

@php
    $isAdmin   = auth()->user()->role === 'admin';
    $prefix    = $isAdmin ? 'admin' : 'training-coordinator';
    $dashboard = $isAdmin ? route('admin.dashboard') : route('training-coordinator.dashboard');
@endphp

@push('styles')
<style>
    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0d3882;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eff6ff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .input-group-modern {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .input-group-modern:focus-within {
        box-shadow: 0 0 0 3px rgba(13, 56, 130, 0.12);
    }
    .input-group-modern .input-group-text {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
        font-size: 0.9rem;
    }
    .input-group-modern .form-control,
    .input-group-modern .form-select {
        border-color: #e2e8f0;
        font-size: 0.9rem;
        padding: 0.65rem 0.85rem;
    }
    .input-group-modern .form-control:focus,
    .input-group-modern .form-select:focus {
        border-color: #3b82f6;
        box-shadow: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="إضافة برنامج تدريبي جديد"
        subtitle="إنشاء وتوثيق دورة أو ورشة عمل جديدة وتحديد المواعيد والمقاعد"
        icon="fas fa-plus-circle"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => $isAdmin ? 'لوحة تحكم المدير' : 'لوحة تحكم التدريب', 'url' => $dashboard],
            ['label' => 'إدارة برامج التدريب', 'url' => route($prefix . '.trainings')],
            ['label' => 'إضافة برنامج جديد']
        ]"
        badge="إنشاء برنامج تدريبي"
    >
        <a href="{{ route($prefix . '.trainings') }}" class="btn btn-light bg-white text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لقائمة البرامج</span>
        </a>
    </x-page-hero>

    <!-- بطاقة النموذج العصرية المعتمدة -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 1.15rem;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark fs-6">نموذج بيانات البرنامج التدريبي</h6>
                        <small class="text-muted">أدخل كافة المعلومات المطلوبة لبدء الإعلان وقبول تسجيل المتقدمين</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>يرجى مراجعة وتصحيح الأخطاء التالية:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route($prefix . '.trainings.store') }}" method="POST">
                        @csrf

                        <!-- القسم 1: المعلومات الأساسية -->
                        <div class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            <span>1. المعلومات الأساسية للبرنامج</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-8">
                                <label for="title" class="form-label fw-semibold text-dark small mb-1">اسم البرنامج التدريبي <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-heading"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="أدخل اسم البرنامج التدريبي كاملاً" required>
                                </div>
                                @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="type" class="form-label fw-semibold text-dark small mb-1">نوع البرنامج <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-tag"></i></span>
                                    <select class="form-select border-start-0 @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="" disabled selected>اختر نوع التدريب</option>
                                        <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>ورشة عمل</option>
                                        <option value="course" {{ old('type') == 'course' ? 'selected' : '' }}>دورة تدريبية</option>
                                        <option value="seminar" {{ old('type') == 'seminar' ? 'selected' : '' }}>ندوة علمية</option>
                                        <option value="internship" {{ old('type') == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                                    </select>
                                </div>
                                @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="category" class="form-label fw-semibold text-dark small mb-1">المجال / الفئة التخصصية <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-layer-group"></i></span>
                                    <select class="form-select border-start-0 @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="" disabled selected>اختر الفئة</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="company_id" class="form-label fw-semibold text-dark small mb-1">الجهة / الشركة المنظمة</label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-building"></i></span>
                                    <select class="form-select border-start-0 @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                                        <option value="">جامعة طرابلس (مكتب تدريب الخريجين)</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('company_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- القسم 2: الجدول الزمني والمكان والمقاعد -->
                        <div class="form-section-title">
                            <i class="fas fa-calendar-alt"></i>
                            <span>2. المواعيد والمكان والقدرة الاستيعابية</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <label for="start_date" class="form-label fw-semibold text-dark small mb-1">تاريخ البدء <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                                    <input type="date" class="form-control border-start-0 font-monospace @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                </div>
                                @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="end_date" class="form-label fw-semibold text-dark small mb-1">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-calendar-check text-success"></i></span>
                                    <input type="date" class="form-control border-start-0 font-monospace @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                </div>
                                @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="duration" class="form-label fw-semibold text-dark small mb-1">المدة التقديرية <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-clock text-warning"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" placeholder="مثال: 4 أسابيع (40 ساعة)" required>
                                </div>
                                @error('duration') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="location" class="form-label fw-semibold text-dark small mb-1">مكان التدريب / القاعة <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" placeholder="مثال: قاعة التدريب 1 / مختبر الحاسوب" required>
                                </div>
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="seats" class="form-label fw-semibold text-dark small mb-1">عدد المقاعد المتاحة <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-users text-primary"></i></span>
                                    <input type="number" min="1" max="1000" class="form-control border-start-0 @error('seats') is-invalid @enderror" id="seats" name="seats" value="{{ old('seats', 30) }}" required>
                                </div>
                                @error('seats') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="status" class="form-label fw-semibold text-dark small mb-1">حالة البرنامج <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-chart-line"></i></span>
                                    <select class="form-select border-start-0 @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>🟢 نشط (متاح للتسجيل)</option>
                                        <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>⏸ غير نشط (معلق / مسودة)</option>
                                        <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>✅ مكتمل (انتهت الدورة)</option>
                                    </select>
                                </div>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- القسم 3: المدرب والوصف -->
                        <div class="form-section-title">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>3. المدرب المشرف ووصف البرنامج</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="instructor_name" class="form-label fw-semibold text-dark small mb-1">اسم المدرب المسؤول</label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-user-tie"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('instructor_name') is-invalid @enderror" id="instructor_name" name="instructor_name" value="{{ old('instructor_name') }}" placeholder="اسم المحاضر أو المدرب">
                                </div>
                                @error('instructor_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="trainer_id" class="form-label fw-semibold text-dark small mb-1">تحديد مدرب مسجل بالمنظومة</label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-id-card"></i></span>
                                    <select class="form-select border-start-0 @error('trainer_id') is-invalid @enderror" id="trainer_id" name="trainer_id">
                                        <option value="">بدون ربط مباشر (أو مدرب خارجي)</option>
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('trainer_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-dark small mb-1">وصف ومحاور البرنامج التدريبي <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0 align-items-start pt-2"><i class="fas fa-align-right"></i></span>
                                    <textarea class="form-control border-start-0 @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="أدخل ملخصاً شاملاً عن البرنامج ومحاوره التدريبية وأهدافه..." required>{{ old('description') }}</textarea>
                                </div>
                                @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- أزرار الإجراءات السفلية المعتمدة -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                            <a href="{{ route($prefix . '.trainings') }}" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-semibold">
                                <i class="fas fa-times me-1"></i> إلغاء والعودة
                            </a>
                            <button type="submit" class="btn btn-primary-modern px-5 py-2.5 rounded-3 fw-bold shadow-sm d-flex align-items-center gap-2">
                                <i class="fas fa-plus-circle"></i>
                                <span>حفظ وإنشاء البرنامج</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection