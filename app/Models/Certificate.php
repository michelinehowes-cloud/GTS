<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_code',
        'user_id',
        'training_id',
        'company_id',
        'type',
        'title',
        'recipient_name',
        'hours',
        'issue_date',
        'start_date',
        'end_date',
        'instructor_name',
        'has_company_collaboration',
        'company_name',
        'company_logo',
        'status',
        'qr_code_data',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'has_company_collaboration' => 'boolean',
        'hours' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Label of the certificate type in Arabic
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'training_attendance' => 'شهادة حضور تدريب',
            'workshop_attendance' => 'شهادة حضور ورشة عمل',
            'cooperative_attendance' => 'شهادة حضور تعاونية',
            default => 'شهادة مشاركة',
        };
    }

    /**
     * Badge CSS class for the certificate type
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'training_attendance' => 'badge-training',
            'workshop_attendance' => 'badge-workshop',
            'cooperative_attendance' => 'badge-cooperative',
            default => 'badge-training',
        };
    }

    /**
     * Generate unique verification code
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        do {
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $code = "UOT-CERT-{$year}-{$random}";
        } while (self::where('certificate_code', $code)->exists());

        return $code;
    }
}
