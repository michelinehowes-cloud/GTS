<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>بيانات تفعيل حسابك - منظومة تدريب وتأهيل الخريجين</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #2d3748;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f6f9;
            padding: 35px 15px;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background-color: #ffffff;
            padding: 25px 20px;
            text-align: center;
            border-bottom: 3px solid #0056b3;
        }
        .logo {
            max-height: 75px;
            max-width: 220px;
            height: auto;
            width: auto;
            display: inline-block;
        }
        .email-body {
            padding: 35px 30px;
            line-height: 1.8;
            font-size: 15px;
            text-align: right;
        }
        .email-title {
            font-size: 21px;
            font-weight: 700;
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 12px;
        }
        .credentials-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: 4px solid #0056b3;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .cred-row {
            margin-bottom: 12px;
            font-size: 15px;
        }
        .cred-row:last-child {
            margin-bottom: 0;
        }
        .cred-label {
            font-weight: 600;
            color: #475569;
            display: inline-block;
            min-width: 130px;
        }
        .cred-value {
            font-weight: 700;
            color: #0056b3;
            background: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px dashed #94a3b8;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-right: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 15px 18px;
            margin: 20px 0;
            font-size: 14px;
            color: #92400e;
        }
        .notice-title {
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .notice-box ul {
            margin: 0;
            padding-right: 20px;
        }
        .notice-box li {
            margin-bottom: 5px;
        }
        .action-container {
            text-align: center;
            margin: 30px 0 20px;
        }
        .action-button {
            display: inline-block;
            background-color: #0056b3;
            color: #ffffff !important;
            padding: 13px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 2px 6px rgba(0, 86, 179, 0.25);
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 22px 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            line-height: 1.6;
        }
        .footer-note {
            margin-top: 8px;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <!-- الهيدر والشعار الرسمي -->
            <div class="email-header">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب وتأهيل الخريجين" class="logo">
            </div>

            <!-- المحتوى الأساسي -->
            <div class="email-body">
                <h2 class="email-title">بيانات تفعيل حسابك في المنظومة</h2>
                
                <div class="greeting">مرحباً بك يا {{ $graduateName }}،</div>
                
                <p>يسعدنا إعلامك بأنه تم إنشاء وتفعيل حسابك الرسمي في <strong>منظومة تدريب وتأهيل الخريجين بجامعة طرابلس</strong>.</p>
                <p>يمكنك الآن تسجيل الدخول للاطلاع على فرص التدريب، والتقديم على الوظائف الشاغرة، وتنزيل بطاقة الخريج الذكية الخاصة بك.</p>

                <!-- صندوق بيانات الدخول -->
                <div class="credentials-box">
                    <div class="cred-row">
                        <span class="cred-label">البريد الإلكتروني:</span>
                        <span class="cred-value">{{ $email }}</span>
                    </div>
                    <div class="cred-row">
                        <span class="cred-label">كلمة المرور المؤقتة:</span>
                        <span class="cred-value">{{ $temporaryPassword }}</span>
                    </div>
                </div>

                <!-- صندوق التنبيهات الأمنية -->
                <div class="notice-box">
                    <div class="notice-title">تعليمات أمنية هامة:</div>
                    <ul>
                        <li>يجب عليك <strong>تغيير كلمة المرور</strong> فور تسجيل دخولك لأول مرة لضمان خصوصية وأمان حسابك.</li>
                        <li>لا تشارك كلمة المرور مع أي طرف آخر.</li>
                        <li>احرص على استكمال وتحديث سيرتك الذاتية ومهاراتك في ملفك الشخصي للحصول على ترشيحات وظيفية ملائمة.</li>
                    </ul>
                </div>

                <!-- زر الإجراء -->
                <div class="action-container">
                    <a href="{{ url('/login') }}" class="action-button">تسجيل الدخول إلى حسابك الآن</a>
                </div>

                <p style="font-size: 14px; color: #64748b; margin-top: 25px;">
                    إذا واجهتك أي صعوبة أثناء تسجيل الدخول، يرجى التواصل مع مكتب تدريب الخريجين بجامعة طرابلس.
                </p>
            </div>

            <!-- التذييل الرسمي -->
            <div class="email-footer">
                <p style="margin: 0;"><strong>مكتب تدريب وتأهيل الخريجين - جامعة طرابلس</strong></p>
                <p style="margin: 4px 0 0;">جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
                <div class="footer-note">
                    هذا البريد الإلكتروني أُرسل تلقائياً من المنظومة، يرجى عدم الرد على هذه الرسالة.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
