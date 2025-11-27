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
        Company::create([
            'name' => 'شركة التقنية المتقدمة',
            'email' => 'info@advancedtech.com',
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
        ]);

        Company::create([
            'name' => 'مؤسسة الإبداع الرقمي',
            'email' => 'contact@digitalcreativity.ly',
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
        ]);

        Company::create([
            'name' => 'شركة المستقبل للاستشارات',
            'email' => 'info@futureconsulting.com',
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
        ]);
    }
}
