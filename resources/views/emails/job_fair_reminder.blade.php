<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طھط°ظƒظٹط± ط¨ظ…ط¹ط±ط¶ ط§ظ„طھظˆط¸ظٹظپ</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #045db0;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #f59e0b;
        }
        .content {
            padding: 30px;
            color: #374151;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .info-box {
            background: #f8fafc;
            border-right: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-item strong {
            color: #045db0;
            display: inline-block;
            width: 100px;
        }
        .btn {
            display: inline-block;
            background: #f59e0b;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }
        .footer {
            background: #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ط؛ط¯ط§ظ‹ ظ‡ظˆ ظ…ظˆط¹ط¯ظ†ط§!</h1>
            <p style="margin-top: 10px; opacity: 0.9;">ظ…ط¹ط±ط¶ ط§ظ„طھظˆط¸ظٹظپ ط§ظ„ط³ظ†ظˆظٹ - ط¬ط§ظ…ط¹ط© ط·ط±ط§ط¨ظ„ط³</p>
        </div>
        
        <div class="content">
            <div class="greeting">ط£ظ‡ظ„ط§ظ‹ {{ $registration->graduate->name }}طŒ</div>
            
            <p>ظ†ظˆط¯ طھط°ظƒظٹط±ظƒ ط¨ط£ظ† ظ…ظˆط¹ط¯ <strong>{{ $fair->title }}</strong> ط§ظ„ط°ظٹ ظ‚ظ…طھ ط¨ط§ظ„طھط³ط¬ظٹظ„ ظپظٹظ‡ ظ‡ظˆ ط؛ط¯ط§ظ‹.</p>
            
            <div class="info-box">
                <div class="info-item">
                    <strong>ط§ظ„طھط§ط±ظٹط®:</strong> {{ \Carbon\Carbon::parse($fair->event_date)->format('Y-m-d') }}
                </div>
                <div class="info-item">
                    <strong>ط§ظ„ظ…ظˆظ‚ط¹:</strong> {{ $fair->location }}
                </div>
                <div class="info-item">
                    <strong>ط±ظ‚ظ… طھط°ظƒط±طھظƒ:</strong> {{ $registration->registration_number }}
                </div>
            </div>
            
            <p>ظ†ط±ط¬ظˆ ظ…ظ†ظƒ ط§ظ„ط­ط¶ظˆط± ظپظٹ ط§ظ„ظˆظ‚طھ ط§ظ„ظ…ط­ط¯ط¯ ظˆط¥ط¨ط±ط§ط² ط¨ط·ط§ظ‚طھظƒ ط§ظ„ط°ظƒظٹط© (ط£ظˆ ط±ظ…ط² QR) ط¹ظ†ط¯ ط¨ظˆط§ط¨ط© ط§ظ„ط¯ط®ظˆظ„.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('job-fair.my-ticket', $registration->id) }}" class="btn">ط¹ط±ط¶ ط¨ط·ط§ظ‚ط© ط§ظ„ط¯ط®ظˆظ„</a>
            </div>
        </div>
        
        <div class="footer">
            <p>ظ…ظƒطھط¨ طھط¯ط±ظٹط¨ ط§ظ„ط®ط±ظٹط¬ظٹظ† - ط¬ط§ظ…ط¹ط© ط·ط±ط§ط¨ظ„ط³ &copy; {{ date('Y') }}</p>
            <p style="font-size: 12px;">ظ‡ط°ظ‡ ط±ط³ط§ظ„ط© طھظ„ظ‚ط§ط¦ظٹط©طŒ ظٹط±ط¬ظ‰ ط¹ط¯ظ… ط§ظ„ط±ط¯ ط¹ظ„ظٹظ‡ط§.</p>
        </div>
    </div>
</body>
</html>

