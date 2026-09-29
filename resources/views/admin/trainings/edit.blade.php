@extends('layouts.app')

@section('title', 'تعديل برنامج التدريب - ' . $training->title)

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
    .day-select-card {
        cursor: pointer;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }
    .day-select-card:hover {
        border-color: #93c5fd;
        background: #f8fafc;
        transform: translateY(-2px);
    }
    .day-select-card.active {
        border-color: #0d3882;
        background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
        box-shadow: 0 4px 12px rgba(13, 56, 130, 0.08);
    }
    .day-select-card.active .day-name {
        color: #0d3882 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="تعديل برنامج التدريب"
        :subtitle="'تحديث تفاصيل وجدول برنامج: ' . $training->title"
        icon="fas fa-edit"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => $isAdmin ? 'لوحة تحكم المدير' : 'لوحة تحكم التدريب', 'url' => $dashboard],
            ['label' => 'إدارة برامج التدريب', 'url' => route($prefix . '.trainings')],
            ['label' => 'تعديل التدريب']
        ]"
        badge="نموذج تعديل معتمد"
    >
        <a href="{{ route($prefix . '.trainings.show', $training->id) }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-eye"></i>
            <span>معاينة تفاصيل التدريب</span>
        </a>
        <a href="{{ route($prefix . '.trainings') }}" class="btn btn-light bg-white text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </x-page-hero>

    <!-- بطاقة النموذج العصرية المعتمدة -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 1.15rem;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark fs-6">تعديل بيانات برنامج التدريب والتأهيل</h6>
                        <small class="text-muted">يرجى تعديل الحقول والتأكد من توافق المواعيد وعدد المقاعد</small>
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

                    <form action="{{ route($prefix . '.trainings.update', $training->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                    <input type="text" class="form-control border-start-0 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $training->title) }}" placeholder="أدخل اسم البرنامج التدريبي كاملاً" required>
                                </div>
                                @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="type" class="form-label fw-semibold text-dark small mb-1">نوع البرنامج <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-tag"></i></span>
                                    <select class="form-select border-start-0 @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="workshop" {{ old('type', $training->type) == 'workshop' ? 'selected' : '' }}>ورشة عمل</option>
                                        <option value="course" {{ old('type', $training->type) == 'course' ? 'selected' : '' }}>دورة تدريبية</option>
                                        <option value="seminar" {{ old('type', $training->type) == 'seminar' ? 'selected' : '' }}>ندوة علمية</option>
                                        <option value="internship" {{ old('type', $training->type) == 'internship' ? 'selected' : '' }}>تدريب عملي</option>
                                    </select>
                                </div>
                                @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="category" class="form-label fw-semibold text-dark small mb-1">المجال / الفئة التخصصية <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-layer-group text-primary"></i></span>
                                    <select class="form-select border-start-0 @error('category') is-invalid @enderror" id="category" name="category" required>
                                        @if($training->category && !in_array($training->category, $categories))
                                            <option value="{{ $training->category }}" selected>{{ $training->category }}</option>
                                        @endif
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category', $training->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
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
                                            <option value="{{ $company->id }}" {{ old('company_id', $training->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
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
                                    <input type="date" class="form-control border-start-0 font-monospace @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $training->start_date ? $training->start_date->format('Y-m-d') : '') }}" required>
                                </div>
                                @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="end_date" class="form-label fw-semibold text-dark small mb-1">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-calendar-check text-success"></i></span>
                                    <input type="date" class="form-control border-start-0 font-monospace @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $training->end_date ? $training->end_date->format('Y-m-d') : '') }}" required>
                                </div>
                                @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="duration" class="form-label fw-semibold text-dark small mb-1">المدة التقديرية <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-clock text-warning"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $training->duration) }}" placeholder="مثال: 4 أسابيع (40 ساعة)" required>
                                </div>
                                @error('duration') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="location" class="form-label fw-semibold text-dark small mb-1">مكان التدريب / القاعة <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $training->location) }}" placeholder="مثال: قاعة التدريب 1 / مختبر الحاسوب" required>
                                </div>
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="seats" class="form-label fw-semibold text-dark small mb-1">عدد المقاعد المتاحة <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-users text-primary"></i></span>
                                    <input type="number" min="1" max="1000" class="form-control border-start-0 @error('seats') is-invalid @enderror" id="seats" name="seats" value="{{ old('seats', $training->seats) }}" required>
                                </div>
                                @error('seats') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="status" class="form-label fw-semibold text-dark small mb-1">حالة البرنامج <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0"><i class="fas fa-chart-line"></i></span>
                                    <select class="form-select border-start-0 @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $training->status) == 'active' ? 'selected' : '' }}>🟢 نشط (متاح للتسجيل)</option>
                                        <option value="inactive" {{ old('status', $training->status) == 'inactive' ? 'selected' : '' }}>⏸ غير نشط (معلق / مسودة)</option>
                                        <option value="completed" {{ old('status', $training->status) == 'completed' ? 'selected' : '' }}>✅ مكتمل (انتهت الدورة)</option>
                                    </select>
                                </div>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <!-- اختيار أيام التدريب الأسبوعية المعتمدة وتحديد / استثناء الجمعة والسبت -->
                            <div class="col-12">
                                <div class="p-3.5 rounded-4 bg-light bg-opacity-50 border border-secondary-subtle">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                        <div>
                                            <label class="form-label fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <i class="fas fa-calendar-week text-primary"></i>
                                                <span>أيام التدريب الأسبوعية المعتمدة <span class="text-danger">*</span></span>
                                            </label>
                                            <div class="text-muted small mt-0.5">حدد أيام الأسبوع التي تعقد فيها جلسات التدريب (يمكنك استثناء الجمعة والسبت أو اختيار أيام معينة)</div>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap gap-1.5">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" onclick="applySchedulePreset('workdays')">
                                                <i class="fas fa-briefcase me-1"></i> أيام العمل (الأحد - الخميس)
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" onclick="applySchedulePreset('all')">
                                                <i class="fas fa-calendar-alt me-1"></i> كامل الأسبوع
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applySchedulePreset('odd')" title="الأحد، الثلاثاء، الخميس">
                                                أيام فردية
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applySchedulePreset('even')" title="السبت، الإثنين، الأربعاء">
                                                أيام زوجية
                                            </button>
                                        </div>
                                    </div>

                                    <!-- بطاقات أيام الأسبوع السبعة -->
                                    @php
                                        $weekDays = [
                                            ['val' => 0, 'name' => 'الأحد', 'short' => 'Sun', 'is_weekend' => false],
                                            ['val' => 1, 'name' => 'الإثنين', 'short' => 'Mon', 'is_weekend' => false],
                                            ['val' => 2, 'name' => 'الثلاثاء', 'short' => 'Tue', 'is_weekend' => false],
                                            ['val' => 3, 'name' => 'الأربعاء', 'short' => 'Wed', 'is_weekend' => false],
                                            ['val' => 4, 'name' => 'الخميس', 'short' => 'Thu', 'is_weekend' => false],
                                            ['val' => 5, 'name' => 'الجمعة', 'short' => 'Fri', 'is_weekend' => true],
                                            ['val' => 6, 'name' => 'السبت', 'short' => 'Sat', 'is_weekend' => true],
                                        ];
                                        // إذا كانت مسجلة في قاعدة البيانات نستخدمها، وإلا افتراضياً استثناء الجمعة والسبت
                                        $selectedDays = old('training_days_of_week', $training->training_days_of_week ?? [0, 1, 2, 3, 4]);
                                    @endphp

                                    <div class="row g-2 mb-3" id="trainingDaysContainer">
                                        @foreach($weekDays as $day)
                                            @php
                                                $isChecked = in_array($day['val'], (array)$selectedDays);
                                            @endphp
                                            <div class="col-6 col-sm-4 col-md-3 col-lg">
                                                <label class="day-select-card d-flex flex-column align-items-center justify-content-center p-2.5 rounded-3 text-center transition-all {{ $isChecked ? 'active' : '' }}" for="day_{{ $day['val'] }}">
                                                    <input type="checkbox" name="training_days_of_week[]" value="{{ $day['val'] }}" id="day_{{ $day['val'] }}" class="day-checkbox d-none" {{ $isChecked ? 'checked' : '' }} onchange="onDayToggle(this)">
                                                    <div class="d-flex align-items-center justify-content-between w-100 mb-1 px-1">
                                                        <span class="day-icon-check rounded-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                                            <i class="fas {{ $isChecked ? 'fa-check-circle text-primary' : 'fa-circle text-muted opacity-50' }}"></i>
                                                        </span>
                                                        @if($day['is_weekend'])
                                                            <span class="badge bg-warning bg-opacity-25 text-dark" style="font-size: 0.65rem;">عطلة</span>
                                                        @else
                                                            <span class="badge bg-light text-muted" style="font-size: 0.65rem;">عمل</span>
                                                        @endif
                                                    </div>
                                                    <div class="fw-bold day-name fs-6">{{ $day['name'] }}</div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $day['is_weekend'] ? 'نهاية أسبوع' : 'يوم تدريبي' }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('training_days_of_week') <div class="text-danger small mt-1 mb-2">{{ $message }}</div> @enderror

                                    <!-- عداد الأيام التفاعلي الحقيقي وملخص الجدولة -->
                                    <div id="scheduleSummaryBox" class="p-2.5 rounded-3 bg-white border d-flex align-items-center justify-content-between flex-wrap gap-2 text-dark small">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill" id="totalDaysBadge">
                                                <i class="fas fa-calendar-check me-1"></i> <span id="calcTotalDays">0</span> يوم تدريبي فعلي
                                            </span>
                                            <span class="text-muted" id="scheduleNote">جاري الحساب...</span>
                                        </div>
                                        <div class="text-muted" id="excludedDaysNote">
                                            <i class="fas fa-info-circle text-info me-1"></i>
                                            <span id="excludedDaysText">يتم استثناء أيام العطلة غير المحددة تلقائياً من الحضور والمصفوفة</span>
                                        </div>
                                    </div>
                                </div>
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
                                    <input type="text" class="form-control border-start-0 @error('instructor_name') is-invalid @enderror" id="instructor_name" name="instructor_name" value="{{ old('instructor_name', $training->instructor_name) }}" placeholder="اسم المحاضر أو المدرب">
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
                                            <option value="{{ $trainer->id }}" {{ old('trainer_id', $training->trainer_id) == $trainer->id ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('trainer_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-dark small mb-1">وصف ومحاور البرنامج التدريبي <span class="text-danger">*</span></label>
                                <div class="input-group input-group-modern rounded-3 overflow-hidden">
                                    <span class="input-group-text border-end-0 align-items-start pt-2"><i class="fas fa-align-right"></i></span>
                                    <textarea class="form-control border-start-0 @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="أدخل ملخصاً شاملاً عن البرنامج ومحاوره التدريبية وأهدافه..." required>{{ old('description', $training->description) }}</textarea>
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

@push('scripts')
<script>
    function applySchedulePreset(preset) {
        const checkboxes = document.querySelectorAll('.day-checkbox');
        checkboxes.forEach(cb => {
            const val = parseInt(cb.value);
            let shouldCheck = false;
            if (preset === 'workdays') {
                // Sunday(0) to Thursday(4)
                shouldCheck = (val >= 0 && val <= 4);
            } else if (preset === 'all') {
                shouldCheck = true;
            } else if (preset === 'odd') {
                shouldCheck = [0, 2, 4].includes(val);
            } else if (preset === 'even') {
                shouldCheck = [1, 3, 6].includes(val);
            }
            cb.checked = shouldCheck;
            updateDayCardUI(cb);
        });
        calculateTrainingDays();
    }

    function onDayToggle(checkbox) {
        updateDayCardUI(checkbox);
        calculateTrainingDays();
    }

    function updateDayCardUI(checkbox) {
        const card = checkbox.closest('.day-select-card');
        if (!card) return;
        const icon = card.querySelector('.day-icon-check i');
        if (checkbox.checked) {
            card.classList.add('active');
            if (icon) {
                icon.className = 'fas fa-check-circle text-primary';
            }
        } else {
            card.classList.remove('active');
            if (icon) {
                icon.className = 'fas fa-circle text-muted opacity-50';
            }
        }
    }

    function calculateTrainingDays() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const totalDaysBadge = document.getElementById('calcTotalDays');
        const scheduleNote = document.getElementById('scheduleNote');
        const excludedDaysText = document.getElementById('excludedDaysText');

        if (!startDateInput || !endDateInput) return;

        const startVal = startDateInput.value;
        const endVal = endDateInput.value;

        // الأيام المحددة
        const checkedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => parseInt(cb.value));

        // نص الأيام المستثناة
        const dayNames = {0: 'الأحد', 1: 'الإثنين', 2: 'الثلاثاء', 3: 'الأربعاء', 4: 'الخميس', 5: 'الجمعة', 6: 'السبت'};
        const excluded = [0, 1, 2, 3, 4, 5, 6].filter(d => !checkedDays.includes(d)).map(d => dayNames[d]);

        if (excludedDaysText) {
            if (excluded.length > 0) {
                excludedDaysText.innerHTML = 'الأيام المستثناة أسبوعياً: <span class="fw-bold text-danger">' + excluded.join('، ') + '</span>';
            } else {
                excludedDaysText.innerHTML = '<span class="fw-bold text-success">شامل لكامل أيام الأسبوع بدون استثناء</span>';
            }
        }

        if (!startVal || !endVal) {
            if (totalDaysBadge) totalDaysBadge.innerText = '0';
            if (scheduleNote) scheduleNote.innerText = 'اختر تواريخ البدء والانتهاء لحساب الأيام بدقة';
            return;
        }

        const start = new Date(startVal + 'T00:00:00');
        const end = new Date(endVal + 'T00:00:00');

        if (end < start) {
            if (totalDaysBadge) totalDaysBadge.innerText = '0';
            if (scheduleNote) scheduleNote.innerHTML = '<span class="text-danger fw-bold">تنبيه: تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء</span>';
            return;
        }

        if (checkedDays.length === 0) {
            if (totalDaysBadge) totalDaysBadge.innerText = '0';
            if (scheduleNote) scheduleNote.innerHTML = '<span class="text-danger fw-bold">تنبيه: يرجى اختيار يوم واحد على الأقل</span>';
            return;
        }

        // حساب عدد الأيام الفعلية الواقعة ضمن الأيام المحددة
        let count = 0;
        let cur = new Date(start);
        while (cur <= end) {
            const dayOfWeek = cur.getDay(); // 0 is Sunday, 6 is Saturday
            if (checkedDays.includes(dayOfWeek)) {
                count++;
            }
            cur.setDate(cur.getDate() + 1);
        }

        if (totalDaysBadge) totalDaysBadge.innerText = count;

        const weeks = Math.max(1, Math.ceil(count / Math.max(checkedDays.length, 1)));
        if (scheduleNote) {
            scheduleNote.innerHTML = `الفترة تتضمن <strong>${count} يوم تدريبي فعلي</strong> (حوالي ${weeks} أسبوع تدريبي)`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        if (startInput) startInput.addEventListener('change', calculateTrainingDays);
        if (endInput) endInput.addEventListener('change', calculateTrainingDays);
        calculateTrainingDays();
    });
</script>
@endpush