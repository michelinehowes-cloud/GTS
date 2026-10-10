<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>بوابة التحقق من صحة الشهادات — جامعة طرابلس</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #0b192e 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #ffffff;
        }
        .verify-card {
            background: #ffffff;
            color: #1e293b;
            border-radius: 28px;
            overflow: hidden;
            width: 100%;
            max-width: 680px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        }
        .verify-header-valid {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .verify-header-invalid {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: #ffffff;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .verify-icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
            border: 2px solid rgba(255, 255, 255, 0.4);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .info-val {
            color: #0f172a;
            font-weight: 700;
            font-size: 0.95rem;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="verify-card">
        @if($certificate)
            {{-- VALID CERTIFICATE --}}
            <div class="verify-header-valid">
                <div class="verify-icon-box">
                    <i class="fas fa-check-double"></i>
                </div>
                <h3 class="fw-bold mb-1">شهادة رسمية ومعتمدة</h3>
                <p class="mb-0 text-white-50 small">تم التحقق بنجاح من صحة السجل في قاعدة بيانات جامعة طرابلس</p>
            </div>

            <div class="p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/logo.jpg') }}" alt="شعار المكتب" style="width: 45px; height: 45px; object-fit: contain;">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">مكتب تدريب وتوظيف الخريجين</div>
                            <small class="text-muted">جامعة طرابلس — دولة ليبيا</small>
                        </div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">
                        <i class="fas fa-shield-alt me-1"></i> سارية المفعول
                    </span>
                </div>

                <div class="mb-4">
                    <div class="info-row">
                        <span class="info-label">اسم المتدرب/ـة:</span>
                        <span class="info-val text-primary" style="font-size: 1.1rem;">{{ $certificate->recipient_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">عنوان الشهادة:</span>
                        <span class="info-val">{{ $certificate->title }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">نوع الشهادة:</span>
                        <span class="info-val">
                            <span class="badge {{ $certificate->type_badge_class }} rounded-pill px-2.5 py-1">
                                {{ $certificate->type_label }}
                            </span>
                        </span>
                    </div>
                    @if($certificate->has_company_collaboration)
                    <div class="info-row">
                        <span class="info-label">الشركة الشريكة بالبرنامج:</span>
                        <span class="info-val text-success fw-bold">{{ $certificate->company_name }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">الساعات التدريبية:</span>
                        <span class="info-val">{{ $certificate->hours }} ساعة تدريبية</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الإصدار:</span>
                        <span class="info-val">{{ $certificate->issue_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الرمز الرقمي المعتمد:</span>
                        <span class="info-val font-monospace text-muted">{{ $certificate->certificate_code }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-home me-1"></i> الرئيسية
                    </a>
                    @auth
                        <a href="{{ route('graduate.certificates.show', $certificate->id) }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="fas fa-certificate me-1"></i> استعراض الشهادة
                        </a>
                    @endauth
                </div>
            </div>

        @else
            {{-- INVALID CERTIFICATE --}}
            <div class="verify-header-invalid">
                <div class="verify-icon-box">
                    <i class="fas fa-times"></i>
                </div>
                <h3 class="fw-bold mb-1">الشهادة غير موجودة أو ملغاة</h3>
                <p class="mb-0 text-white-50 small">لم يتم العثور على سجل يطابق كود التحقق المدخل</p>
            </div>

            <div class="p-4 p-md-5 text-center">
                <p class="text-muted mb-4">
                    كود التحقق: <strong class="font-monospace text-danger">{{ $code }}</strong>
                    <br>
                    يرجى التأكد من مسح رمز QR الصحيح أو مراجعة إدارة التدريب بالجامعة.
                </p>
                <a href="{{ route('home') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-home me-1"></i> العودة للصفحة الرئيسية
                </a>
            </div>
        @endif
    </div>

</body>
</html>
