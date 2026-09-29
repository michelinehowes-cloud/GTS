@extends("layouts.app")

@section("title", "تفاصيل برنامج التدريب - " . $training->title)

@push('styles')
<style>
    .info-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-list-item:last-child {
        border-bottom: none;
    }
    .info-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .content-box {
        background-color: #f8fafc;
        border-radius: 14px;
        padding: 1.25rem;
        border: 1px solid #f1f5f9;
        height: 100%;
    }
    .star-rating-box {
        display: inline-flex;
        gap: 3px;
        color: #f59e0b;
        font-size: 0.95rem;
    }
    .star-rating-box .empty {
        color: #e2e8f0;
    }
</style>
@endpush

@php
    $isAdmin   = auth()->user()->role === 'admin';
    $prefix    = $isAdmin ? 'admin' : 'training-coordinator';
    $dashboard = $isAdmin ? route('admin.dashboard') : route('training-coordinator.dashboard');
@endphp

@section("content")
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        :title="$training->title"
        :subtitle="($training->company->name ?? 'مكتب تدريب الخريجين') . ' • ' . ($training->type_arabic ?? 'برنامج تدريبي') . ' • ' . ($training->duration ?? 'محدد المدة')"
        icon="fas fa-graduation-cap"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => $isAdmin ? 'لوحة تحكم المدير' : 'لوحة تحكم منسق التدريب', 'url' => $dashboard],
            ['label' => 'إدارة برامج التدريب', 'url' => route($prefix . '.trainings')],
            ['label' => Str::limit($training->title, 25)]
        ]"
        :badge="$training->status === 'active' ? '🟢 تدريب نشط' : ($training->status === 'completed' ? '✅ تدريب مكتمل' : '⏸ غير نشط')"
    >
        <a href="{{ route($prefix . '.trainings.attendance', $training->id) }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-clipboard-check"></i>
            <span>سجل ومصفوفة الحضور</span>
        </a>
        {{-- زر تصدير إكسل السريع --}}
        <div class="dropdown d-inline-block">
            <button class="btn btn-light bg-white text-success fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-excel text-success"></i>
                <span>تصدير الحضور (Excel)</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="border-radius: 12px; min-width: 250px;">
                <li>
                    <a class="dropdown-item py-2.5 d-flex align-items-center gap-2" href="{{ route($prefix . '.trainings.attendance.export', ['training' => $training->id, 'attended_only' => 1]) }}">
                        <i class="fas fa-user-check text-success fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark">تصدير الحاضرين فقط</div>
                            <small class="text-muted" style="font-size: 0.75rem;">بيانات الطلبة الذين حضروا التدريب بالفعل</small>
                        </div>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2.5 d-flex align-items-center gap-2" href="{{ route($prefix . '.trainings.attendance.export', $training->id) }}">
                        <i class="fas fa-users text-primary fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark">تصدير الكشف الشامل</div>
                            <small class="text-muted" style="font-size: 0.75rem;">جميع الطلبة المسجلين ومصفوفة الأيام</small>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        @if($training->status == 'active')
        <a href="{{ route($prefix . '.trainings.scanner', $training->id) }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-qrcode"></i>
            <span>ماسح QR</span>
        </a>
        @endif
        <a href="{{ route($prefix . '.trainings.edit', $training->id) }}" class="btn btn-outline-light py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-edit"></i>
            <span>تعديل</span>
        </a>
        <a href="{{ route($prefix . '.trainings') }}" class="btn btn-outline-light py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-arrow-right"></i>
            <span>رجوع</span>
        </a>
    </x-page-hero>

    {{-- بطاقات الإحصائيات --}}
    <div class="row mb-4">
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-sm-6 mb-4',
            'title' => 'المقاعد المتاحة',
            'value' => $training->seats,
            'icon' => 'fas fa-users',
            'color' => 'primary',
            'description' => 'العدد الكلي للمقاعد'
        ])
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-sm-6 mb-4',
            'title' => 'المسجلون المقبولون',
            'value' => $applications->where('status','approved')->count(),
            'icon' => 'fas fa-user-check',
            'color' => 'success',
            'description' => 'المتدربون المؤكد قبولهم'
        ])
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-sm-6 mb-4',
            'title' => 'متوسط التقييم',
            'value' => $evaluations->count() > 0 ? number_format($evaluations->avg('overall_rating'), 1) . ' / 5' : '—',
            'icon' => 'fas fa-star',
            'color' => 'warning',
            'description' => 'تقييم جودة البرنامج'
        ])
        @include('components.stat-card', [
            'col' => 'col-xl-3 col-sm-6 mb-4',
            'title' => 'عدد التقييمات',
            'value' => $evaluations->count(),
            'icon' => 'fas fa-clipboard-list',
            'color' => 'info',
            'description' => 'التقييمات المكتملة'
        ])
    </div>

    {{-- معلومات البرنامج والتوقيت --}}
    <div class="row g-4 mb-4">
        {{-- المعلومات الأساسية --}}
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-info-circle me-2"></i>المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-primary"><i class="fas fa-graduation-cap"></i></div>
                        <div>
                            <small class="text-muted d-block">اسم البرنامج التدريبي</small>
                            <strong class="text-dark">{{ $training->title }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-info"><i class="fas fa-tag"></i></div>
                        <div>
                            <small class="text-muted d-block">الفئة / المجال</small>
                            <strong class="text-dark">{{ $training->category ?? 'غير محدد' }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-success"><i class="fas fa-lightbulb"></i></div>
                        <div>
                            <small class="text-muted d-block">نوع البرنامج</small>
                            <span class="badge bg-light text-success border border-success rounded-pill px-2 py-1">
                                {{ $training->type_arabic ?? 'تدريب' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-warning"><i class="fas fa-building"></i></div>
                        <div>
                            <small class="text-muted d-block">الجهة / الشركة المنظمة</small>
                            <strong class="text-dark">{{ $training->company->name ?? 'مكتب تدريب الخريجين' }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-danger"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <small class="text-muted d-block">منسق التدريب</small>
                            <strong class="text-dark">{{ $training->coordinator->name ?? 'غير محدد' }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-secondary"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div>
                            <small class="text-muted d-block">اسم المدرب</small>
                            <strong class="text-dark">{{ $training->instructor_name ?? ($training->trainer->name ?? 'غير محدد') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- التوقيت والمكان --}}
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-calendar-alt me-2"></i>التوقيت والمكان
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-primary"><i class="fas fa-clock"></i></div>
                        <div>
                            <small class="text-muted d-block">المدة الإجمالية</small>
                            <strong class="text-dark">{{ $training->duration }} ({{ $training->total_days_count }} أيام تدريبية)</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-success"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <small class="text-muted d-block">تاريخ البدء</small>
                            <strong class="text-dark">{{ $training->start_date ? $training->start_date->format('Y-m-d') : 'غير محدد' }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-warning"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <small class="text-muted d-block">تاريخ الانتهاء</small>
                            <strong class="text-dark">{{ $training->end_date ? $training->end_date->format('Y-m-d') : 'غير محدد' }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-info"><i class="fas fa-calendar-week"></i></div>
                        <div>
                            <small class="text-muted d-block">أيام التدريب الأسبوعية</small>
                            <strong class="text-dark">{{ $training->training_days_text }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-danger"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <small class="text-muted d-block">المقر / القاعة</small>
                            <strong class="text-dark">{{ $training->location ?? 'جامعة طرابلس' }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-info"><i class="fas fa-chair"></i></div>
                        <div>
                            <small class="text-muted d-block">المقاعد المتاحة</small>
                            <strong class="text-dark">{{ $training->available_seats }} متبقي من أصل {{ $training->seats }}</strong>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <div class="info-icon-box bg-light text-secondary"><i class="fas fa-history"></i></div>
                        <div>
                            <small class="text-muted d-block">تاريخ النشر</small>
                            <strong class="text-dark">{{ $training->created_at->format('Y-m-d') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- تفاصيل المحتوى والأهداف --}}
    @if($training->description || $training->objectives || $training->requirements || $training->instructor_qualifications)
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-file-alt me-2"></i>تفاصيل ومحتوى البرنامج
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @if($training->description)
                <div class="col-md-6">
                    <div class="content-box">
                        <h6 class="fw-bold text-primary mb-2"><i class="fas fa-align-right me-2"></i>وصف البرنامج</h6>
                        <p class="text-muted small mb-0 lh-lg">{{ $training->description }}</p>
                    </div>
                </div>
                @endif

                @if($training->objectives)
                <div class="col-md-6">
                    <div class="content-box">
                        <h6 class="fw-bold text-success mb-2"><i class="fas fa-bullseye me-2"></i>أهداف البرنامج</h6>
                        <p class="text-muted small mb-0 lh-lg">{{ $training->objectives }}</p>
                    </div>
                </div>
                @endif

                @if($training->requirements)
                <div class="col-md-6">
                    <div class="content-box">
                        <h6 class="fw-bold text-warning mb-2"><i class="fas fa-list-check me-2"></i>شروط ومتطلبات الالتحاق</h6>
                        <p class="text-muted small mb-0 lh-lg">{{ $training->requirements }}</p>
                    </div>
                </div>
                @endif

                @if($training->instructor_qualifications)
                <div class="col-md-6">
                    <div class="content-box">
                        <h6 class="fw-bold text-info mb-2"><i class="fas fa-user-graduate me-2"></i>مؤهلات وخبرات المدرب</h6>
                        <p class="text-muted small mb-0 lh-lg">{{ $training->instructor_qualifications }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- جدول المتدربين والطلبات المسجلة --}}
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="card-title mb-0 text-primary fw-bold">
                    <i class="fas fa-users me-2"></i>المتدربون وطلبات الالتحاق بهذا البرنامج
                </h5>
                @if($applications->where('status', 'pending')->count() > 0)
                    <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">
                        {{ $applications->where('status', 'pending')->count() }} بانتظار اتخاذ قرار
                    </span>
                @endif
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- أزرار الإجراءات على المحدد -->
                <div id="show-bulk-toolbar" class="d-none d-flex align-items-center gap-2 bg-light p-1 px-2 rounded-pill border">
                    <span class="small fw-bold text-dark me-1">المحدد (<span id="show-selected-count" class="text-primary">0</span>):</span>
                    <button type="button" class="btn btn-success-modern btn-sm py-1 px-3" onclick="submitShowBulk('approve')">
                        <i class="fas fa-check me-1"></i>قبول
                    </button>
                    <button type="button" class="btn btn-danger-modern btn-sm py-1 px-3" onclick="submitShowBulk('reject')">
                        <i class="fas fa-times me-1"></i>رفض
                    </button>
                    <button type="button" class="btn btn-outline-danger-modern btn-sm py-1 px-3" onclick="submitShowBulk('delete')">
                        <i class="fas fa-trash me-1"></i>إلغاء وحذف
                    </button>
                </div>

                <a href="{{ route($prefix . '.trainings.attendance', $training->id) }}" class="btn btn-sm btn-outline-primary-modern">
                    <i class="fas fa-clipboard-check me-1"></i> مصفوفة الحضور اليومية
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if($applications->count() > 0)
                <form id="show-bulk-form" method="POST" class="d-none">
                    @csrf
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-3 border-0" style="width: 40px;">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="show-select-all" title="تحديد الكل">
                                    </div>
                                </th>
                                <th class="py-3 border-0" style="width: 40px;">#</th>
                                <th class="py-3 border-0">الخريج</th>
                                <th class="py-3 border-0">تاريخ التقديم</th>
                                <th class="py-3 border-0 text-center">حالة الطلب</th>
                                <th class="py-3 border-0 text-center">حضور الدورة</th>
                                <th class="py-3 border-0 text-center" style="min-width: 170px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                            <tr>
                                <td class="px-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input show-app-checkbox" type="checkbox" name="application_ids[]" value="{{ $app->id }}" data-status="{{ $app->status }}">
                                    </div>
                                </td>
                                <td class="fw-bold text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 38px; height: 38px;">
                                            {{ mb_substr($app->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('graduate.profile.public', $app->user->id ?? 0) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                {{ $app->user->name ?? 'غير محدد' }}
                                            </a>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ $app->user->email ?? 'لا يوجد بريد' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $app->applied_at ? $app->applied_at->format('Y-m-d') : $app->created_at->format('Y-m-d') }}
                                </td>
                                <td class="text-center">
                                    @if($app->status == 'pending')
                                        <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">
                                            <i class="fas fa-clock me-1 small"></i>قيد المراجعة
                                        </span>
                                    @elseif($app->status == 'approved')
                                        <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">
                                            <i class="fas fa-check-circle me-1 small"></i>مقبول
                                        </span>
                                    @else
                                        <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">
                                            <i class="fas fa-times-circle me-1 small"></i>مرفوض
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($app->status == 'approved')
                                        @php
                                            $attCount = \App\Models\TrainingAttendance::where('training_id', $training->id)
                                                ->where('user_id', $app->user_id)
                                                ->where('status', 'present')
                                                ->count();
                                            $totalDays = $training->total_days_count;
                                        @endphp
                                        <a href="{{ route($prefix . '.trainings.attendance', $training->id) }}" class="badge bg-light text-primary border border-primary text-decoration-none rounded-pill px-2 py-1 small">
                                            <i class="fas fa-check me-1"></i>{{ $attCount }} / {{ $totalDays }} أيام
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($app->status == 'pending')
                                            <form action="{{ route($prefix . '.applications.approve', $app->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success-modern" title="قبول">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route($prefix . '.applications.reject', $app->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger-modern" title="رفض">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route($prefix . '.applications.pending', $app->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning-modern" title="إعادة للمراجعة">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($app->user)
                                            <a href="{{ route('graduate.profile.public', $app->user->id) }}" target="_blank" class="btn btn-outline-primary-modern ms-1" title="الملف التعريفي">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        @endif

                                        <!-- زر إلغاء وحذف الطلب -->
                                        <form action="{{ route($prefix . '.applications.destroy', $app->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('هل أنت متأكد من إلغاء وحذف هذا الطلب نهائياً؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger-modern" title="إلغاء وحذف الطلب">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted opacity-50 mb-3"></i>
                    <h6 class="fw-bold text-dark mb-1">لا توجد طلبات التحاق مسجلة حتى الآن</h6>
                    <p class="text-muted small mb-0">عند تقديم الخريجين ستظهر طلباتهم هنا مباشرة</p>
                </div>
            @endif
        </div>
    </div>

    {{-- قسم التقييمات --}}
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-star me-2 text-warning"></i>تقييمات البرنامج التدريبي
                @if($evaluations->count() > 0)
                    <span class="badge bg-light text-warning border border-warning rounded-pill ms-2">{{ $evaluations->count() }} تقييم</span>
                @endif
            </h5>
            @if($evaluations->count() > 0)
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-5 fw-bold text-dark">{{ number_format($evaluations->avg('overall_rating'), 1) }} / 5</span>
                    <div class="star-rating-box">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($evaluations->avg('overall_rating')) ? '' : 'empty' }}"></i>
                        @endfor
                    </div>
                </div>
            @endif
        </div>

        <div class="card-body p-4">
            @if($evaluations->count() > 0)
                <div class="accordion" id="evaluationsAccordion">
                    @foreach($evaluations as $evaluation)
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                            <h2 class="accordion-header" id="heading{{ $evaluation->id }}">
                                <button class="accordion-button collapsed bg-light py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $evaluation->id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-user-check text-primary"></i>
                                            <strong class="text-dark">{{ $evaluation->evaluator->name ?? 'مقيم' }}</strong>
                                            <span class="text-muted small">({{ $evaluation->created_at->format('Y-m-d') }})</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary rounded-pill px-3 py-1">{{ $evaluation->overall_rating ?? 0 }}/5</span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $evaluation->id }}" class="accordion-collapse collapse" data-bs-parent="#evaluationsAccordion">
                                <div class="accordion-body p-4">
                                    {{-- تفاصيل التقييم --}}
                                    @if($evaluation->strengths || $evaluation->weaknesses || $evaluation->recommendations || $evaluation->comments)
                                        <div class="row g-3">
                                            @if($evaluation->strengths)
                                                <div class="col-md-6">
                                                    <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle">
                                                        <strong class="text-success d-block mb-1"><i class="fas fa-plus-circle me-1"></i>النقاط القوية:</strong>
                                                        <p class="mb-0 small text-dark">{{ $evaluation->strengths }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($evaluation->weaknesses)
                                                <div class="col-md-6">
                                                    <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle">
                                                        <strong class="text-danger d-block mb-1"><i class="fas fa-minus-circle me-1"></i>نقاط التحسين:</strong>
                                                        <p class="mb-0 small text-dark">{{ $evaluation->weaknesses }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($evaluation->recommendations)
                                                <div class="col-md-6">
                                                    <div class="p-3 bg-info-subtle rounded-3 border border-info-subtle">
                                                        <strong class="text-info d-block mb-1"><i class="fas fa-lightbulb me-1"></i>التوصيات:</strong>
                                                        <p class="mb-0 small text-dark">{{ $evaluation->recommendations }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($evaluation->comments)
                                                <div class="col-md-6">
                                                    <div class="p-3 bg-light rounded-3 border">
                                                        <strong class="text-secondary d-block mb-1"><i class="fas fa-comment me-1"></i>ملاحظات عامة:</strong>
                                                        <p class="mb-0 small text-dark">{{ $evaluation->comments }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-muted small mb-0">تم تسجيل الدرجة الإجمالية بنجاح.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-star-half-alt fa-3x text-muted opacity-50 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">لا توجد تقييمات مسجلة بعد</h6>
                    <p class="text-muted small mb-0">سيتم رصد تقييمات المتدربين والمدرب بعد انتهاء البرنامج التدريبي</p>
                </div>
            @endif
        </div>
    </div>

    {{-- أزرار التحكم السفلية --}}
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route($prefix . '.trainings.edit', $training->id) }}" class="btn btn-warning-modern">
            <i class="fas fa-edit me-1"></i> تعديل البرنامج
        </a>
        <form action="{{ route($prefix . '.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger-modern" onclick="return confirm('هل أنت متأكد من حذف هذا البرنامج التدريبي؟')">
                <i class="fas fa-trash me-1"></i> حذف البرنامج
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const showRoutes = {
        approve: "{{ route($prefix . '.applications.bulk-approve') }}",
        reject: "{{ route($prefix . '.applications.bulk-reject') }}",
        delete: "{{ route($prefix . '.applications.bulk-delete') }}"
    };

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('show-select-all');
        const checkboxes = document.querySelectorAll('.show-app-checkbox');
        const toolbar = document.getElementById('show-bulk-toolbar');
        const countSpan = document.getElementById('show-selected-count');

        function updateToolbar() {
            const checkedBoxes = document.querySelectorAll('.show-app-checkbox:checked');
            const checkedCount = checkedBoxes.length;
            if (countSpan) countSpan.textContent = checkedCount;
            if (toolbar) {
                if (checkedCount > 0) {
                    toolbar.classList.remove('d-none');
                } else {
                    toolbar.classList.add('d-none');
                }
            }
            if (selectAll && checkboxes.length > 0) {
                selectAll.checked = checkedCount === checkboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateToolbar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateToolbar);
        });
    });

    function submitShowBulk(actionType) {
        const checked = document.querySelectorAll('.show-app-checkbox:checked');
        if (checked.length === 0) return;
        
        let confirmMsg = '';
        if (actionType === 'approve') confirmMsg = `هل أنت متأكد من قبول ${checked.length} طلب(ات) محددة؟`;
        else if (actionType === 'reject') confirmMsg = `هل أنت متأكد من رفض ${checked.length} طلب(ات) محددة؟`;
        else if (actionType === 'delete') confirmMsg = `هل أنت متأكد من إلغاء وحذف ${checked.length} طلب(ات) محددة نهائياً؟`;

        if (confirm(confirmMsg)) {
            const form = document.getElementById('show-bulk-form');
            form.action = showRoutes[actionType];
            form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());
            
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'application_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            
            form.submit();
        }
    }
</script>
@endpush
