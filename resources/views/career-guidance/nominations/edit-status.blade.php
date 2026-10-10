@extends('layouts.app')

@section('title', 'تعديل حالة الترشيح')

@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : (request()->routeIs('partnership.*') ? 'partnership' : 'career-guidance');
@endphp

@push('styles')
<style>
    .status-decision-panel {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
    }
    .status-btn {
        padding: 16px 14px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
        background: #f8fafc;
        position: relative;
    }
    .status-btn:hover {
        transform: translateY(-2px);
    }
    .status-btn.accepted-btn {
        border-color: #86efac;
    }
    .status-btn.accepted-btn:hover, .status-btn.accepted-btn.selected {
        background: #f0fdf4;
        border-color: #22c55e;
        box-shadow: 0 4px 12px rgba(34,197,94,0.15);
    }
    .status-btn.rejected-btn {
        border-color: #fca5a5;
    }
    .status-btn.rejected-btn:hover, .status-btn.rejected-btn.selected {
        background: #fef2f2;
        border-color: #ef4444;
        box-shadow: 0 4px 12px rgba(239,68,68,0.15);
    }
    .status-btn .btn-icon {
        font-size: 1.6rem;
        margin-bottom: 6px;
    }
    .status-btn .btn-label {
        font-size: 0.95rem;
        font-weight: 700;
    }
    .status-btn .btn-desc {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 4px;
    }
    .status-btn input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .collapsible-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
        background: #ffffff;
    }
    .collapsible-card-header {
        background: #f8fafc;
        padding: 14px 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        user-select: none;
    }
    .collapsible-card-header:hover {
        background: #f1f5f9;
    }
    .collapsible-card-body {
        padding: 18px;
        border-top: 1px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة التحكم', 'url' => Route::has($routePrefix . '.dashboard') ? route($routePrefix . '.dashboard') : route('home')],
            ['label' => 'إدارة الترشيحات', 'url' => Route::has($routePrefix . '.nominations') ? route($routePrefix . '.nominations') : '#'],
            ['label' => 'تعديل حالة الترشيح', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-edit me-2"></i> تعديل حالة الترشيح
            </h2>
            <div class="text-muted small mt-1">
                المرشح: <strong class="text-dark">{{ $nomination->graduate?->name ?? 'غير محدد' }}</strong> &bull; الفرصة: <strong class="text-dark">{{ $nomination->jobOpportunity?->title ?? 'غير محدد' }}</strong>
            </div>
        </div>
        <a href="{{ Route::has($routePrefix . '.nominations') ? route($routePrefix . '.nominations') : '#' }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> العودة للترشيحات
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route($routePrefix . '.nominations.update-status-fullpage', $nomination->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ======= Right Column: Graduate & Opportunity Cards ======= --}}
            <div class="col-lg-5">

                {{-- Graduate Details Card --}}
                <div class="card-modern mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-user-graduate me-2"></i>بيانات الخريج
                        </h5>
                        @if($nomination->graduate)
                            @php
                                $gradRoute = Route::has($routePrefix . '.graduates.show')
                                    ? route($routePrefix . '.graduates.show', $nomination->graduate->id)
                                    : (Route::has('career-guidance.graduates.show') ? route('career-guidance.graduates.show', $nomination->graduate->id) : null);
                            @endphp
                            @if($gradRoute)
                            <a href="{{ $gradRoute }}" class="btn btn-sm btn-outline-info-modern">
                                <i class="fas fa-external-link-alt me-1"></i> البروفايل
                            </a>
                            @endif
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-weight: 700; font-size: 1.2rem;">
                                {{ mb_substr($nomination->graduate?->name ?? 'خ', 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $nomination->graduate?->name ?? 'غير محدد' }}</h6>
                                <div class="text-muted small">{{ $nomination->graduate?->major ?? 'خريج' }} @if($nomination->graduate?->graduation_year) ({{ $nomination->graduate->graduation_year }}) @endif</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            @if($nomination->graduate?->email)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-envelope me-1"></i> البريد الإلكتروني</small>
                                    <span class="fw-bold text-dark text-break small">{{ $nomination->graduate->email }}</span>
                                </div>
                            </div>
                            @endif

                            @if($nomination->graduate?->phone)
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-phone me-1"></i> الهاتف</small>
                                    <span class="fw-bold text-dark small">{{ $nomination->graduate->phone }}</span>
                                </div>
                            </div>
                            @endif

                            @if($nomination->graduate?->gpa)
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-star me-1 text-warning"></i> المعدل التراكمي</small>
                                    <span class="fw-bold text-dark small">{{ $nomination->graduate->gpa }}%</span>
                                </div>
                            </div>
                            @endif

                            @if($nomination->graduate?->university)
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-university me-1"></i> الجامعة</small>
                                    <span class="fw-bold text-dark small">{{ $nomination->graduate->university }}</span>
                                </div>
                            </div>
                            @endif

                            @if($nomination->graduate?->national_id)
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-id-badge me-1"></i> رقم القيد الجامعي</small>
                                    <span class="fw-bold text-dark small">{{ $nomination->graduate->national_id }}</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Skills --}}
                        @if(!empty($nomination->graduate?->skills))
                            @php
                                $skills = is_array($nomination->graduate->skills) ? $nomination->graduate->skills : explode(',', $nomination->graduate->skills);
                            @endphp
                            @if(count($skills) > 0)
                            <div class="mt-3">
                                <small class="text-muted d-block mb-2 fw-bold"><i class="fas fa-tools me-1"></i> المهارات:</small>
                                <div>
                                    @foreach($skills as $skill)
                                        @if(trim($skill))
                                            <span class="badge bg-light text-primary border border-primary px-2 py-1 rounded-pill me-1 mb-1">{{ trim($skill) }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endif

                        {{-- Links --}}
                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            @if($nomination->graduate?->cv_path)
                                <a href="{{ Storage::url($nomination->graduate->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-file-pdf me-1"></i> السيرة الذاتية
                                </a>
                            @endif
                            @if($nomination->graduate?->linkedin_url)
                                <a href="{{ $nomination->graduate->linkedin_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fab fa-linkedin me-1"></i> LinkedIn
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Job Opportunity Card --}}
                <div class="card-modern mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-briefcase me-2"></i>فرصة العمل المرشح لها
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="p-3 bg-light rounded-3">
                            <div class="fw-bold text-dark fs-6 mb-1">{{ $nomination->jobOpportunity?->title ?? 'غير محدد' }}</div>
                            @if($nomination->jobOpportunity?->company)
                                <div class="text-primary fw-semibold small mb-2">
                                    <i class="fas fa-building me-1"></i> {{ $nomination->jobOpportunity->company->name }}
                                </div>
                            @endif
                            @if($nomination->jobOpportunity?->location)
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ $nomination->jobOpportunity->location }}
                                </div>
                            @endif
                            @if($nomination->nominated_at)
                                <hr class="my-2 border-secondary border-opacity-25">
                                <div class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i> تاريخ الترشيح: {{ $nomination->nominated_at->format('Y-m-d') }}
                                </div>
                            @endif
                            @if($nomination->nominator)
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-user-tie me-1"></i> رشّحه: {{ $nomination->nominator->name }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ======= Left Column: Status Update Form ======= --}}
            <div class="col-lg-7">

                {{-- Step 1: Nomination Status --}}
                <div class="card-modern mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-tasks me-2"></i>1. حالة الترشيح الإدارية
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-2">
                            <label for="status" class="form-label-modern">المرحلة الإدارية الحالية للترشيح <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select-modern @error('status') is-invalid @enderror">
                                <option value="pending" {{ $nomination->status == 'pending' ? 'selected' : '' }}>⏳ قيد المراجعة</option>
                                <option value="sent_to_company" {{ $nomination->status == 'sent_to_company' ? 'selected' : '' }}>📤 مرسل للشركة</option>
                                <option value="under_review" {{ $nomination->status == 'under_review' ? 'selected' : '' }}>🔍 قيد الدراسة</option>
                                <option value="interview_scheduled" {{ $nomination->status == 'interview_scheduled' ? 'selected' : '' }}>📅 مقابلة مجدولة</option>
                                <option value="accepted" {{ $nomination->status == 'accepted' ? 'selected' : '' }}>✅ مقبول</option>
                                <option value="rejected" {{ $nomination->status == 'rejected' ? 'selected' : '' }}>❌ مرفوض</option>
                                <option value="withdrawn" {{ $nomination->status == 'withdrawn' ? 'selected' : '' }}>↩️ مسحوب</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Step 2: Final Employment Decision --}}
                <div class="card-modern mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-check-double me-2"></i>2. قرار التوظيف النهائي
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="status-decision-panel">
                            <label class="status-btn accepted-btn {{ $nomination->final_status == 'hired' ? 'selected' : '' }}" id="accepted-btn-label">
                                <input type="radio" name="final_status" value="hired"
                                    {{ $nomination->final_status == 'hired' ? 'checked' : '' }}
                                    onchange="selectStatusBtn(this)">
                                <div class="btn-icon">✅</div>
                                <div class="btn-label text-success">مقبول / تم التوظيف</div>
                                <div class="btn-desc">الخريج حصل على الوظيفة بنجاح</div>
                            </label>

                            <label class="status-btn rejected-btn {{ $nomination->final_status == 'not_hired' ? 'selected' : '' }}" id="rejected-btn-label">
                                <input type="radio" name="final_status" value="not_hired"
                                    {{ $nomination->final_status == 'not_hired' ? 'checked' : '' }}
                                    onchange="selectStatusBtn(this)">
                                <div class="btn-icon">❌</div>
                                <div class="btn-label text-danger">مرفوض / لم يتم التوظيف</div>
                                <div class="btn-desc">لم يُوظَّف الخريج في هذه الفرصة</div>
                            </label>
                        </div>

                        <div class="text-center pt-2">
                            <label class="d-inline-flex align-items-center gap-2 text-muted small cursor-pointer">
                                <input type="radio" name="final_status" value="in_progress"
                                    {{ ($nomination->final_status == 'in_progress' || !$nomination->final_status) ? 'checked' : '' }}
                                    onchange="selectStatusBtn(this)">
                                <span>لا يزال قيد المتابعة والمعالجة</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Interview Details (Collapsible Card) --}}
                <div class="collapsible-card mb-4">
                    <div class="collapsible-card-header" onclick="toggleSection('interview-section', this)">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-primary fw-bold"><i class="fas fa-calendar-alt me-2"></i>3. تفاصيل وموعد المقابلة</span>
                            @if($nomination->interview_date)
                                <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1 small">
                                    {{ $nomination->interview_date->format('Y-m-d') }}
                                </span>
                            @endif
                        </div>
                        <i class="fas fa-chevron-down text-muted transition-icon" id="interview-section-icon"></i>
                    </div>
                    <div class="collapsible-card-body" id="interview-section" style="display: {{ $nomination->interview_date ? 'block' : 'none' }};">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-modern">تاريخ المقابلة</label>
                                <input type="date" name="interview_date" class="form-control-modern"
                                    value="{{ old('interview_date', optional($nomination->interview_date)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">وقت المقابلة</label>
                                <input type="text" name="interview_time" class="form-control-modern" placeholder="مثال: 10:00 صباحاً"
                                    value="{{ old('interview_time', $nomination->interview_time) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label-modern">مكان المقابلة أو رابط الاجتماع</label>
                                <input type="text" name="interview_location" class="form-control-modern" placeholder="عنوان المقر أو رابط Google Meet / Zoom..."
                                    value="{{ old('interview_location', $nomination->interview_location) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label-modern">ملاحظات المقابلة</label>
                                <textarea name="interview_notes" rows="3" class="form-control-modern" placeholder="أي ملاحظات حول أداء الخريج أو متطلبات المقابلة...">{{ old('interview_notes', $nomination->interview_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 4: Feedback & Notes (Collapsible Card) --}}
                <div class="collapsible-card mb-4">
                    <div class="collapsible-card-header" onclick="toggleSection('feedback-section', this)">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-primary fw-bold"><i class="fas fa-comment-dots me-2"></i>4. الملاحظات والتقييم ومبررات الترشيح</span>
                        </div>
                        <i class="fas fa-chevron-down text-muted transition-icon" id="feedback-section-icon"></i>
                    </div>
                    <div class="collapsible-card-body" id="feedback-section" style="display:none;">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-modern">ملاحظات الترشيح</label>
                                <textarea name="nomination_notes" rows="2" class="form-control-modern" placeholder="ملاحظات الترشيح العامة...">{{ old('nomination_notes', $nomination->nomination_notes) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label-modern">أسباب ومبررات المطابقة مع الوظيفة</label>
                                <textarea name="matching_reasons" rows="2" class="form-control-modern" placeholder="أسباب اختيار هذا الخريج للوظيفة...">{{ old('matching_reasons', $nomination->matching_reasons) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">ملاحظات وتقييم الشركة</label>
                                <textarea name="company_feedback" rows="3" class="form-control-modern" placeholder="ملاحظات مستلمة من ممثل الشركة...">{{ old('company_feedback', $nomination->company_feedback) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">ملاحظات وتغذية راجعة من الخريج</label>
                                <textarea name="graduate_feedback" rows="3" class="form-control-modern" placeholder="ملاحظات مقدمة من الخريج حول الفرصة...">{{ old('graduate_feedback', $nomination->graduate_feedback) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-3 align-items-center pt-2">
                    <button type="submit" class="btn btn-primary-modern flex-grow-1 py-3">
                        <i class="fas fa-save me-2"></i> حفظ التحديثات
                    </button>
                    <a href="{{ Route::has($routePrefix . '.nominations') ? route($routePrefix . '.nominations') : '#' }}" class="btn btn-outline-secondary px-4 py-3">
                        <i class="fas fa-arrow-right me-1"></i> رجوع
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Status button selection
    function selectStatusBtn(radio) {
        document.querySelectorAll('.status-btn').forEach(btn => btn.classList.remove('selected'));
        const label = radio.closest('label.status-btn');
        if (label) label.classList.add('selected');
    }

    // Collapsible sections
    function toggleSection(sectionId, headerEl) {
        const section = document.getElementById(sectionId);
        const icon = headerEl.querySelector('.transition-icon');
        if (section.style.display === 'none') {
            section.style.display = 'block';
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            section.style.display = 'none';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    // Set icon rotation for pre-open sections
    document.addEventListener('DOMContentLoaded', function() {
        const interviewSection = document.getElementById('interview-section');
        if (interviewSection && interviewSection.style.display !== 'none') {
            const icon = document.getElementById('interview-section-icon');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    });
</script>
@endpush


