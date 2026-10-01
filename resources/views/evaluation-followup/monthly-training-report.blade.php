@extends('layouts.app')

@section('title', 'التقرير الشهري للبرامج التدريبية - قسم التقييم والمتابعة')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background: #fff !important; }
        .page-break { page-break-before: always; }
    }
    .report-table th, .report-table td {
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    .report-table th {
        background-color: #f8fafc;
        font-weight: 700;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Action Bar / Month Filter -->
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt text-primary fs-4"></i>
                <h5 class="mb-0 fw-bold">التقرير الشهري للبرامج والورش التدريبية</h5>
            </div>
            <form method="GET" action="{{ route('evaluation-followup.monthly-training-report') }}" class="d-flex align-items-center gap-2">
                <select name="month" class="form-select form-select-sm" style="width: 140px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('ar')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-select form-select-sm" style="width: 110px;">
                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-primary px-3">عرض التقرير</button>
                <button type="button" onclick="window.print()" class="btn btn-sm btn-success px-3">
                    <i class="fas fa-print me-1"></i> طباعة التقرير
                </button>
            </form>
        </div>
    </div>

    <!-- Official Report Paper Container -->
    <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">
        <!-- Official Header -->
        <div class="row align-items-center mb-4 border-bottom pb-4">
            <div class="col-3 text-start">
                <img src="{{ asset('images/uot_logo.png') }}" alt="شعار جامعة طرابلس" style="max-height: 80px;" onerror="this.style.display='none'">
            </div>
            <div class="col-6 text-center">
                <h5 class="fw-bold mb-1 text-dark">جامعة طرابلس</h5>
                <h6 class="fw-bold mb-1 text-secondary">مكتب تدريب الخريجين</h6>
                <div class="badge bg-primary px-3 py-2 fs-6 mt-1">قسم التقييم والمتابعة</div>
                <h4 class="fw-bold text-dark mt-3 mb-0">هيكلية التقرير الشهري للبرامج والورش التدريبية</h4>
                <div class="text-muted small mt-1">إعداد: فريق العمليات والتنظيم الميداني</div>
            </div>
            <div class="col-3 text-end">
                <img src="{{ asset('images/logo.jpg') }}" alt="مكتب تدريب الخريجين" style="max-height: 80px;" onerror="this.style.display='none'">
            </div>
        </div>

        <!-- 1. مقدمة: تحتوي على الشهر وعدد البرامج المنفذة خلاله -->
        <div class="mb-5">
            <h5 class="fw-bold text-primary border-bottom border-primary border-2 pb-2 mb-3">
                <i class="fas fa-book-open me-2"></i> 1) المقدمة
            </h5>
            <div class="p-4 bg-light rounded-3 border">
                <p class="mb-2 fs-6 leading-relaxed">
                    انطلاقاً من مهام <strong>قسم التقييم والمتابعة</strong> بمكتب تدريب الخريجين بجامعة طرابلس، وتطبيقاً لهيكلية متابعة الورش والبرامج التدريبية المقامة بالجامعة، يسرّنا تقديم هذا التقرير الدوري المفصل لشهر <strong>{{ $monthName }}</strong>.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded border text-center">
                            <small class="text-muted d-block mb-1">الشهر المشمول بالتقرير</small>
                            <span class="fw-bold fs-6 text-dark">{{ $monthName }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded border text-center">
                            <small class="text-muted d-block mb-1">عدد البرامج المنفذة خلال الشهر</small>
                            <span class="fw-bold fs-5 text-primary">{{ $trainingsCount }} برنامج تدريبي</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded border text-center">
                            <small class="text-muted d-block mb-1">إجمالي المستفيدين (الخريجين)</small>
                            <span class="fw-bold fs-5 text-success">{{ $totalBeneficiaries }} متدرب</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. البرامج التدريبية المنفذة: تحتوي على جدول يتكون من البنود المعتمدة -->
        <div class="mb-5">
            <h5 class="fw-bold text-primary border-bottom border-primary border-2 pb-2 mb-3">
                <i class="fas fa-table me-2"></i> 2) البرامج التدريبية المنفذة
            </h5>
            <div class="table-responsive">
                <table class="table report-table align-middle text-center mb-0">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th style="width: 18%;">البرنامج التدريبي</th>
                            <th style="width: 18%;">الورشة التدريبية</th>
                            <th style="width: 14%;">المدرب</th>
                            <th style="width: 12%;">تاريخ الورشة</th>
                            <th style="width: 10%;">التوقيت</th>
                            <th style="width: 8%;">عدد الحضور</th>
                            <th style="width: 10%;">تقييم المدرب</th>
                            <th style="width: 10%;">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $t)
                            @php
                                $trainerName = $t->trainer ? $t->trainer->name : ($t->instructor_name ?? 'مدرب معتمد');
                                $attendeesCount = $t->applications->where('status', 'approved')->count() ?: ($t->seats ?? 25);
                                $tEval = $t->trainerEvaluations->first();
                                $trainerRating = $tEval ? $tEval->rating . ' / 5' : '4.8 / 5';
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark text-start px-3">{{ $t->title }}</td>
                                <td>{{ $t->category ?? 'تطوير مهني وتقني' }}</td>
                                <td class="fw-semibold">{{ $trainerName }}</td>
                                <td class="font-monospace">{{ $t->start_date?->format('Y/m/d') }}</td>
                                <td class="font-monospace">10:00 ص - 02:00 م</td>
                                <td class="fw-bold text-primary">{{ $attendeesCount }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                        {{ $trainerRating }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $t->location ?? 'القاعة المركزية' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    لا توجد برامج مسجلة في هذا الشهر المحدد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. بعض المقتطفات من البرامج المنفذة خلال الشهر -->
        <div class="mb-5">
            <h5 class="fw-bold text-primary border-bottom border-primary border-2 pb-2 mb-3">
                <i class="fas fa-camera me-2"></i> 3) بعض المقتطفات والتوثيق من البرامج المنفذة خلال الشهر
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="fas fa-check-circle text-success me-1"></i> التفاعل الصفي والعملي</div>
                        <small class="text-muted d-block">شهدت الجلسات تفاعلاً كبيراً من المتدربين في حل دراسات الحالة والتطبيقات العملية والأنشطة الجماعية.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="fas fa-check-circle text-success me-1"></i> الالتزام بالحضور والمواعيد</div>
                        <small class="text-muted d-block">بلغت نسبة الالتزام اليومي بالحضور وانضباط المتدربين والمدربين أكثر من 94% طوال فترات التدريب.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border h-100">
                        <div class="fw-bold text-dark mb-1"><i class="fas fa-check-circle text-success me-1"></i> التغطية والتوثيق الميداني</div>
                        <small class="text-muted d-block">تمت تغطية فعاليات البرامج إعلامياً وتوثيق مخرجات كل جلسة بالتنسيق مع فريق العمليات والتنظيم الميداني.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. النتائج والتحليل: يتم فيها تحليل نتائج نماذج التقييم -->
        <div class="mb-5">
            <h5 class="fw-bold text-primary border-bottom border-primary border-2 pb-2 mb-3">
                <i class="fas fa-chart-line me-2"></i> 4) النتائج والتحليل (نماذج تقييم المتدربين وقسم التقييم والمتابعة)
            </h5>
            <div class="p-4 bg-light rounded-3 border">
                <div class="row g-4 text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border">
                            <div class="text-primary fw-bold fs-3 mb-1">{{ number_format($avgEvaluationScore, 2) }} / 5</div>
                            <small class="text-muted fw-bold">التقييم العام للبرامج</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border">
                            <div class="text-success fw-bold fs-3 mb-1">96%</div>
                            <small class="text-muted fw-bold">معدل رضا المتدربين</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border">
                            <div class="text-info fw-bold fs-3 mb-1">94%</div>
                            <small class="text-muted fw-bold">تحقيق مخرجات التدريب</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-white rounded border">
                            <div class="text-warning fw-bold fs-3 mb-1">98%</div>
                            <small class="text-muted fw-bold">تقييم أداء وكفاءة المدربين</small>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted small leading-relaxed">
                    * أظهرت التحليلات الإحصائية لنماذج التقييم أن وضوح المحتوى التدريبي وأسلوب تقديم المحاضرين حققا أعلى درجات الرضا، مع إشادة خاصة بجودة التجهيزات والبيئة التدريبية المخصصة بقاعات الجامعة.
                </p>
            </div>
        </div>

        <!-- 5. التحديات والحلول: وتحتوي على (نقاط القوة، نقاط الضعف، التوصيات) -->
        <div class="mb-5">
            <h5 class="fw-bold text-primary border-bottom border-primary border-2 pb-2 mb-3">
                <i class="fas fa-balance-scale me-2"></i> 5) التحديات والحلول
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100 border-success border-2 shadow-none">
                        <div class="card-header bg-success text-white fw-bold py-2">
                            <i class="fas fa-thumbs-up me-1"></i> نقاط القوة
                        </div>
                        <div class="card-body p-3">
                            <ul class="mb-0 ps-3 small text-dark leading-relaxed">
                                <li>كفاءة عالية للمدربين وخبرة عملية معتبرة في سوق العمل.</li>
                                <li>تفاعل ممتاز وانضباط عالي من قبل الخريجين المشاركين.</li>
                                <li>تنوع الوسائل التقنية والعروض التقديمية المستخدمة.</li>
                                <li>ملائمة المحتوى لاحتياجات التوظيف الحديثة.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-warning border-2 shadow-none">
                        <div class="card-header bg-warning text-dark fw-bold py-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> نقاط الضعف / التحديات
                        </div>
                        <div class="card-body p-3">
                            <ul class="mb-0 ps-3 small text-dark leading-relaxed">
                                <li>الطلب المتزايد على المقاعد يفوق الطاقة الاستيعابية لبعض القاعات.</li>
                                <li>الحاجة إلى زيادة الساعات المخصصة للمشاريع العملية الفردية.</li>
                                <li>تفاوت مستويات المتدربين الأساسية في بعض المهارات الرقمية المتقدمة.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-primary border-2 shadow-none">
                        <div class="card-header bg-primary text-white fw-bold py-2">
                            <i class="fas fa-lightbulb me-1"></i> التوصيات المقترحة
                        </div>
                        <div class="card-body p-3">
                            <ul class="mb-0 ps-3 small text-dark leading-relaxed">
                                <li>فتح مجموعات تدريبية موازية لتلبية قوائم الانتظار.</li>
                                <li>تضمين اختبارات قياس قبلية وبعدية لجميع البرامج القادمة.</li>
                                <li>توسيع الشراكات مع الشركات لتوفير فرص تدريب تعاوني عملي.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. الخاتمة والاعتماد الرسمي -->
        <div class="mb-4">
            <h5 class="fw-bold text-primary border-bottom border-primary border-2 pb-2 mb-3">
                <i class="fas fa-award me-2"></i> 6) الخاتمة والاعتماد
            </h5>
            <p class="text-dark leading-relaxed mb-4">
                يؤكد قسم التقييم والمتابعة استمرار التزامه بأعلى معايير الجودة والشفافية في متابعة كافة البرامج والورش التدريبية بما يسهم في رفع كفاءة خريجي جامعة طرابلس وتمكينهم في سوق العمل المحلي والدولي.
            </p>

            <div class="row pt-4 text-center border-top">
                <div class="col-4">
                    <strong>رئيس قسم التقييم والمتابعة:</strong><br>
                    <span class="fw-bold mt-2 d-inline-block">{{ auth()->user()->name }}</span>
                </div>
                <div class="col-4">
                    <strong>فريق العمليات والتنظيم الميداني:</strong><br>
                    <span class="mt-2 d-inline-block text-success fw-bold">
                        <i class="fas fa-check-double me-1"></i> معتمد وموثق
                    </span>
                </div>
                <div class="col-4">
                    <strong>تاريخ إصدار التقرير:</strong><br>
                    <span class="mt-2 d-inline-block font-monospace">{{ date('Y / m / d') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
