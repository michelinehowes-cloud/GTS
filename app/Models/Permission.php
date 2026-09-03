<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'module',
        'description',
    ];

    /**
     * العلاقة مع المستخدمين
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }

    /**
     * تعريف الوحدات وأيقوناتها وألوانها
     */
    public static function getModuleMeta(): array
    {
        return [
            'graduates' => [
                'label' => 'إدارة شؤون الخريجين',
                'icon' => 'fas fa-user-graduate',
                'color' => '#1d4ed8',
                'badge' => 'bg-primary',
            ],
            'companies' => [
                'label' => 'الشركات والشراكات',
                'icon' => 'fas fa-handshake',
                'color' => '#059669',
                'badge' => 'bg-success',
            ],
            'trainings' => [
                'label' => 'البرامج والورش التدريبية',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#7c3aed',
                'badge' => 'bg-purple',
            ],
            'job_fair' => [
                'label' => 'معرض التوظيف السنوي',
                'icon' => 'fas fa-store',
                'color' => '#ea580c',
                'badge' => 'bg-warning text-dark',
            ],
            'jobs' => [
                'label' => 'فرص العمل والترشيحات',
                'icon' => 'fas fa-briefcase',
                'color' => '#0284c7',
                'badge' => 'bg-info text-white',
            ],
            'evaluations' => [
                'label' => 'التقييم والاستبيانات',
                'icon' => 'fas fa-chart-line',
                'color' => '#0891b2',
                'badge' => 'bg-teal',
            ],
            'media' => [
                'label' => 'الإعلام والمحتوى الرقمي',
                'icon' => 'fas fa-photo-video',
                'color' => '#d97706',
                'badge' => 'bg-orange',
            ],
            'system' => [
                'label' => 'إدارة النظام والرقابة الأمنية',
                'icon' => 'fas fa-shield-alt',
                'color' => '#dc2626',
                'badge' => 'bg-danger',
            ],
        ];
    }

    /**
     * جلب كافة الصلاحيات مقسمة حسب الوحدة
     */
    public static function getGrouped()
    {
        $all = self::all();
        $meta = self::getModuleMeta();
        $grouped = [];

        foreach ($meta as $moduleKey => $moduleInfo) {
            $perms = $all->where('module', $moduleKey)->values();
            $grouped[$moduleKey] = [
                'meta' => $moduleInfo,
                'permissions' => $perms,
                'items' => $perms,
            ];
        }

        return $grouped;
    }
}
