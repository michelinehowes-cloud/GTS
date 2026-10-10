<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>تم استلام طلب المشروع بنجاح — {{ $project->title }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --uot-blue:       #0d3882;
            --uot-blue-hover: #09275e;
            --uot-gold:       #eeca3e;
            --uot-gold-dark:  #d97706;
            --slate-50:       #f8fafc;
            --slate-100:      #f1f5f9;
            --slate-200:      #e2e8f0;
            --slate-700:      #334155;
            --slate-800:      #1e293b;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--slate-100);
            color: var(--slate-800);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .success-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-top: 5px solid var(--uot-blue);
            border-radius: 24px;
            padding: 42px 32px;
            max-width: 700px;
            width: 100%;
            text-align: center;
            box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.08);
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .uot-logo-header {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            border: 2px solid var(--uot-gold);
            object-fit: cover;
            margin-bottom: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .icon-celebration {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: #ecfdf5;
            border: 2px solid #10b981;
            color: #059669;
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        .card-title {
            font-size: 1.85rem;
            font-weight: 900;
            color: var(--uot-blue);
            margin-bottom: 10px;
        }

        .card-desc {
            font-size: 1rem;
            color: var(--slate-700);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .project-receipt-box {
            background: #f8fafc;
            border: 1.5px solid var(--slate-200);
            border-radius: 16px;
            padding: 18px 22px;
            text-align: right;
            margin-bottom: 24px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--slate-200);
            font-size: 0.94rem;
        }

        .receipt-row:last-child {
            border-bottom: none;
            padding-bottom: 2px;
        }

        .receipt-label {
            color: #64748b;
            font-weight: 600;
        }

        .receipt-value {
            color: #0f172a;
            font-weight: 700;
        }

        .btn-action-main {
            background: linear-gradient(135deg, var(--uot-blue) 0%, #1565c0 100%);
            border: none;
            color: #ffffff;
            border-radius: 12px;
            padding: 13px 28px;
            font-weight: 800;
            font-size: 0.98rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(13, 56, 130, 0.25);
        }

        .btn-action-main:hover {
            background: linear-gradient(135deg, var(--uot-blue-hover) 0%, var(--uot-blue) 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 56, 130, 0.35);
        }

        .btn-action-outline {
            background: #ffffff;
            border: 1.5px solid var(--slate-300);
            color: var(--slate-700);
            border-radius: 12px;
            padding: 13px 24px;
            font-weight: 700;
            font-size: 0.98rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .btn-action-outline:hover {
            background: var(--slate-100);
            border-color: var(--slate-400);
            color: var(--uot-blue);
        }
    </style>
</head>
<body>

    <div class="success-card">
        <img src="{{ asset('images/logo.jpg') }}" alt="جامعة طرابلس" class="uot-logo-header" onerror="this.style.display='none'">

        <div class="icon-celebration">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="card-title">تم استلام طلب المشروع بنجاح!</h1>
        <p class="card-desc">
            نشكركم على مساهمتكم المتميزة في {{ $fair ? $fair->title : 'معرض التوظيف بجامعة طرابلس' }}. تم تسجيل المشروع في النظام وهو الآن في مرحلة <strong>المراجعة والاعتماد من قِبل اللجنة المنظمة</strong>.
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
                <span class="receipt-value text-primary">{{ $project->project_type ?? 'مشروع تخرج' }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">الحالة الحالية:</span>
                <span class="receipt-value">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                        <i class="fas fa-hourglass-half me-1"></i> بانتظار الاعتماد الإداري
                    </span>
                </span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">تاريخ ووقت التقديم:</span>
                <span class="receipt-value">{{ $project->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="alert alert-primary rounded-3 small text-end mb-4 border-0" style="background-color: #eff6ff; color: #1e40af;">
            <i class="fas fa-info-circle me-1"></i>
            بمجرد قيام اللجنة الإشرافية باعتماد ونشر المشروع، سيظهر مباشرة في المعرض الرقمي العام مع رمز QR خاص به ورقم الجناح المخصص للعرض في حال تمت الموافقة عليه.
        </div>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="btn-action-main">
                <i class="fas fa-th-large"></i>
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
