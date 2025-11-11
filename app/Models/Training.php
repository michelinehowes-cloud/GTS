<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'duration',
        'start_date',
        'end_date',
        'location',
        'seats',
        'status',
        'company_id',
        'coordinator_id'
    
    ];
    public function coordinator()
{
    return $this->belongsTo(User::class, 'coordinator_id');
}

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * العلاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * العلاقة مع طلبات التدريب
     */
    public function applications()
    {
        return $this->hasMany(TrainingApplication::class);
    }

    /**
     * الحصول على النوع بالعربية
     */
    public function getTypeArabicAttribute()
    {
        $types = [
            'workshop' => 'ورشة عمل',
            'course' => 'دورة',
            'seminar' => 'ندوة',
            'internship' => 'تدريب عملي'
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * الحصول على الحالة بالعربية
     */
    public function getStatusArabicAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'completed' => 'مكتمل'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * التحقق إذا كان التدريب نشطاً
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && $this->start_date >= now();
    }

    /**
     * التحقق إذا كان هناك مقاعد متاحة
     */
    public function getAvailableSeatsAttribute()
    {
        $takenSeats = $this->applications()->where('status', 'approved')->count();
        return $this->seats - $takenSeats;
    }
}