<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>رمز استعادة كلمة المرور</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #333333;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f5f5f5;
            padding: 40px 0;
        }

        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .email-header {
            background-color: #ffffff;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #0056b3;
        }

        .logo {
            max-height: 80px;
            width: auto;
        }

        .email-body {
            padding: 40px 30px;
            line-height: 1.8;
            font-size: 16px;
            text-align: center;
        }

        .verification-code {
            font-size: 32px;
            font-weight: bold;
            color: #0056b3;
            letter-spacing: 5px;
            margin: 30px 0;
            padding: 15px;
            background-color: #f0f8ff;
            border: 1px dashed #0056b3;
            display: inline-block;
        }

        .email-footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <img src="{{ $message->embed(public_path('images/logo.jpg')) }}" alt="شعار المكتب" class="logo">
            </div>

            <div class="email-body">
                <h3>استعادة كلمة المرور</h3>
                <p>لقد طلبت استعادة كلمة المرور الخاصة بحسابك. استخدم الرمز التالي لإكمال العملية:</p>

                <div class="verification-code">
                    {{ $code }}
                </div>

                <p>هذا الرمز صالح لمدة 15 دقيقة.</p>
                <p>إذا لم تطلب هذا الرمز، يرجى تجاهل هذا البريد.</p>
            </div>

            <div class="email-footer">
                <p>&copy; {{ date('Y') }} مكتب الخريجين والتدريب المهني. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </div>
</body>

</html>