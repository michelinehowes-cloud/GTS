@extends('layouts.app')

@section('title', 'إدارة مرشحي التوظيف (ATS)')
@section('page-title', 'إدارة مرشحي التوظيف')

@section('content')
<div class="container-fluid py-4">
    <!-- الشريط الترحيبي بالهوية الموحدة للمنظومة -->
    <x-page-hero
        title="إدارة مرشحي التوظيف (ATS)"
        subtitle="متابعة وتحديث حالات المتقدمين للفرص الوظيفية الخاصة بشركة {{ $company->name ?? 'الشريكة' }}"
        icon="fas fa-users"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'المرشحون للوظائف']
        ]"
        secondaryBadge="{{ $nominations->count() }} مرشح"
        secondaryBadgeIcon="fas fa-user-check"
    >
        <a href="{{ route('job-opportunities.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>+ إضافة فرصة عمل</span>
        </a>
        <a href="{{ route('job-opportunities.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>إدارة الوظائف</span>
        </a>
        <a href="{{ route('company.dashboard') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-th-large fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm py-3 px-4 d-flex align-items-center gap-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <span class="fw-semibold">{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm py-3 px-4 d-flex align-items-center gap-3 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.1); color: #991b1b;">
            <i class="fas fa-exclamation-circle fs-5 text-danger"></i>
            <span class="fw-semibold">{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- بطاقة فلاتر البحث المتقدمة -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">تصفية وتحديد المرشحين</h6>
                </div>
                @if(request('opportunity_id') || request('status') || request('from_date') || request('to_date'))
                    <a href="{{ route('company.nominations') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> إعادة ضبط الفلاتر
                    </a>
                @endif
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-briefcase text-primary me-1"></i> الفرصة الوظيفية
                    </label>
                    <select class="form-select rounded-3" onchange="window.location.href = this.value">
                        <option value="{{ route('company.nominations') }}">جميع الفرص الوظيفية</option>
                        @foreach($opportunities as $opportunity)
                            <option value="{{ route('company.nominations', array_merge(request()->query(), ['opportunity_id' => $opportunity->id])) }}" 
                                {{ request('opportunity_id') == $opportunity->id ? 'selected' : '' }}>
                                {{ $opportunity->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-tasks text-primary me-1"></i> حالة الترشيح
                    </label>
                    <select class="form-select rounded-3" onchange="window.location.href = this.value">
                        <option value="{{ route('company.nominations') }}">جميع الحالات</option>
                        @php
                            $statuses = [
                                'pending' => 'قيد المراجعة',
                                'sent_to_company' => 'مرسل للشركة',
                                'under_review' => 'قيد الدراسة',
                                'interview_scheduled' => 'مقابلة مجدولة',
                                'accepted' => 'مقبول للتوظيف',
                                'rejected' => 'مرفوض',
                                'withdrawn' => 'ملغي'
                            ];
                        @endphp
                        @foreach($statuses as $value => $text)
                            <option value="{{ route('company.nominations', array_merge(request()->query(), ['status' => $value])) }}" 
                                {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $text }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5" for="from_date">
                        <i class="fas fa-calendar-alt text-primary me-1"></i> من تاريخ
                    </label>
                    <input type="date" id="from_date" name="from_date" class="form-control rounded-3" 
                           value="{{ request('from_date') }}" 
                           onchange="applyDateFilter()">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5" for="to_date">
                        <i class="fas fa-calendar-check text-primary me-1"></i> إلى تاريخ
                    </label>
                    <input type="date" id="to_date" name="to_date" class="form-control rounded-3" 
                           value="{{ request('to_date') }}" 
                           onchange="applyDateFilter()">
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المرشحين والمتقدمين -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(30, 58, 138, 0.08); color: #1e3a8a;">
                    <i class="fas fa-users fs-6"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6">قائمة المرشحين والمتقدمين</h5>
                    <small class="text-muted">إدارة وتقييم الخريجين المتقدمين لفرص العمل الخاصة بشركتكم</small>
                </div>
            </div>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-1.5 fw-bold">
                إجمالي النتائج: {{ $nominations->count() }} مرشح
            </span>
        </div>

        <div class="card-body p-0">
            @if($nominations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4 py-3" style="width: 60px;">#</th>
                                <th class="py-3">المرشح (الخريج)</th>
                                <th class="py-3">الفرصة الوظيفية</th>
                                <th class="py-3">تاريخ التقدم / الترشيح</th>
                                <th class="py-3 text-center">الحالة</th>
                                <th class="pe-4 py-3 text-center" style="width: 160px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nominations as $nomination)
                                @php
                                    $statusBadges = [
                                        'pending' => ['قيد المراجعة', 'warning'],
                                        'sent_to_company' => ['مرسل للشركة', 'info'],
                                        'under_review' => ['قيد الدراسة', 'primary'],
                                        'interview_scheduled' => ['مقابلة مجدولة', 'indigo'],
                                        'accepted' => ['مقبول', 'success'],
                                        'rejected' => ['مرفوض', 'danger'],
                                        'withdrawn' => ['ملغي', 'secondary'],
                                    ];
                                    $st = $statusBadges[$nomination->status] ?? [$nomination->status_text ?? $nomination->status, 'secondary'];
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3 text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width: 42px; height: 42px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); font-size: 0.95rem; box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);">
                                                {{ mb_substr($nomination->graduate->full_name ?? $nomination->graduate->name ?? 'خ', 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                                    {{ $nomination->graduate->full_name ?? $nomination->graduate->name ?? 'خريج' }}
                                                </h6>
                                                <small class="text-muted d-flex align-items-center gap-1 mt-0.5">
                                                    <i class="fas fa-graduation-cap text-warning"></i>
                                                    <span>{{ $nomination->graduate->specialization ?? $nomination->graduate->major ?? 'جامعة طرابلس' }}</span>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-dark fw-bold d-block">{{ $nomination->jobOpportunity->title ?? 'وظيفة عامة' }}</span>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $nomination->jobOpportunity->location ?? 'طرابلس' }}
                                        </small>
                                    </td>
                                    <td class="py-3 small text-muted">
                                        <i class="far fa-calendar-alt me-1 text-primary"></i>
                                        <span>{{ $nomination->nominated_at ? \Carbon\Carbon::parse($nomination->nominated_at)->format('Y/m/d') : 'مؤخراً' }}</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($st[1] === 'indigo')
                                            <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                                                <i class="fas fa-calendar-check me-1"></i>{{ $st[0] }}
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-{{ $st[1] }} bg-opacity-10 text-{{ $st[1] }} px-2.5 py-1.5 fw-bold">
                                                {{ $st[0] }}
                                            </span>
                                        @endif
                                        
                                        @if($nomination->final_status)
                                            <div class="mt-1">
                                                @if($nomination->final_status == 'hired')
                                                    <span class="badge rounded-pill bg-success text-white px-2 py-0.5 small" style="font-size: 0.72rem;">
                                                        <i class="fas fa-check-circle me-1"></i>تم التوظيف
                                                    </span>
                                                @elseif($nomination->final_status == 'not_hired')
                                                    <span class="badge rounded-pill bg-danger text-white px-2 py-0.5 small" style="font-size: 0.72rem;">
                                                        <i class="fas fa-times-circle me-1"></i>لم يتم التوظيف
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="pe-4 py-3 text-center">
                                        <a href="{{ route('company.nominations.show', $nomination->id) }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1.5" style="border-color: #1e3a8a; color: #1e3a8a;">
                                            <i class="fas fa-id-card"></i>
                                            <span>معاينة وتحديث</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 px-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background: rgba(30, 58, 138, 0.06); color: #1e3a8a; font-size: 1.8rem;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">لا يوجد مرشحون حالياً</h5>
                    <p class="text-muted small mb-4 mx-auto" style="max-width: 440px;">
                        لم يتم العثور على أي مرشحين وفق معايير التصفية المحددة، أو لم يتقدم خريجون لهذه الفرص بعد.
                    </p>
                    <a href="{{ route('job-opportunities.index') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold" style="background: #1e3a8a;">
                        <i class="fas fa-briefcase me-1.5"></i> استعراض الفرص الوظيفية
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function applyDateFilter() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    let url = new URL(window.location.href);

    if (fromDate) {
        url.searchParams.set('from_date', fromDate);
    } else {
        url.searchParams.delete('from_date');
    }

    if (toDate) {
        url.searchParams.set('to_date', toDate);
    } else {
        url.searchParams.delete('to_date');
    }

    window.location.href = url.toString();
}
</script>
@endpush
