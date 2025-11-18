@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">تعديل حالة الترشيح: {{ $nomination->graduate->name }} لـ {{ $nomination->jobOpportunity->title }}</div>

                <div class="card-body">
                    <form action="{{ route('partnership.nominations.update-status', $nomination->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ $nomination->status == 'pending' ? 'selected' : '' }}>معلق</option>
                                <option value="sent_to_company" {{ $nomination->status == 'sent_to_company' ? 'selected' : '' }}>مرسل للشركة</option>
                                <option value="under_review" {{ $nomination->status == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="interview_scheduled" {{ $nomination->status == 'interview_scheduled' ? 'selected' : '' }}>مقابلة مجدولة</option>
                                <option value="accepted" {{ $nomination->status == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                <option value="rejected" {{ $nomination->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="withdrawn" {{ $nomination->status == 'withdrawn' ? 'selected' : '' }}>مسحوب</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="final_status" class="form-label">الحالة النهائية</label>
                            <select class="form-control @error('final_status') is-invalid @enderror" id="final_status" name="final_status">
                                <option value="">اختر حالة نهائية</option>
                                <option value="hired" {{ $nomination->final_status == 'hired' ? 'selected' : '' }}>تم التوظيف</option>
                                <option value="not_hired" {{ $nomination->final_status == 'not_hired' ? 'selected' : '' }}>لم يتم التوظيف</option>
                                <option value="in_progress" {{ $nomination->final_status == 'in_progress' ? 'selected' : '' }}>قيد التقدم</option>
                            </select>
                            @error('final_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nomination_notes" class="form-label">ملاحظات الترشيح</label>
                            <textarea class="form-control @error('nomination_notes') is-invalid @enderror" id="nomination_notes" name="nomination_notes" rows="3">{{ old('nomination_notes', $nomination->nomination_notes) }}</textarea>
                            @error('nomination_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="matching_reasons" class="form-label">أسباب المطابقة</label>
                            <textarea class="form-control @error('matching_reasons') is-invalid @enderror" id="matching_reasons" name="matching_reasons" rows="3">{{ old('matching_reasons', $nomination->matching_reasons) }}</textarea>
                            @error('matching_reasons')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="interview_date" class="form-label">تاريخ المقابلة</label>
                            <input type="date" class="form-control @error('interview_date') is-invalid @enderror" id="interview_date" name="interview_date" value="{{ old('interview_date', optional($nomination->interview_date)->format('Y-m-d')) }}">
                            @error('interview_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="interview_time" class="form-label">وقت المقابلة</label>
                            <input type="text" class="form-control @error('interview_time') is-invalid @enderror" id="interview_time" name="interview_time" value="{{ old('interview_time', $nomination->interview_time) }}" placeholder="مثال: 10:00 صباحاً">
                            @error('interview_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="interview_location" class="form-label">مكان المقابلة</label>
                            <input type="text" class="form-control @error('interview_location') is-invalid @enderror" id="interview_location" name="interview_location" value="{{ old('interview_location', $nomination->interview_location) }}">
                            @error('interview_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="interview_notes" class="form-label">ملاحظات المقابلة</label>
                            <textarea class="form-control @error('interview_notes') is-invalid @enderror" id="interview_notes" name="interview_notes" rows="3">{{ old('interview_notes', $nomination->interview_notes) }}</textarea>
                            @error('interview_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company_feedback" class="form-label">ملاحظات الشركة</label>
                            <textarea class="form-control @error('company_feedback') is-invalid @enderror" id="company_feedback" name="company_feedback" rows="3">{{ old('company_feedback', $nomination->company_feedback) }}</textarea>
                            @error('company_feedback')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="graduate_feedback" class="form-label">ملاحظات الخريج</label>
                            <textarea class="form-control @error('graduate_feedback') is-invalid @enderror" id="graduate_feedback" name="graduate_feedback" rows="3">{{ old('graduate_feedback', $nomination->graduate_feedback) }}</textarea>
                            @error('graduate_feedback')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">تحديث الحالة</button>
                        <a href="{{ route('partnership.nominations.show', $nomination->id) }}" class="btn btn-secondary">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
