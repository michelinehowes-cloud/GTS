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
    <option value="{{ route($routePrefix . '.nominations') }}">جميع الفرص</option>
                                @foreach($opportunities as $opportunity)
                                    <option value="{{ route($routePrefix . '.nominations', ['opportunity_id' => $opportunity->id]) }}" 
                                        {{ request('opportunity_id') == $opportunity->id ? 'selected' : '' }}>
                                        {{ $opportunity->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>فلترة حسب الحالة:</label>
                            <select class="form-select" onchange="window.location.href = this.value">
                                <option value="{{ route($routePrefix . '.nominations') }}">جميع الحالات</option>
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
                                    <option value="{{ route($routePrefix . '.nominations', ['status' => $value]) }}" 
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
<a href="{{ route($routePrefix . '.nominations.show', $nomination->id) }}" class="btn btn-outline-info btn-sm" title="عرض التفاصيل">
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
                                                        <form action="{{ route($routePrefix . '.nominations.update-status', $nomination->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">الحالة:</label>
                                                                    <select name="status" class="form-select" required onchange="toggleFields('{{ $nomination->id }}', this.value)">
                                                                        @foreach($statuses as $value => $text)
                                                                            <option value="{{ $value }}" {{ $nomination->status == $value ? 'selected' : '' }}>
                                                                                {{ $text }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">النتيجة النهائية (اختياري):</label>
                                                                    <select name="final_status" class="form-select">
                                                                        <option value="">-- اختر النتيجة --</option>
                                                                        <option value="hired" {{ $nomination->final_status == 'hired' ? 'selected' : '' }}>تم التوظيف</option>
                                                                        <option value="not_hired" {{ $nomination->final_status == 'not_hired' ? 'selected' : '' }}>لم يتم التوظيف</option>
                                                                        <option value="in_progress" {{ $nomination->final_status == 'in_progress' ? 'selected' : '' }}>قيد الإجراء</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div id="interviewFields{{ $nomination->id }}" style="display: {{ $nomination->status == 'interview_scheduled' ? 'block' : 'none' }};">
                                                                    <div class="border-top pt-3 mt-3 mb-3">
                                                                        <h6 class="text-primary"><i class="fas fa-calendar-alt"></i> تفاصيل المقابلة</h6>
                                                                        <div class="row g-2">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">التاريخ:</label>
                                                                                <input type="date" name="interview_date" class="form-control" 
                                                                                       value="{{ $nomination->interview_date ? $nomination->interview_date->format('Y-m-d') : '' }}">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">الوقت:</label>
                                                                                <input type="time" name="interview_time" class="form-control" 
                                                                                       value="{{ $nomination->interview_time }}">
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <label class="form-label">المكان:</label>
                                                                                <input type="text" name="interview_location" class="form-control" 
                                                                                       value="{{ $nomination->interview_location }}" 
                                                                                       placeholder="رابط الاجتماع أو العنوان">
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <label class="form-label">ملاحظات المقابلة:</label>
                                                                                <textarea name="interview_notes" class="form-control" rows="2">{{ $nomination->interview_notes }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">ملاحظات عامة:</label>
                                                                    <textarea name="nomination_notes" class="form-control" rows="3" placeholder="أضف أي ملاحظات إضافية هنا...">{{ $nomination->nomination_notes }}</textarea>
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
    let url = "{{ route($routePrefix . '.nominations') }}";
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

function toggleFields(nominationId, status) {
    const interviewFields = document.getElementById(`interviewFields${nominationId}`);
    
    if (interviewFields) {
        if (status === 'interview_scheduled') {
            interviewFields.style.display = 'block';
            // جعل الحقول مطلوبة
            interviewFields.querySelectorAll('input[type="date"], input[type="time"]').forEach(input => {
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
</script>
@endsection
