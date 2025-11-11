<?php
// ملف: app/Exports/CompaniesExport.php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Company::all();
    }

    public function headings(): array
    {
        return [
            'الرقم',
            'اسم الشركة',
            'المجال الصناعي',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'العنوان',
            'الوصف',
            'تاريخ التسجيل'
        ];
    }

    public function map($company): array
    {
        return [
            $company->id,
            $company->name,
            $company->industry ?? 'غير محدد',
            $company->email,
            $company->phone,
            $company->address,
            $company->description ?? 'لا يوجد وصف',
            $company->created_at->format('Y-m-d')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // تنسيق رأس الجدول
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a8a']]
            ],
            
            // محاذاة النص
            'A:H' => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center'
                ]
            ],
            
            // تنسيق الخلايا
            'A:H' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            
            // جعل عمود الوصف أوسع
            'G' => [
                'width' => 40
            ]
        ];
    }
}