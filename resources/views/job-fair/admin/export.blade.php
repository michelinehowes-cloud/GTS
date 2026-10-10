<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>قائمة الخريجين - {{ $fair->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body { background: #f8fafc; }

        .export-header {
            background: linear-gradient(135deg, #045db0 0%, #03488a 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 20px 20px;
            margin-bottom: 2rem;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .export-header { border-radius: 0; }
        }
    </style>
</head>
<body>
<div class="container-fluid">

    <div class="export-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="fw-bold mb-1">🎓 {{ $fair->title }}</h3>
                <p class="mb-1 opacity-75">قائمة الخريجين المسجلين</p>
                <small class="opacity-50">{{ $fair->event_date->format('d/m/Y') }} — {{ $fair->location }}</small>
            </div>
            <div class="text-center">
                <div style="font-size: 2rem; font-weight: 900; color: #eeca3e">{{ $registrations->count() }}</div>
                <div class="opacity-75 small">خريج مسجل</div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="no-print d-flex gap-2 mb-3 px-3">
        <button onclick="window.print()" class="btn btn-dark rounded-pill">
            <i class="fas fa-print me-2"></i>طباعة القائمة
        </button>
        <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-light rounded-pill">
            <i class="fas fa-arrow-right me-2"></i>العودة
        </a>
    </div>

    <!-- Table -->
    <div class="px-3">
        <table class="table table-bordered table-hover align-middle" style="font-size: 0.9rem">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>رقم التسجيل</th>
                    <th>اسم الخريج</th>
                    <th>التخصص</th>
                    <th>الكلية</th>
                    <th>سنة التخرج</th>
                    <th>المعدل</th>
                    <th>الهاتف</th>
                    <th>الحضور</th>
                    <th>وقت الدخول</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $i => $reg)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong class="text-primary">{{ $reg->registration_number }}</strong></td>
                    <td>{{ $reg->graduate->name }}</td>
                    <td>{{ $reg->graduate->major ?? '—' }}</td>
                    <td>{{ $reg->graduate->faculty ?? '—' }}</td>
                    <td>{{ $reg->graduate->graduation_year ?? '—' }}</td>
                    <td>{{ $reg->graduate->gpa ? number_format($reg->graduate->gpa, 2) : '—' }}</td>
                    <td>{{ $reg->graduate->phone ?? '—' }}</td>
                    <td>
                        @if($reg->attended)
                        <span class="badge bg-success">✓ حضر</span>
                        @else
                        <span class="badge bg-secondary">غائب</span>
                        @endif
                    </td>
                    <td>{{ $reg->check_in_at ? $reg->check_in_at->format('H:i') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3 text-muted small">
            تمت الطباعة: {{ now()->format('d/m/Y H:i') }}
            &nbsp;—&nbsp;
            الحاضرون: {{ $registrations->where('attended', true)->count() }} من {{ $registrations->count() }}
        </div>
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</body>
</html>
