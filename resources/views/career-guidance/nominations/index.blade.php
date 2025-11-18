@extends('layouts.app')

@section('title', 'إدارة الترشيحات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>إدارة الترشيحات</h3>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary fs-6">إجمالي الترشيحات: {{ $nominations->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- الفلاتر -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label>فلترة حسب الفرصة:</label>
                            <select class="form-select" onchange="window.location.href = this.value">
    <option value="{{ route('career-guidance.nominations') }}">جميع الفرص</option>
                                @foreach($opportunities as $opportunity)
                                    <option value="{{ route('career-guidance.nominations', ['opportunity_id' => $opportunity->id]) }}" 
                                        {{ request('opportunity_id') == $opportunity->id ? 'selected' : '' }}>
                                        {{ $opportunity->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>فلترة حسب الحالة:</label>
                            <select class="form-select" onchange="window.location.href = this.value">
                                <option value="{{ route('career-guidance.nominations') }}">جميع الحالات</option>
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
                                @foreach($statuses as $value => $text)
                                    <option value="{{ route('career-guidance.nominations', ['status' => $value]) }}" 
                                        {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $text }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="from_date">من تاريخ الترشيح:</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" 
                                   value="{{ request('from_date') }}" 
                                   onchange="applyDateFilter()">
                        </div>
                        <div class="col-md-4">
                            <label for="to_date">إلى تاريخ الترشيح:</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" 
                                   value="{{ request('to_date') }}" 
                                   onchange="applyDateFilter()">
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($nominations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الخريج</th>
                                        <th>الفرصة</th>
                                        <th>الشركة</th>
                                        <th>تاريخ الترشيح</th>
                                        <th>الحالة</th>
                                        <th>النتيجة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nominations as $nomination)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $nomination->graduate->name }}</strong>
                                            <br><small class="text-muted">{{ $nomination->graduate->major }}</small>
                                        </td>
                                        <td>{{ $nomination->jobOpportunity->title }}</td>
                                        <td>{{ $nomination->jobOpportunity->company->name }}</td>
                                        <td>{{ $nomination->nominated_at->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $nomination->status == 'accepted' ? 'success' : ($nomination->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $nomination->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($nomination->final_status)
                                                <span class="badge bg-{{ $nomination->final_status == 'hired' ? 'success' : 'secondary' }}">
                                                    {{ $nomination->final_status_text }}
                                                </span>
                                            @else
                                                <span class="text-muted">لم يتم</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- زر تحديث الحالة -->
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#updateStatusModal{{ $nomination->id }}"
                                                        title="تحديث الحالة">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- زر عرض التفاصيل -->
<a href="{{ route('career-guidance.nominations.show', $nomination->id) }}" class="btn btn-outline-info btn-sm" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>

                                            <!-- ✅ Modal لتحديث الحالة - هذا ما كان مفقودًا -->
                                            <div class="modal fade" id="updateStatusModal{{ $nomination->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تحديث حالة الترشيح</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                       <form action="{{ route('career-guidance.nominations.update-status', $nomination->id) }}" method="POST">
    @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label>الحالة:</label>
                                                                    <select name="status" class="form-select" required>
                                                                        @foreach($statuses as $value => $text)
                                                                            <option value="{{ $value }}" {{ $nomination->status == $value ? 'selected' : '' }}>
                                                                                {{ $text }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                
                                                                <div id="interviewFields{{ $nomination->id }}" style="display: none;">
                                                                    <div class="border-top pt-3 mt-3">
                                                                        <h6>تفاصيل المقابلة:</h6>
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <label>تاريخ المقابلة:</label>
                                                                                <input type="date" name="interview_date" class="form-control" 
                                                                                       value="{{ $nomination->interview_date ? $nomination->interview_date->format('Y-m-d') : '' }}">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>وقت المقابلة:</label>
                                                                                <input type="time" name="interview_time" class="form-control" 
                                                                                       value="{{ $nomination->interview_time }}">
                                                                            </div>
                                                                            <div class="col-12 mt-2">
                                                                                <label>مكان المقابلة:</label>
                                                                                <input type="text" name="interview_location" class="form-control" 
                                                                                       value="{{ $nomination->interview_location }}" 
                                                                                       placeholder="مكان المقابلة">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- نهاية Modal -->

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">لا توجد ترشيحات حالياً</h4>
                            <p class="text-muted">سيظهر هنا جميع الترشيحات التي تم إجراؤها</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function applyDateFilter() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    let url = "{{ route('career-guidance.nominations') }}";
    const params = new URLSearchParams(window.location.search);

    if (params.has('status')) {
        url += `?status=${params.get('status')}`;
    } else if (params.has('opportunity_id')) {
        url += `?opportunity_id=${params.get('opportunity_id')}`;
    } else {
        url += `?`;
    }

    if (fromDate) {
        url += `${url.includes('?') ? '&' : '?'}from_date=${fromDate}`;
    }
    if (toDate) {
        url += `${url.includes('?') ? '&' : '?'}to_date=${toDate}`;
    }
    window.location.href = url;
}

// دالة بسيطة لإظهار/إخفاء حقول المقابلة
function toggleInterviewFields(nominationId) {
    const statusSelect = document.querySelector(`#updateStatusModal${nominationId} select[name="status"]`);
    const interviewFields = document.getElementById(`interviewFields${nominationId}`);
    
    if (statusSelect && interviewFields) {
        if (statusSelect.value === 'interview_scheduled') {
            interviewFields.style.display = 'block';
            // جعل الحقول مطلوبة
            interviewFields.querySelectorAll('input').forEach(input => {
                input.required = true;
            });
        } else {
            interviewFields.style.display = 'none';
            // إزالة الصفة المطلوبة
            interviewFields.querySelectorAll('input').forEach(input => {
                input.required = false;
            });
        }
    }
}

// تفعيل عند فتح كل مودال
document.addEventListener('DOMContentLoaded', function() {
    // إضافة event listener لكل زر تحديث
    document.querySelectorAll('[data-bs-target^="#updateStatusModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-bs-target').replace('#', '');
            const nominationId = modalId.replace('updateStatusModal', '');
            
            // تأخير بسيط لضمان تحميل المودال
            setTimeout(() => {
                // تفعيل عند تغيير الحالة
                const statusSelect = document.querySelector(`#${modalId} select[name="status"]`);
                if (statusSelect) {
                    statusSelect.addEventListener('change', function() {
                        toggleInterviewFields(nominationId);
                    });
                    
                    // التهيئة الأولية
                    toggleInterviewFields(nominationId);
                }
            }, 100);
        });
    });
});

// تحقق قبل الإرسال
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const statusSelect = this.querySelector('select[name="status"]');
            const interviewFields = this.querySelector('[id^="interviewFields"]');
            
            if (statusSelect && statusSelect.value === 'interview_scheduled' && interviewFields) {
                const requiredFields = interviewFields.querySelectorAll('input[required]');
                let isValid = true;
                let firstInvalidField = null;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('يرجى ملء جميع حقول المقابلة المطلوبة');
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                    }
                    return false;
                }
            }
            return true;
        });
    });
});
</script>
@endsection
