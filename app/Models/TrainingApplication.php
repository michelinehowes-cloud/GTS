<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'user_id',
        'status',
        'message',
        'applied_at',
        'attended_at'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'attended_at' => 'datetime'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(TrainingAttendance::class, 'training_application_id');
    }

    /**
     * عدد الأيام التي حضرها المتدرب بالفعل في هذا البرنامج التدريبي
     */
    public function getAttendedDaysCountAttribute(): int
    {
        $count = 0;
        if ($this->relationLoaded('attendances') && $this->attendances->isNotEmpty()) {
            $count = $this->attendances->whereIn('status', ['present', 'late'])->count();
        } else {
            $count = TrainingAttendance::where('training_id', $this->training_id)
                ->where('user_id', $this->user_id)
                ->whereIn('status', ['present', 'late'])
                ->count();
        }

        // دعم التوافق مع البرامج المسجل حضورها في حقل attended_at مباشرة
        if ($count === 0 && $this->attended_at !== null) {
            $count = 1;
        }

        return $count;
    }

    /**
     * النسبة المئوية لحضور المتدرب في البرنامج التدريبي (0 إلى 100)
     */
    public function getAttendancePercentageAttribute(): int
    {
        $totalDays = $this->training?->total_days_count ?? 1;
        if ($totalDays <= 0) {
            $totalDays = 1;
        }

        $attendedDays = $this->attended_days_count;
        $pct = (int) round(($attendedDays / $totalDays) * 100);

        return min(100, max(0, $pct));
    }

    /**
     * لون شارة نسبة الحضور
     */
    public function getAttendanceBadgeColorAttribute(): string
    {
        $pct = $this->attendance_percentage;
        if ($pct >= 80) {
            return 'success';
        } elseif ($pct >= 50) {
            return 'warning';
        }
        return 'danger';
    }
}