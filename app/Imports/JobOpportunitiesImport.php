<?php

namespace App\Imports;

use App\Models\JobOpportunity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JobOpportunitiesImport implements ToCollection, WithHeadingRow
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // تخطي الصفوف الفارغة
            if (empty($row['title'])) {
                continue;
            }

            JobOpportunity::create([
                'title' => $row['title'] ?? 'بدون عنوان',
                'description' => $row['description'] ?? 'لا يوجد وصف',
                'type' => $this->mapType($row['type'] ?? 'job'),
                'contract_type' => $this->mapContractType($row['contract_type'] ?? 'full_time'),
                'company_id' => $this->companyId,
                'location' => $row['location'] ?? 'طرابلس',
                'seats' => $row['seats'] ?? 1,
                'start_date' => $this->parseDate($row['start_date']) ?? now()->addDays(30),
                'end_date' => $this->parseDate($row['end_date']) ?? now()->addDays(60),
                'application_deadline' => $this->parseDate($row['application_deadline']) ?? now()->addDays(15),
                'required_specializations' => $this->parseArray($row['required_specializations']),
                'required_skills' => $this->parseArray($row['required_skills']),
                'required_experience' => $row['required_experience'] ?? 'مبتدئ',
                'salary' => $row['salary'] ?? null,
                'status' => 'open',
                'benefits' => $row['benefits'] ?? null,
                'requirements' => $row['requirements'] ?? null,
                'created_by' => Auth::id(),
            ]);
        }
    }

    protected function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function mapType($type)
    {
        $type = strtolower(trim($type));
        
        if (in_array($type, ['وظيفة', 'job'])) {
            return 'job';
        } elseif (in_array($type, ['تدريب', 'training'])) {
            return 'training';
        } elseif (in_array($type, ['تدريب عملي', 'internship'])) {
            return 'internship';
        }
        
        return 'job';
    }

    protected function mapContractType($type)
    {
        $type = strtolower(trim($type));
        
        if (in_array($type, ['دوام كامل', 'full_time'])) {
            return 'full_time';
        } elseif (in_array($type, ['دوام جزئي', 'part_time'])) {
            return 'part_time';
        } elseif (in_array($type, ['عقد', 'contract'])) {
            return 'contract';
        } elseif (in_array($type, ['عمل حر', 'freelance'])) {
            return 'freelance';
        }
        
        return 'full_time';
    }

    protected function parseArray($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return array_map('trim', explode(',', $value));
        }

        return $value;
    }
}