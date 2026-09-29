<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\TrainingAttendance;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MonthNineTrainingsSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password123');

        // 1. خريجون وطلبة مشاركون
        $graduatesData = [
            ['name' => 'أحمد مفتاح الترهوني', 'email' => 'ahmed.tarhouni@tripoliuniversity.edu.ly', 'faculty' => 'كلية تقنية المعلومات', 'dept' => 'هندسة برمجيات'],
            ['name' => 'سارة علي الزروق', 'email' => 'sara.zarroug@tripoliuniversity.edu.ly', 'faculty' => 'كلية الهندسة', 'dept' => 'هندسة الحاسوب'],
            ['name' => 'محمد عادل الشريف', 'email' => 'mohammed.sharif@tripoliuniversity.edu.ly', 'faculty' => 'كلية الاقتصاد والعلوم السياسية', 'dept' => 'إدارة أعمال'],
            ['name' => 'مريم حسن الفرجاني', 'email' => 'maryam.ferjani@tripoliuniversity.edu.ly', 'faculty' => 'كلية تقنية المعلومات', 'dept' => 'شبكات واتصالات'],
            ['name' => 'عمر عبد الله بن يونس', 'email' => 'omar.younis@tripoliuniversity.edu.ly', 'faculty' => 'كلية الهندسة', 'dept' => 'هندسة كهربائية'],
            ['name' => 'رنا كمال السويحلي', 'email' => 'rana.swehli@tripoliuniversity.edu.ly', 'faculty' => 'كلية الاقتصاد والعلوم السياسية', 'dept' => 'تسويق'],
            ['name' => 'أسامة جمال القرقني', 'email' => 'osama.gargani@tripoliuniversity.edu.ly', 'faculty' => 'كلية تقنية المعلومات', 'dept' => 'أمن معلومات'],
            ['name' => 'هدى سالم بن ناصر', 'email' => 'huda.nasser@tripoliuniversity.edu.ly', 'faculty' => 'كلية اللغات', 'dept' => 'ترجمة لغة إنجليزية'],
        ];

        $graduateUsers = [];
        foreach ($graduatesData as $grad) {
            $user = User::updateOrCreate(
                ['email' => $grad['email']],
                [
                    'name' => $grad['name'],
                    'password' => $password,
                    'role' => 'graduate',
                    'is_active' => true,
                    'is_approved' => true,
                    'graduation_year' => 2025,
                    'faculty' => $grad['faculty'],
                    'major' => $grad['dept'],
                    'email_verified_at' => now(),
                ]
            );
            $graduateUsers[] = $user;
        }

        $company = Company::first();
        $coordinator = User::where('role', 'training_coordinator')->first() ?? User::where('role', 'admin')->first();

        // 2. تدريبات وورش عمل تم تنفيذها في شهر 9 (سبتمبر 2026)
        $trainingsData = [
            [
                'title' => 'ورشة عمل: تطبيقات الذكاء الاصطناعي والتحول الرقمي',
                'description' => 'ورشة عمل مكثفة حول أدوات الذكاء الاصطناعي التوليدي وأثرها في تسريع التحول الرقمي وتحليل البيانات.',
                'type' => 'workshop',
                'category' => 'تقنية المعلومات والتحول الرقمي',
                'instructor_name' => 'د. عبد السلام الورفلي',
                'duration' => '5 أيام (25 ساعة)',
                'start_date' => '2026-09-06', // الأحد
                'end_date' => '2026-09-10',   // الخميس
                'training_days_of_week' => [0, 1, 2, 3, 4], // الأحد - الخميس
                'location' => 'مختبر الابتكار الرقمي - كلية تقنية المعلومات',
                'seats' => 30,
                'status' => 'completed',
            ],
            [
                'title' => 'دورة تدريبية: إدارة المشاريع الاحترافية وتحليل الأعمال (PMP)',
                'description' => 'دورة تدريبية شاملة تغطي محاور منهجيات إدارة المشاريع الرشيقة والتحليل الاستراتيجي لمشاريع التخرج وسوق العمل.',
                'type' => 'course',
                'category' => 'الإدارة والقيادة',
                'instructor_name' => 'أ. خالد بن عثمان',
                'duration' => '10 أيام تدريبية (أسبوعان)',
                'start_date' => '2026-09-13', // الأحد
                'end_date' => '2026-09-24',   // الخميس
                'training_days_of_week' => [0, 1, 2, 3, 4], // الأحد - الخميس
                'location' => 'القاعة الكبرى - مكتب تدريب الخريجين',
                'seats' => 35,
                'status' => 'completed',
            ],
            [
                'title' => 'ورشة عمل: مهارات القيادة والاتصال الفعال في بيئة العمل',
                'description' => 'ورشة عمل تفاعلية لتطوير المهارات الشخصية وأساليب التفاوض وبناء فرق العمل للمهندسين والإداريين حديثي التخرج.',
                'type' => 'workshop',
                'category' => 'التنمية البشرية والمهارات الشخصية',
                'instructor_name' => 'أ. فاطمة المجبري',
                'duration' => '3 أيام (15 ساعة)',
                'start_date' => '2026-09-15', // الثلاثاء
                'end_date' => '2026-09-17',   // الخميس
                'training_days_of_week' => [2, 3, 4], // الثلاثاء، الأربعاء، الخميس
                'location' => 'قاعة ورش العمل - كلية الاقتصاد',
                'seats' => 25,
                'status' => 'completed',
            ],
            [
                'title' => 'ورشة عمل: استراتيجيات التسويق الرقمي وبناء العلامة التجارية الشخصية',
                'description' => 'ورشة تطبيقية في إدارة الحملات الرقمية وصناعة المحتوى المهني وإبراز الخبرات على منصات التوظيف.',
                'type' => 'workshop',
                'category' => 'التسويق والمبيعات',
                'instructor_name' => 'م. طارق القمودي',
                'duration' => '4 أيام تدريبية',
                'start_date' => '2026-09-20', // الأحد
                'end_date' => '2026-09-24',   // الخميس (بدون ثلاثاء مثلاً: 0, 1, 3, 4)
                'training_days_of_week' => [0, 1, 3, 4], // الأحد، الإثنين، الأربعاء، الخميس
                'location' => 'مختبر الميديا والوسائط المتعددة',
                'seats' => 30,
                'status' => 'completed',
            ],
            [
                'title' => 'ورشة عمل: إعداد وكتابة الأوراق البحثية والتحليل الإحصائي',
                'description' => 'دورة تدريبية متقدمة في مناهج البحث العلمي وأساليب النشر في المجلات المحكمة.',
                'type' => 'workshop',
                'category' => 'البحث العلمي والمهارات الأكاديمية',
                'instructor_name' => 'د. نجوى الفيتوري',
                'duration' => '3 أيام تدريبية',
                'start_date' => '2026-09-27', // الأحد
                'end_date' => '2026-09-29',   // الثلاثاء
                'training_days_of_week' => [0, 1, 2], // الأحد، الإثنين، الثلاثاء
                'location' => 'مركز البحوث والاستشارات - جامعة طرابلس',
                'seats' => 25,
                'status' => 'completed',
            ],
        ];

        foreach ($trainingsData as $tData) {
            $training = Training::updateOrCreate(
                ['title' => $tData['title']],
                array_merge($tData, [
                    'company_id' => $company ? $company->id : null,
                    'coordinator_id' => $coordinator ? $coordinator->id : null,
                ])
            );

            // تسجيل الطلبة وإنشاء سجلات حضور حقيقية
            $trainingDays = $training->training_days;
            $totalDays = $trainingDays->count();

            // نحدد مجموعة من الطلبة لكل تدريب
            $assignedGraduates = array_slice($graduateUsers, 0, rand(5, 8));

            foreach ($assignedGraduates as $index => $gradUser) {
                // تسجيل الطلب كمقبول
                $application = TrainingApplication::updateOrCreate(
                    ['training_id' => $training->id, 'user_id' => $gradUser->id],
                    [
                        'status' => 'approved',
                        'applied_at' => Carbon::parse($training->start_date)->subDays(rand(3, 10)),
                        'message' => 'تم قبول المتقدم واستيفاء الشروط',
                    ]
                );

                // تحديد سلوك الحضور للطالب (ملتزم 100%، ملتزم 80%، حضور جزئي)
                // أول 4 طلاب ملتزمون بجميع الأيام، البقية بنسب مختلفة
                $attendanceBehavior = ($index < 4) ? 'full' : (($index < 6) ? 'committed' : 'partial');

                foreach ($trainingDays as $dayIndex => $day) {
                    $shouldAttend = true;
                    if ($attendanceBehavior === 'full') {
                        $shouldAttend = true; // 100% حضور
                    } elseif ($attendanceBehavior === 'committed') {
                        // يغيب يوماً واحداً فقط
                        $shouldAttend = ($dayIndex !== 1);
                    } else {
                        // يغيب في نصف الأيام
                        $shouldAttend = ($dayIndex % 2 === 0);
                    }

                    if ($shouldAttend) {
                        TrainingAttendance::updateOrCreate(
                            [
                                'training_id' => $training->id,
                                'user_id' => $gradUser->id,
                                'date' => $day['date'],
                            ],
                            [
                                'training_application_id' => $application->id,
                                'status' => 'present',
                                'attended_at' => Carbon::parse($day['date'] . ' 09:' . rand(10, 45) . ':00'),
                                'notes' => 'حضور معتمد وموثق',
                            ]
                        );
                    }
                }
            }
        }

        echo "تم بنجاح زراعة بيانات برامج وورش عمل شهر 9 (سبتمبر 2026) مع الطلبة والحضور الملتزم!\n";
    }
}
