@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">تفاصيل الخريج: {{ $graduate->name }}</h1>
            <a href="{{ route('career-guidance.graduates') }}"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-right fa-sm text-white-50"></i> العودة لقائمة الخريجين
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">البيانات الأساسية</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>الاسم الكامل:</strong> {{ $graduate->name }}</p>
                        <p><strong>البريد الإلكتروني:</strong> {{ $graduate->email ?? 'لا يوجد' }}</p>
                        <p><strong>رقم الهاتف:</strong> {{ $graduate->phone ?? 'لا يوجد' }}</p>
                        <p><strong>الجامعة:</strong> {{ $graduate->university ?? 'لا يوجد' }}</p>
                        <p><strong>القطاع:</strong> {{ $graduate->sector ?? 'لا يوجد' }}</p>
                        <p><strong>الكلية:</strong> {{ $graduate->faculty ?? 'لا يوجد' }}</p>
                        <p><strong>التخصص:</strong> {{ $graduate->major }}</p>
                        <p><strong>سنة التخرج:</strong> {{ $graduate->graduation_year }}</p>
                        <p><strong>المعدل التراكمي:</strong> {{ $graduate->gpa ?? 'لا يوجد' }}</p>
                        <p><strong>الدرجة العلمية:</strong> {{ $graduate->degree }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>حالة التوظيف:</strong> {{ $graduate->employment_status }}</p>
                        <p><strong>الخبرة العملية:</strong> {{ $graduate->work_experience ?? 'لا يوجد' }}</p>
                        <p><strong>العنوان:</strong> {{ $graduate->address ?? 'لا يوجد' }}</p>
                        <p><strong>رابط لينكدإن:</strong>
                            @if($graduate->linkedin_url)
                                <a href="{{ $graduate->linkedin_url }}" target="_blank">{{ $graduate->linkedin_url }}</a>
                            @else
                                لا يوجد
                            @endif
                        </p>
                        <p><strong>تاريخ الإضافة:</strong> {{ $graduate->created_at->format('Y-m-d') }}</p>
                        <p><strong>أضيف بواسطة:</strong> {{ $graduate->addedBy->name ?? 'غير معروف' }}</p>
                    </div>
                </div>
                <hr>
                <p><strong>المهارات:</strong>
                    @if(!empty($graduate->skills))
                        @foreach((array) $graduate->skills as $skill)
                            <span class="badge badge-info">{{ $skill }}</span>
                        @endforeach
                    @else
                        لا توجد
                    @endif
                </p>
                <p><strong>اللغات:</strong>
                    @if(!empty($graduate->languages))
                        @foreach((array) $graduate->languages as $lang)
                            <span class="badge badge-secondary">{{ $lang }}</span>
                        @endforeach
                    @else
                        لا توجد
                    @endif
                </p>

                <hr>

                <p><strong>السيرة الذاتية:</strong>
                    @if($graduate->cv_path)
                        <div class="d-flex align-items-center mt-2">
                            <i class="fas fa-file-pdf text-danger me-2 fs-4"></i>
                            <span class="me-3">{{ basename($graduate->cv_path) }}</span>
                            <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-eye me-1"></i> عرض
                            </a>
                            <a href="{{ Storage::url($graduate->cv_path) }}" download class="btn btn-sm btn-success">
                                <i class="fas fa-download me-1"></i> تحميل
                            </a>
                        </div>
                    @else
                    <span class="text-muted">لم يتم رفع السيرة الذاتية</span>
                @endif
                </p>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">الترشيحات الخاصة بالخريج</h6>
            </div>
            <div class="card-body">
                @if($graduate->nominations->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>فرصة العمل</th>
                                    <th>الشركة</th>
                                    <th>الحالة</th>
                                    <th>الحالة النهائية</th>
                                    <th>ملاحظات الشركة</th>
                                    <th>ملاحظات الخريج</th>
                                    <th>تاريخ الترشيح</th>
                                    <th>المرشح بواسطة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($graduate->nominations as $nomination)
                                    <tr>
                                        <td>{{ $nomination->jobOpportunity->title ?? 'N/A' }}</td>
                                        <td>{{ $nomination->jobOpportunity->company->name ?? 'N/A' }}</td>
                                        <td>{{ $nomination->status }}</td>
                                        <td>{{ $nomination->final_status ?? 'N/A' }}</td>
                                        <td>{{ $nomination->company_feedback ?? 'لا يوجد' }}</td>
                                        <td>{{ $nomination->graduate_feedback ?? 'لا يوجد' }}</td>
                                        <td>{{ $nomination->nominated_at->format('Y-m-d') }}</td>
                                        <td>{{ $nomination->nominator->name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('career-guidance.nominations.show', $nomination->id) }}"
                                                class="btn btn-info btn-sm">عرض</a>
                                            <a href="{{ route('career-guidance.nominations.edit-status', $nomination->id) }}"
                                                class="btn btn-warning btn-sm">تعديل الحالة</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>لا توجد ترشيحات لهذا الخريج حتى الآن.</p>
                @endif
            </div>
        </div>
    </div>
@endsection