<?php

namespace App\Services;

use App\Models\JobFair;
use App\Models\JobFairVisit;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\JobOpportunity;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ZipArchive;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobFairRecruitmentExcelExportService
{
    /**
     * تصدير تقرير التوظيف والمقابلات الشامل كملف Excel (XLSX) أو CSV
     */
    public function export(JobFair $fair, string $format = 'xlsx')
    {
        if ($format === 'csv' || !class_exists('ZipArchive')) {
            return $this->exportCsv($fair);
        }

        try {
            return $this->exportXlsx($fair);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('JobFair Excel export fallback to CSV: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return $this->exportCsv($fair);
        }
    }

    /**
     * إنشاء ملف Excel (.xlsx) حقيقي متعدد الصفحات (Multi-sheet) بتنسيق راقٍ ودعم كامل للعربية
     */
    public function exportXlsx(JobFair $fair): BinaryFileResponse
    {
        $data = $this->gatherExportData($fair);

        $tempFile = tempnam(sys_get_temp_dir(), 'gts_recruitment_') . '.xlsx';
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            // Fallback to CSV if zip creation failed
            return $this->exportCsv($fair);
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
        '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
        '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>' .
            '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>' .
            '<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
        '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<sheets>' .
                '<sheet name="إحصائيات الشركات" sheetId="1" r:id="rId1"/>' .
                '<sheet name="تفاصيل المقابلات والمرشحين" sheetId="2" r:id="rId2"/>' .
                '<sheet name="شواغر التوظيف بالمعرض" sheetId="3" r:id="rId3"/>' .
            '</sheets>' .
        '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = $this->buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. Worksheets
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildCompanyStatsSheetXml($data['company_stats'], $data['company_totals']));
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->buildInterviewsSheetXml($data['interviews_details']));
        $zip->addFromString('xl/worksheets/sheet3.xml', $this->buildJobsSheetXml($data['job_stats']));

        $zip->close();

        $safeTitle = Str::slug($fair->title, '_') ?: 'fair';
        $fileName = 'تقرير_التوظيف_والمقابلات_' . $safeTitle . '_' . date('Y-m-d_H-i') . '.xlsx';

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * تصدير CSV مدمج مع دعم كامل للأحرف العربية (UTF-8 BOM)
     */
    public function exportCsv(JobFair $fair): StreamedResponse
    {
        $data = $this->gatherExportData($fair);
        $safeTitle = Str::slug($fair->title, '_') ?: 'fair';
        $fileName = 'تقرير_التوظيف_والمقابلات_' . $safeTitle . '_' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($data, $fair) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens Arabic correctly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // العنوان الرئيسي
            fputcsv($handle, ['تقرير التوظيف والمقابلات الشامل - ' . $fair->title]);
            fputcsv($handle, ['تاريخ التصدير:', date('Y-m-d H:i')]);
            fputcsv($handle, []);

            // 1. إحصائيات الشركات
            fputcsv($handle, ['=== أولاً: إحصائيات التوظيف والترشيحات لكل شركة ===']);
            fputcsv($handle, [
                'م',
                'اسم الشركة',
                'رقم الجناح',
                'البريد الإلكتروني',
                'رقم الهاتف',
                'الشواغر المعلنة',
                'إجمالي السير المستلمة',
                'طلبات لشواغر محددة',
                'سير عامة للجناح',
                'قيد التقييم والمراجعة',
                'القائمة القصيرة / المقابلات',
                'تم القبول والترشيح',
                'غير ملائم / مرفوض',
                'نسبة القبول (%)',
            ]);

            foreach ($data['company_stats'] as $idx => $cs) {
                fputcsv($handle, [
                    $idx + 1,
                    $cs['name'],
                    $cs['booth'],
                    $cs['email'],
                    $cs['phone'],
                    $cs['positions'],
                    $cs['total_leads'],
                    $cs['job_leads'],
                    $cs['general_leads'],
                    $cs['pending'],
                    $cs['shortlisted'],
                    $cs['accepted'],
                    $cs['rejected'],
                    $cs['acceptance_rate'] . '%',
                ]);
            }

            $tot = $data['company_totals'];
            fputcsv($handle, [
                'الإجمالي العام',
                '-',
                '-',
                '-',
                '-',
                $tot['positions'],
                $tot['total_leads'],
                $tot['job_leads'],
                $tot['general_leads'],
                $tot['pending'],
                $tot['shortlisted'],
                $tot['accepted'],
                $tot['rejected'],
                $tot['acceptance_rate'] . '%',
            ]);

            fputcsv($handle, []);
            fputcsv($handle, ['=== ثانياً: سجل المقابلات والترشيحات المفصل ===']);
            fputcsv($handle, [
                'م',
                'اسم الشركة',
                'رقم الجناح',
                'الوظيفة / الشاغر المتقدم له',
                'اسم الخريج / المرشح',
                'رقم القيد / الهوية',
                'التخصص الأكاديمي',
                'الجامعة',
                'سنة التخرج',
                'المعدل التراكمي (%)',
                'البريد الإلكتروني',
                'رقم الهاتف',
                'حالة المقابلة والفرز',
                'تقييم وملاحظات المقابلة',
                'تاريخ ووقت المقابلة / التقديم',
            ]);

            foreach ($data['interviews_details'] as $idx => $row) {
                fputcsv($handle, [
                    $idx + 1,
                    $row['company_name'],
                    $row['booth'],
                    $row['job_title'],
                    $row['graduate_name'],
                    $row['registration_number'],
                    $row['major'],
                    $row['university'],
                    $row['graduation_year'],
                    $row['gpa'],
                    $row['email'],
                    $row['phone'],
                    $row['status_label'],
                    $row['notes'],
                    $row['created_at'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['=== ثالثاً: شواغر التوظيف بالمعرض ===']);
            fputcsv($handle, [
                'م',
                'مسمى الوظيفة',
                'الشركة',
                'المقاعد المطلوبة',
                'المتقدمين',
                'قيد المراجعة',
                'القائمة القصيرة',
                'المقبولين',
                'المرفوضين',
                'حالة الشاغر',
            ]);

            foreach ($data['job_stats'] as $idx => $js) {
                fputcsv($handle, [
                    $idx + 1,
                    $js['title'],
                    $js['company_name'],
                    $js['seats'],
                    $js['total_applied'],
                    $js['pending'],
                    $js['shortlisted'],
                    $js['accepted'],
                    $js['rejected'],
                    $js['status_label'],
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * تجميع كافة بيانات التوظيف والمقابلات والشركات للمعرض
     */
    public function gatherExportData(JobFair $fair): array
    {
        // 1. جلب زيارات وترشيحات المعرض
        $visits = collect();
        if (Schema::hasTable('job_fair_visits')) {
            $hasJobCol = Schema::hasColumn('job_fair_visits', 'job_opportunity_id');
            $withs = ['company.company', 'graduate.graduateData'];
            if ($hasJobCol) {
                $withs[] = 'jobOpportunity.company';
            }
            $visits = JobFairVisit::where('job_fair_id', $fair->id)
                ->with($withs)
                ->latest()
                ->get();
        }

        // جلب أرقام التسجيل بالمعرض لربطها بالخريجين
        $registrationNumbers = JobFairRegistration::where('job_fair_id', $fair->id)
            ->pluck('registration_number', 'user_id')
            ->toArray();

        // 2. تجميع إحصائيات الشركات
        $fairCompanies = $fair->companies()->with(['company.user'])->get();
        $companyStats = [];

        foreach ($fairCompanies as $fc) {
            $comp = $fc->company;
            $userId = $comp ? $comp->user_id : null;
            $compId = $comp ? $comp->id : $fc->company_id;
            $key = $userId ?: ('comp_' . $compId);

            $companyStats[$key] = [
                'company_id'      => $compId,
                'name'            => $comp ? $comp->name : 'شركة مشاركة',
                'booth'           => $fc->booth_number ?: 'غير محدد',
                'email'           => $comp ? ($comp->email ?: ($comp->user?->email ?: '—')) : '—',
                'phone'           => $comp ? ($comp->phone ?: ($comp->user?->phone ?: '—')) : '—',
                'positions'       => (int)($fc->available_positions ?? 0),
                'total_leads'     => 0,
                'job_leads'       => 0,
                'general_leads'   => 0,
                'pending'         => 0,
                'shortlisted'     => 0,
                'accepted'        => 0,
                'rejected'        => 0,
                'acceptance_rate' => 0,
            ];
        }

        // ربط الزيارات والسير بالشركات
        foreach ($visits as $v) {
            $uId = $v->company_id;
            $cUser = $v->company;
            $cProfile = $cUser ? $cUser->company : null;
            $key = $uId ?: ($cProfile ? 'comp_' . $cProfile->id : 'unknown_' . $v->id);

            if (!isset($companyStats[$key])) {
                $companyStats[$key] = [
                    'company_id'      => $cProfile ? $cProfile->id : null,
                    'name'            => $cProfile ? $cProfile->name : ($cUser ? $cUser->name : 'شركة مشاركة'),
                    'booth'           => 'غير محدد',
                    'email'           => $cProfile?->email ?: $cUser?->email ?: '—',
                    'phone'           => $cProfile?->phone ?: $cUser?->phone ?: '—',
                    'positions'       => 0,
                    'total_leads'     => 0,
                    'job_leads'       => 0,
                    'general_leads'   => 0,
                    'pending'         => 0,
                    'shortlisted'     => 0,
                    'accepted'        => 0,
                    'rejected'        => 0,
                    'acceptance_rate' => 0,
                ];
            }

            $companyStats[$key]['total_leads']++;
            if (!empty($v->job_opportunity_id)) {
                $companyStats[$key]['job_leads']++;
            } else {
                $companyStats[$key]['general_leads']++;
            }

            $st = $v->status ?? 'pending';
            if (isset($companyStats[$key][$st])) {
                $companyStats[$key][$st]++;
            } else {
                $companyStats[$key]['pending']++;
            }
        }

        // حساب معدلات القبول لكل شركة والإجمالي
        $companyTotals = [
            'positions'       => 0,
            'total_leads'     => 0,
            'job_leads'       => 0,
            'general_leads'   => 0,
            'pending'         => 0,
            'shortlisted'     => 0,
            'accepted'        => 0,
            'rejected'        => 0,
            'acceptance_rate' => 0,
        ];

        foreach ($companyStats as &$cs) {
            $companyTotals['positions']     += $cs['positions'];
            $companyTotals['total_leads']   += $cs['total_leads'];
            $companyTotals['job_leads']     += $cs['job_leads'];
            $companyTotals['general_leads'] += $cs['general_leads'];
            $companyTotals['pending']       += $cs['pending'];
            $companyTotals['shortlisted']   += $cs['shortlisted'];
            $companyTotals['accepted']      += $cs['accepted'];
            $companyTotals['rejected']      += $cs['rejected'];

            if ($cs['total_leads'] > 0) {
                $cs['acceptance_rate'] = round(($cs['accepted'] / $cs['total_leads']) * 100, 1);
            }
        }
        unset($cs);

        if ($companyTotals['total_leads'] > 0) {
            $companyTotals['acceptance_rate'] = round(($companyTotals['accepted'] / $companyTotals['total_leads']) * 100, 1);
        }

        // 3. تفاصيل المقابلات والمرشحين
        $statusLabels = [
            'pending'     => 'قيد التقييم والمراجعة',
            'shortlisted' => 'القائمة القصيرة (مؤهل للمقابلة)',
            'accepted'    => 'تم القبول والترشيح',
            'rejected'    => 'غير ملائم / مرفوض',
        ];

        $interviewsDetails = [];
        foreach ($visits as $v) {
            $grad = $v->graduate;
            $gData = $grad?->graduateData;
            $cUser = $v->company;
            $cProfile = $cUser ? $cUser->company : null;

            // اسم الشركة
            $compName = $cProfile ? $cProfile->name : ($cUser ? $cUser->name : 'شركة مشاركة');

            // رقم الجناح
            $booth = '—';
            if ($cProfile) {
                $fc = $fairCompanies->firstWhere('company_id', $cProfile->id);
                if ($fc && $fc->booth_number) {
                    $booth = $fc->booth_number;
                }
            }

            // المسمى الوظيفي
            $jobTitle = 'سيرة عامة للجناح';
            if (!empty($v->jobOpportunity)) {
                $jobTitle = $v->jobOpportunity->title;
            }

            // رقم القيد
            $regNum = $registrationNumbers[$v->graduate_id] ?? ($grad?->national_id ?? '—');

            // المعدل
            $gpaVal = $gData?->gpa ?? $grad?->gpa;
            $gpaStr = $gpaVal ? (number_format((float)$gpaVal, 2) . '%') : '—';

            $interviewsDetails[] = [
                'company_name'        => $compName,
                'booth'               => $booth,
                'job_title'           => $jobTitle,
                'graduate_name'       => $grad?->name ?? 'خريج',
                'registration_number' => $regNum,
                'major'               => $gData?->major ?? $grad?->major ?? $grad?->specialization ?? '—',
                'university'          => $gData?->university ?? $grad?->university ?? 'جامعة طرابلس',
                'graduation_year'     => $gData?->graduation_year ?? $grad?->graduation_year ?? '—',
                'gpa'                 => $gpaStr,
                'email'               => $grad?->email ?? '—',
                'phone'               => $grad?->phone ?? '—',
                'status_label'        => $statusLabels[$v->status ?? 'pending'] ?? $v->status,
                'notes'               => $v->notes ? trim($v->notes) : '—',
                'created_at'          => $v->created_at ? $v->created_at->format('Y-m-d H:i') : '—',
            ];
        }

        // 4. إحصائيات الوظائف الشاغرة
        $jobStats = [];
        if (Schema::hasTable('job_opportunities')) {
            $companyIds = collect($companyStats)->pluck('company_id')->filter()->unique()->toArray();
            $jobQuery = JobOpportunity::with('company');

            if (Schema::hasColumn('job_opportunities', 'job_fair_id')) {
                $jobQuery->where(function ($q) use ($fair, $companyIds, $visits) {
                    $q->where('job_fair_id', $fair->id)
                      ->orWhereIn('company_id', $companyIds)
                      ->orWhereIn('id', $visits->pluck('job_opportunity_id')->filter()->unique());
                });
            } else {
                $jobQuery->where(function ($q) use ($companyIds, $visits) {
                    $q->whereIn('company_id', $companyIds)
                      ->orWhereIn('id', $visits->pluck('job_opportunity_id')->filter()->unique());
                });
            }

            $jobs = $jobQuery->get();
            $jobStatusLabels = [
                'open'      => 'متاحة للتقديم',
                'closed'    => 'مغلقة',
                'approved'  => 'معتمدة بالمعرض',
                'pending'   => 'قيد المراجعة',
            ];

            foreach ($jobs as $job) {
                $jobVisits = $visits->where('job_opportunity_id', $job->id);
                $jobStats[] = [
                    'title'        => $job->title,
                    'company_name' => $job->company ? $job->company->name : 'غير محدد',
                    'seats'        => (int)($job->seats ?? 1),
                    'total_applied'=> $jobVisits->count(),
                    'pending'      => $jobVisits->where('status', 'pending')->count(),
                    'shortlisted'  => $jobVisits->where('status', 'shortlisted')->count(),
                    'accepted'     => $jobVisits->where('status', 'accepted')->count(),
                    'rejected'     => $jobVisits->where('status', 'rejected')->count(),
                    'status_label' => $jobStatusLabels[$job->status ?? 'open'] ?? ($job->status ?? 'متاحة'),
                ];
            }
        }

        return [
            'company_stats'      => array_values($companyStats),
            'company_totals'     => $companyTotals,
            'interviews_details' => $interviewsDetails,
            'job_stats'          => $jobStats,
        ];
    }

    /**
     * بناء ورقة إحصائيات الشركات (Sheet 1)
     */
    protected function buildCompanyStatsSheetXml(array $companies, array $totals): string
    {
        $headers = [
            'م',
            'اسم الشركة',
            'رقم الجناح',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'الشواغر المعلنة',
            'إجمالي السير المستلمة',
            'طلبات لشواغر محددة',
            'سير عامة للجناح',
            'قيد التقييم والمراجعة',
            'القائمة القصيرة للمرشحين',
            'تم القبول والترشيح',
            'غير ملائم / مرفوض',
            'نسبة القبول (%)',
        ];

        // عروض الأعمدة
        $colWidths = [8, 30, 14, 26, 18, 16, 20, 20, 18, 20, 22, 18, 18, 16];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<sheetViews><sheetView tabSelected="1" workbookViewId="0" rightToLeft="1"/></sheetViews>' .
            '<sheetFormatPr defaultRowHeight="24"/>' .
            '<cols>';

        foreach ($colWidths as $idx => $w) {
            $colNum = $idx + 1;
            $xml .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $w . '" customWidth="1"/>';
        }
        $xml .= '</cols><sheetData>';

        // 1. صف الترويسة (Header Row)
        $xml .= '<row r="1" ht="30" customHeight="1">';
        foreach ($headers as $colIdx => $header) {
            $cellRef = self::colLetter($colIdx + 1) . '1';
            $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="1"><is><t xml:space="preserve">' . self::xmlEscape($header) . '</t></is></c>';
        }
        $xml .= '</row>';

        // 2. صفوف بيانات الشركات
        $rowNum = 2;
        foreach ($companies as $idx => $c) {
            $sBase = ($rowNum % 2 === 0) ? 0 : 2; // zebra striping
            $xml .= '<row r="' . $rowNum . '" ht="24">';

            $values = [
                ['val' => $idx + 1, 'type' => 'num', 's' => $sBase],
                ['val' => $c['name'], 'type' => 'str', 's' => 3], // right aligned
                ['val' => $c['booth'], 'type' => 'str', 's' => $sBase],
                ['val' => $c['email'], 'type' => 'str', 's' => $sBase],
                ['val' => $c['phone'], 'type' => 'str', 's' => $sBase],
                ['val' => $c['positions'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['total_leads'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['job_leads'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['general_leads'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['pending'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['shortlisted'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['accepted'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['rejected'], 'type' => 'num', 's' => $sBase],
                ['val' => $c['acceptance_rate'] . '%', 'type' => 'str', 's' => $sBase],
            ];

            foreach ($values as $colIdx => $col) {
                $cellRef = self::colLetter($colIdx + 1) . $rowNum;
                if ($col['type'] === 'num') {
                    $xml .= '<c r="' . $cellRef . '" s="' . $col['s'] . '"><v>' . (float)$col['val'] . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="' . $col['s'] . '"><is><t xml:space="preserve">' . self::xmlEscape((string)$col['val']) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
            $rowNum++;
        }

        // 3. صف الإجمالي العام (Summary Total Row)
        $xml .= '<row r="' . $rowNum . '" ht="28" customHeight="1">';
        $totalValues = [
            ['val' => 'الإجمالي العام', 'type' => 'str', 's' => 5], // right aligned bold
            ['val' => count($companies) . ' شركة', 'type' => 'str', 's' => 4],
            ['val' => '—', 'type' => 'str', 's' => 4],
            ['val' => '—', 'type' => 'str', 's' => 4],
            ['val' => '—', 'type' => 'str', 's' => 4],
            ['val' => $totals['positions'], 'type' => 'num', 's' => 4],
            ['val' => $totals['total_leads'], 'type' => 'num', 's' => 4],
            ['val' => $totals['job_leads'], 'type' => 'num', 's' => 4],
            ['val' => $totals['general_leads'], 'type' => 'num', 's' => 4],
            ['val' => $totals['pending'], 'type' => 'num', 's' => 4],
            ['val' => $totals['shortlisted'], 'type' => 'num', 's' => 4],
            ['val' => $totals['accepted'], 'type' => 'num', 's' => 4],
            ['val' => $totals['rejected'], 'type' => 'num', 's' => 4],
            ['val' => $totals['acceptance_rate'] . '%', 'type' => 'str', 's' => 4],
        ];

        foreach ($totalValues as $colIdx => $col) {
            $cellRef = self::colLetter($colIdx + 1) . $rowNum;
            if ($col['type'] === 'num') {
                $xml .= '<c r="' . $cellRef . '" s="' . $col['s'] . '"><v>' . (float)$col['val'] . '</v></c>';
            } else {
                $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="' . $col['s'] . '"><is><t xml:space="preserve">' . self::xmlEscape((string)$col['val']) . '</t></is></c>';
            }
        }
        $xml .= '</row>';

        $xml .= '</sheetData></worksheet>';
        return $xml;
    }

    /**
     * بناء ورقة سجل المقابلات والترشيحات المفصل (Sheet 2)
     */
    protected function buildInterviewsSheetXml(array $interviews): string
    {
        $headers = [
            'م',
            'اسم الشركة',
            'رقم الجناح',
            'الوظيفة / الشاغر المتقدم له',
            'اسم الخريج / المرشح',
            'رقم القيد / الهوية',
            'التخصص الأكاديمي',
            'الجامعة',
            'سنة التخرج',
            'المعدل (%)',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'حالة المقابلة والفرز',
            'تقييم وملاحظات المقابلة',
            'تاريخ ووقت المقابلة / التقديم',
        ];

        $colWidths = [8, 28, 14, 26, 26, 18, 24, 22, 14, 14, 26, 18, 26, 35, 22];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<sheetViews><sheetView tabSelected="0" workbookViewId="0" rightToLeft="1"/></sheetViews>' .
            '<sheetFormatPr defaultRowHeight="24"/>' .
            '<cols>';

        foreach ($colWidths as $idx => $w) {
            $colNum = $idx + 1;
            $xml .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $w . '" customWidth="1"/>';
        }
        $xml .= '</cols><sheetData>';

        // 1. الترويسة
        $xml .= '<row r="1" ht="30" customHeight="1">';
        foreach ($headers as $colIdx => $header) {
            $cellRef = self::colLetter($colIdx + 1) . '1';
            $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="1"><is><t xml:space="preserve">' . self::xmlEscape($header) . '</t></is></c>';
        }
        $xml .= '</row>';

        // 2. البيانات
        $rowNum = 2;
        foreach ($interviews as $idx => $row) {
            $sBase = ($rowNum % 2 === 0) ? 0 : 2;
            $xml .= '<row r="' . $rowNum . '" ht="24">';

            $values = [
                ['val' => $idx + 1, 'type' => 'num', 's' => $sBase],
                ['val' => $row['company_name'], 'type' => 'str', 's' => 3],
                ['val' => $row['booth'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['job_title'], 'type' => 'str', 's' => 3],
                ['val' => $row['graduate_name'], 'type' => 'str', 's' => 3],
                ['val' => $row['registration_number'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['major'], 'type' => 'str', 's' => 3],
                ['val' => $row['university'], 'type' => 'str', 's' => 3],
                ['val' => $row['graduation_year'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['gpa'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['email'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['phone'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['status_label'], 'type' => 'str', 's' => $sBase],
                ['val' => $row['notes'], 'type' => 'str', 's' => 3], // right aligned notes
                ['val' => $row['created_at'], 'type' => 'str', 's' => $sBase],
            ];

            foreach ($values as $colIdx => $col) {
                $cellRef = self::colLetter($colIdx + 1) . $rowNum;
                if ($col['type'] === 'num') {
                    $xml .= '<c r="' . $cellRef . '" s="' . $col['s'] . '"><v>' . (float)$col['val'] . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="' . $col['s'] . '"><is><t xml:space="preserve">' . self::xmlEscape((string)$col['val']) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
            $rowNum++;
        }

        if (empty($interviews)) {
            $xml .= '<row r="2" ht="28">';
            $xml .= '<c r="A2" t="inlineStr" s="0"><is><t xml:space="preserve">لا توجد مقابلات أو سير ذاتية مسجلة حتى الآن</t></is></c>';
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';
        return $xml;
    }

    /**
     * بناء ورقة شواغر التوظيف بالمعرض (Sheet 3)
     */
    protected function buildJobsSheetXml(array $jobs): string
    {
        $headers = [
            'م',
            'مسمى الوظيفة',
            'الشركة',
            'المقاعد المطلوبة',
            'إجمالي المتقدمين',
            'قيد المراجعة',
            'القائمة القصيرة',
            'المقبولين',
            'المرفوضين',
            'حالة الشاغر',
        ];

        $colWidths = [8, 30, 28, 18, 18, 16, 18, 16, 16, 18];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<sheetViews><sheetView tabSelected="0" workbookViewId="0" rightToLeft="1"/></sheetViews>' .
            '<sheetFormatPr defaultRowHeight="24"/>' .
            '<cols>';

        foreach ($colWidths as $idx => $w) {
            $colNum = $idx + 1;
            $xml .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $w . '" customWidth="1"/>';
        }
        $xml .= '</cols><sheetData>';

        // 1. الترويسة
        $xml .= '<row r="1" ht="30" customHeight="1">';
        foreach ($headers as $colIdx => $header) {
            $cellRef = self::colLetter($colIdx + 1) . '1';
            $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="1"><is><t xml:space="preserve">' . self::xmlEscape($header) . '</t></is></c>';
        }
        $xml .= '</row>';

        // 2. البيانات
        $rowNum = 2;
        foreach ($jobs as $idx => $j) {
            $sBase = ($rowNum % 2 === 0) ? 0 : 2;
            $xml .= '<row r="' . $rowNum . '" ht="24">';

            $values = [
                ['val' => $idx + 1, 'type' => 'num', 's' => $sBase],
                ['val' => $j['title'], 'type' => 'str', 's' => 3],
                ['val' => $j['company_name'], 'type' => 'str', 's' => 3],
                ['val' => $j['seats'], 'type' => 'num', 's' => $sBase],
                ['val' => $j['total_applied'], 'type' => 'num', 's' => $sBase],
                ['val' => $j['pending'], 'type' => 'num', 's' => $sBase],
                ['val' => $j['shortlisted'], 'type' => 'num', 's' => $sBase],
                ['val' => $j['accepted'], 'type' => 'num', 's' => $sBase],
                ['val' => $j['rejected'], 'type' => 'num', 's' => $sBase],
                ['val' => $j['status_label'], 'type' => 'str', 's' => $sBase],
            ];

            foreach ($values as $colIdx => $col) {
                $cellRef = self::colLetter($colIdx + 1) . $rowNum;
                if ($col['type'] === 'num') {
                    $xml .= '<c r="' . $cellRef . '" s="' . $col['s'] . '"><v>' . (float)$col['val'] . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="' . $col['s'] . '"><is><t xml:space="preserve">' . self::xmlEscape((string)$col['val']) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
            $rowNum++;
        }

        if (empty($jobs)) {
            $xml .= '<row r="2" ht="28">';
            $xml .= '<c r="A2" t="inlineStr" s="0"><is><t xml:space="preserve">لا توجد شواغر معلنة لهذا المعرض بعد</t></is></c>';
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';
        return $xml;
    }

    /**
     * أنماط التنسيق في ملف Excel (styles.xml)
     */
    protected function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<fonts count="4">' .
                '<font><sz val="11"/><name val="Segoe UI"/><color rgb="FF0F172A"/></font>' . // 0: Normal
                '<font><b/><sz val="11"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>' . // 1: Header Bold White
                '<font><b/><sz val="11"/><name val="Segoe UI"/><color rgb="FF1E3A8A"/></font>' . // 2: Total Bold Navy
                '<font><sz val="11"/><name val="Segoe UI"/><color rgb="FF0F172A"/></font>' .
            '</fonts>' .
            '<fills count="5">' .
                '<fill><patternFill patternType="none"/></fill>' . // 0
                '<fill><patternFill patternType="gray125"/></fill>' . // 1
                '<fill><patternFill patternType="solid"><fgColor rgb="FF1E3A8A"/></patternFill></fill>' . // 2: Navy header
                '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>' . // 3: Zebra light
                '<fill><patternFill patternType="solid"><fgColor rgb="FFE0E7FF"/></patternFill></fill>' . // 4: Summary total light blue
            '</fills>' .
            '<borders count="3">' .
                '<border><left/><right/><top/><bottom/></border>' . // 0
                '<border>' . // 1: Thin light border
                    '<left style="thin"><color rgb="FFE2E8F0"/></left>' .
                    '<right style="thin"><color rgb="FFE2E8F0"/></right>' .
                    '<top style="thin"><color rgb="FFE2E8F0"/></top>' .
                    '<bottom style="thin"><color rgb="FFE2E8F0"/></bottom>' .
                '</border>' .
                '<border>' . // 2: Double bottom border for summary
                    '<left style="thin"><color rgb="FFCBD5E1"/></left>' .
                    '<right style="thin"><color rgb="FFCBD5E1"/></right>' .
                    '<top style="thin"><color rgb="FF94A3B8"/></top>' .
                    '<bottom style="double"><color rgb="FF1E3A8A"/></bottom>' .
                '</border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
                '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="6">' .
                // 0: Regular cell centered
                '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1">' .
                    '<alignment horizontal="center" vertical="center" wrapText="1"/>' .
                '</xf>' .
                // 1: Header cell (Navy, Bold White, centered)
                '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1">' .
                    '<alignment horizontal="center" vertical="center" wrapText="1"/>' .
                '</xf>' .
                // 2: Alternating row cell centered (Light gray #f8fafc)
                '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1">' .
                    '<alignment horizontal="center" vertical="center" wrapText="1"/>' .
                '</xf>' .
                // 3: Right-aligned text cell (for company/student/major names)
                '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1">' .
                    '<alignment horizontal="right" vertical="center" wrapText="1"/>' .
                '</xf>' .
                // 4: Summary total cell (centered, light blue, double border)
                '<xf numFmtId="0" fontId="2" fillId="4" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1">' .
                    '<alignment horizontal="center" vertical="center" wrapText="1"/>' .
                '</xf>' .
                // 5: Summary total cell (right-aligned, light blue, double border)
                '<xf numFmtId="0" fontId="2" fillId="4" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1">' .
                    '<alignment horizontal="right" vertical="center" wrapText="1"/>' .
                '</xf>' .
            '</cellXfs>' .
        '</styleSheet>';
    }

    /**
     * تحويل رقم العمود إلى حرف Excel (1 => A, 2 => B, 27 => AA ...)
     */
    public static function colLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $colIndex = (int)(($colIndex - $mod) / 26);
        }
        return $letter;
    }

    /**
     * الهروب الآمن للأحرف في XML
     */
    public static function xmlEscape(string $str): string
    {
        return htmlspecialchars($str, ENT_XML1, 'UTF-8');
    }
}
