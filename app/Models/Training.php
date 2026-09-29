<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $end_date
 */
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
        'training_days_of_week',
        'location',
        'seats',
        'status',
        'company_id',
        'coordinator_id',
        'media_coverage_status',
        'media_coverage_summary',
        'media_press_release',
        'media_coverage_notes',
        'media_team_members',
        'media_coverage_links',
        'media_coverage_date',
        'is_advertised',
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
        'media_coverage_date' => 'date',
        'training_days_of_week' => 'array',
    ];

    /**
     * قائمة التخصصات والمجالات المعتمدة
     */
    public static function getCategories(): array
    {
        return [
            'الهندسة والتخصصات التقنية',
            'تقنية المعلومات والتحول الرقمي',
            'الإدارة والقيادة',
            'الاقتصاد والمالية والمحاسبة',
            'التسويق والمبيعات',
            'الموارد البشرية',
            'القانون',
            'الطب والعلوم الصحية',
            'الإعلام',
            'ريادة الأعمال',
            'اللغات والترجمة',
            'البحث العلمي والمهارات الأكاديمية',
            'العلوم والبيئة والطاقة',
            'التنمية البشرية والمهارات الشخصية',
            'أخرى',
        ];
    }

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
     * مع تطبيق استثناء الأيام غير المحددة (مثل الجمعة والسبت)
     * @return \Illuminate\Support\Collection
     */
    public function getTrainingDaysAttribute()
    {
        $dates = collect();
        if (!$this->start_date) {
            return $dates;
        }

        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = $this->end_date ? \Carbon\Carbon::parse($this->end_date) : $startDate->copy();

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        $current = $startDate->copy();
        $dayIndex = 1;

        $allowedDays = $this->training_days_of_week;
        if (is_array($allowedDays)) {
            $allowedDays = array_map('intval', $allowedDays);
        }

        while ($current->lte($endDate)) {
            // إذا تم تحديد أيام معينة للأسبوع، نستثني الأيام غير المحددة
            if (is_array($allowedDays) && count($allowedDays) > 0) {
                if (!in_array($current->dayOfWeek, $allowedDays, true)) {
                    $current->addDay();
                    continue;
                }
            }

            $dates->push([
                'day_number' => $dayIndex,
                'date' => $current->format('Y-m-d'),
                'carbon' => $current->copy(),
                'formatted' => $current->format('Y-m-d'),
                'day_name' => $this->getArabicDayName($current->dayOfWeek),
                'day_of_week' => $current->dayOfWeek,
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
     * إجمالي عدد أيام التدريب الفعلية
     */
    public function getTotalDaysCountAttribute()
    {
        $days = $this->training_days;
        return $days->count() > 0 ? $days->count() : 1;
    }

    /**
     * اسم اليوم بالعربية
     */
    public function getArabicDayName($dayOfWeek)
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
     * نص توصيف أيام التدريب الأسبوعية المعتمدة
     */
    public function getTrainingDaysTextAttribute(): string
    {
        $days = $this->training_days_of_week;
        if (!is_array($days) || empty($days)) {
            return 'طيلة أيام الأسبوع (شاملة الجمعة والسبت)';
        }

        $days = array_map('intval', $days);
        sort($days);

        if ($days === [0, 1, 2, 3, 4]) {
            return 'أيام العمل الرسمية: الأحد - الخميس (استثناء الجمعة والسبت)';
        }

        if (count($days) === 7) {
            return 'طيلة أيام الأسبوع (شاملة الجمعة والسبت)';
        }

        $names = array_map(fn($d) => $this->getArabicDayName($d), $days);
        return implode('، ', $names);
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
            'pending' => 'بانتظار التغطية الإعلامية',
            'covered' => 'تمت التغطية والتوثيق',
            'not_required' => 'تغطية غير مطلوبة'
        ];

        return $statuses[$this->media_coverage_status] ?? 'بانتظار التغطية الإعلامية';
    }

    /**
     * الحصول على نص حالة التغطية الإعلامية
     */
    public function getMediaCoverageStatusText(): string
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
