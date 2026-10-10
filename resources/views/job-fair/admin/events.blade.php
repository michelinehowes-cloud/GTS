@extends('layouts.app')

@section('title', 'إدارة البرنامج العلمي والفعاليات - ' . $fair->title)

@section('content')
@include('job-fair.admin.partials.modal-styles')
<div class="container-fluid py-4" dir="rtl">
    {{-- الشريط العلوي الموحد مع الشعارات الرسمية المتطابقة مع صفحة المعرض العامة --}}
    @include('job-fair.admin.partials.header', [
        'fair' => $fair,
        'page' => 'events',
        'title' => 'إدارة البرنامج العلمي والفعاليات',
        'subtitle' => $fair->title . ' — جدول الماستر كلاس، ورش العمل والجلسات الحوارية'
    ])

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> يرجى مراجعة الأخطاء التالية:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-primary">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase mb-1">إجمالي الفعاليات</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-warning">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase mb-1">Masterclass</div>
                    <div class="h4 mb-0 font-weight-bold text-warning">{{ $stats['masterclass'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-info">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase mb-1">ورش العمل</div>
                    <div class="h4 mb-0 font-weight-bold text-info">{{ $stats['workshops'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-secondary">
                <div class="card-body p-3">
                    <div class="text-muted small font-weight-bold text-uppercase mb-1">الجلسات الحوارية</div>
                    <div class="h4 mb-0 font-weight-bold text-secondary">{{ $stats['panels'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-8 col-sm-12">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-success">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">إجمالي الحجوزات والمقاعد</div>
                        <div class="h5 mb-0 font-weight-bold text-success">
                            {{ $stats['attendees'] ?? 0 }} <span class="text-muted font-weight-normal small">/ {{ $stats['capacity'] ?? 0 }} مقعد</span>
                        </div>
                    </div>
                    <div class="text-success display-6">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table Card -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-ul me-1"></i> قائمة فعاليات البرنامج العلمي ({{ $events->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="border-0" style="width: 50px;">#</th>
                            <th class="border-0">نوع الفعالية</th>
                            <th class="border-0">عنوان الفعالية</th>
                            <th class="border-0">المتحدث / المدرب</th>
                            <th class="border-0">الموعد والقاعة</th>
                            <th class="border-0 text-center">التسجيل والمقاعد</th>
                            <th class="border-0 text-center">الحالة</th>
                            <th class="border-0 text-center" style="width: 180px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td class="align-middle text-muted">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-pill px-2 py-1 
                                        {{ $event->type === 'masterclass' ? 'badge-warning text-dark' : ($event->type === 'workshop' ? 'badge-primary' : 'badge-info') }}">
                                        <i class="{{ $event->type_icon }}"></i>
                                        {{ $event->type_short_label }}
                                    </span>
                                    @if($event->is_featured)
                                        <div class="text-warning small mt-1 font-weight-bold">
                                            <i class="fas fa-star"></i> مميزة
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ $event->title }}</h6>
                                    @if($event->target_audience)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-bullseye text-secondary"></i> {{ Str::limit($event->target_audience, 40) }}
                                        </small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @if($event->speaker_image_url)
                                            <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}" class="rounded-circle mr-2" style="width: 38px; height: 38px; object-fit: cover; border: 1px solid #ddd;">
                                        @else
                                            <div class="rounded-circle mr-2 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border: 1px solid #ddd;">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $event->speaker_name ?? '-' }}</div>
                                            @if($event->speaker_title)
                                                <small class="text-muted d-block">{{ Str::limit($event->speaker_title, 35) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="small">
                                        <i class="far fa-calendar-alt text-primary"></i> 
                                        {{ $event->start_time ? $event->start_time->format('Y-m-d') : '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="far fa-clock text-info"></i> 
                                        {{ $event->start_time ? $event->start_time->format('H:i') : '' }} - {{ $event->end_time ? $event->end_time->format('H:i') : '' }}
                                    </div>
                                    @if($event->location)
                                        <div class="small text-secondary">
                                            <i class="fas fa-map-marker-alt text-danger"></i> {{ $event->location }}
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="font-weight-bold mb-1">
                                        {{ $event->attendees_count }} {{ $event->capacity ? '/ ' . $event->capacity : '' }}
                                    </div>
                                    @if($event->capacity)
                                        @php
                                            $fill = min(100, round(($event->attendees_count / $event->capacity) * 100));
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $fill >= 90 ? 'bg-danger' : ($fill >= 70 ? 'bg-warning' : 'bg-success') }}" 
                                                 role="progressbar" style="width: {{ $fill }}%;"></div>
                                        </div>
                                        <small class="text-muted">{{ $fill }}%</small>
                                    @else
                                        <span class="badge badge-light">سعة مفتوحة</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge 
                                        {{ $event->effective_status === 'open' ? 'badge-success' : ($event->effective_status === 'completed' ? 'badge-danger' : 'badge-secondary') }} px-2 py-1">
                                        {{ $event->status_label }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Show Attendees -->
                                        <button type="button" class="btn btn-outline-info" title="عرض المسجلين" onclick="loadAttendees({{ $event->id }}, '{{ addslashes($event->title) }}')">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <!-- Export CSV -->
                                        <a href="{{ route('job-fair.admin.events.export-attendees', $event->id) }}" class="btn btn-outline-success" title="تصدير كشف الحضور (CSV)">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                        <!-- Public Page + QR -->
                                        <a href="{{ route('job-fair.public.events.show', $event->id) }}" target="_blank" class="btn btn-outline-secondary" title="الصفحة العامة ورمز QR">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                        <!-- Edit Event -->
                                        <button type="button" class="btn btn-outline-primary" title="تعديل" onclick="editEventById({{ $event->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete Event -->
                                        <form action="{{ route('job-fair.admin.events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفعالية نهائياً؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3 d-block text-gray-300"></i>
                                    لا توجد فعاليات مضافة لهذا المعرض حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Event -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog uot-modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content uot-modal-content">
            <form action="{{ route('job-fair.admin.events.store', $fair->id) }}" method="POST" enctype="multipart/form-data" class="uot-modal-form">
                @csrf
                <div class="modal-header uot-modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="uot-modal-icon-badge">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div>
                            <h5 class="uot-modal-title" id="addEventModalLabel">
                                إضافة فعالية علمية جديدة
                            </h5>
                            <p class="uot-modal-subtitle">{{ $fair->title }} &bull; البرنامج العلمي وجدول الورش والماستر كلاس</p>
                        </div>
                    </div>
                    <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4 uot-modal-body">
                    {{-- القسم 1: تفاصيل ونوع الفعالية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-layer-group"></i></span>
                        <h6 class="divider-title">تفاصيل ونوع الفعالية</h6>
                        <span class="divider-badge">المعلومات الأساسية</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="uot-form-label">
                                نوع الفعالية <span class="req">*</span>
                            </label>
                            <select name="type" class="form-select uot-select" required>
                                <option value="workshop">ورشة عمل تطبيقية (Workshop)</option>
                                <option value="masterclass">ماستر كلاس (Masterclass)</option>
                                <option value="panel_discussion">جلسة حوارية (Panel Discussion)</option>
                                <option value="keynote">جلسة رئيسية / كلمة افتتاحية</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="uot-form-label">
                                عنوان الفعالية <span class="req">*</span>
                            </label>
                            <input type="text" name="title" class="form-control uot-input" required placeholder="مثال: خريطة سوق العمل: أين تتجه الوظائف؟">
                        </div>
                    </div>

                    {{-- القسم 2: بيانات المتحدث / المدرب --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-user-tie"></i></span>
                        <h6 class="divider-title">بيانات المتحدث / المدرب</h6>
                        <span class="divider-badge">المسؤول عن التقديم</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="uot-form-label">اسم المتحدث / المدرب</label>
                            <input type="text" name="speaker_name" class="form-control uot-input" placeholder="مثال: د. محمد قويش">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">الصفة المهنية للمتحدث</label>
                            <input type="text" name="speaker_title" class="form-control uot-input" placeholder="مثال: مدرب ومستشار في الذكاء الاصطناعي">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">صورة المتحدث (اختياري)</label>
                            <input type="file" name="speaker_image" class="form-control uot-input" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="uot-form-label">نبذة عن المتحدث</label>
                            <textarea name="speaker_bio" class="form-control uot-textarea" rows="2" placeholder="نبذة مختصرة عن خبرات وإنجازات المتحدث الأكاديمية والمهنية..."></textarea>
                        </div>
                    </div>

                    {{-- القسم 3: فكرة ومحاور الفعالية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-lightbulb"></i></span>
                        <h6 class="divider-title">فكرة ومحاور الفعالية</h6>
                        <span class="divider-badge">المحتوى العلمي</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="uot-form-label">فكرة الفعالية والهدف منها</label>
                            <textarea name="description" class="form-control uot-textarea" rows="3" placeholder="أهداف الفعالية وما سيخرج به المشاركون من مهارات ومعارف..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="uot-form-label">محاور الفعالية (ضع كل محور في سطر مستقل)</label>
                            <textarea name="topics" class="form-control uot-textarea" rows="4" placeholder="المحور الأول: قراءة في واقع المهارات المطلوبة&#10;المحور الثاني: أدوات بناء السيرة الذاتية المهنية&#10;المحور الثالث: استراتيجيات اجتياز مقابلات العمل"></textarea>
                            <small class="text-muted d-block mt-1">سيتم عرض كل سطر كعنصر مستقل في قائمة المحاور.</small>
                        </div>
                    </div>

                    {{-- القسم 4: التوقيت، القاعة، وتفاصيل الحضور --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-clock"></i></span>
                        <h6 class="divider-title">التوقيت، المكان، وتفاصيل الحضور</h6>
                        <span class="divider-badge">الجدولة والسعة</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="uot-form-label">وقت البداية <span class="req">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control uot-input" required value="{{ \Carbon\Carbon::parse($fair->event_date)->format('Y-m-d\T10:00') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">وقت النهاية <span class="req">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control uot-input" required value="{{ \Carbon\Carbon::parse($fair->event_date)->format('Y-m-d\T12:00') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="uot-form-label">المكان / القاعة</label>
                            <input type="text" name="location" class="form-control uot-input" placeholder="مثال: المدرج الرئيسي - جامعة طرابلس">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">الفئة المستهدفة</label>
                            <input type="text" name="target_audience" class="form-control uot-input" placeholder="مثال: خريجو وطلبة كليات الهندسة والتقنية">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">السعة الاستيعابية (المقاعد)</label>
                            <input type="number" name="capacity" class="form-control uot-input" min="1" placeholder="مثال: 150">
                            <small class="text-muted d-block mt-1">اتركه فارغاً إذا كانت السعة مفتوحة.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">حالة التسجيل</label>
                            <select name="status" class="form-select uot-select">
                                <option value="open">مفتوح للتسجيل</option>
                                <option value="upcoming">قريباً</option>
                                <option value="completed">مكتمل المقاعد</option>
                                <option value="ended">انتهت الفعالية</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch p-3 rounded-3 bg-light border w-100 d-flex align-items-center gap-3">
                                <input type="checkbox" name="is_featured" value="1" class="form-check-input m-0" id="addFeatured">
                                <label class="form-check-label fw-bold text-dark m-0" for="addFeatured">
                                    <i class="fas fa-star text-warning me-1"></i> تمييز الفعالية في الصفحة الرئيسية للمعرض
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer uot-modal-footer">
                    <button type="button" class="uot-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </button>
                    <button type="submit" class="uot-btn-submit">
                        <i class="fas fa-save me-1"></i> حفظ وتثبيت الفعالية
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Event -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog uot-modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content uot-modal-content">
            <form id="editEventForm" method="POST" enctype="multipart/form-data" class="uot-modal-form">
                @csrf
                @method('PUT')
                <div class="modal-header uot-modal-header uot-modal-header-edit">
                    <div class="d-flex align-items-center gap-3">
                        <div class="uot-modal-icon-badge">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h5 class="uot-modal-title" id="editEventModalLabel">
                                تعديل بيانات الفعالية العلمية
                            </h5>
                            <p class="uot-modal-subtitle">{{ $fair->title }} &bull; تحديث البرنامج العلمي والتفاصيل</p>
                        </div>
                    </div>
                    <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4 uot-modal-body">
                    {{-- القسم 1: تفاصيل ونوع الفعالية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-layer-group"></i></span>
                        <h6 class="divider-title">تفاصيل ونوع الفعالية</h6>
                        <span class="divider-badge">المعلومات الأساسية</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="uot-form-label">
                                نوع الفعالية <span class="req">*</span>
                            </label>
                            <select name="type" id="edit_type" class="form-select uot-select" required>
                                <option value="workshop">ورشة عمل تطبيقية (Workshop)</option>
                                <option value="masterclass">ماستر كلاس (Masterclass)</option>
                                <option value="panel_discussion">جلسة حوارية (Panel Discussion)</option>
                                <option value="keynote">جلسة رئيسية / كلمة افتتاحية</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="uot-form-label">
                                عنوان الفعالية <span class="req">*</span>
                            </label>
                            <input type="text" name="title" id="edit_title" class="form-control uot-input" required>
                        </div>
                    </div>

                    {{-- القسم 2: بيانات المتحدث / المدرب --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-user-tie"></i></span>
                        <h6 class="divider-title">بيانات المتحدث / المدرب</h6>
                        <span class="divider-badge">المسؤول عن التقديم</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="uot-form-label">اسم المتحدث / المدرب</label>
                            <input type="text" name="speaker_name" id="edit_speaker_name" class="form-control uot-input">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">الصفة المهنية للمتحدث</label>
                            <input type="text" name="speaker_title" id="edit_speaker_title" class="form-control uot-input">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">تحديث صورة المتحدث</label>
                            <input type="file" name="speaker_image" class="form-control uot-input" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="uot-form-label">نبذة عن المتحدث</label>
                            <textarea name="speaker_bio" id="edit_speaker_bio" class="form-control uot-textarea" rows="2"></textarea>
                        </div>
                    </div>

                    {{-- القسم 3: فكرة ومحاور الفعالية --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-lightbulb"></i></span>
                        <h6 class="divider-title">فكرة ومحاور الفعالية</h6>
                        <span class="divider-badge">المحتوى العلمي</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="uot-form-label">فكرة الفعالية والهدف منها</label>
                            <textarea name="description" id="edit_description" class="form-control uot-textarea" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="uot-form-label">محاور الفعالية (ضع كل محور في سطر مستقل)</label>
                            <textarea name="topics" id="edit_topics" class="form-control uot-textarea" rows="4"></textarea>
                        </div>
                    </div>

                    {{-- القسم 4: التوقيت، القاعة، وتفاصيل الحضور --}}
                    <div class="uot-section-divider">
                        <span class="divider-icon"><i class="fas fa-clock"></i></span>
                        <h6 class="divider-title">التوقيت، المكان، وتفاصيل الحضور</h6>
                        <span class="divider-badge">الجدولة والسعة</span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="uot-form-label">وقت البداية <span class="req">*</span></label>
                            <input type="datetime-local" name="start_time" id="edit_start_time" class="form-control uot-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="uot-form-label">وقت النهاية <span class="req">*</span></label>
                            <input type="datetime-local" name="end_time" id="edit_end_time" class="form-control uot-input" required>
                        </div>

                        <div class="col-md-4">
                            <label class="uot-form-label">المكان / القاعة</label>
                            <input type="text" name="location" id="edit_location" class="form-control uot-input">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">الفئة المستهدفة</label>
                            <input type="text" name="target_audience" id="edit_target_audience" class="form-control uot-input">
                        </div>
                        <div class="col-md-4">
                            <label class="uot-form-label">السعة الاستيعابية</label>
                            <input type="number" name="capacity" id="edit_capacity" class="form-control uot-input" min="1">
                        </div>

                        <div class="col-md-6">
                            <label class="uot-form-label">حالة التسجيل <span class="req">*</span></label>
                            <select name="status" id="edit_status" class="form-select uot-select" required>
                                <option value="open">مفتوح للتسجيل</option>
                                <option value="upcoming">قريباً</option>
                                <option value="completed">مكتمل المقاعد</option>
                                <option value="ended">انتهت الفعالية</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch p-3 rounded-3 bg-light border w-100 d-flex align-items-center gap-3">
                                <input type="checkbox" name="is_featured" value="1" class="form-check-input m-0" id="editFeatured">
                                <label class="form-check-label fw-bold text-dark m-0" for="editFeatured">
                                    <i class="fas fa-star text-warning me-1"></i> تمييز الفعالية في الصفحة الرئيسية للمعرض
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer uot-modal-footer">
                    <button type="button" class="uot-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </button>
                    <button type="submit" class="uot-btn-submit">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: View Attendees -->
<div class="modal fade" id="attendeesModal" tabindex="-1" role="dialog" aria-labelledby="attendeesModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog uot-modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content uot-modal-content">
            <div class="modal-header uot-modal-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 60%, #075985 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="uot-modal-icon-badge" style="border-color:#7dd3fc; background:rgba(125,211,252,0.18); color:#7dd3fc;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h5 class="uot-modal-title" id="attendeesModalLabel">
                            قائمة المسجلين في الفعالية
                        </h5>
                        <p class="uot-modal-subtitle">كشف الحضور والبيانات الرسمية المسجلة</p>
                    </div>
                </div>
                <button type="button" class="uot-btn-close" data-bs-dismiss="modal" aria-label="Close" title="إغلاق">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-0 uot-modal-body">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" id="attendeeEventTitle">-</h6>
                        <small class="text-muted" id="attendeeEventCount">جاري التحميل...</small>
                    </div>
                    <a href="#" id="modalExportBtn" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> تصدير كشف الحضور (CSV)
                    </a>
                </div>
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>اسم الخريج / الطالب</th>
                                <th>الكلية / التخصص</th>
                                <th>البريد الإلكتروني / الهاتف</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody id="attendeesTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">جاري تحميل البيانات...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer uot-modal-footer">
                <button type="button" class="uot-btn-cancel ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const fairEvents = @json($events->keyBy('id'));

    function showModalSafe(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
            return;
        }
        if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
            return;
        }
        // Vanilla JS Fallback
        modalEl.style.display = 'block';
        modalEl.classList.add('show');
        modalEl.removeAttribute('aria-hidden');
        modalEl.setAttribute('aria-modal', 'true');
        
        let backdrop = document.getElementById(modalId + '-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = modalId + '-backdrop';
            backdrop.onclick = function() { hideModalSafe(modalId); };
            document.body.appendChild(backdrop);
        }
        document.body.classList.add('modal-open');
    }

    function hideModalSafe(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getInstance(modalEl);
            if (bsModal) { bsModal.hide(); return; }
        }
        if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('hide');
            return;
        }
        // Vanilla JS Fallback
        modalEl.style.display = 'none';
        modalEl.classList.remove('show');
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        const backdrop = document.getElementById(modalId + '-backdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('modal-open');
    }

    // Auto bind all close/dismiss buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-bs-dismiss="modal"], [data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) hideModalSafe(modal.id);
            });
        });
    });

    function editEventById(id) {
        const event = fairEvents[id];
        if (!event) {
            console.error('Event not found with ID:', id);
            return;
        }

        document.getElementById('editEventForm').action = "/admin/job-fair/events/" + event.id;
        document.getElementById('edit_type').value = event.type || 'workshop';
        document.getElementById('edit_title').value = event.title || '';
        document.getElementById('edit_speaker_name').value = event.speaker_name || '';
        document.getElementById('edit_speaker_title').value = event.speaker_title || '';
        document.getElementById('edit_speaker_bio').value = event.speaker_bio || '';
        document.getElementById('edit_description').value = event.description || '';
        document.getElementById('edit_topics').value = event.topics || '';
        document.getElementById('edit_location').value = event.location || '';
        document.getElementById('edit_target_audience').value = event.target_audience || '';
        document.getElementById('edit_capacity').value = event.capacity || '';
        document.getElementById('edit_status').value = event.status || 'open';
        document.getElementById('editFeatured').checked = Boolean(event.is_featured);

        if (event.start_time) {
            document.getElementById('edit_start_time').value = event.start_time.substring(0, 16);
        }
        if (event.end_time) {
            document.getElementById('edit_end_time').value = event.end_time.substring(0, 16);
        }

        showModalSafe('editEventModal');
    }

    function loadAttendees(eventId, eventTitle) {
        document.getElementById('attendeeEventTitle').textContent = eventTitle;
        document.getElementById('attendeeEventCount').textContent = 'جاري التحميل...';
        document.getElementById('modalExportBtn').href = "/admin/job-fair/events/" + eventId + "/export-attendees";
        
        const tbody = document.getElementById('attendeesTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin"></i> جاري جلب المسجلين...</td></tr>';
        
        showModalSafe('attendeesModal');

        fetch("/admin/job-fair/events/" + eventId + "/attendees")
            .then(res => res.json())
            .then(data => {
                document.getElementById('attendeeEventCount').textContent = "إجمالي المسجلين: " + data.count + " مشارك";
                if (data.count === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">لا يوجد مسجلون في هذه الفعالية حتى الآن.</td></tr>';
                    return;
                }
                
                let rows = '';
                data.attendees.forEach((att, idx) => {
                    const grad = att.graduate || {};
                    const dataObj = grad.graduate_data || {};
                    const college = dataObj.faculty || dataObj.major || '-';
                    const contact = grad.email || dataObj.phone || '-';
                    const regDate = att.created_at ? new Date(att.created_at).toLocaleDateString('ar-LY') : '-';

                    rows += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td class="font-weight-bold text-dark">${grad.name || '-'}</td>
                            <td>${college}</td>
                            <td>${contact}</td>
                            <td class="text-muted small">${regDate}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = rows;
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">حدث خطأ أثناء تحميل بيانات المسجلين.</td></tr>';
            });
    }
</script>
@endpush
@endsection
