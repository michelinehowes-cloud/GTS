<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>باركود استلام السير الذاتية - {{ $company->name_ar }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <style>
        * { font-family: 'Cairo', sans-serif; }
        body {
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .booth-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
            border: 4px solid #045db0;
        }

        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px dashed #e2e8f0;
        }

        .header-logos img {
            height: 70px;
            object-fit: contain;
        }

        .booth-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: #045db0;
            margin-bottom: 10px;
        }

        .booth-subtitle {
            font-size: 1.2rem;
            color: #475569;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .qr-container {
            background: white;
            padding: 20px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }

        #qrcode {
            display: flex;
            justify-content: center;
        }

        .scan-instruction {
            font-size: 1.5rem;
            font-weight: 700;
            color: #10b981;
            margin-top: 20px;
        }

        .scan-instruction i {
            margin-left: 10px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }

        .fair-name {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #e2e8f0;
            font-size: 1.1rem;
            color: #64748b;
            font-weight: 600;
        }

        @media print {
            body {
                background: white;
            }
            .booth-card {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0 auto;
                height: 90vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="booth-card">
        <div class="header-logos">
            <img src="{{ asset('images/logo.jpg') }}" alt="جامعة طرابلس">
            @if($company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name_ar }}">
            @else
                <i class="fas fa-building fa-3x text-secondary"></i>
            @endif
        </div>

        <h1 class="booth-title">{{ $company->name_ar }}</h1>
        <div class="booth-subtitle">نرحب بكم لتقديم سيرتكم الذاتية</div>

        <div class="qr-container">
            <div id="qrcode"></div>
        </div>

        <div class="scan-instruction">
            <i class="fas fa-camera"></i> امسح الرمز لتسليم سيرتك الذاتية فوراً
        </div>

        <div class="fair-name">
            {{ $fair->title }}
        </div>
    </div>

    <!-- Print Button -->
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-lg rounded-pill px-5">
            <i class="fas fa-print me-2"></i> طباعة هذه الصفحة
        </button>
    </div>

    <script>
        // الرابط الذي سيتم توجيه الخريج إليه عند مسح الكود
        const scanUrl = "{{ route('graduate.job-fairs.company.scan', ['fair' => $fair->id, 'company' => $company->id]) }}";

        new QRCode(document.getElementById("qrcode"), {
            text: scanUrl,
            width: 300,
            height: 300,
            colorDark : "#045db0",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
