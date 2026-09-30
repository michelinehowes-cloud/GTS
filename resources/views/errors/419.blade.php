<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتهت صلاحية الجلسة - جامعة طرابلس</title>
    <meta http-equiv="refresh" content="2;url={{ url('/?open_login=1') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #ffffff;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            color: #1e293b;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .btn-uot {
            background: #1565c0;
            color: white;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }
        .btn-uot:hover {
            background: #0d3882;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: rgba(245, 158, 11, 0.15); color: #d97706; font-size: 2rem;">
                <i class="fas fa-clock-rotate-left"></i>
            </span>
        </div>
        <h3 class="fw-bold mb-2">انتهت صلاحية الجلسة</h3>
        <p class="text-muted small mb-4">
            نظراً لتحديث أمان النظام أو عدم النشاط لفترة، تم تجديد رمز الحماية لبياناتك تلقائياً. جاري توجيهك لصفحة تسجيل الدخول...
        </p>
        <a href="{{ url('/?open_login=1') }}" class="btn btn-uot">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لتسجيل الدخول الآن</span>
        </a>
    </div>
</body>
</html>
