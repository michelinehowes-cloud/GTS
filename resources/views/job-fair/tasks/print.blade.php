<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>جامعة طرابلس - مشروع سنة 2026 - نموذج المهام</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .page-container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
        }
        .header-title {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .official-table th, .official-table td {
            border: 1px solid #000 !important;
            vertical-align: middle;
            padding: 10px;
        }
        .official-table th {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            text-align: center;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            width: 450px;
            pointer-events: none;
        }
        @media print {
            body {
                background: #fff;
            }
            .page-container {
                box-shadow: none;
                padding: 15px;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center my-3">
    <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
        <i class="fas fa-print me-1"></i> طباعة النموذج الرسمي
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary px-3 py-2 ms-2">
        إغلاق النافذة
    </button>
</div>

<div class="page-container position-relative">
    <!-- Header -->
    <div class="row align-items-center mb-3">
        <div class="col-3 text-start">
            <img src="{{ asset('images/uot_logo.png') }}" alt="شعار جامعة طرابلس" style="max-height: 80px;" onerror="this.style.display='none'">
        </div>
        <div class="col-6 text-center">
            <h5 class="fw-bold mb-1">جامعة طرابلس</h5>
            <h6 class="fw-bold mb-1">مكتب تدريب الخريجين</h6>
            <h6 class="fw-bold text-primary mb-1">مشروع سنة 2026</h6>
            <h4 class="fw-bolder mt-2 text-dark">نموذج المهام</h4>
        </div>
        <div class="col-3 text-end">
            <img src="{{ asset('images/logo.jpg') }}" alt="مكتب تدريب الخريجين" style="max-height: 80px;" onerror="this.style.display='none'">
        </div>
    </div>

    <div class="border-top border-dark border-2 mb-3"></div>

    <!-- Intro Text -->
    <p class="text-center fw-semibold px-4 mb-4" style="line-height: 1.8; font-size: 1.02rem;">
        جاء إعداد هذا النموذج في إطار تنظيم ومتابعة مهام الموظفين والفرق العاملة على التجهيز لمعرض التوظيف 2026،
        بما يضمن وضوح المسؤوليات، ودقة التنفيذ، وسلاسة العمل والالتزام بالجداول الزمنية المعتمدة.
    </p>

    <!-- Employee & Team Info -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2 fs-6">
        <div>
            <strong>اسم الموظف:</strong>
            <span class="border-bottom border-dark px-3 fw-bold text-primary">
                {{ $employee ? $employee->name : '..........................................................' }}
            </span>
        </div>
        <div>
            <strong>التابع لفريق:</strong>
            <span class="border-bottom border-dark px-3 fw-bold">
                {{ $assignedTeam ?? '..........................................................' }}
            </span>
        </div>
    </div>

    <!-- Official Tasks Table -->
    <table class="table official-table mb-4">
        <thead>
            <tr>
                <th style="width: 38%;">المهام</th>
                <th style="width: 15%;">تاريخ البدء</th>
                <th style="width: 15%;">تاريخ التسليم</th>
                <th style="width: 14%;">التقييم</th>
                <th style="width: 18%;">الملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->task_description }}</td>
                    <td class="text-center font-monospace">{{ $t->start_date?->format('Y/m/d') }}</td>
                    <td class="text-center font-monospace">{{ $t->due_date?->format('Y/m/d') }}</td>
                    <td class="text-center fw-bold text-success">{{ $t->evaluation_score ?? ($t->status == 'completed' ? 'مكتمل' : 'قيد التنفيذ') }}</td>
                    <td>{{ $t->notes ?? '-' }}</td>
                </tr>
            @empty
                @for($i = 0; $i < 6; $i++)
                    <tr style="height: 48px;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse

            {{-- ملء باقي الصفوف إذا كانت المهام أقل من 6 لضمان مظهر الصفحة الكامل --}}
            @if($tasks->count() > 0 && $tasks->count() < 6)
                @for($j = $tasks->count(); $j < 6; $j++)
                    <tr style="height: 48px;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Footer Signatures -->
    <div class="row mt-5 pt-4 text-center fs-6">
        <div class="col-4">
            <strong>اسم مسؤول الفريق:</strong><br>
            <span class="fw-bold mt-2 d-inline-block">{{ $supervisor ? $supervisor->name : '....................................' }}</span>
        </div>
        <div class="col-4">
            <strong>التوقيع:</strong><br>
            <span class="mt-2 d-inline-block text-success fw-bold">
                <i class="fas fa-check-circle me-1"></i> معتمد رسمياً
            </span>
        </div>
        <div class="col-4">
            <strong>التاريخ:</strong><br>
            <span class="mt-2 d-inline-block font-monospace">{{ date('Y / m / d') }}</span>
        </div>
    </div>
</div>

</body>
</html>
