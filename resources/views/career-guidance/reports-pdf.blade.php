<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>تقرير الإرشاد المهني المتقدم</title>
    <style>
        @page {
            margin: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
            font-size: 14px;
            line-height: 1.8;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #1e3a8a;
            font-size: 26px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            overflow: hidden;
            background-color: #fff;
        }

        .section-header {
            background-color: #1e3a8a;
            color: white;
            padding: 12px 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .section-header.info {
            background-color: #36b9cc;
        }

        .section-body {
            padding: 15px;
        }

        .stats-grid {
            width: 100%;
        }

        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .stats-item {
            display: table-cell;
            width: 33.33%;
            border: 1px solid #eee;
            padding: 12px;
            text-align: center;
        }

        .stats-item strong {
            color: #1e3a8a;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .stats-item span {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .insight-item {
            padding: 12px;
            margin-bottom: 12px;
            background-color: #f8f9fc;
            border-right: 4px solid #36b9cc;
        }

        .insight-item strong {
            color: #1e3a8a;
            font-size: 14px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 11px;
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>تقرير الإرشاد المهني المتقدم</h1>
        <p>تاريخ التقرير: {{ date('Y-m-d') }}</p>
        <p>نظام تدريب الخريجين - جامعة طرابلس</p>
    </div>

    <div class="section">
        <div class="section-header">الإحصائيات الرئيسية</div>
        <div class="section-body">
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-item">
                        <strong>إجمالي الخريجين</strong>
                        <span>{{ $stats['totalGraduates'] ?? 0 }}</span>
                    </div>
                    <div class="stats-item">
                        <strong>خريجين موظفين</strong>
                        <span>{{ $stats['employedGraduates'] ?? 0 }}</span>
                    </div>
                    <div class="stats-item">
                        <strong>باحثين عن عمل</strong>
                        <span>{{ $stats['seekingOpportunities'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="stats-row">
                    <div class="stats-item">
                        <strong>معدل التوظيف</strong>
                        <span>{{ $stats['employmentRate'] ?? 0 }}%</span>
                    </div>
                    <div class="stats-item">
                        <strong>الترشيحات النشطة</strong>
                        <span>{{ $stats['activeNominations'] ?? 0 }}</span>
                    </div>
                    <div class="stats-item">
                        <strong>نسبة النجاح</strong>
                        <span>{{ $stats['successRate'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header info">استنتاجات وتحليلات ذكية</div>
        <div class="section-body">
            @forelse($insights as $insight)
                <div class="insight-item">
                    <strong>{{ $insight['title'] }}</strong>: {{ $insight['description'] }}
                </div>
            @empty
                <p>لا توجد استنتاجات متاحة حالياً.</p>
            @endforelse
        </div>
    </div>

    <div class="footer">
        نظام تدريب الخريجين - جامعة طرابلس &copy; {{ date('Y') }}
    </div>
</body>

</html>