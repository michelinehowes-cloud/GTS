<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class JobOpportunityTemplate implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            [
                'مطور ويب',
                'وظيفة',
                'تطوير وتصميم مواقع الويب باستخدام Laravel وVue.js',
                'دوام كامل',
                'طرابلس',
                3,
                '2024-02-01',
                '2024-12-31',
                '2024-01-20',
                '["هندسة حاسوب", "تكنولوجيا معلومات"]',
                '["Laravel", "Vue.js", "PHP", "JavaScript"]',
                '0-2 سنوات',
                1500,
                'تأمين صحي, وجبات, مواصلات',
                'شهادة بكالوريوس في مجال الحاسوب'
            ],
            [
                'متدرب تسويق',
                'تدريب',
                'برنامج تدريبي في مجال التسويق الرقمي',
                'دوام جزئي',
                'بنغازي',
                5,
                '2024-03-01',
                '2024-08-31',
                '2024-02-15',
                '["تسويق", "إدارة أعمال"]',
                '["التسويق الرقمي", "وسائل التواصل الاجتماعي", "تحليل البيانات"]',
                'لا يشترط خبرة',
                500,
                'شهادة تدريب, توصية',
                'طالب في السنة النهائية أو خريج حديث'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'العنوان',
            'النوع (وظيفة/تدريب/تدريب_عملي)',
            'الوصف',
            'نوع العقد (دوام_كامل/دوام_جزئي/عقد/عمل_حر)',
            'المكان',
            'عدد المقاعد',
            'تاريخ البدء (YYYY-MM-DD)',
            'تاريخ الانتهاء (YYYY-MM-DD)',
            'آخر موعد للتقديم (YYYY-MM-DD)',
            'التخصصات المطلوبة (JSON array)',
            'المهارات المطلوبة (JSON array)',
            'الخبرة المطلوبة',
            'الراتب',
            'المزايا',
            'المتطلبات'
        ];
    }

    public function title(): string
    {
        return 'نموذج فرص العمل';
    }
}