<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Announcement;
use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class PlatformContentSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $company = Company::first();

        // 1. الأخبار الرسمية للمنصة
        $newsList = [
            [
                'title' => 'انطلاق فعاليات معرض التوظيف والتدريب السنوي 2026 برعاية جامعة طرابلس',
                'content' => 'افتتحت جامعة طرابلس رسمياً فعاليات معرض التوظيف والتدريب السنوي بمشاركة أكثر من 40 شركة ومؤسسة رائدة في القطاعين العام والخاص، بهدف توفير فرص تدريب وتوظيف نوعية للخريجين الجدد.',
                'thumbnail_path' => null,
                'published_at' => Carbon::now()->subDays(2),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'created_by' => $admin ? $admin->id : 1,
            ],
            [
                'title' => 'توقيع اتفاقيات تعاون استراتيجية لربط مخرجات التعليم بسوق العمل',
                'content' => 'أبرم مكتب تدريب وتأهيل الخريجين سلسلة اتفاقيات تفاهم مشتركة مع شركات تقنية ومصرفية لتدريب وتأهيل أكثر من 500 خريج ضمن برامج تدريبية متخصصة ومكثفة منتهية بالتوظيف.',
                'thumbnail_path' => null,
                'published_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'created_by' => $admin ? $admin->id : 1,
            ],
            [
                'title' => 'اختتام ورش عمل الذكاء الاصطناعي والتحول الرقمي لدفعة خريجي 2025/2026',
                'content' => 'اختتم المكتب بنجاح البرنامج التدريبي المتخصص في تقنيات الذكاء الاصطناعي التوليدي وتحليل البيانات بمشاركة نخبة من خريجي كليات تقنية المعلومات والهندسة وتوزيع شهادات معتمدة للمجتازين.',
                'thumbnail_path' => null,
                'published_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'created_by' => $admin ? $admin->id : 1,
            ],
        ];

        foreach ($newsList as $news) {
            News::updateOrCreate(['title' => $news['title']], $news);
        }

        // 2. الإعلانات التنبيهية العامة
        $announcementsList = [
            [
                'title' => 'إعلان هام لجميع الخريجين: فتح باب تحديث البيانات الأكاديمية والمهارية',
                'content' => 'يهيب مكتب تدريب وتأهيل الخريجين بجميع المسجلين والراغبين في الترشح للفرص الوظيفية القادمة سرعة تحديث سيرهم الذاتية ومعدلاتهم ومهاراتهم الشخصية عبر لوحة التحكم.',
                'start_date' => Carbon::now()->subDays(3)->toDateString(),
                'end_date' => Carbon::now()->addDays(30)->toDateString(),
                'link' => '#',
                'is_active' => true,
                'created_by' => $admin ? $admin->id : 1,
            ],
            [
                'title' => 'دعوة للمشاركة في هاكاثون الابتكار وحلول التحول الرقمي',
                'content' => 'ندعو طلبة مشاريع التخرج والخريجين لتقديم أفكارهم ومشاريعهم المبتكرة للتنافس على جوائز تمويلية وفرص احتضان مقدمة من الشركاء الصناعيين.',
                'start_date' => Carbon::now()->subDays(1)->toDateString(),
                'end_date' => Carbon::now()->addDays(20)->toDateString(),
                'link' => '#',
                'is_active' => true,
                'created_by' => $admin ? $admin->id : 1,
            ],
            [
                'title' => 'بدء التسجيل في الدورات التدريبية المعتمدة لشهر أكتوبر 2026',
                'content' => 'أعلن قسم التدريب عن فتح باب القبول في مسارات إدارة المشاريع، الأمن السيبراني، والتسويق الرقمي. المقاعد محدودة والتسجيل متاح عبر المنصة.',
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(15)->toDateString(),
                'link' => '#',
                'is_active' => true,
                'created_by' => $admin ? $admin->id : 1,
            ],
        ];

        foreach ($announcementsList as $announcement) {
            Announcement::updateOrCreate(['title' => $announcement['title']], $announcement);
        }

        // 3. الفرص الوظيفية والتدريبية المتاحة
        if ($company) {
            $jobsList = [
                [
                    'title' => 'مهندس برمجيات وتطبيقات سحابية (Junior Cloud Developer)',
                    'description' => 'مطلوب خريج حديث في هندسة البرمجيات أو علوم الحاسوب للانضمام لفريق تطوير الحلول السحابية والتحول الرقمي.',
                    'type' => 'full_time',
                    'contract_type' => 'عقد سنوي محدد المدة',
                    'company_id' => $company->id,
                    'location' => 'طرابلس، النوفليين',
                    'seats' => 3,
                    'start_date' => Carbon::now()->addDays(10),
                    'end_date' => Carbon::now()->addYear(),
                    'application_deadline' => Carbon::now()->addDays(25),
                    'required_specializations' => ['هندسة برمجيات', 'علوم حاسوب', 'تقنية معلومات'],
                    'required_skills' => ['PHP / Laravel', 'JavaScript / Vue', 'MySQL', 'Git'],
                    'required_experience' => 'حديث تخرج مع مشاريع تخرج متميزة',
                    'salary' => 2500.00,
                    'status' => 'approved',
                    'created_by' => $admin ? $admin->id : 1,
                ],
                [
                    'title' => 'أخصائي تحليل بيانات وبحوث تسويقية',
                    'description' => 'فرصة توظيف وتدريب على رأس العمل في إعداد التقارير الإحصائية وتحليل مؤشرات الأداء التسويقي للشركات.',
                    'type' => 'full_time',
                    'contract_type' => 'دوام كامل',
                    'company_id' => $company->id,
                    'location' => 'طرابلس، شارع النصر',
                    'seats' => 2,
                    'start_date' => Carbon::now()->addDays(15),
                    'end_date' => Carbon::now()->addYear(),
                    'application_deadline' => Carbon::now()->addDays(30),
                    'required_specializations' => ['إحصاء', 'إدارة أعمال', 'تقنية معلومات'],
                    'required_skills' => ['Excel المتقدم', 'Power BI', 'SQL', 'تحليل البيانات'],
                    'required_experience' => 'خريج حديث أو خبرة سنة',
                    'salary' => 2200.00,
                    'status' => 'approved',
                    'created_by' => $admin ? $admin->id : 1,
                ],
                [
                    'title' => 'متدرب أمن معلومات وحماية شبكات (Cybersecurity Intern)',
                    'description' => 'برنامج تدريب عملي مكثف لمدة 6 أشهر مع إمكانية التثبيت الوظيفي للمتميزين في مجال مراقبة الشبكات والامتثال الأمني.',
                    'type' => 'training',
                    'contract_type' => 'تدريب مدفوع',
                    'company_id' => $company->id,
                    'location' => 'طرابلس، قرقارش',
                    'seats' => 5,
                    'start_date' => Carbon::now()->addDays(5),
                    'end_date' => Carbon::now()->addMonths(6),
                    'application_deadline' => Carbon::now()->addDays(18),
                    'required_specializations' => ['أمن معلومات', 'شبكات واتصالات', 'هندسة حاسوب'],
                    'required_skills' => ['أساسيات الشبكات CCNA', 'Linux', 'مبادئ الأمن السيبراني'],
                    'required_experience' => 'حديث تخرج',
                    'salary' => 1500.00,
                    'status' => 'approved',
                    'created_by' => $admin ? $admin->id : 1,
                ],
            ];

            foreach ($jobsList as $job) {
                JobOpportunity::updateOrCreate(['title' => $job['title'], 'company_id' => $job['company_id']], $job);
            }
        }

        echo "تم بنجاح زراعة الأخبار، الإعلانات، والفرص الوظيفية في المنصة!\n";
    }
}
