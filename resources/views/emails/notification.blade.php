<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $notificationTitle }}</title>
    <style>
        /* إعدادات الخطوط والأساسيات */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #333333;
        }

        /* الحاوية الرئيسية */
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
            /* زوايا أقل حدة لتصميم رسمي */
            overflow: hidden;
        }

        /* الترويسة (Header) */
        .email-header {
            background-color: #ffffff;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #0056b3;
            /* خط ملون بسيط يمثل الهوية */
        }

        .logo {
            max-height: 80px;
            width: auto;
        }

        /* المحتوى (Body) */
        .email-body {
            padding: 40px 30px;
            line-height: 1.8;
            font-size: 16px;
        }

        .email-title {
            font-size: 20px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 20px;
            text-align: right;
        }

        .message-content {
            color: #444444;
            margin-bottom: 30px;
        }

        /* الزر (Button) */
        .action-button {
            display: inline-block;
            background-color: #0056b3;
            /* لون أزرق رسمي */
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }

        .action-container {
            text-align: center;
            margin: 30px 0;
        }

        /* التذييل (Footer) */
        .email-footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #eeeeee;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 11px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-card">
            <!-- الترويسة مع الشعار -->
            <div class="email-header">
                <img src="{{ $message->embed(public_path('images/logo.jpg')) }}" alt="شعار المكتب" class="logo">
            </div>

            <!-- محتوى الرسالة -->
            <div class="email-body">
                <div class="email-title">
                    {{ $notificationTitle }}
                </div>

                <div class="message-content">
                    مرحباً،<br><br>
                    {{ $notificationMessage }}
                </div>

                <!-- قسم التفاصيل الإضافية -->
                @if(isset($notificationData['details']) && is_array($notificationData['details']))
                    <div
                        style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                        <h3
                            style="margin-top: 0; color: #0056b3; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            تفاصيل إضافية:</h3>
                        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                            @foreach($notificationData['details'] as $label => $value)
                                <tr>
                                    <td
                                        style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #555; width: 40%;">
                                        {{ $label }}:</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #333;">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

                @if(isset($actionUrl))
                    <div class="action-container">
                        <a href="{{ $actionUrl }}" class="action-button">عرض التفاصيل</a>
                    </div>
                @endif

                <br>
                <p style="font-size: 14px; color: #666;">
                    مع تحيات،<br>
                    <strong>مكتب الخريجين والتدريب المهني</strong>
                </p>
            </div>

            <!-- التذييل -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة.</p>
                <div class="footer-note">
                    هذا البريد تم إرساله تلقائياً من النظام. يرجى عدم الرد عليه.
                </div>
            </div>
        </div>
    </div>
</body>

</html>