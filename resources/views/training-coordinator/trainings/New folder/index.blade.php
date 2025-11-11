<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة برامج التدريب - منسق التدريب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">نظام تدريب الخريجين - منسق التدريب</a>
            <div class="navbar-nav">
                <a href="{{ route('training-coordinator.dashboard') }}" class="btn btn-outline-light me-2">لوحة التحكم</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>برامج التدريب الخاصة بي</h3>
                <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة برنامج تدريب جديد
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($myTrainings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم البرنامج</th>
                                    <th>النوع</th>
                                    <th>المدة</th>
                                    <th>تاريخ البدء</th>
                                    <th>المقاعد</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myTrainings as $training)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $training->title }}</strong>
                                        <br><small class="text-muted">{{ $training->description }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $training->type }}</span>
                                    </td>
                                    <td>{{ $training->duration }}</td>
                                    <td>{{ $training->start_date }}</td>
                                    <td>{{ $training->seats }}</td>
                                    <td>
                                        <span class="badge bg-{{ $training->status == 'active' ? 'success' : 'warning' }}">
                                            {{ $training->status == 'active' ? 'نشط' : 'متوقف' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('training-coordinator.trainings.edit', $training->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('training-coordinator.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">لا توجد برامج تدريب حتى الآن</h4>
                        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-primary btn-lg mt-3">
                            <i class="fas fa-plus me-2"></i>إضافة أول برنامج تدريب
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>