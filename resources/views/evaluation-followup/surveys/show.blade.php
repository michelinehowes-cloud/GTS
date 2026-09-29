@extends('layouts.app')

@section('title', 'التقرير الإحصائي: ' . $survey->title)
@section('page-title', 'التقرير الإحصائي للاستبيان')

@push('styles')
<style>
/* Hero Section */
.survey-report-hero {
    background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 50%, #0284c7 100%);
    border-radius: 20px;
    padding: 30px 36px;
    margin-bottom: 26px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 44px rgba(12, 74, 110, 0.28);
}
.survey-report-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 220px;
    height: 220px;
    background: rgba(255, 255, 255, 0.06);
    border-radius: 50%;
}
.hero-tag {
    background: rgba(186, 230, 253, 0.22);
    border: 1px solid rgba(186, 230, 253, 0.4);
    color: #bae6fd;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}

/* KPI Cards */
.kpi-stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.kpi-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.09);
}
.kpi-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--card-accent);
}
.kpi-icon-wrap {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    background: var(--card-bg);
    color: var(--card-color);
}
.kpi-val {
    font-size: 1.85rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin: 0;
}
.kpi-lbl {
    font-size: 0.82rem;
    color: #64748b;
    font-weight: 600;
    margin-top: 4px;
}
.kc-blue { --card-accent: #0284c7; --card-bg: #f0f9ff; --card-color: #0284c7; }
.kc-amber { --card-accent: #f59e0b; --card-bg: #fffbeb; --card-color: #d97706; }
.kc-green { --card-accent: #10b981; --card-bg: #ecfdf5; --card-color: #059669; }
.kc-purple { --card-accent: #8b5cf6; --card-bg: #f5f3ff; --card-color: #7c3aed; }

/* Custom Tabs */
.custom-nav-tabs {
    border-bottom: 2px solid #e2e8f0;
    gap: 8px;
    margin-bottom: 24px;
}
.custom-nav-tabs .nav-link {
    border: none;
    padding: 12px 22px;
    font-weight: 700;
    font-size: 0.92rem;
    color: #64748b;
    border-radius: 12px 12px 0 0;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.custom-nav-tabs .nav-link:hover {
    color: #0284c7;
    background: #f8fafc;
}
.custom-nav-tabs .nav-link.active {
    color: #0284c7;
    background: #fff;
    border-bottom: 3px solid #0284c7;
}

/* Question Analytics Cards */
.q-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 22px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 22px;
    overflow: hidden;
    transition: border-color 0.2s;
}
.q-card:hover {
    border-color: #cbd5e1;
}
.q-card-header {
    padding: 20px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.q-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: baseline;
    gap: 10px;
}
.q-num-badge {
    background: #0284c7;
    color: #fff;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    font-weight: 800;
    flex-shrink: 0;
}
.q-card-body {
    padding: 24px;
}

/* Rating score box */
.rating-highlight-box {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fde68a;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
}
.rating-big-num {
    font-size: 3.2rem;
    font-weight: 900;
    color: #b45309;
    line-height: 1;
}
.stars-display {
    color: #f59e0b;
    font-size: 1.25rem;
    margin: 10px 0;
}

/* Progress bars for options & stars */
.stat-bar-row {
    margin-bottom: 14px;
}
.stat-bar-labels {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.88rem;
    margin-bottom: 6px;
}
.stat-bar-name {
    font-weight: 600;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
}
.stat-bar-val {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.85rem;
}
.stat-progress {
    height: 10px;
    background: #f1f5f9;
    border-radius: 20px;
    overflow: hidden;
}
.stat-progress-bar {
    height: 100%;
    border-radius: 20px;
    transition: width 0.6s ease;
}

/* Qualitative text answers */
.text-answers-list {
    max-height: 380px;
    overflow-y: auto;
    padding-right: 6px;
}
.text-answer-bubble {
    background: #f8fafc;
    border-right: 4px solid #0284c7;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 12px;
}
.text-answer-text {
    font-size: 0.92rem;
    color: #1e293b;
    line-height: 1.6;
    margin-bottom: 6px;
}
.text-answer-meta {
    font-size: 0.76rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Print Friendly Rules */
@media print {
    body { background: #fff !important; }
    .navbar, .sidebar, .custom-nav-tabs, .btn, .card-tools, .survey-report-hero .btn { display: none !important; }
    .survey-report-hero { background: #0284c7 !important; color: #fff !important; box-shadow: none !important; }
    .q-card { box-shadow: none !important; border: 1px solid #ccc !important; break-inside: avoid; }
    .kpi-stat-card { border: 1px solid #ccc !important; box-shadow: none !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Hero Header -->
    <div class="survey-report-hero">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="hero-tag">
                    <i class="fas fa-chart-line"></i>
                    <span>التقرير والتحليل الإحصائي المتقدم</span>
                </div>
                <h1 class="text-white fw-bold m-0" style="font-size: 1.65rem;">
                    {{ $survey->title }}
                </h1>
                <p class="text-white-50 mt-1 mb-0" style="font-size: 0.88rem;">
                    {{ $survey->description ?? 'تحليل إحصائي شامل لردود وتقييمات الفئة المستهدفة' }}
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('evaluation-followup.surveys.export-responses', $survey->id) }}" class="btn btn-success fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" title="تنزيل شيت إكسل بالردود">
                    <i class="fas fa-file-excel"></i>
                    <span>تصدير إلى Excel</span>
                </a>
                <button onclick="window.print()" class="btn btn-light bg-white text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
                    <i class="fas fa-print text-primary"></i>
                    <span>طباعة التقرير</span>
                </button>
                <button type="button" class="btn text-white fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5" style="background:#8b5cf6;" onclick="openSaveAsTemplateModal({{ $survey->id }}, '{{ addslashes($survey->title) }}', '{{ addslashes($survey->description ?? '') }}', '{{ $survey->type ?? 'general' }}')">
                    <i class="fas fa-bookmark"></i>
                    <span>حفظ كقالب</span>
                </button>
                <a href="{{ route('evaluation-followup.surveys.edit', $survey) }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
                    <i class="fas fa-edit"></i>
                    <span>تعديل</span>
                </a>
                <a href="{{ route('evaluation-followup.surveys.index') }}" class="btn btn-outline-light py-2 px-3 rounded-3 d-flex align-items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <!-- إجمالي الردود -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-stat-card kc-blue">
                <div class="kpi-icon-wrap"><i class="fas fa-comments"></i></div>
                <div>
                    <h3 class="kpi-val">{{ number_format($analytics['total_responses']) }}</h3>
                    <div class="kpi-lbl">إجمالي الردود المستلمة</div>
                    <small class="text-muted" style="font-size: 0.74rem;">
                        {{ $analytics['internal_count'] }} داخلي • {{ $analytics['external_count'] }} خارجي
                    </small>
                </div>
            </div>
        </div>

        <!-- مؤشر الرضا العام -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-stat-card kc-amber">
                <div class="kpi-icon-wrap"><i class="fas fa-star"></i></div>
                <div>
                    @if($analytics['overall_satisfaction'] !== null)
                        <h3 class="kpi-val">{{ number_format($analytics['overall_satisfaction'], 1) }} <span style="font-size: 1rem; color: #94a3b8;">/ 5.0</span></h3>
                        <div class="kpi-lbl">مؤشر الرضا العام ({{ $analytics['satisfaction_percentage'] }}%)</div>
                        <small class="text-warning" style="font-size: 0.74rem;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= round($analytics['overall_satisfaction']) ? '' : '-half-alt' }}"></i>
                            @endfor
                        </small>
                    @else
                        <h3 class="kpi-val" style="font-size: 1.4rem;">لا توجد أسئلة تقييم</h3>
                        <div class="kpi-lbl">مؤشر الرضا العام</div>
                        <small class="text-muted" style="font-size: 0.74rem;">استبيان وصفي / خيارات</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- نسبة المشاركة -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-stat-card kc-green">
                <div class="kpi-icon-wrap"><i class="fas fa-user-check"></i></div>
                <div>
                    <h3 class="kpi-val">{{ number_format($analytics['completion_rate'], 1) }}<span style="font-size: 1rem; color: #94a3b8;">%</span></h3>
                    <div class="kpi-lbl">نسبة الاستجابة للجمهور</div>
                    <small class="text-muted" style="font-size: 0.74rem;">
                        من إجمالي {{ number_format($analytics['target_audience_count']) }} مستهدف
                    </small>
                </div>
            </div>
        </div>

        <!-- حالة الاستبيان والفترة -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-stat-card kc-purple">
                <div class="kpi-icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="mb-1">
                        @if($survey->isActive())
                            <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i> نشط حالياً
                            </span>
                        @elseif($survey->start_date && $survey->start_date->isFuture())
                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size: 0.72rem;">مجدول</span>
                        @else
                            <span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size: 0.72rem;">منتهي</span>
                        @endif
                    </div>
                    <div class="kpi-lbl">الفترة الزمنية</div>
                    <small class="text-muted" style="font-size: 0.74rem;">
                        {{ $survey->start_date ? $survey->start_date->format('Y-m-d') : '—' }} إلى {{ $survey->end_date ? $survey->end_date->format('Y-m-d') : '—' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav custom-nav-tabs" id="surveyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="stats-tab" data-bs-toggle="tab" data-bs-target="#statsTabContent" type="button" role="tab">
                <i class="fas fa-poll"></i>
                <span>التقرير والتحليل الإحصائي للنتائج</span>
                <span class="badge bg-primary rounded-pill ms-1">{{ $analytics['total_responses'] }} رد</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="structure-tab" data-bs-toggle="tab" data-bs-target="#structureTabContent" type="button" role="tab">
                <i class="fas fa-list-check"></i>
                <span>أسئلة الاستبيان وإعداداته</span>
                <span class="badge bg-light text-dark rounded-pill ms-1">{{ $analytics['questions_count'] }} سؤال</span>
            </button>
        </li>
        <li class="nav-item ms-auto" role="presentation">
            <a href="{{ route('evaluation-followup.survey-responses.index') }}?survey_id={{ $survey->id }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 mt-2">
                <i class="fas fa-users-viewfinder"></i>
                <span>عرض قائمة الردود الفردية</span>
            </a>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="surveyTabsContent">

        <!-- ==================== TAB 1: التحليل الإحصائي المباشر ==================== -->
        <div class="tab-pane fade show active" id="statsTabContent" role="tabpanel">

            @if($analytics['total_responses'] == 0)
                <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 mb-4">
                    <div class="py-4">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-primary mb-3" style="width: 76px; height: 76px; font-size: 2rem;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h4 class="fw-bold text-dark">لا توجد ردود مسجلة لهذا الاستبيان حتى الآن</h4>
                        <p class="text-muted mx-auto" style="max-width: 520px; font-size: 0.92rem;">
                            بمجرد قيام الفئات المستهدفة (الخريجين أو الشركات) بتعبئة الاستبيان، سيقوم النظام تلقائياً باحتساب كافة الإحصائيات والمتوسطات ونسب الاختيارات ورسم مؤشرات الأداء بشكل فوري.
                        </p>
                        @if($survey->is_public)
                            <div class="mt-3">
                                <span class="text-muted small d-block mb-1">رابط الاستبيان العام للمشاركة:</span>
                                <code class="p-2 bg-light rounded text-primary">{{ $survey->public_url }}</code>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Loop over Question Analytics -->
            @foreach($analytics['questions_analytics'] as $q)
                <div class="q-card">
                    <div class="q-card-header">
                        <div class="d-flex align-items-start gap-3 flex-grow-1">
                            <span class="q-num-badge">{{ $q['index'] + 1 }}</span>
                            <div>
                                <h3 class="q-card-title">
                                    {{ $q['question'] }}
                                    @if($q['required'])
                                        <span class="text-danger" title="إجباري">*</span>
                                    @endif
                                </h3>
                                @if($q['description'])
                                    <div class="text-muted small mt-1">{{ $q['description'] }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <!-- Type Badge -->
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">
                                @switch($q['type'])
                                    @case('rating')
                                        <i class="fas fa-star text-warning me-1"></i> تقييم بالنجوم
                                        @break
                                    @case('radio')
                                        <i class="fas fa-dot-circle text-primary me-1"></i> اختيار واحد
                                        @break
                                    @case('checkbox')
                                        <i class="fas fa-check-square text-success me-1"></i> خيارات متعددة
                                        @break
                                    @case('select')
                                        <i class="fas fa-list text-info me-1"></i> قائمة منسدلة
                                        @break
                                    @case('text')
                                    @case('textarea')
                                        <i class="fas fa-comment-dots text-secondary me-1"></i> نص وإجابة نوعية
                                        @break
                                    @case('number')
                                        <i class="fas fa-hashtag text-purple me-1"></i> رقم عددي
                                        @break
                                    @default
                                        {{ $q['type'] }}
                                @endswitch
                            </span>

                            <!-- Responses Count Badge -->
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">
                                <i class="fas fa-user-check me-1"></i>
                                {{ $q['total_answered'] }} رد ({{ $q['answer_rate'] }}%)
                            </span>
                        </div>
                    </div>

                    <div class="q-card-body">
                        <!-- ==================== CASE 1: RATING ==================== -->
                        @if($q['type'] === 'rating')
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-md-4 col-lg-3">
                                    <div class="rating-highlight-box">
                                        <div class="rating-big-num">{{ number_format($q['average_score'] ?? 0, 1) }}</div>
                                        <div class="text-muted small fw-bold mt-1">من {{ $q['max_rating'] }} نجوم</div>
                                        <div class="stars-display">
                                            @php $avg = $q['average_score'] ?? 0; @endphp
                                            @for($s = 1; $s <= ($q['max_rating'] ?? 5); $s++)
                                                <i class="fas fa-star{{ $s <= round($avg) ? '' : ' text-black-50' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.8rem;">
                                            {{ $q['percentage_score'] }}% نسبة الرضا
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 col-lg-9">
                                    <div class="p-2">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;">
                                            <i class="fas fa-chart-bar text-warning me-1.5"></i> توزيع الدرجات وتقييمات المشاركين:
                                        </h6>
                                        @foreach($q['distribution'] ?? [] as $starLevel => $starData)
                                            <div class="stat-bar-row">
                                                <div class="stat-bar-labels">
                                                    <span class="stat-bar-name">
                                                        <span>{{ $starLevel }}</span>
                                                        <i class="fas fa-star text-warning" style="font-size: 0.75rem;"></i>
                                                    </span>
                                                    <span class="stat-bar-val">
                                                        <span class="text-dark fw-bold">{{ $starData['count'] }} مشارك</span>
                                                        <span class="text-muted ms-1">({{ $starData['percentage'] }}%)</span>
                                                    </span>
                                                </div>
                                                <div class="stat-progress">
                                                    <div class="stat-progress-bar bg-warning" style="width: {{ $starData['percentage'] }}%;"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        <!-- ==================== CASE 2: RADIO OR SELECT ==================== -->
                        @elseif(in_array($q['type'], ['radio', 'select']))
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="mb-2 d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">
                                            <i class="fas fa-poll-h text-primary me-1.5"></i> توزيع اختيارات المشاركين:
                                        </h6>
                                        @if(!empty($q['top_choice']))
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.76rem;">
                                                🏆 الخيار الأكثر تصويتاً: <strong>{{ $q['top_choice'] }}</strong> ({{ $q['top_choice_percentage'] }}%)
                                            </span>
                                        @endif
                                    </div>

                                    @php
                                        $barColors = ['#0284c7', '#10b981', '#6366f1', '#f59e0b', '#ec4899', '#14b8a6'];
                                        $colorIdx = 0;
                                    @endphp

                                    @foreach($q['options_breakdown'] ?? [] as $optName => $optData)
                                        @php
                                            $currentColor = $barColors[$colorIdx % count($barColors)];
                                            $colorIdx++;
                                            $isTop = ($optName === ($q['top_choice'] ?? null) && ($optData['count'] ?? 0) > 0);
                                        @endphp
                                        <div class="stat-bar-row mb-3 p-2 rounded-3 {{ $isTop ? 'bg-light' : '' }}">
                                            <div class="stat-bar-labels">
                                                <span class="stat-bar-name">
                                                    <i class="fas fa-circle" style="color: {{ $currentColor }}; font-size: 0.55rem;"></i>
                                                    <strong class="{{ $isTop ? 'text-primary' : 'text-dark' }}">{{ $optName }}</strong>
                                                    @if($isTop)
                                                        <span class="badge bg-primary text-white ms-1 px-1.5 py-0.5 rounded-pill" style="font-size: 0.65rem;">الأعلى</span>
                                                    @endif
                                                </span>
                                                <span class="stat-bar-val">
                                                    <span class="fw-bold text-dark">{{ $optData['count'] }} صوت</span>
                                                    <span class="badge bg-light text-dark border ms-1">{{ $optData['percentage'] }}%</span>
                                                </span>
                                            </div>
                                            <div class="stat-progress" style="height: 12px;">
                                                <div class="stat-progress-bar" style="width: {{ $optData['percentage'] }}%; background-color: {{ $currentColor }};"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        <!-- ==================== CASE 3: CHECKBOX ==================== -->
                        @elseif($q['type'] === 'checkbox')
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;">
                                        <i class="fas fa-tasks text-success me-1.5"></i> نسبة وتكرار كل خيار محدد:
                                    </h6>

                                    @php
                                        $checkColors = ['#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#f59e0b'];
                                        $cIdx = 0;
                                    @endphp

                                    @foreach($q['options_breakdown'] ?? [] as $optName => $optData)
                                        @php
                                            $currentColor = $checkColors[$cIdx % count($checkColors)];
                                            $cIdx++;
                                        @endphp
                                        <div class="stat-bar-row mb-3">
                                            <div class="stat-bar-labels">
                                                <span class="stat-bar-name">
                                                    <i class="fas fa-check-circle" style="color: {{ $currentColor }}; font-size: 0.8rem;"></i>
                                                    <span>{{ $optName }}</span>
                                                </span>
                                                <span class="stat-bar-val">
                                                    <span class="fw-bold text-dark">{{ $optData['count'] }} اختيار</span>
                                                    <span class="badge bg-light text-dark border ms-1">{{ $optData['percentage'] }}%</span>
                                                </span>
                                            </div>
                                            <div class="stat-progress">
                                                <div class="stat-progress-bar" style="width: {{ $optData['percentage'] }}%; background-color: {{ $currentColor }};"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        <!-- ==================== CASE 4: TEXT / TEXTAREA ==================== -->
                        @elseif(in_array($q['type'], ['text', 'textarea']))
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">
                                        <i class="fas fa-quote-right text-primary me-1.5"></i> إجابات وملاحظات المشاركين النوعية ({{ $q['text_responses_count'] ?? 0 }}):
                                    </h6>
                                </div>

                                @if(!empty($q['text_responses']) && count($q['text_responses']) > 0)
                                    <div class="text-answers-list">
                                        @foreach($q['text_responses'] as $txtItem)
                                            <div class="text-answer-bubble">
                                                <div class="text-answer-text">
                                                    "{{ $txtItem['value'] }}"
                                                </div>
                                                <div class="text-answer-meta">
                                                    <span><i class="fas fa-user-circle me-1 text-primary"></i> {{ $txtItem['user_name'] }}</span>
                                                    <span><i class="fas fa-tag me-1 text-muted"></i> {{ $txtItem['user_role'] }}</span>
                                                    @if($txtItem['date'])
                                                        <span><i class="fas fa-clock me-1 text-muted"></i> {{ is_string($txtItem['date']) ? $txtItem['date'] : $txtItem['date']->format('Y-m-d H:i') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 text-center text-muted bg-light rounded-3">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block text-black-50"></i>
                                        لا توجد إجابات نصية مقدمة لهذا السؤال حتى الآن
                                    </div>
                                @endif
                            </div>

                        <!-- ==================== CASE 5: NUMBER ==================== -->
                        @elseif($q['type'] === 'number')
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded-3 text-center">
                                        <div class="text-muted small">المتوسط الحسابي</div>
                                        <div class="fw-bold text-primary fs-4">{{ $q['average_number'] ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded-3 text-center">
                                        <div class="text-muted small">الحد الأدنى</div>
                                        <div class="fw-bold text-dark fs-4">{{ $q['min_number'] ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded-3 text-center">
                                        <div class="text-muted small">الحد الأقصى</div>
                                        <div class="fw-bold text-success fs-4">{{ $q['max_number'] ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach

        </div>

        <!-- ==================== TAB 2: هيكل الأسئلة والإعدادات ==================== -->
        <div class="tab-pane fade" id="structureTabContent" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold text-dark m-0">
                                <i class="fas fa-clipboard-list text-primary me-2"></i> قائمة أسئلة الاستبيان ({{ count($survey->questions ?? []) }})
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if($survey->questions && count($survey->questions) > 0)
                                @foreach($survey->questions as $index => $question)
                                    <div class="border rounded-3 p-3 mb-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">
                                                    {{ $index + 1 }}. {{ $question['question'] }}
                                                    @if(isset($question['required']) && $question['required'])
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </h6>
                                                @if(isset($question['description']) && $question['description'])
                                                    <p class="text-muted small mb-2">{{ $question['description'] }}</p>
                                                @endif
                                                <div class="mb-2">
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.75rem;">
                                                        النوع: {{ $question['type'] }}
                                                    </span>
                                                    @if($question['type'] === 'rating')
                                                        <span class="badge bg-warning-subtle text-warning ms-1" style="font-size: 0.75rem;">
                                                            الحد الأقصى: {{ $question['max_rating'] ?? 5 }} نجوم
                                                        </span>
                                                    @endif
                                                </div>

                                                @if(in_array($question['type'], ['radio', 'checkbox', 'select']) && isset($question['options']))
                                                    <div class="mt-2">
                                                        <strong class="small text-muted d-block mb-1">الخيارات المتاحة:</strong>
                                                        <ul class="list-unstyled mb-0 ms-2">
                                                            @foreach($question['options'] as $option)
                                                                <li class="small text-dark mb-1">
                                                                    <i class="fas fa-circle text-primary me-1" style="font-size: 6px;"></i> {{ $option }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center py-4">لا توجد أسئلة مسجلة في هذا الاستبيان</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold text-dark m-0">
                                <i class="fas fa-cog text-primary me-2"></i> إعدادات وبيانات الاستبيان
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3 pb-3 border-bottom">
                                <label class="text-muted small d-block">الجمهور المستهدف:</label>
                                <span class="badge bg-primary px-3 py-1.5 rounded-pill fw-bold mt-1">
                                    @switch($survey->target_audience)
                                        @case('graduates') 🎓 الخريجين @break
                                        @case('companies') 💼 الشركات وأرباب العمل @break
                                        @case('training_coordinators') 👨‍🏫 منسقي التدريب @break
                                        @default 🌐 الكل
                                    @endswitch
                                </span>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <label class="text-muted small d-block">تصنيف الاستبيان:</label>
                                <span class="fw-bold text-dark">
                                    @switch($survey->type)
                                        @case('training') تدريب وورش عمل @break
                                        @case('job_opportunity') وظائف وتوظيف @break
                                        @case('job_fair') معارض وفعاليات @break
                                        @default عام
                                    @endswitch
                                </span>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <label class="text-muted small d-block">الوصول العام (Public Link):</label>
                                <div class="mt-1">
                                    @if($survey->is_public)
                                        <span class="badge bg-success-subtle text-success">متاح للعامة</span>
                                        <a href="{{ $survey->public_url }}" target="_blank" class="small d-block text-primary text-truncate mt-1">
                                            {{ $survey->public_url }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">داخلي فقط (يتطلب تسجيل الدخول)</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="text-muted small d-block">تاريخ الإنشاء:</label>
                                <span class="fw-bold text-dark">{{ $survey->created_at ? $survey->created_at->format('Y-m-d H:i') : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal حفظ الاستبيان كقالب -->
<div class="modal fade" id="saveAsTemplateModal" tabindex="-1" aria-labelledby="saveTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="saveAsTemplateForm" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #8b5cf6;">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0">حفظ هذا الاستبيان كقالب دائم</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        سيتم حفظ كافة أسئلة هذا الاستبيان وخياراته في مكتبة القوالب لتتمكن من إعادة استخدامها بضغطة زر واحدة في أي وقت.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">اسم القالب الجديد <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="template_title" id="modal_template_title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">تصنيف القالب <span class="text-danger">*</span></label>
                        <select class="form-select" name="template_category" id="modal_template_category" required>
                            <option value="training">🎓 التدريب وورش العمل</option>
                            <option value="employment">💼 التوظيف والشراكات</option>
                            <option value="events">🎪 المعارض والفعاليات</option>
                            <option value="general">📋 قالب عام</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">وصف مختصر للقالب</label>
                        <textarea class="form-control" name="template_description" id="modal_template_description" rows="2" placeholder="أدخل وصفاً يوضح الغرض من هذا القالب..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: #8b5cf6; border-color: #8b5cf6;">
                        <i class="fas fa-save me-1"></i> حفظ في مكتبة القوالب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSaveAsTemplateModal(surveyId, title, description, type) {
    const form = document.getElementById('saveAsTemplateForm');
    form.action = `{{ url('/evaluation-followup/surveys') }}/${surveyId}/save-template`;

    document.getElementById('modal_template_title').value = title + ' (قالب)';
    document.getElementById('modal_template_description').value = description || '';

    let category = 'general';
    if (type === 'training') category = 'training';
    else if (type === 'job_opportunity') category = 'employment';
    else if (type === 'job_fair') category = 'events';

    document.getElementById('modal_template_category').value = category;

    const modal = new bootstrap.Modal(document.getElementById('saveAsTemplateModal'));
    modal.show();
}
</script>
@endpush
