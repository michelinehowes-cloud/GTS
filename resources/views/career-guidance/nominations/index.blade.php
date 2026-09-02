@extends('layouts.app')

@section('title', 'إدارة الترشيحات')

@section('content')
@php
    if (request()->routeIs('admin.*')) {
        $routePrefix = 'admin.career-guidance';
    } elseif (request()->routeIs('partnership.*')) {
        $routePrefix = 'partnership';
    } else {
        $routePrefix = 'career-guidance';
    }
@endphp

<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($routePrefix . '.dashboard')],
            ['label' => 'إدارة الترشيحات', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold mb-0">
            <i class="fas fa-paper-plane me-2"></i> إدارة الترشيحات
        </h2>
        <a href="{{ route($routePrefix . '.nominations.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus me-2"></i> إضافة ترشيح جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card-modern mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route($routePrefix . '.nominations') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label-modern"><i class="fas fa-briefcase me-1"></i> الفرصة الوظيفية</label>
                    <select name="opportunity_id" class="form-select-modern">
                        <option value="">جميع الفرص</option>
                        @foreach($opportunities as $opportunity)
                            <option value="{{ $opportunity->id }}" {{ request('opportunity_id') == $opportunity->id ? 'selected' : '' }}>
                                {{ $opportunity->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label-modern"><i class="fas fa-tasks me-1"></i> حالة الترشيح</label>
                    @php
                        $statuses = [
                            'pending' => 'قيد المراجعة',
                            'sent_to_company' => 'مرسل للشركة',
                            'under_review' => 'قيد الدراسة',
                            'interview_scheduled' => 'مقابلة مجدولة',
                            'accepted' => 'مقبول',
                            'rejected' => 'مرفوض',
                            'withdrawn' => 'ملغي'
                        ];
                    @endphp
                    <select name="status" class="form-select-modern">
                        <option value="">جميع الحالات</option>
                        @foreach($statuses as $value => $text)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $text }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label-modern"><i class="fas fa-calendar-alt me-1"></i> من تاريخ</label>
                    <input type="date" name="from_date" class="form-control-modern" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label-modern"><i class="fas fa-calendar-check me-1"></i> إلى تاريخ</label>
                    <input type="date" name="to_date" class="form-control-modern" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary-modern w-100">
                        تصفية النتائج
                    </button>
                    @if(request()->hasAny(['opportunity_id', 'status', 'from_date', 'to_date']))
                        <a href="{{ route($routePrefix . '.nominations') }}" class="btn btn-outline-secondary" title="إلغاء التصفية">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Nominations Table -->
    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-list me-2"></i>قائمة الترشيحات المسجلة
            </h5>
            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-2">
                {{ $nominations->count() }} ترشيح
            </span>
        </div>
        <div class="card-body p-0">
            @if($nominations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold">الخريج</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">الفرصة والشركة</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold text-center">حالة الترشيح</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold text-center">القرار النهائي</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold text-center">تاريخ الترشيح</th>
                                <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($nominations as $nomination)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 700;">
                                            {{ mb_substr($nomination->graduate->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $nomination->graduate->name }}</div>
                                            <div class="small text-muted">{{ $nomination->graduate->major ?? 'خريج' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $nomination->jobOpportunity->title }}</div>
                                    <div class="small text-primary">{{ $nomination->jobOpportunity->company->name ?? 'غير محدد' }}</div>
                                </td>
                                <td class="text-center">
                                    @switch($nomination->status)
                                        @case('pending')
                                            <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-2">⏳ قيد المراجعة</span>
                                            @break
                                        @case('sent_to_company')
                                            <span class="badge bg-light text-info border border-info rounded-pill px-3 py-2">📤 مرسل للشركة</span>
                                            @break
                                        @case('under_review')
                                            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-2">🔍 قيد الدراسة</span>
                                            @break
                                        @case('interview_scheduled')
                                            <span class="badge bg-light text-info border border-info rounded-pill px-3 py-2">📅 مقابلة مجدولة</span>
                                            @break
                                        @case('accepted')
                                            <span class="badge bg-light text-success border border-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> مقبول</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> مرفوض</span>
                                            @break
                                        @case('withdrawn')
                                            <span class="badge bg-light text-secondary border border-secondary rounded-pill px-3 py-2">مسحوب</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-2">{{ $nomination->status_text }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    @if($nomination->final_status == 'hired')
                                        <span class="badge bg-light text-success border border-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> تم التوظيف
                                        </span>
                                    @elseif($nomination->final_status == 'not_hired')
                                        <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i> لم يتم التوظيف
                                        </span>
                                    @elseif($nomination->final_status == 'in_progress')
                                        <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-2">
                                            قيد المعالجة
                                        </span>
                                    @else
                                        <span class="text-muted small">لم يحدد بعد</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $nomination->nominated_at ? $nomination->nominated_at->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route($routePrefix . '.nominations.show', $nomination->id) }}" class="btn btn-sm btn-outline-info-modern" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route($routePrefix . '.nominations.edit-status', $nomination->id) }}" class="btn btn-sm btn-outline-warning-modern" title="تحديث حالة الترشيح">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted display-4 mb-3 opacity-50"></i>
                    <h5 class="text-dark fw-bold">لا توجد ترشيحات لعرضها</h5>
                    <p class="text-muted">لم يتم العثور على أي ترشيحات تتطابق مع بحثك أو لم يتم إضافة ترشيحات بعد.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

