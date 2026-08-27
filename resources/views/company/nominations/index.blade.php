@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fw-bold">المرشحين للوظائف (ATS)</h2>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($nominations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>اسم الخريج</th>
                                <th>الوظيفة</th>
                                <th>تاريخ الترشيح</th>
                                <th>حالة الطلب</th>
                                <th>القرار النهائي</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nominations as $nomination)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $nomination->graduate->name_ar ?? 'غير متوفر' }}</div>
                                        <div class="small text-muted">{{ $nomination->graduate->university_specialization ?? '' }}</div>
                                    </td>
                                    <td>{{ $nomination->jobOpportunity->title ?? 'غير متوفر' }}</td>
                                    <td>{{ $nomination->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'secondary',
                                                'sent_to_company' => 'primary',
                                                'under_review' => 'info',
                                                'interview_scheduled' => 'warning',
                                                'accepted' => 'success',
                                                'rejected' => 'danger',
                                                'withdrawn' => 'dark'
                                            ];
                                            $color = $statusColors[$nomination->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ $nomination->status_text }}</span>
                                    </td>
                                    <td>
                                        @if($nomination->final_status === 'hired')
                                            <span class="badge bg-success">تم التوظيف</span>
                                        @elseif($nomination->final_status === 'not_hired')
                                            <span class="badge bg-danger">لم يتم التوظيف</span>
                                        @else
                                            <span class="badge bg-secondary">قيد المعالجة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('company.nominations.show', $nomination->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> مراجعة الطلب
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $nominations->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    لا يوجد مرشحين متاحين حالياً لأي من وظائفك.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
