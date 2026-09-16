<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobFairRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'user_id',
        'qr_code',
        'registration_number',
        'attended',
        'check_in_at',
        'interests',
        'notes',
        'status',
    ];

    protected $casts = [
        'attended'     => 'boolean',
        'check_in_at'  => 'datetime',
    ];

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class);
    }

    public function graduate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * توليد رمز QR فريد
     */
    public static function generateQrCode(int $jobFairId, int $userId): string
    {
        return 'JF-' . $jobFairId . '-' . $userId . '-' . strtoupper(Str::random(8));
    }

    /**
     * توليد رقم تسجيل
     */
    public static function generateRegistrationNumber(int $jobFairId): string
    {
        $count = self::where('job_fair_id', $jobFairId)->count() + 1;
        $candidate = 'JF' . $jobFairId . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        while (self::where('registration_number', $candidate)->exists()) {
            $count++;
            $candidate = 'JF' . $jobFairId . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }
        return $candidate;
    }
}
