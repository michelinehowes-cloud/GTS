@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fw-bold">تفاصيل ترشيح الخريج: {{ $nomination->graduate->name_ar ?? 'غير متوفر' }}</h2>
            <a href="{{ route('company.nominations') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> عودة للقائمة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- بيانات المرشح والفرصة -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-graduate me-2"></i> بيانات الخريج</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 30%">الاسم:</th>
                                <td>{{ $nomination->graduate->name_ar ?? 'غير متوفر' }}</td>
                            </tr>
                            <tr>
                                <th>التخصص:</th>
                                <td>{{ $nomination->graduate->university_specialization ?? 'غير متوفر' }}</td>
                            </tr>
                            <tr>
                                <th>المعدل التراكمي:</th>
                                <td>{{ $nomination->graduate->gpa ?? 'غير متوفر' }}</td>
                            </tr>
                            <tr>
                                <th>رقم الهاتف:</th>
                                <td><a href="tel:{{ $nomination->graduate->phone }}" class="text-decoration-none">{{ $nomination->graduate->phone ?? 'غير متوفر' }}</a></td>
                            </tr>
                            <tr>
                                <th>البريد الإلكتروني:</th>
                                <td><a href="mailto:{{ $nomination->graduate->email }}" class="text-decoration-none">{{ $nomination->graduate->email ?? 'غير متوفر' }}</a></td>
                            </tr>
                        </tbody>
                    </table>
                    @if($nomination->graduate->cv_path)
                        <div class="mt-3">
                            <a href="{{ Storage::url($nomination->graduate->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-pdf"></i> عرض السيرة الذاتية
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-briefcase me-2"></i> بيانات الوظيفة والترشيح</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 30%">المسمى الوظيفي:</th>
                                <td>{{ $nomination->jobOpportunity->title ?? 'غير متوفر' }}</td>
                            </tr>
                            <tr>
                                <th>تاريخ الترشيح:</th>
                                <td>{{ $nomination->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>ملاحظات الإرشاد المهني:</th>
                                <td>{{ $nomination->nomination_notes ?? 'لا توجد ملاحظات' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- تحديث الحالة والمقابلة -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-tasks me-2"></i> تحديث حالة الطلب</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.nominations.update-status', $nomination->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة المرشح <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required id="statusSelect">
                                <option value="under_review" {{ $nomination->status === 'under_review' ? 'selected' : '' }}>قيد الدراسة (تحت المراجعة)</option>
                                <option value="interview_scheduled" {{ $nomination->status === 'interview_scheduled' ? 'selected' : '' }}>تحديد موعد مقابلة</option>
                                <option value="accepted" {{ $nomination->status === 'accepted' ? 'selected' : '' }}>قبول وتوظيف (تم اجتياز المقابلة)</option>
                                <option value="rejected" {{ $nomination->status === 'rejected' ? 'selected' : '' }}>رفض (لم يتم القبول)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="interviewDetails" class="p-3 bg-light rounded mb-3 border d-none">
                            <h6 class="fw-bold mb-3"><i class="far fa-calendar-alt me-2"></i> تفاصيل المقابلة</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">تاريخ المقابلة</label>
                                <input type="date" name="interview_date" class="form-control" value="{{ $nomination->interview_date ? $nomination->interview_date->format('Y-m-d') : '' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">وقت المقابلة</label>
                                <input type="time" name="interview_time" class="form-control" value="{{ $nomination->interview_time ?? '' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">مكان المقابلة / رابط الاجتماع</label>
                                <input type="text" name="interview_location" class="form-control" value="{{ $nomination->interview_location ?? '' }}" placeholder="مقر الشركة أو رابط زووم...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات ورسالة للخريج (اختياري)</label>
                            <textarea name="company_feedback" class="form-control" rows="3" placeholder="أضف أي ملاحظات سيراها قسم الإرشاد المهني أو الخريج...">{{ $nomination->company_feedback ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> حفظ التحديثات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('statusSelect');
        const interviewDetails = document.getElementById('interviewDetails');

        function toggleInterviewFields() {
            if (statusSelect.value === 'interview_scheduled') {
                interviewDetails.classList.remove('d-none');
            } else {
                interviewDetails.classList.add('d-none');
            }
        }

        statusSelect.addEventListener('change', toggleInterviewFields);
        toggleInterviewFields(); // Run on page load
    });
</script>
@endsection
@endsection
