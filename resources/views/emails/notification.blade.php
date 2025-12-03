<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notificationTitle }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-body {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.8;
        }

        .notification-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background-color: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .notification-message {
            font-size: 16px;
            color: #555555;
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-right: 4px solid #667eea;
            border-radius: 6px;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #e9ecef;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }

        .info-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #004085;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 20px;
            }

            .email-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="notification-icon">
                🔔
            </div>
            <h1>{{ $notificationTitle }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="notification-message">
                {{ $notificationMessage }}
            </div>

            <div class="divider"></div>

            <div class="info-box">
                <strong>ℹ️ ملاحظة:</strong> هذا إشعار تلقائي من نظام التدريب والتوظيف للخريجين. يرجى عدم الرد على هذا
                البريد الإلكتروني.
            </div>

            @if(isset($actionUrl))
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="button">عرض التفاصيل</a>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 10px 0;">
                <strong>نظام التدريب والتوظيف للخريجين</strong>
            </p>
            <p style="margin: 0; color: #aaaaaa;">
                © {{ date('Y') }} جميع الحقوق محفوظة
            </p>
            <p style="margin: 10px 0 0 0; font-size: 12px;">
                تم الإرسال في: {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
</body>

</html>