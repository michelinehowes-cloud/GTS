<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\GraduateData;
use App\Models\Nomination;
use App\Models\JobOpportunity;

class AdvancedReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $stats;
    protected $insights;

    public function __construct($stats, $insights)
    {
        $this->stats = $stats;
        $this->insights = $insights;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];

        // Add main statistics
        $data[] = ['الإحصائيات الرئيسية'];
        $data[] = ['إجمالي الخريجين', $this->stats['totalGraduates'] ?? 0];
        $data[] = ['خريجين موظفين', $this->stats['employedGraduates'] ?? 0];
        $data[] = ['باحثين عن عمل', $this->stats['seekingOpportunities'] ?? 0];
        $data[] = ['معدل التوظيف', ($this->stats['employmentRate'] ?? 0) . '%'];
        $data[] = ['الترشيحات النشطة', $this->stats['activeNominations'] ?? 0];
        $data[] = ['نسبة النجاح', ($this->stats['successRate'] ?? 0) . '%'];
        $data[] = ['فرص عمل متاحة', $this->stats['availableOpportunities'] ?? 0];
        $data[] = ['خريجون جدد هذا الشهر', $this->stats['newGraduatesThisMonth'] ?? 0];
        $data[] = ['فرص جديدة هذا الأسبوع', $this->stats['newOpportunitiesThisWeek'] ?? 0];
        $data[] = ['معدل النجاح الكلي', ($this->stats['overallSuccessRate'] ?? 0) . '%'];
        $data[] = ['']; // Empty row for spacing

        // Add insights
        $data[] = ['استنتاجات وتحليلات ذكية'];
        if (!empty($this->insights)) {
            foreach ($this->insights as $insight) {
                $data[] = [$insight['title'], $insight['description']];
            }
        } else {
            $data[] = ['لا توجد استنتاجات متاحة حالياً.'];
        }
        $data[] = ['']; // Empty row for spacing

        // Add detailed graduate data (optional, can be very large)
        // For simplicity, we'll just add a summary or a few key fields
        $data[] = ['ملخص بيانات الخريجين'];
        $graduates = GraduateData::select('name', 'major', 'graduation_year', 'employment_status')->get();
        if ($graduates->count() > 0) {
            $data[] = ['الاسم', 'التخصص', 'سنة التخرج', 'حالة التوظيف'];
            foreach ($graduates as $graduate) {
                $data[] = [
                    $graduate->name,
                    $graduate->major,
                    $graduate->graduation_year,
                    $graduate->employment_status
                ];
            }
        } else {
            $data[] = ['لا توجد بيانات خريجين مفصلة.'];
        }

        return collect($data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Headings are dynamically added in the collection method for better formatting
        return [];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return $row;
    }
}
