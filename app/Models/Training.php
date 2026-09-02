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
        'coordinator_id',
        'media_coverage_status',
        'is_advertised',
        'category',
        'category',
        'instructor_name',
        'trainer_id',
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
     * العلاقة مع سجلات الحضور اليومية
     */
    public function attendances()
    {
        return $this->hasMany(TrainingAttendance::class);
    }

    /**
     * الحصول على قائمة تواريخ أيام التدريب من تاريخ البدء إلى تاريخ الانتهاء
     * @return \Illuminate\Support\Collection
     */
    public function getTrainingDaysAttribute()
    {
        $dates = collect();
        if (!$this->start_date) {
            return $dates;
        }

        $startDate = $this->start_date->copy();
        $endDate = $this->end_date ? $this->end_date->copy() : $startDate->copy();

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        $current = $startDate->copy();
        $dayIndex = 1;

        while ($current->lte($endDate)) {
            $dates->push([
                'day_number' => $dayIndex,
                'date' => $current->format('Y-m-d'),
                'carbon' => $current->copy(),
                'formatted' => $current->format('Y-m-d'),
                'day_name' => $this->getArabicDayName($current->dayOfWeek),
                'is_today' => $current->isToday(),
                'is_past' => $current->isPast() && !$current->isToday(),
                'is_future' => $current->isFuture() && !$current->isToday(),
            ]);
            $current->addDay();
            $dayIndex++;
        }

        return $dates;
    }

    /**
     * إجمالي عدد أيام التدريب
     */
    public function getTotalDaysCountAttribute()
    {
        $days = $this->training_days;
        return $days->count() > 0 ? $days->count() : 1;
    }

    /**
     * اسم اليوم بالعربية
     */
    private function getArabicDayName($dayOfWeek)
    {
        $days = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];
        return $days[$dayOfWeek] ?? '';
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

    /**
     * العلاقة مع الوسائط
     */
    public function media()
    {
        return $this->hasMany(TrainingMedia::class);
    }

    /**
     * الحصول على حالة التغطية الإعلامية بالعربية
     */
    public function getMediaCoverageStatusArabicAttribute()
    {
        $statuses = [
            'pending' => 'تحت التغطية',
            'covered' => 'تمت التغطية',
            'not_required' => 'لا يتطلب تغطية'
        ];

        return $statuses[$this->media_coverage_status] ?? $this->media_coverage_status;
    }

    /**
     * الحصول على نص حالة التغطية الإعلامية
     */
    public function getMediaCoverageStatusText()
    {
        return $this->getMediaCoverageStatusArabicAttribute();
    }

    /**
     * العلاقة مع المدرب
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
