@extends('layouts.app')

@section('title', 'تعديل حالة الترشيح')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-9 col-md-10 mx-auto">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0" style="color: #2c5aa0;">
                        <i class="fas fa-edit me-2"></i>تعديل حالة الترشيح
                    </h4>
                    <a href="{{ route('career-guidance.nominations') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>العودة للترشيحات
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('career-guidance.nominations.update-status-fullpage', $nomination->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h5 class="text-primary">تفاصيل الترشيح</h5>
                            <p class="mb-1"><strong>الخريج:</strong> {{ $nomination->graduate->name }}</p>
                            <p class="mb-1"><strong>الفرصة:</strong> {{ $nomination->jobOpportunity->title }}</p>
                            <p class="mb-1"><strong>الشركة:</strong> {{ $nomination->jobOpportunity->company->name }}</p>
                            <p class="mb-1"><strong>الحالة الحالية:</strong> 
                                <span class="badge py-2 px-3 text-white" style="background-color: {{ $statusColors[$nomination->status] ?? '#6b7280' }}; border-radius: 8px;">
                                    <i class="fas fa-{{ $statusIcons[$nomination->status] ?? 'info-circle' }} me-1"></i>
                                    {{ $nomination->status_text }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة الجديدة <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach($statuses as $value => $text)
                                    <option value="{{ $value }}" {{ $nomination->status == $value ? 'selected' : '' }}>
                                        {{ $text }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="interviewFields" style="display: {{ $nomination->status == 'interview_scheduled' ? 'block' : 'none' }};">
                            <div class="border-top pt-3 mt-3">
                                <h6 class="text-primary">تفاصيل المقابلة</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="interview_date" class="form-label">تاريخ المقابلة</label>
                                        <input type="date" name="interview_date" id="interview_date" class="form-control @error('interview_date') is-invalid @enderror" 
                                               value="{{ old('interview_date', $nomination->interview_date ? $nomination->interview_date->format('Y-m-d') : '') }}">
                                        @error('interview_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="interview_time" class="form-label">وقت المقابلة</label>
                                        <input type="time" name="interview_time" id="interview_time" class="form-control @error('interview_time') is-invalid @enderror" 
                                               value="{{ old('interview_time', $nomination->interview_time) }}">
                                        @error('interview_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="interview_location" class="form-label">مكان المقابلة</label>
                                        <input type="text" name="interview_location" id="interview_location" class="form-control @error('interview_location') is-invalid @enderror" 
                                               value="{{ old('interview_location', $nomination->interview_location) }}" 
                                               placeholder="مثال: مكتب الشركة، رابط اجتماع عبر الإنترنت">
                                        @error('interview_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="finalStatusFields" style="display: {{ in_array($nomination->status, ['accepted', 'rejected', 'hired']) ? 'block' : 'none' }};">
                            <div class="border-top pt-3 mt-3">
                                <h6 class="text-primary">الحالة النهائية</h6>
                                <div class="mb-3">
                                    <label for="final_status" class="form-label">الحالة النهائية للتوظيف</label>
                                    <select name="final_status" id="final_status" class="form-select @error('final_status') is-invalid @enderror">
                                        <option value="">اختر الحالة النهائية</option>
                                        <option value="hired" {{ $nomination->final_status == 'hired' ? 'selected' : '' }}>تم التوظيف</option>
                                        <option value="not_hired" {{ $nomination->final_status == 'not_hired' ? 'selected' : '' }}>لم يتم التوظيف</option>
                                    </select>
                                    @error('final_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $statusColors = [
        'pending' => '#f59e0b',
        'sent_to_company' => '#3b82f6',
        'under_review' => '#8b5cf6',
        'interview_scheduled' => '#10b981',
        'accepted' => '#10b981',
        'rejected' => '#ef4444',
        'hired' => '#10b981',
        'withdrawn' => '#6b7280'
    ];
    $statusIcons = [
        'pending' => 'clock',
        'sent_to_company' => 'paper-plane',
        'under_review' => 'search',
        'interview_scheduled' => 'calendar-check',
        'accepted' => 'check-circle',
        'rejected' => 'times-circle',
        'hired' => 'user-check',
        'withdrawn' => 'ban'
    ];
@endphp

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('status');
    const interviewFields = document.getElementById('interviewFields');
    const finalStatusFields = document.getElementById('finalStatusFields');

    function toggleFieldsVisibility() {
        const selectedStatus = statusSelect.value;

        if (selectedStatus === 'interview_scheduled') {
            interviewFields.style.display = 'block';
        } else {
            interviewFields.style.display = 'none';
        }

        if (['accepted', 'rejected', 'hired'].includes(selectedStatus)) {
            finalStatusFields.style.display = 'block';
        } else {
            finalStatusFields.style.display = 'none';
        }
    }

    statusSelect.addEventListener('change', toggleFieldsVisibility);

    // Initial call to set visibility based on current status
    toggleFieldsVisibility();
});
</script>
@endsection
