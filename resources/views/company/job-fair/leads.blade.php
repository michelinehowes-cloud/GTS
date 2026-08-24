@extends('layouts.app')

@section('title', 'السير الذاتية المستلمة - ' . $fair->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users text-primary"></i> السير الذاتية المستلمة (Leads)
            </h1>
            <p class="text-muted mt-2">معرض: {{ $fair->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="btn btn-primary">
                <i class="fas fa-qrcode"></i> ماسح السير
            </a>
            <a href="{{ route('company.job-fairs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            @if($visits->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>لم تقم باستلام أي سير ذاتية بعد في هذا المعرض!</h5>
                    <p>استخدم <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="fw-bold">ماسح السير الذاتية</a> لجمع بيانات الخريجين.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>اسم الخريج</th>
                                <th>التخصص</th>
                                <th>المعدل (GPA)</th>
                                <th>وقت الزيارة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $visit)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                                {{ mb_substr($visit->graduate->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $visit->graduate->name }}</div>
                                                <div class="small text-muted">{{ $visit->graduate->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $visit->graduate->major ?? '—' }}</td>
                                    <td>
                                        @if($visit->graduate->gpa)
                                            <span class="badge bg-info text-dark">{{ number_format($visit->graduate->gpa, 2) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-calendar-day text-muted"></i> {{ $visit->created_at->format('Y-m-d') }}<br>
                                            <i class="fas fa-clock text-muted"></i> {{ $visit->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($visit->graduate->graduateData && $visit->graduate->graduateData->cv_path)
                                            <a href="{{ Storage::url($visit->graduate->graduateData->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="تحميل السيرة الذاتية">
                                                <i class="fas fa-download"></i> السيرة الذاتية
                                            </a>
                                        @else
                                            <span class="text-muted small">لا يوجد ملف</span>
                                        @endif
                                        
                                        <!-- زر المراسلة -->
                                        <a href="{{ route('messages.show', $visit->graduate->id) }}" class="btn btn-sm btn-outline-success ms-1" title="مراسلة">
                                            <i class="fas fa-envelope"></i> مراسلة
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
