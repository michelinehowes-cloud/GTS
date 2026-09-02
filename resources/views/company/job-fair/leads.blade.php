@extends('layouts.app')

@section('title', 'السير الذاتية المستلمة - ' . $fair->title)
@section('page-title', 'السير الذاتية المستلمة')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="bento-card mb-4 p-0 overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-4"
             style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-users text-white" style="font-size:1.5rem;"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-1">السير الذاتية المستلمة</h4>
                    <p class="text-white mb-0" style="opacity:.8;font-size:.9rem;">
                        <i class="fas fa-briefcase me-1"></i>{{ $fair->title }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('company.job-fairs.scanner', $fair->id) }}"
                   class="btn btn-light fw-bold rounded-pill px-4" style="color:#1e3a8a;">
                    <i class="fas fa-qrcode me-2"></i>ماسح السير
                </a>
                <a href="{{ route('company.job-fairs.index') }}"
                   class="btn rounded-pill px-4 fw-bold" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-arrow-right me-2"></i>رجوع
                </a>
            </div>
        </div>
        {{-- Stats Bar --}}
        <div class="d-flex border-top" style="border-color:rgba(0,0,0,0.05) !important;">
            <div class="flex-fill text-center py-3 px-2" style="border-left:1px solid rgba(0,0,0,0.05);">
                <div class="fw-bold fs-4" style="color:#1e3a8a;">{{ $visits->total() }}</div>
                <div class="text-muted small">إجمالي السير</div>
            </div>
            <div class="flex-fill text-center py-3 px-2" style="border-left:1px solid rgba(0,0,0,0.05);">
                <div class="fw-bold fs-4 text-success">{{ $visits->filter(fn($v) => $v->graduate?->gpa >= 3.5)->count() }}</div>
                <div class="text-muted small">معدل ≥ 3.5</div>
            </div>
            <div class="flex-fill text-center py-3 px-2">
                <div class="fw-bold fs-4 text-info">{{ $visits->filter(fn($v) => $v->graduate?->graduateData?->cv_path)->count() }}</div>
                <div class="text-muted small">لديهم سيرة ذاتية</div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    @if($visits->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-user-slash"></i>
            </div>
            <h4>لم تستلم أي سير ذاتية بعد!</h4>
            <p>استخدم ماسح السير الذاتية لجمع بيانات الخريجين في المعرض.</p>
            <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="btn btn-primary-modern px-5 mt-2">
                <i class="fas fa-qrcode me-2"></i>افتح الماسح
            </a>
        </div>
    @else
        {{-- Cards Grid --}}
        <div class="row g-4">
            @foreach($visits as $visit)
            @php $grad = $visit->graduate; @endphp
            <div class="col-xl-4 col-md-6">
                <div class="bento-card h-100 lead-card" style="cursor:default;">
                    {{-- Card Top --}}
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-circle me-3 flex-shrink-0" style="width:52px;height:52px;font-size:1.3rem;">
                            {{ mb_substr($grad->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark" style="font-size:1rem;">{{ $grad->name }}</div>
                            <div class="text-muted small text-truncate">{{ $grad->email }}</div>
                        </div>
                        @if($grad->gpa)
                        <div class="flex-shrink-0">
                            <span class="badge rounded-pill px-3 py-2 fw-bold"
                                  style="background:#f1f5f9; color:#0f172a; border: 1px solid #e2e8f0;">
                                <i class="fas fa-star text-warning me-1"></i>{{ number_format($grad->gpa, 2) }}%
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Info Grid --}}
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="rounded-3 p-2" style="background:#f8fafc;">
                                <div class="text-muted" style="font-size:.7rem;">التخصص</div>
                                <div class="fw-bold text-dark small text-truncate">{{ $grad->major ?? $grad->specialization ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 p-2" style="background:#f8fafc;">
                                <div class="text-muted" style="font-size:.7rem;">سنة التخرج</div>
                                <div class="fw-bold text-dark small">{{ $grad->graduation_year ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 p-2" style="background:#f8fafc;">
                                <div class="text-muted" style="font-size:.7rem;">الجامعة</div>
                                <div class="fw-bold text-dark small text-truncate">{{ $grad->university ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 p-2" style="background:#f8fafc;">
                                <div class="text-muted" style="font-size:.7rem;">وقت الزيارة</div>
                                <div class="fw-bold text-dark small">{{ $visit->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2 mt-auto pt-2 border-top" style="border-color:rgba(0,0,0,0.04) !important;">
                        <button type="button"
                                class="btn btn-sm flex-fill fw-bold rounded-pill"
                                style="background:#eff6ff;color:#1e3a8a;border:none;"
                                data-bs-toggle="modal"
                                data-bs-target="#leadModal{{ $visit->id }}">
                            <i class="fas fa-eye me-1"></i>التفاصيل
                        </button>
                        <a href="{{ route('messages.show', $grad->id) }}"
                           class="btn btn-sm flex-fill fw-bold rounded-pill"
                           style="background:#f0fdf4;color:#166534;border:none;">
                            <i class="fas fa-envelope me-1"></i>مراسلة
                        </a>
                        @if($grad->graduateData?->cv_path)
                        <a href="{{ Storage::url($grad->graduateData->cv_path) }}"
                           target="_blank"
                           class="btn btn-sm btn-icon rounded-circle"
                           style="background:#fdf4ff;color:#7c3aed;border:none;width:34px;height:34px;"
                           title="تحميل السيرة الذاتية">
                            <i class="fas fa-download"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $visits->links() }}
        </div>
    @endif
</div>

{{-- Detail Modals --}}
@foreach($visits as $visit)
@php 
    $grad = $visit->graduate;
    $gData = $grad->graduateData;
    $major = $gData->major ?? $grad->major ?? $grad->specialization ?? '—';
    $university = $gData->university ?? $grad->university ?? '—';
    $gradYear = $gData->graduation_year ?? $grad->graduation_year ?? '—';
    $gpa = $gData->gpa ?? $grad->gpa;
    $phone = $gData->phone ?? $grad->phone ?? '—';
@endphp
<div class="modal fade" id="leadModal{{ $visit->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            {{-- Modal Header --}}
            <div class="modal-header p-0 border-0">
                <div class="w-100" style="background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%);padding:1.5rem 1.5rem 3rem;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;color:#fff;border:2px solid rgba(255,255,255,0.4);">
                                {{ mb_substr($grad->name, 0, 1) }}
                            </div>
                            <div>
                                <h5 class="fw-bold text-white mb-1">{{ $grad->name }}</h5>
                                <p class="text-white mb-0 small" style="opacity:.8;">
                                    <i class="fas fa-envelope me-1"></i>{{ $grad->email }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn text-white opacity-75" data-bs-dismiss="modal" style="border:none;background:rgba(255,255,255,0.15);border-radius:50%;width:36px;height:36px;padding:0;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-0">

                {{-- Pull-up info cards --}}
                <div class="px-4" style="margin-top:-1.75rem;">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="bento-card text-center py-3 px-2 m-0">
                                <div class="text-muted small mb-1">سنة التخرج</div>
                                <div class="fw-bold text-dark fs-5">{{ $gradYear }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bento-card text-center py-3 px-2 m-0">
                                <div class="text-muted small mb-1">المعدل التراكمي (%)</div>
                                @if($gpa)
                                    <div class="fw-bold fs-5 text-primary">
                                        {{ number_format($gpa, 2) }}%
                                    </div>
                                @else
                                    <div class="fw-bold text-muted fs-5">—</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bento-card text-center py-3 px-2 m-0">
                                <div class="text-muted small mb-1">وقت الزيارة</div>
                                <div class="fw-bold text-dark fs-5">{{ $visit->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Details Section --}}
                <div class="p-4 pt-3">

                    {{-- Basic Info --}}
                    <h6 class="fw-bold text-dark mb-3 mt-2">
                        <i class="fas fa-info-circle me-2 text-primary"></i>المعلومات الأساسية
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-graduation-cap text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:.75rem;">التخصص</div>
                                    <div class="fw-bold text-dark small">{{ $major }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-university text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:.75rem;">الجامعة</div>
                                    <div class="fw-bold text-dark small">{{ $university }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-phone text-success"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:.75rem;">رقم الهاتف</div>
                                    <div class="fw-bold text-dark small" dir="ltr">{{ $phone }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#fdf4ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-calendar text-purple" style="color:#7c3aed;"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:.75rem;">تاريخ الزيارة</div>
                                    <div class="fw-bold text-dark small">{{ $visit->created_at->format('Y-m-d') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trainings --}}
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-certificate me-2 text-warning"></i>الدورات التدريبية المنجزة
                    </h6>
                    @php
                        $trainings = collect();
                        if(isset($grad->trainingApplications)) {
                            $trainings = $grad->trainingApplications;
                        }
                    @endphp

                    @if($trainings->isEmpty())
                        <div class="text-center py-3 rounded-3" style="background:#f8fafc;">
                            <i class="fas fa-info-circle text-muted mb-2 d-block" style="font-size:1.5rem;"></i>
                            <p class="text-muted small mb-0">لم يحضر أي دورات تدريبية بعد.</p>
                        </div>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($trainings as $app)
                                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                                     style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                    <i class="fas fa-check-circle text-success" style="font-size:.8rem;"></i>
                                    <span class="fw-bold small text-dark">{{ $app->training->title ?? 'دورة تدريبية' }}</span>
                                    <span class="text-muted" style="font-size:.75rem;">
                                        {{ $app->attended_at ? $app->attended_at->format('Y-m-d') : '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Update Outcome Form --}}
                    <h6 class="fw-bold text-dark mt-4 mb-3">
                        <i class="fas fa-tasks me-2 text-primary"></i>حالة التوظيف (نتيجة المقابلة)
                    </h6>
                    <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                        <form action="{{ route('company.job-fairs.visit-outcome', $visit->id) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2 align-items-center">
                                <select name="outcome" class="form-select flex-grow-1 border-0 shadow-sm rounded-3">
                                    <option value="pending" {{ $visit->status == 'pending' ? 'selected' : '' }}>⏳ قيد المراجعة</option>
                                    <option value="shortlisted" {{ $visit->status == 'shortlisted' ? 'selected' : '' }}>⭐ مدرج في القائمة القصيرة</option>
                                    <option value="accepted" {{ $visit->status == 'accepted' ? 'selected' : '' }}>✅ مقبول مبدئياً</option>
                                    <option value="rejected" {{ $visit->status == 'rejected' ? 'selected' : '' }}>❌ غير متوافق (مرفوض)</option>
                                </select>
                                <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">حفظ الحالة</button>
                            </div>
                            <div class="text-muted small mt-2">
                                * تحديث الحالة هنا يساعدك في تنظيم وفرز الخريجين ولن يظهر للخريج نفسه.
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                @if($grad->graduateData?->cv_path)
                <a href="{{ Storage::url($grad->graduateData->cv_path) }}" target="_blank"
                   class="btn fw-bold rounded-pill px-4" style="background:#fdf4ff;color:#7c3aed;border:none;">
                    <i class="fas fa-download me-2"></i>تحميل السيرة الذاتية
                </a>
                @endif
                <a href="{{ route('messages.show', $grad->id) }}"
                   class="btn fw-bold rounded-pill px-4" style="background:#f0fdf4;color:#166534;border:none;">
                    <i class="fas fa-envelope me-2"></i>مراسلة
                </a>
                <button type="button" class="btn fw-bold rounded-pill px-4"
                        style="background:#f1f5f9;color:#475569;border:none;"
                        data-bs-dismiss="modal">إغلاق</button>
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection
