<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>تم استلام طلب المشروع بنجاح — {{ $project->title }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:      #eeca3e;
            --gold-lt:   #FDE68A;
            --navy:      #045db0;
            --navy-dark: #092347;
            --green:     #10B981;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--navy);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .bg-layer {
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(245,158,11,0.2) 0%, transparent 60%),
                radial-gradient(ellipse 70% 80% at 80% 20%, rgba(14,165,233,0.25) 0%, transparent 55%),
                linear-gradient(160deg, #045db0 0%, #03488a 50%, #092347 100%);
            pointer-events: none;
            z-index: 0;
        }

        .success-card {
            position: relative;
            z-index: 1;
            background: rgba(8, 34, 69, 0.85);
            border: 2px solid rgba(238, 202, 62, 0.4);
            border-radius: 28px;
            padding: 45px 35px;
            max-width: 720px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
        }

        .icon-celebration {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(16, 185, 129, 0.1));
            border: 2px solid #10b981;
            color: #34d399;
            font-size: 2.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.35);
        }

        .card-title {
            font-size: 2rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .card-desc {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .project-receipt-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            padding: 20px;
            text-align: right;
            margin-bottom: 25px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.95rem;
        }

        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { color: rgba(255, 255, 255, 0.65); font-weight: 600; }
        .receipt-value { color: #ffffff; font-weight: 700; }

        .btn-action-main {
            background: linear-gradient(135deg, var(--gold) 0%, #f59e0b 100%);
            border: none;
            color: #092347;
            border-radius: 14px;
            padding: 12px 28px;
            font-weight: 800;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-action-main:hover {
            transform: translateY(-2px);
            color: #06172d;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        .btn-action-outline {
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            border-radius: 14px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-action-outline:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--gold);
            border-color: var(--gold);
        }
    </style>
</head>
<body>

    <div class="bg-layer"></div>

    <div class="success-card">
        <div class="icon-celebration">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1 class="card-title">تم استلام طلب مشروعكم بنجاح!</h1>
        <p class="card-desc">
            نشكركم على مساهمتكم المتميزة في {{ $fair ? $fair->title : 'معرض التوظيف بجامعة طرابلس' }}. تم تسجيل المشروع في النظام وهو الآن في مرحلة <strong>المراجعة والاعتماد الإداري</strong>.
        </p>

        <div class="project-receipt-box">
            <div class="receipt-row">
                <span class="receipt-label">عنوان المشروع:</span>
                <span class="receipt-value">{{ $project->title }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">الكلية والتخصص:</span>
                <span class="receipt-value">{{ $project->faculty }} — {{ $project->department }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">نوع المشروع:</span>
                <span class="receipt-value text-warning">{{ $project->project_type ?? 'مشروع تخرج' }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">الحالة الحالية:</span>
                <span class="receipt-value">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                        <i class="fas fa-hourglass-half me-1"></i> بانتظار اعتماد إدارة المعرض
                    </span>
                </span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">تاريخ ووقت التقديم:</span>
                <span class="receipt-value">{{ $project->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="alert alert-info text-white rounded-3 small text-end mb-4" style="background: rgba(14, 165, 233, 0.2); border: 1px solid rgba(14, 165, 233, 0.4);">
            <i class="fas fa-info-circle me-1 text-info"></i>
            بمجرد قيام اللجنة الإشرافية باعتماد ونشر المشروع، سيظهر مباشرة في المعرض الرقمي العام مع رمز QR خاص به ورقم الجناح المخصص للعرض في حال تمت الموافقة عليه.
        </div>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="btn-action-main">
                <i class="fas fa-layer-group"></i>
                <span>استعراض معرض المشاريع المنشورة</span>
            </a>
            @if($fair)
                <a href="{{ route('job-fair.public', $fair->id) }}" class="btn-action-outline">
                    <i class="fas fa-home"></i>
                    <span>العودة لصفحة المعرض الرئيسية</span>
                </a>
            @endif
        </div>
    </div>

</body>
</html>
