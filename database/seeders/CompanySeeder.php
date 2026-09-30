<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::updateOrCreate(
            ['email' => 'info@advancedtech.com'],
            [
                'name' => 'شركة التقنية المتقدمة',
                'phone' => '123456789',
                'address' => 'طرابلس، ليبيا',
                'website' => 'https://www.advancedtech.com',
                'description' => 'شركة رائدة في حلول وخدمات تكنولوجيا المعلومات.',
                'partnership_status' => 'active',
                'partnership_start_date' => now(),
                'partnership_end_date' => now()->addYears(5),
                'contact_person' => 'أحمد علي',
                'contact_email' => 'ahmed.ali@advancedtech.com',
                'contact_phone' => '987654321',
            ]
        );

        Company::updateOrCreate(
            ['email' => 'contact@digitalcreativity.ly'],
            [
                'name' => 'مؤسسة الإبداع الرقمي',
                'phone' => '987654321',
                'address' => 'بنغازي، ليبيا',
                'website' => 'https://www.digitalcreativity.ly',
                'description' => 'متخصصون في التسويق الرقمي وتطوير الويب.',
                'partnership_status' => 'active',
                'partnership_start_date' => now(),
                'partnership_end_date' => now()->addYears(3),
                'contact_person' => 'فاطمة محمد',
                'contact_email' => 'fatima.mohamed@digitalcreativity.ly',
                'contact_phone' => '123123123',
            ]
        );

        Company::updateOrCreate(
            ['email' => 'info@futureconsulting.com'],
            [
                'name' => 'شركة المستقبل للاستشارات',
                'phone' => '555111222',
                'address' => 'مصراتة، ليبيا',
                'website' => 'https://www.futureconsulting.com',
                'description' => 'تقديم استشارات إدارية ومالية للشركات.',
                'partnership_status' => 'expired',
                'partnership_start_date' => now(),
                'partnership_end_date' => now()->addYears(1),
                'contact_person' => 'سالم محمود',
                'contact_email' => 'salem.mahmoud@futureconsulting.com',
                'contact_phone' => '456456456',
            ]
        );
    }
}
