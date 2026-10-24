<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $notificationTitle }}</title>
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
            font-size: 20px;
            font-weight: 700;
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 15px;
        }
        .message-content {
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        .details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: 4px solid #0056b3;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 25px;
        }
        .details-title {
            margin-top: 0;
            color: #0056b3;
            font-size: 15px;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .details-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #edf2f7;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-label {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .details-value {
            color: #1e293b;
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
            <!-- الترويسة مع الشعار -->
            <div class="email-header">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين" class="logo">
            </div>

            <!-- محتوى الرسالة -->
            <div class="email-body">
                <h2 class="email-title">{{ $notificationTitle }}</h2>

                <div class="greeting">
                    مرحباً {{ $recipientName ?? 'عزيزنا المستخدم' }}،
                </div>

                <div class="message-content">
                    {!! nl2br(e($notificationMessage)) !!}
                </div>

                <!-- قسم التفاصيل الإضافية إن وجدت -->
                @if(!empty($notificationData['details']) && is_array($notificationData['details']))
                    <div class="details-box">
                        <div class="details-title">تفاصيل إضافية:</div>
                        <table class="details-table">
                            @foreach($notificationData['details'] as $label => $value)
                                @php
                                    $icon = '';
                                    if (str_contains($label, 'توقيت') || str_contains($label, 'وقت') || str_contains($label, 'ساعة')) {
                                        $icon = '⏰ ';
                                    } elseif (str_contains($label, 'تاريخ') || str_contains($label, 'موعد') || str_contains($label, 'أيام')) {
                                        $icon = '📅 ';
                                    } elseif (str_contains($label, 'مكان') || str_contains($label, 'مقر') || str_contains($label, 'قاعة')) {
                                        $icon = '📍 ';
                                    } elseif (str_contains($label, 'مدرب') || str_contains($label, 'محاضر') || str_contains($label, 'متحدث')) {
                                        $icon = '👤 ';
                                    } elseif (str_contains($label, 'نوع')) {
                                        $icon = '🏷️ ';
                                    }
                                    $isHighlight = str_contains($label, 'توقيت') || str_contains($label, 'تاريخ');
                                @endphp
                                <tr>
                                    <td class="details-label">{{ $icon }}{{ $label }}:</td>
                                    <td class="details-value" style="{{ $isHighlight ? 'font-weight: 700; color: #0056b3;' : '' }}">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

                <!-- زر الانتقال إن وجد -->
                @if(!empty($actionUrl))
                    <div class="action-container">
                        <a href="{{ $actionUrl }}" class="action-button">عرض التفاصيل في المنظومة</a>
                    </div>
                @endif

                <p style="font-size: 14px; color: #64748b; margin-top: 25px;">
                    مع تحيات،<br>
                    <strong>مكتب تدريب الخريجين - جامعة طرابلس</strong>
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