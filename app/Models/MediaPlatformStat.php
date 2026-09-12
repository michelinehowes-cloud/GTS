<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaPlatformStat extends Model
{
    use HasFactory;

    protected $table = 'media_platform_stats';

    protected $fillable = [
        'is_ribbon_visible',
        'global_mode',
        'graduates_mode',
        'graduates_custom_value',
        'graduates_visible',
        'graduates_label',
        'graduates_prefix',
        'companies_mode',
        'companies_custom_value',
        'companies_visible',
        'companies_label',
        'companies_prefix',
        'trainings_mode',
        'trainings_custom_value',
        'trainings_visible',
        'trainings_label',
        'trainings_prefix',
        'opportunities_mode',
        'opportunities_custom_value',
        'opportunities_visible',
        'opportunities_label',
        'opportunities_prefix',
        'updated_by_user_id',
    ];

    protected $casts = [
        'is_ribbon_visible' => 'boolean',
        'graduates_visible' => 'boolean',
        'companies_visible' => 'boolean',
        'trainings_visible' => 'boolean',
        'opportunities_visible' => 'boolean',
        'graduates_custom_value' => 'integer',
        'companies_custom_value' => 'integer',
        'trainings_custom_value' => 'integer',
        'opportunities_custom_value' => 'integer',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    /**
     * Singleton instance provider
     */
    public static function current(): self
    {
        $stat = self::first();
        if (!$stat) {
            $stat = self::create([
                'is_ribbon_visible' => true,
                'global_mode' => 'auto',
                'graduates_mode' => 'auto',
                'graduates_custom_value' => null,
                'graduates_visible' => true,
                'graduates_label' => 'خريج مسجل ومعتمد',
                'graduates_prefix' => '+',
                'companies_mode' => 'auto',
                'companies_custom_value' => null,
                'companies_visible' => true,
                'companies_label' => 'شركة ومؤسسة شريكة',
                'companies_prefix' => '+',
                'trainings_mode' => 'auto',
                'trainings_custom_value' => null,
                'trainings_visible' => true,
                'trainings_label' => 'برنامج تدريبي وتأهيلي',
                'trainings_prefix' => '+',
                'opportunities_mode' => 'auto',
                'opportunities_custom_value' => null,
                'opportunities_visible' => true,
                'opportunities_label' => 'فرصة عمل وترشيح',
                'opportunities_prefix' => '+',
            ]);
        }
        return $stat;
    }

    /**
     * Calculate and return live homepage stats settings and formatted card data
     */
    public static function getHomepageStats(): array
    {
        $setting = self::current();

        // Real counts from active tables
        $realGraduates = User::where('role', 'graduate')->count();
        $realCompanies = Company::count();
        $realTrainings = Training::count();
        $realOpportunities = JobOpportunity::count();

        // Compute display values according to media office configuration
        $graduatesValue = ($setting->graduates_mode === 'manual' && $setting->graduates_custom_value !== null)
            ? $setting->graduates_custom_value
            : $realGraduates;

        $companiesValue = ($setting->companies_mode === 'manual' && $setting->companies_custom_value !== null)
            ? $setting->companies_custom_value
            : $realCompanies;

        $trainingsValue = ($setting->trainings_mode === 'manual' && $setting->trainings_custom_value !== null)
            ? $setting->trainings_custom_value
            : $realTrainings;

        $opportunitiesValue = ($setting->opportunities_mode === 'manual' && $setting->opportunities_custom_value !== null)
            ? $setting->opportunities_custom_value
            : $realOpportunities;

        return [
            'is_ribbon_visible' => (bool) $setting->is_ribbon_visible,
            'global_mode' => $setting->global_mode,
            'cards' => [
                'graduates' => [
                    'key' => 'graduates',
                    'title' => 'الخريجون',
                    'label' => $setting->graduates_label ?: 'خريج مسجل ومعتمد',
                    'value' => $graduatesValue,
                    'real_count' => $realGraduates,
                    'mode' => $setting->graduates_mode,
                    'is_custom' => ($setting->graduates_mode === 'manual' && $setting->graduates_custom_value !== null),
                    'prefix' => $setting->graduates_prefix ?? '',
                    'is_visible' => (bool) $setting->graduates_visible,
                    'icon' => 'fas fa-user-graduate',
                    'color' => '#2563eb',
                    'bg_color' => 'rgba(37, 99, 235, 0.1)',
                ],
                'companies' => [
                    'key' => 'companies',
                    'title' => 'الشركات الشريكة',
                    'label' => $setting->companies_label ?: 'شركة ومؤسسة شريكة',
                    'value' => $companiesValue,
                    'real_count' => $realCompanies,
                    'mode' => $setting->companies_mode,
                    'is_custom' => ($setting->companies_mode === 'manual' && $setting->companies_custom_value !== null),
                    'prefix' => $setting->companies_prefix ?? '',
                    'is_visible' => (bool) $setting->companies_visible,
                    'icon' => 'fas fa-building',
                    'color' => '#10b981',
                    'bg_color' => 'rgba(16, 185, 129, 0.1)',
                ],
                'trainings' => [
                    'key' => 'trainings',
                    'title' => 'البرامج التدريبية',
                    'label' => $setting->trainings_label ?: 'برنامج تدريبي وتأهيلي',
                    'value' => $trainingsValue,
                    'real_count' => $realTrainings,
                    'mode' => $setting->trainings_mode,
                    'is_custom' => ($setting->trainings_mode === 'manual' && $setting->trainings_custom_value !== null),
                    'prefix' => $setting->trainings_prefix ?? '',
                    'is_visible' => (bool) $setting->trainings_visible,
                    'icon' => 'fas fa-chalkboard-teacher',
                    'color' => '#f59e0b',
                    'bg_color' => 'rgba(245, 158, 11, 0.1)',
                ],
                'opportunities' => [
                    'key' => 'opportunities',
                    'title' => 'فرص العمل والترشيح',
                    'label' => $setting->opportunities_label ?: 'فرصة عمل وترشيح',
                    'value' => $opportunitiesValue,
                    'real_count' => $realOpportunities,
                    'mode' => $setting->opportunities_mode,
                    'is_custom' => ($setting->opportunities_mode === 'manual' && $setting->opportunities_custom_value !== null),
                    'prefix' => $setting->opportunities_prefix ?? '',
                    'is_visible' => (bool) $setting->opportunities_visible,
                    'icon' => 'fas fa-briefcase',
                    'color' => '#8b5cf6',
                    'bg_color' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
        ];
    }
}
