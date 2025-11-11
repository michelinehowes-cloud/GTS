<?php
// ملف: app/Exports/UsersExport.php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return [
            'الرقم',
            'الاسم',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'الدور',
            'الحالة',
            'تاريخ التسجيل',
            'آخر تحديث'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone ?? 'غير محدد',
            $this->getRoleName($user->role),
            'نشط',
            $user->created_at->format('Y-m-d'),
            $user->updated_at->format('Y-m-d')
        ];
    }

    private function getRoleName($role)
    {
        $roles = [
            'admin' => 'مدير النظام',
            'training_coordinator' => 'منسق التدريب',
            'placement_coordinator' => 'منسق التوظيف',
            'graduate' => 'خريج'
        ];

        return $roles[$role] ?? $role;
    }
}