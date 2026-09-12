@extends('layouts.app')

@section('title', 'عرض مرشح للتوظيف')
@section('page-title', 'تفاصيل وتحديث حالة المرشح')

@push('styles')
<style>
    .graduate-profile-header {
        background: linear-gradient(135deg, #0b1f3a 0%, #15386a 50%, #1e4b8a 100%);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 25px rgba(11, 31, 58, 0.2);
    }
    .graduate-profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .graduate-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .info-card {
        background: var(--bento-card-bg, rgba(255,255,255,0.03));
        border: 1px solid var(--bento-border, rgba(255,255,255,0.08));
        border-radius: 12px;
        padding: 16px;
    }
    .info-card-label {
        font-size: 0.72rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .info-card-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--bento-text, #e2e8f0);
    }
    .status-decision-panel {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }
    .status-btn {
        padding: 18px 16px;
        border-radius: 14px;
        border: 2px solid transparent;
        cursor: pointer;
        text-align: center;
        transition: all 0.25s ease;
        background: rgba(255,255,255,0.03);
        position: relative;
    }
    .status-btn:hover {
        transform: translateY(-2px);
    }
    .status-btn.accepted-btn {
        border-color: rgba(34,197,94,0.3);
    }
    .status-btn.accepted-btn:hover, .status-btn.accepted-btn.selected {
        background: rgba(34,197,94,0.15);
        border-color: #22c55e;
        box-shadow: 0 0 20px rgba(34,197,94,0.2);
    }
    .status-btn.rejected-btn {
        border-color: rgba(239,68,68,0.3);
    }
    .status-btn.rejected-btn:hover, .status-btn.rejected-btn.selected {
        background: rgba(239,68,68,0.15);
        border-color: #ef4444;
        box-shadow: 0 0 20px rgba(239,68,68,0.2);
    }
    .status-btn .btn-icon {
        font-size: 2rem;
        margin-bottom: 8px;
    }
    .status-btn .btn-label {
        font-size: 1.1rem;
        font-weight: 700;
    }
    .status-btn .btn-desc {
        font-size: 0.78rem;
        opacity: 0.7;
        margin-top: 4px;
    }
    .status-btn input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .skills-tag {
        display: inline-block;
        background: rgba(30, 58, 138, 0.08);
        border: 1px solid rgba(30, 58, 138, 0.2);
        color: #1e3a8a;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        margin: 3px;
    }
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
    .step-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .collapsible-section {
        border: 1px solid var(--bento-border, rgba(255,255,255,0.08));
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
    }
    .collapsible-header {
        background: rgba(255,255,255,0.03);
        padding: 14px 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        user-select: none;
    }
    .collapsible-header:hover {
        background: rgba(255,255,255,0.06);
    }
    .collapsible-body {
        padding: 18px;
        border-top: 1px solid var(--bento-border, rgba(255,255,255,0.08));
    }
    .bento-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    /* Dark mode adjustments for specific elements */
    [data-bs-theme="dark"] .bento-card {
        background: #1e293b;
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- الشريط الترحيبي بالهوية الموحدة للمنظومة -->
    <x-page-hero
        title="ملف المرشح: {{ $nomination->graduate->full_name ?? $nomination->graduate->name }}"
        subtitle="مراجعة السيرة الذاتية واتخاذ القرار للفرصة الوظيفية: {{ $nomination->jobOpportunity->title ?? 'فرصة وظيفية' }}"
        icon="fas fa-user-check"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'المرشحون للوظائف', 'url' => route('company.nominations')],
            ['label' => $nomination->graduate->full_name ?? $nomination->graduate->name]
        ]"
        secondaryBadge="{{ $nomination->status_text }}"
        secondaryBadgeIcon="fas fa-tag"
    >
        <a href="{{ route('company.nominations') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3.5 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-arrow-right fs-6"></i>
            <span>العودة لقائمة المرشحين</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm py-3 px-4 d-flex align-items-center gap-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <span class="fw-semibold">{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm py-3 px-4 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #991b1b;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('company.nominations.update-status', $nomination->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ======= Right Column: Graduate Info ======= --}}
            <div class="col-lg-5">

                @php
                    $grad = $nomination->graduate;
                    $gData = $grad->graduateData;
                    $major = $gData->major ?? $grad->major ?? $grad->specialization ?? 'غير محدد';
                    $gradYear = $gData->graduation_year ?? $grad->graduation_year;
                    $gpa = $gData->gpa ?? $grad->gpa;
                    $degree = $gData->degree ?? $grad->degree;
                    $email = $gData->email ?? $grad->email;
                    $phone = $gData->phone ?? $grad->phone;
                    $university = $gData->university ?? $grad->university;
                    $faculty = $gData->faculty ?? $grad->faculty;
                    $skills = $gData->skills ?? $grad->skills ?? [];
                    $languages = $gData->languages ?? $grad->languages ?? [];
                    $cvPath = $gData->cv_path ?? $grad->cv_path;
                    $linkedinUrl = $gData->linkedin_url ?? $grad->linkedin_url;
                @endphp

                {{-- Graduate Profile Header --}}
                <div class="graduate-profile-header mb-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="graduate-avatar">
                            {{ mb_substr($grad->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-white fw-bold mb-1 fs-5">{{ $grad->name }}</h4>
                            <div class="text-white-50 small">
                                <i class="fas fa-graduation-cap me-1"></i>
                                {{ $major }}
                                @if($gradYear)
                                    &nbsp;·&nbsp; {{ $gradYear }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        @if($gpa)
                        <div class="col-6">
                            <div style="background:rgba(255,255,255,0.1); border-radius:10px; padding:10px 14px;">
                                <div class="text-white-50" style="font-size:0.7rem">المعدل التراكمي</div>
                                <div class="text-white fw-bold fs-5">{{ $gpa }}%</div>
                            </div>
                        </div>
                        @endif
                        @if($degree)
                        <div class="col-6">
                            <div style="background:rgba(255,255,255,0.1); border-radius:10px; padding:10px 14px;">
                                <div class="text-white-50" style="font-size:0.7rem">الدرجة العلمية</div>
                                <div class="text-white fw-bold">{{ $degree }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Graduate Details --}}
                <div class="bento-card p-4 mb-4" style="background: var(--bento-card-bg, rgba(255,255,255,0.03)); border-color: var(--bento-border, rgba(255,255,255,0.08));">
                    <h6 class="fw-bold mb-3" style="color: var(--bento-text);">
                        <i class="fas fa-id-card me-2 text-primary"></i>
                        معلومات المرشح
                    </h6>

                    <div class="info-grid" style="grid-template-columns: 1fr 1fr;">
                        @if($email)
                        <div class="info-card">
                            <div class="info-card-label"><i class="fas fa-envelope"></i> البريد الإلكتروني</div>
                            <div class="info-card-value" style="font-size:0.82rem; word-break:break-all;">{{ $email }}</div>
                        </div>
                        @endif

                        @if($phone)
                        <div class="info-card">
                            <div class="info-card-label"><i class="fas fa-phone"></i> الهاتف</div>
                            <div class="info-card-value">{{ $phone }}</div>
                        </div>
                        @endif

                        @if($university)
                        <div class="info-card">
                            <div class="info-card-label"><i class="fas fa-university"></i> الجامعة</div>
                            <div class="info-card-value">{{ $university }}</div>
                        </div>
                        @endif

                        @if($faculty)
                        <div class="info-card">
                            <div class="info-card-label"><i class="fas fa-building-columns"></i> الكلية</div>
                            <div class="info-card-value">{{ $faculty }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- Skills --}}
                    @if($skills && count($skills) > 0)
                    <div class="mt-3">
                        <div class="info-card-label mb-2"><i class="fas fa-tools"></i> المهارات</div>
                        <div>
                            @foreach($skills as $skill)
                                <span class="skills-tag">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Languages --}}
                    @if($languages && count($languages) > 0)
                    <div class="mt-3">
                        <div class="info-card-label mb-2"><i class="fas fa-language"></i> اللغات</div>
                        <div>
                            @foreach($languages as $lang)
                                <span class="skills-tag" style="background:rgba(139,92,246,0.15); border-color:rgba(139,92,246,0.3); color:#c4b5fd;">{{ $lang }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-4 d-flex gap-2">
                        @if($linkedinUrl)
                        <a href="{{ $linkedinUrl }}" target="_blank" class="btn btn-sm" style="background: #0077b5; color: white;">
                            <i class="fab fa-linkedin me-1"></i> LinkedIn
                        </a>
                        @endif

                        @if($cvPath)
                        <a href="{{ Storage::url($cvPath) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="color: var(--bento-text); border-color: var(--bento-border);">
                            <i class="fas fa-file-pdf me-1"></i> عرض السيرة الذاتية
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Job Opportunity Info --}}
                <div class="bento-card p-4" style="background: var(--bento-card-bg, rgba(255,255,255,0.03)); border-color: var(--bento-border, rgba(255,255,255,0.08));">
                    <h6 class="fw-bold mb-3" style="color: var(--bento-text);">
                        <i class="fas fa-briefcase me-2 text-warning"></i>
                        الفرصة الوظيفية المرشح لها
                    </h6>
                    <div class="fw-semibold fs-6 mb-2" style="color: var(--bento-text);">{{ $nomination->jobOpportunity->title }}</div>
                    @if($nomination->jobOpportunity->location)
                        <div class="small mb-2" style="color: #94a3b8;">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $nomination->jobOpportunity->location }}
                        </div>
                    @endif
                    @if($nomination->nominated_at)
                        <hr style="border-color: var(--bento-border);">
                        <div class="small" style="color: #94a3b8;">
                            <i class="fas fa-calendar me-1"></i>
                            تاريخ التقدم: {{ $nomination->nominated_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ======= Left Column: Status Update Form ======= --}}
            <div class="col-lg-7">

                <div class="bento-card p-4 mb-4" style="background: var(--bento-card-bg, rgba(255,255,255,0.03)); border-color: var(--bento-border, rgba(255,255,255,0.08));">
                    <div class="step-indicator">
                        <div class="step-badge">1</div>
                        <h6 class="fw-bold mb-0" style="color: var(--bento-text);">حالة التقديم الحالية</h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color: #94a3b8;">حالة التقديم</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                            style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: var(--bento-text); border-radius:10px;">
                            <option value="pending" {{ $nomination->status == 'pending' ? 'selected' : '' }}>⏳ قيد المراجعة</option>
                            <option value="under_review" {{ $nomination->status == 'under_review' ? 'selected' : '' }}>🔍 قيد الدراسة (لدينا)</option>
                            <option value="interview_scheduled" {{ $nomination->status == 'interview_scheduled' ? 'selected' : '' }}>📅 مقابلة مجدولة</option>
                            <option value="accepted" {{ $nomination->status == 'accepted' ? 'selected' : '' }}>✅ مجتاز (تأهيل مبدئي)</option>
                            <option value="rejected" {{ $nomination->status == 'rejected' ? 'selected' : '' }}>❌ مرفوض (استبعاد)</option>
                        </select>
                        <div class="form-text" style="color: #64748b; font-size: 0.8rem;">استخدم هذه الحالة لتتبع مرحلة المرشح في عملية التوظيف.</div>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Employment Decision --}}
                <div class="bento-card p-4 mb-4" style="background: var(--bento-card-bg, rgba(255,255,255,0.03)); border-color: var(--bento-border, rgba(255,255,255,0.08));">
                    <div class="step-indicator">
                        <div class="step-badge">2</div>
                        <h6 class="fw-bold mb-0" style="color: var(--bento-text);">قرار التوظيف النهائي</h6>
                    </div>

                    <div class="status-decision-panel">
                        <label class="status-btn accepted-btn {{ $nomination->final_status == 'hired' ? 'selected' : '' }}" id="accepted-btn-label">
                            <input type="radio" name="final_status" value="hired"
                                {{ $nomination->final_status == 'hired' ? 'checked' : '' }}
                                onchange="selectStatusBtn(this)">
                            <div class="btn-icon">✅</div>
                            <div class="btn-label text-success">مقبول / تم التوظيف</div>
                            <div class="btn-desc text-white-50">تم اختيار المرشح وتوظيفه بالوظيفة</div>
                        </label>

                        <label class="status-btn rejected-btn {{ $nomination->final_status == 'not_hired' ? 'selected' : '' }}" id="rejected-btn-label">
                            <input type="radio" name="final_status" value="not_hired"
                                {{ $nomination->final_status == 'not_hired' ? 'checked' : '' }}
                                onchange="selectStatusBtn(this)">
                            <div class="btn-icon">❌</div>
                            <div class="btn-label text-danger">مرفوض / لم يتم التوظيف</div>
                            <div class="btn-desc text-white-50">تم رفض المرشح لعدم المطابقة</div>
                        </label>
                    </div>

                    <div class="text-center">
                        <label class="d-inline-flex align-items-center gap-2 small cursor-pointer" style="color: #94a3b8;">
                            <input type="radio" name="final_status" value="in_progress"
                                {{ ($nomination->final_status == 'in_progress' || !$nomination->final_status) ? 'checked' : '' }}
                                onchange="selectStatusBtn(this)">
                            لا يزال قيد المعالجة ولم يتم اتخاذ قرار نهائي
                        </label>
                    </div>
                </div>

                {{-- Interview Details (Collapsible) --}}
                <div class="collapsible-section mb-4">
                    <div class="collapsible-header" onclick="toggleSection('interview-section', this)">
                        <div class="d-flex align-items-center gap-2">
                            <div class="step-badge">3</div>
                            <span class="fw-semibold" style="color: var(--bento-text);">تفاصيل المقابلة</span>
                            @if($nomination->interview_date)
                                <span class="badge" style="background:rgba(59,130,246,0.2); color:#93c5fd; border:1px solid rgba(59,130,246,0.3); border-radius:20px; padding:2px 10px; font-size:0.7rem;">
                                    {{ $nomination->interview_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                        <i class="fas fa-chevron-down transition-icon" id="interview-section-icon" style="color: #94a3b8;"></i>
                    </div>
                    <div class="collapsible-body" id="interview-section" style="display: {{ $nomination->interview_date ? 'block' : 'none' }};">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small" style="color: #94a3b8;">تاريخ المقابلة</label>
                                <input type="date" name="interview_date" class="form-control"
                                    value="{{ old('interview_date', optional($nomination->interview_date)->format('Y-m-d')) }}"
                                    style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: var(--bento-text); border-radius:10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small" style="color: #94a3b8;">وقت المقابلة</label>
                                <input type="text" name="interview_time" class="form-control" placeholder="مثال: 10:00 صباحاً"
                                    value="{{ old('interview_time', $nomination->interview_time) }}"
                                    style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: var(--bento-text); border-radius:10px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label small" style="color: #94a3b8;">مكان المقابلة</label>
                                <input type="text" name="interview_location" class="form-control" placeholder="عنوان أو رابط اجتماع..."
                                    value="{{ old('interview_location', $nomination->interview_location) }}"
                                    style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: var(--bento-text); border-radius:10px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label small" style="color: #94a3b8;">ملاحظات المقابلة (تظهر للخريج)</label>
                                <textarea name="interview_notes" rows="3" class="form-control" placeholder="أي ملاحظات عن المقابلة..."
                                    style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: var(--bento-text); border-radius:10px;">{{ old('interview_notes', $nomination->interview_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Feedback & Notes (Collapsible) --}}
                <div class="collapsible-section mb-4">
                    <div class="collapsible-header" onclick="toggleSection('feedback-section', this)">
                        <div class="d-flex align-items-center gap-2">
                            <div class="step-badge">4</div>
                            <span class="fw-semibold" style="color: var(--bento-text);">ملاحظات التقييم</span>
                        </div>
                        <i class="fas fa-chevron-down transition-icon" id="feedback-section-icon" style="color: #94a3b8;"></i>
                    </div>
                    <div class="collapsible-body" id="feedback-section" style="display:none;">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small" style="color: #94a3b8;">ملاحظات الشركة عن المرشح (داخلية)</label>
                                <textarea name="nomination_notes" rows="4" class="form-control" placeholder="أضف انطباعاتك وملاحظاتك الفنية والشخصية عن المرشح هنا..."
                                    style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: var(--bento-text); border-radius:10px;">{{ old('nomination_notes', $nomination->nomination_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-3">
                    <button type="submit" class="btn flex-grow-1 py-3 text-white fw-bold shadow-sm" style="font-size:1rem; background: linear-gradient(135deg, #1e3a8a, #2563eb); border-radius: 12px; border: none;">
                        <i class="fas fa-save me-2"></i>
                        حفظ التحديثات
                    </button>
                    <a href="{{ route('company.nominations') }}" class="btn btn-outline-secondary px-4 py-3" style="border-radius: 12px;">
                        <i class="fas fa-arrow-right me-1"></i>
                        رجوع
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

        // Form select styling fix based on theme
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        if(isDarkMode) {
            document.querySelectorAll('select.form-select option').forEach(opt => {
                opt.style.background = '#1e293b';
                opt.style.color = '#e2e8f0';
            });
        }
    });
</script>
@endpush
