<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnershipDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'uploaded_by',
        'document_name',
        'document_type',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'document_date',
        'effective_date',
        'expiry_date',
        'document_status',
        'is_shared_with_company',
        'is_confidential',
        'company_signed',
        'university_signed',
        'university_signed_date',
        'company_signed_date',
        'version',
        'previous_version_id',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
        'is_archived',
        'archival_reason'
    ];

    protected $casts = [
        'document_date' => 'date',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'university_signed_date' => 'date',
        'company_signed_date' => 'date',
        'reviewed_at' => 'datetime',
        'is_shared_with_company' => 'boolean',
        'is_confidential' => 'boolean',
        'company_signed' => 'boolean',
        'university_signed' => 'boolean',
        'is_archived' => 'boolean'
    ];

    /**
     * العلاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * العلاقة مع المستخدم الذي رفع الوثيقة
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * العلاقة مع المستخدم المراجع
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * العلاقة مع الإصدار السابق
     */
    public function previousVersion()
    {
        return $this->belongsTo(PartnershipDocument::class, 'previous_version_id');
    }

    /**
     * العلاقة مع الإصدارات اللاحقة
     */
    public function nextVersions()
    {
        return $this->hasMany(PartnershipDocument::class, 'previous_version_id');
    }

    /**
     * التحقق إذا كانت الوثيقة نشطة
     */
    public function getIsActiveAttribute()
    {
        return $this->document_status === 'active' && 
               (!$this->expiry_date || $this->expiry_date >= now());
    }

    /**
     * التحقق إذا كانت الوثيقة منتهية
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * التحقق إذا كانت الوثيقة موقعة من الطرفين
     */
    public function getIsFullySignedAttribute()
    {
        return $this->company_signed && $this->university_signed;
    }

    /**
     * الحصول على نوع الوثيقة كنص
     */
    public function getDocumentTypeTextAttribute()
    {
        $types = [
            'mou' => 'مذكرة تفاهم',
            'contract' => 'عقد',
            'agreement' => 'اتفاقية',
            'amendment' => 'تعديل',
            'renewal' => 'تجديد',
            'termination' => 'إنهاء',
            'other' => 'أخرى'
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    /**
     * الحصول على حالة الوثيقة كنص
     */
    public function getDocumentStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'مسودة',
            'active' => 'نشطة',
            'expired' => 'منتهية',
            'cancelled' => 'ملغاة'
        ];

        return $statuses[$this->document_status] ?? $this->document_status;
    }

    /**
     * البحث عن وثائق الشركة
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * البحث عن الوثائق النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('document_status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    });
    }

    /**
     * البحث عن الوثائق التي تحتاج للتوقيع
     */
    public function scopeNeedsSignature($query)
    {
        return $query->where(function($q) {
            $q->where('company_signed', false)
              ->orWhere('university_signed', false);
        });
    }

    /**
     * البحث عن الوثائق المنتهية
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->orWhere('document_status', 'expired');
    }
}