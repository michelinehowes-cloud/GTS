<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>تذكير بموعد معرض التوظيف - جامعة طرابلس</title>
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
        .banner {
            background: linear-gradient(135deg, #045db0 0%, #003e7e 100%);
            color: #ffffff;
            padding: 25px 20px;
            text-align: center;
        }
        .banner h1 {
            margin: 0;
            font-size: 22px;
            color: #ffffff;
            font-weight: 700;
        }
        .banner p {
            margin: 8px 0 0;
            font-size: 14px;
            color: #e0f2fe;
        }
        .email-body {
            padding: 35px 30px;
            line-height: 1.8;
            font-size: 15px;
            text-align: right;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: 4px solid #0056b3;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .info-row {
            margin-bottom: 12px;
            font-size: 14px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            display: inline-block;
            min-width: 120px;
        }
        .info-value {
            font-weight: 700;
            color: #0056b3;
        }
        .ticket-badge {
            background-color: #eff6ff;
            color: #0056b3;
            padding: 3px 10px;
            border-radius: 4px;
            border: 1px dashed #3b82f6;
            font-family: monospace;
            font-size: 15px;
        }
        .action-container {
            text-align: center;
            margin: 30px 0 15px;
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
            <!-- الهيدر والشعار -->
            <div class="email-header">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب وتأهيل الخريجين" class="logo">
            </div>

            <!-- شريط التذكير -->
            <div class="banner">
                <h1>غداً هو موعدنا! 🎯</h1>
                <p>معرض التوظيف السنوي - مكتب تدريب وتأهيل الخريجين بجامعة طرابلس</p>
            </div>

            <!-- المحتوى الأساسي -->
            <div class="email-body">
                <div class="greeting">أهلاً بك {{ $registration->graduate->name ?? 'عزيزنا الخريج' }}،</div>
                
                <p>نود تذكيرك بأن موعد فعاليات <strong>{{ $fair->title }}</strong> الذي قمت بالتسجيل فيه سيبدأ غداً بإذن الله.</p>
                <p>استعد للقاء ممثلي كبرى الشركات والمؤسسات، واستكشاف أحدث الفرص الوظيفية والتدريبية المتاحة لخريجي جامعة طرابلس.</p>

                <!-- تفاصيل المعرض والتذكرة -->
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">📅 التاريخ:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($fair->event_date)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">📍 الموقع:</span>
                        <span class="info-value">{{ $fair->location }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">🎫 رقم التسجيل:</span>
                        <span class="ticket-badge">{{ $registration->registration_number }}</span>
                    </div>
                </div>

                <p style="font-size: 14px; color: #475569;">
                    💡 <strong>تنبيه هام:</strong> نرجو الحضور في الموعد المحدد مع إبراز بطاقة الدخول الذكية أو رمز الاستجابة السريع (QR Code) عند بوابة الدخول لتسجيل حضورك.
                </p>

                <!-- زر عرض التذكرة -->
                <div class="action-container">
                    <a href="{{ route('job-fair.my-ticket', $registration->id) }}" class="action-button">عرض بطاقة وتذكرة الدخول</a>
                </div>
            </div>

            <!-- التذييل -->
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
