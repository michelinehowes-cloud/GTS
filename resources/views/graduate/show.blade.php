<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل البرنامج - نظام تدريب الخريجين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .university-blue { color: #1e3a8a; }
        .university-gold { color: #d4af37; }
        .card { border-right: 4px solid #d4af37; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="http://localhost:8000/storage/logo.png" alt="شعار الجامعة" height="40" 
                     onerror="this.style.display='none'" class="me-2">
                نظام تدريب الخريجين - الخريج
            </a>
            <div class="navbar-nav">
                <a href="{{ route('graduate.dashboard') }}" class="btn btn-outline-light me-2">لوحة التحكم</a>
                <a href="{{ route('graduate.trainings') }}" class="btn btn-outline-light me-2">برامج التدريب</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">تفاصيل برنامج التدريب</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="university-blue">معلومات البرنامج</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">اسم البرنامج</th>
                                        <td>{{ $training->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>نوع البرنامج</th>
                                        <td>
                                            @switch($training->type)
                                                @case('workshop') ورشة عمل @break
                                                @case('course') دورة @break
                                                @case('seminar') ندوة @break
                                                @case('internship') تدريب عملي @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>المدة</th>
                                        <td>{{ $training->duration }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ البدء</th>
                                        <td>{{ $training->start_date->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الانتهاء</th>
                                        <td>{{ $training->end_date->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="university-blue">التفاصيل الإضافية</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">المكان</th>
                                        <td>{{ $training->location }}</td>
                                    </tr>
                                    <tr>
                                        <th>عدد المقاعد</th>
                                        <td>{{ $training->seats }}</td>
                                    </tr>
                                    <tr>
                                        <th>المقاعد المتبقية</th>
                                        <td>
                                            @php
                                                $approvedApplications = \App\Models\TrainingApplication::where('training_id', $training->id)
                                                    ->where('status', 'approved')
                                                    ->count();
                                                $remainingSeats = $training->seats - $approvedApplications;
                                            @endphp
                                            <span class="badge bg-{{ $remainingSeats > 0 ? 'success' : 'danger' }}">
                                                {{ $remainingSeats }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>الحالة</th>
                                        <td>
                                            <span class="badge bg-{{ $training->status == 'active' ? 'success' : 'warning' }}">
                                                {{ $training->status == 'active' ? 'نشط' : 'متوقف' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="university-blue">وصف البرنامج</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text">{{ $training->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="university-blue">التقديم للبرنامج</h5>
                                <div class="card">
                                    <div class="card-body">
                                        @php
                                            $userApplication = \App\Models\TrainingApplication::where('training_id', $training->id)
                                                ->where('user_id', auth()->id())
                                                ->first();
                                        @endphp

                                        @if($userApplication)
                                            <div class="alert alert-info">
                                                <h6>حالة طلبك:</h6>
                                                <span class="badge bg-{{ $userApplication->status == 'approved' ? 'success' : ($userApplication->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    @if($userApplication->status == 'pending') قيد المراجعة @endif
                                                    @if($userApplication->status == 'approved') مقبول @endif
                                                    @if($userApplication->status == 'rejected') مرفوض @endif
                                                </span>
                                                <p class="mt-2 mb-0">تاريخ التقديم: {{ $userApplication->created_at->format('Y-m-d') }}</p>
                                            </div>
                                        @elseif($remainingSeats > 0 && $training->status == 'active')
                                            <form action="{{ route('training.apply', $training->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="message" class="form-label">رسالة التقديم (اختياري)</label>
                                                    <textarea class="form-control" id="message" name="message" rows="3" 
                                                              placeholder="أكتب رسالة توضح اهتمامك بالبرنامج..."></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="fas fa-paper-plane me-2"></i>تقديم طلب المشاركة
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                @if($training->status != 'active')
                                                    هذا البرنامج غير متاح حالياً للتقديم
                                                @else
                                                    لا توجد مقاعد متاحة حالياً
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ route('graduate.trainings') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>العودة لقائمة البرامج
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>