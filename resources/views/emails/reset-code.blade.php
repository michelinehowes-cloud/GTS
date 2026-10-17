<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>رمز استعادة كلمة المرور - مكتب تدريب الخريجين</title>
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
            text-align: center;
        }
        .email-title {
            font-size: 21px;
            font-weight: 700;
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .verification-code {
            font-size: 34px;
            font-weight: 800;
            color: #0056b3;
            letter-spacing: 6px;
            margin: 25px auto;
            padding: 14px 28px;
            background-color: #f0f7ff;
            border: 2px dashed #0056b3;
            border-radius: 8px;
            display: inline-block;
            direction: ltr;
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 20px 0;
            font-size: 13px;
            color: #92400e;
            text-align: right;
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
            <!-- الهيدر مع الشعار -->
            <div class="email-header">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين" class="logo">
            </div>

            <!-- المحتوى -->
            <div class="email-body">
                <h2 class="email-title">استعادة كلمة المرور</h2>
                <p style="color: #4a5568; margin-bottom: 20px;">
                    لقد تلقينا طلباً لاستعادة كلمة المرور الخاصة بحسابك في منظومة تدريب الخريجين. استخدم الرمز التالي لإكمال العملية:
                </p>

                <div class="verification-code">
                    {{ $code }}
                </div>

                <div class="notice-box">
                    ⚠️ <strong>تنبيه أمني:</strong>
                    هذا الرمز صالح للاستخدام لمدة <strong>15 دقيقة فقط</strong>. لا تشارك هذا الرمز مع أي شخص، حيث لن يطلب منك فريق الدعم هذا الرمز أبداً.
                </div>

                <p style="font-size: 13px; color: #718096; margin-top: 20px;">
                    إذا لم تكن قد طلبت استعادة كلمة المرور، يمكنك تجاهل هذه الرسالة بأمان ويبقى حسابك محمياً.
                </p>
            </div>

            <!-- التذييل -->
            <div class="email-footer">
                <p style="margin: 0;"><strong>مكتب تدريب الخريجين - جامعة طرابلس</strong></p>
                <p style="margin: 4px 0 0;">جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
                <div class="footer-note">
                    هذا البريد تم إرساله تلقائياً من المنظومة، يرجى عدم الرد على هذه الرسالة.
                </div>
            </div>
        </div>
    </div>
</body>
</html>