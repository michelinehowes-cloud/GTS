<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * تشغيل بذور الصلاحيات وتعيين الصلاحيات للمستخدمين الحاليين
     */
    public function run(): void
    {
        $permissions = [
            // 🎓 وحدة شؤون الخريجين
            [
                'name' => 'graduates.view',
                'display_name' => 'استعراض بيانات الخريجين',
                'module' => 'graduates',
                'description' => 'الاطلاع على قائمة الخريجين وسجلاتهم الأكاديمية والمهنية',
            ],
            [
                'name' => 'graduates.create',
                'display_name' => 'إضافة خريج جديد',
                'module' => 'graduates',
                'description' => 'إدخال وتسجيل بيانات خريج جديد يدوياً في النظام',
            ],
            [
                'name' => 'graduates.edit',
                'display_name' => 'تعديل بيانات الخريجين',
                'module' => 'graduates',
                'description' => 'تحديث وتعديل البيانات الشخصية والأكاديمية للخريجين',
            ],
            [
                'name' => 'graduates.delete',
                'display_name' => 'حذف سجلات الخريجين',
                'module' => 'graduates',
                'description' => 'حذف بيانات وحسابات الخريجين من المنصة',
            ],
            [
                'name' => 'graduates.approve',
                'display_name' => 'مراجعة واعتماد طلبات التسجيل',
                'module' => 'graduates',
                'description' => 'تدقيق واعتماد حسابات الخريجين المسجلين ذاتياً عبر المنصة',
            ],
            [
                'name' => 'graduates.import_export',
                'display_name' => 'استيراد وتصدير بيانات الخريجين',
                'module' => 'graduates',
                'description' => 'تنزيل ورفع ملفات Excel وتصدير تقارير PDF للخريجين',
            ],

            // 🏢 وحدة الشركات والشراكات
            [
                'name' => 'companies.view',
                'display_name' => 'استعراض الشركات الشريكة',
                'module' => 'companies',
                'description' => 'الاطلاع على قائمة الشركات وملفاتها ومسؤولي الاتصال بها',
            ],
            [
                'name' => 'companies.create',
                'display_name' => 'إضافة وتوثيق شركة جديدة',
                'module' => 'companies',
                'description' => 'تسجيل وإدراج شركة ومؤسسة جديدة في المنصة',
            ],
            [
                'name' => 'companies.edit',
                'display_name' => 'تعديل واعتماد الشركات',
                'module' => 'companies',
                'description' => 'تعديل بيانات الشركة وتفعيل وتوثيق حسابها الرسمي',
            ],
            [
                'name' => 'companies.delete',
                'display_name' => 'حذف الشركات',
                'module' => 'companies',
                'description' => 'إزالة سجلات وملفات الشركات من النظام',
            ],
            [
                'name' => 'partnerships.documents',
                'display_name' => 'إدارة وثائق واتفاقيات الشراكة',
                'module' => 'companies',
                'description' => 'رفع وتدقيق وتنزيل مذكرات التفاهم واتفاقيات التعاون والتدريب',
            ],

            // 📚 وحدة البرامج التدريبية
            [
                'name' => 'trainings.view',
                'display_name' => 'استعراض البرامج والدورات التدريبية',
                'module' => 'trainings',
                'description' => 'الاطلاع على ورش العمل والدورات والتقويم التدريبي',
            ],
            [
                'name' => 'trainings.create',
                'display_name' => 'إنشاء برنامج تدريبي جديد',
                'module' => 'trainings',
                'description' => 'إضافة ورش عمل ودورات وتحديد المواعيد والمستهدفين',
            ],
            [
                'name' => 'trainings.edit',
                'display_name' => 'تعديل الدورات والبرامج',
                'module' => 'trainings',
                'description' => 'تحديث تفاصيل التدريبات وتعديل الجداول الزمنية والحالة',
            ],
            [
                'name' => 'trainings.delete',
                'display_name' => 'حذف البرامج التدريبية',
                'module' => 'trainings',
                'description' => 'إلغاء أو حذف دورات وورش عمل من النظام',
            ],
            [
                'name' => 'trainings.applications',
                'display_name' => 'إدارة طلبات الالتحاق بالتدريب',
                'module' => 'trainings',
                'description' => 'مراجعة وقبول أو رفض طلبات التحاق الخريجين بالدورات',
            ],
            [
                'name' => 'trainings.attendance',
                'display_name' => 'تسجيل الحضور والباركود',
                'module' => 'trainings',
                'description' => 'تسجيل حضور وغياب المتدربين واستخدام ماسح الباركود السريع',
            ],
            [
                'name' => 'trainings.trainers',
                'display_name' => 'إدارة بيانات المدربين',
                'module' => 'trainings',
                'description' => 'إضافة وتعديل وتعيين المدربين والمدربات للبرامج',
            ],

            // 🎪 وحدة معرض التوظيف 2026
            [
                'name' => 'job_fair.view',
                'display_name' => 'استعراض معرض التوظيف',
                'module' => 'job_fair',
                'description' => 'الاطلاع على تفاصيل وإحصائيات وشركات معرض التوظيف',
            ],
            [
                'name' => 'job_fair.manage',
                'display_name' => 'إدارة فعاليات وأجنحة المعرض',
                'module' => 'job_fair',
                'description' => 'التحكم في إعدادات المعرض، أجنحة الشركات، وجدول الفعاليات',
            ],
            [
                'name' => 'job_fair.registrations',
                'display_name' => 'إدارة تذاكر وزوار المعرض',
                'module' => 'job_fair',
                'description' => 'متابعة تسجيلات الخريجين والزوار وتأكيد الدخول عبر التذاكر',
            ],

            // 💼 وحدة فرص العمل والترشيحات
            [
                'name' => 'jobs.view',
                'display_name' => 'استعراض الفرص الوظيفية',
                'module' => 'jobs',
                'description' => 'الاطلاع على الوظائف الشاغرة وطلبات التقديم عليها',
            ],
            [
                'name' => 'jobs.manage',
                'display_name' => 'نشر وإدارة فرص العمل',
                'module' => 'jobs',
                'description' => 'إضافة وتعديل وإغلاق الفرص الوظيفية المقدمة من الشركات',
            ],
            [
                'name' => 'nominations.manage',
                'display_name' => 'إدارة الترشيحات الوظيفية',
                'module' => 'jobs',
                'description' => 'ترشيح الخريجين المتميزين للوظائف ومتابعة نتائج مقابلاتهم',
            ],

            // 📊 وحدة التقييم والاستبيانات
            [
                'name' => 'surveys.manage',
                'display_name' => 'إدارة الاستبيانات ونماذج الرأي',
                'module' => 'evaluations',
                'description' => 'إنشاء الاستبيانات واستطلاع آراء الخريجين وأرباب العمل',
            ],
            [
                'name' => 'evaluations.manage',
                'display_name' => 'إدارة تقييمات الدورات والمدربين',
                'module' => 'evaluations',
                'description' => 'توثيق وتحليل تقييمات الأداء والبرامج التدريبية',
            ],
            [
                'name' => 'reports.view',
                'display_name' => 'الاطلاع على التقارير والإحصائيات',
                'module' => 'evaluations',
                'description' => 'عرض وتحليل التقارير الدورية ومؤشرات الأداء العامة للنظام',
            ],

            // 📢 وحدة الإعلام والمحتوى
            [
                'name' => 'media.manage',
                'display_name' => 'إدارة مكتبة الوسائط والتغطيات',
                'module' => 'media',
                'description' => 'رفع وتنظيم وتوثيق صور وفيديوهات الفعاليات التدريبية',
            ],
            [
                'name' => 'news.manage',
                'display_name' => 'نشر وإدارة الأخبار والإعلانات',
                'module' => 'media',
                'description' => 'إضافة وتحرير الأخبار الجامعية والإعلانات الهامة في المنصة',
            ],

            // ⚙️ وحدة إدارة النظام والموظفين
            [
                'name' => 'users.view',
                'display_name' => 'استعراض المستخدمين والموظفين',
                'module' => 'system',
                'description' => 'الاطلاع على قائمة مستخدمي وموظفي النظام وحالاتهم',
            ],
            [
                'name' => 'users.manage',
                'display_name' => 'إدارة الموظفين وتخصيص الصلاحيات',
                'module' => 'system',
                'description' => 'إضافة وتعديل موظفي النظام وتحديد مصفوفة الصلاحيات الممنوحة لهم',
            ],
            [
                'name' => 'audit_logs.view',
                'display_name' => 'سجل الرقابة والأمان (Audit Logs)',
                'module' => 'system',
                'description' => 'متابعة السجل الأمني الميداني لكافة الحركات والعمليات بالنظام',
            ],
        ];

        // 1. إدراج أو تحديث الصلاحيات
        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'module' => $perm['module'],
                    'description' => $perm['description'],
                ]
            );
        }

        // 2. مزامنة الصلاحيات الافتراضية للمستخدمين الحاليين حسب أدوارهم
        $syncRolePerms = function ($role, $permNames) {
            $ids = Permission::whereIn('name', $permNames)->pluck('id')->toArray();
            $users = User::where('role', $role)->get();
            foreach ($users as $user) {
                $user->permissions()->sync($ids);
            }
        };

        // منسق التدريب
        $syncRolePerms('training_coordinator', [
            'trainings.view', 'trainings.create', 'trainings.edit', 'trainings.delete',
            'trainings.applications', 'trainings.attendance', 'trainings.trainers',
            'reports.view'
        ]);

        // مسؤول الشراكات
        $syncRolePerms('partnership_officer', [
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'partnerships.documents', 'jobs.view', 'jobs.manage', 'nominations.manage',
            'job_fair.view', 'job_fair.manage', 'job_fair.registrations',
            'reports.view'
        ]);

        // مسؤول الإرشاد المهني
        $syncRolePerms('career_guidance_officer', [
            'graduates.view', 'graduates.create', 'graduates.edit', 'graduates.delete',
            'graduates.approve', 'graduates.import_export', 'nominations.manage',
            'reports.view'
        ]);

        // مسؤول التقييم والمتابعة
        $syncRolePerms('evaluation_followup', [
            'surveys.manage', 'evaluations.manage', 'reports.view',
            'trainings.view'
        ]);

        // مسؤول الميديا
        $syncRolePerms('media_officer', [
            'media.manage', 'news.manage'
        ]);
    }
}
