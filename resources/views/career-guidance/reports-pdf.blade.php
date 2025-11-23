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
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: rtl;
            text-align: right;
            padding: 20px;
            font-size: 12px;
            line-height: 1.6;
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
            font-size: 24px;
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
            border-radius: 5px;
            overflow: hidden;
            background-color: #fff;
        }

        .section-header {
            background-color: #1e3a8a;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #152c69;
        }

        .section-header.info {
            background-color: #36b9cc;
            border-bottom: 1px solid #2a96a5;
        }

        .section-body {
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 8px;
            vertical-align: top;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .stats-row {
            display: table-row;
        }

        .stats-item {
            display: table-cell;
            width: 33.33%;
            border: 1px solid #eee;
            padding: 10px;
            text-align: center;
        }

        .stats-item strong {
            color: #1e3a8a;
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .stats-item span {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .insight-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f8f9fc;
            border-right: 4px solid #36b9cc;
            page-break-inside: avoid;
        }

        .insight-item strong {
            color: #1e3a8a;
            font-size: 13px;
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
            font-size: 10px;
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ \App\Helpers\Arabic::reshape('تقرير الإرشاد المهني المتقدم') }}</h1>
        <p>{{ \App\Helpers\Arabic::reshape('تاريخ التقرير') }}: {{ date('Y-m-d') }}</p>
        <p>{{ \App\Helpers\Arabic::reshape('نظام تدريب الخريجين - جامعة طرابلس') }}</p>
    </div>

    <div class="section">
        <div class="section-header">{{ \App\Helpers\Arabic::reshape('الإحصائيات الرئيسية') }}</div>
        <div class="section-body">
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-item">
                        <strong>{{ \App\Helpers\Arabic::reshape('إجمالي الخريجين') }}</strong>
                        <span>{{ $stats['totalGraduates'] ?? 0 }}</span>
                    </div>
                    <div class="stats-item">
                        <strong>{{ \App\Helpers\Arabic::reshape('خريجين موظفين') }}</strong>
                        <span>{{ $stats['employedGraduates'] ?? 0 }}</span>
                    </div>
                    <div class="stats-item">
                        <strong>{{ \App\Helpers\Arabic::reshape('باحثين عن عمل') }}</strong>
                        <span>{{ $stats['seekingOpportunities'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="stats-row">
                    <div class="stats-item">
                        <strong>{{ \App\Helpers\Arabic::reshape('معدل التوظيف') }}</strong>
                        <span>{{ $stats['employmentRate'] ?? 0 }}%</span>
                    </div>
                    <div class="stats-item">
                        <strong>{{ \App\Helpers\Arabic::reshape('الترشيحات النشطة') }}</strong>
                        <span>{{ $stats['activeNominations'] ?? 0 }}</span>
                    </div>
                    <div class="stats-item">
                        <strong>{{ \App\Helpers\Arabic::reshape('نسبة النجاح') }}</strong>
                        <span>{{ $stats['successRate'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header info">{{ \App\Helpers\Arabic::reshape('استنتاجات وتحليلات ذكية') }}</div>
        <div class="section-body">
            @forelse($insights as $insight)
                <div class="insight-item">
                    <strong>{{ \App\Helpers\Arabic::reshape($insight['title']) }}</strong>:
                    {{ \App\Helpers\Arabic::reshape($insight['description']) }}
                </div>
            @empty
                <p>{{ \App\Helpers\Arabic::reshape('لا توجد استنتاجات متاحة حالياً.') }}</p>
            @endforelse
        </div>
    </div>

    <div class="footer">
        {{ \App\Helpers\Arabic::reshape('نظام تدريب الخريجين - جامعة طرابلس') }} &copy; {{ date('Y') }}
    </div>
</body>

</html>