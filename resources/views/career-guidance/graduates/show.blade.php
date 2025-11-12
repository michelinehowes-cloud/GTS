@extends('layouts.app')

@section('title', 'تفاصيل الخريج - ' . $graduate->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>تفاصيل الخريج</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('career-guidance.graduates.edit', $graduate->id) }}" class="btn btn-warning btn-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                        <a href="{{ route('career-guidance.graduates.edit', $graduate->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- المعلومات الشخصية -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>المعلومات الشخصية</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <strong>الاسم الكامل:</strong>
                                            <p class="mb-0">{{ $graduate->name }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>البريد الإلكتروني:</strong>
                                            <p class="mb-0">{{ $graduate->email }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>رقم الهاتف:</strong>
                                            <p class="mb-0">{{ $graduate->phone ?? 'غير محدد' }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>الرقم الوطني:</strong>
                                            <p class="mb-0">{{ $graduate->national_id ?? 'غير محدد' }}</p>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <strong>العنوان:</strong>
                                            <p class="mb-0">{{ $graduate->address ?? 'غير محدد' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المعلومات الأكاديمية -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>المعلومات الأكاديمية</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <strong>التخصص:</strong>
                                            <p class="mb-0">{{ $graduate->major }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>الجامعة:</strong>
                                            <p class="mb-0">{{ $graduate->university }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>سنة التخرج:</strong>
                                            <p class="mb-0">{{ $graduate->graduation_year }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>المعدل التراكمي:</strong>
                                            <p class="mb-0">{{ $graduate->gpa ?? 'غير محدد' }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>الدرجة العلمية:</strong>
                                            <p class="mb-0">{{ $graduate->degree }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <strong>حالة التوظيف:</strong>
                                            @php
                                                $statusColors = [
                                                    'employed' => 'success',
                                                    'unemployed' => 'danger',
                                                    'seeking_opportunities' => 'warning',
                                                    'continuing_education' => 'info'
                                                ];
                                                $statusText = [
                                                    'employed' => 'موظف',
                                                    'unemployed' => 'غير موظف',
                                                    'seeking_opportunities' => 'باحث عن فرص',
                                                    'continuing_education' => 'مستكمل للدراسة'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$graduate->employment_status] }}">
                                                {{ $statusText[$graduate->employment_status] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- المهارات والخبرات -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>المهارات</h5>
                                </div>
                                <div class="card-body">
                                    @if($graduate->skills && count($graduate->skills) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($graduate->skills as $skill)
                                                <span class="badge bg-primary">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">لا توجد مهارات مسجلة</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-language me-2"></i>اللغات</h5>
                                </div>
                                <div class="card-body">
                                    @if($graduate->languages && count($graduate->languages) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($graduate->languages as $language)
                                                <span class="badge bg-success">{{ $language }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">لا توجد لغات مسجلة</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الترشيحات السابقة -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>الترشيحات السابقة</h5>
                        </div>
                        <div class="card-body">
                            @if($graduate->nominations && $graduate->nominations->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>الفرصة</th>
                                                <th>الشركة</th>
                                                <th>تاريخ الترشيح</th>
                                                <th>الحالة</th>
                                                <th>النتيجة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($graduate->nominations as $nomination)
                                            <tr>
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
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">لا توجد ترشيحات سابقة لهذا الخريج</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
