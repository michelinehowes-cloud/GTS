<?php

namespace App\Services;

use App\Models\JobFair;
use App\Models\JobFairRegistration;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class JobFairAutoRegistrationService
{
    /**
     * Ensure required tables and approval columns exist before syncing.
     */
    public static function hasRequiredTables(): bool
    {
        try {
            return Schema::hasTable('users')
                && Schema::hasTable('job_fairs')
                && Schema::hasTable('job_fair_registrations')
                && Schema::hasColumn('users', 'is_approved')
                && Schema::hasColumn('users', 'is_active');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Only approved and active graduate accounts are eligible for auto-registration.
     */
    public static function isGraduateEligible(User $user): bool
    {
        if (!$user->id || $user->role !== 'graduate') {
            return false;
        }

        if ((bool) $user->is_approved !== true) {
            return false;
        }

        // DB defaults `is_active` to true; a newly created model before refresh() may still hold null.
        return is_null($user->is_active) || (bool) $user->is_active === true;
    }

    /**
     * Published or ongoing fairs that have not ended yet receive auto-registered graduates,
     * regardless of `max_graduates`.
     */
    public static function isFairEligible(JobFair $fair): bool
    {
        if (!$fair->id) {
            return false;
        }

        if ($fair->status === 'ongoing') {
            return true;
        }

        return $fair->status === 'published' && !$fair->is_ended;
    }

    /**
     * Get all currently eligible job fairs.
     */
    public static function eligibleFairs(): Collection
    {
        if (!static::hasRequiredTables()) {
            return collect();
        }

        return JobFair::query()
            ->whereIn('status', ['published', 'ongoing'])
            ->orderBy('id')
            ->get()
            ->filter(fn (JobFair $fair) => static::isFairEligible($fair))
            ->values();
    }

    /**
     * Ensure one eligible graduate has a registration record and fair badge for one eligible fair.
     */
    public static function ensureGraduateRegisteredForFair(User $graduate, JobFair $fair): ?JobFairRegistration
    {
        if (!static::hasRequiredTables() || !static::isGraduateEligible($graduate) || !static::isFairEligible($fair)) {
            return null;
        }

        $existing = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('user_id', $graduate->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $nextSeq = static::computeNextSequenceForFair($fair->id);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $regNumber = $attempt === 0
                ? static::formatRegistrationNumber($fair->id, $nextSeq)
                : static::formatRegistrationNumber($fair->id, $nextSeq + $attempt) . '-' . Str::upper(Str::random(3));

            while (JobFairRegistration::where('registration_number', $regNumber)->exists()) {
                $nextSeq++;
                $regNumber = static::formatRegistrationNumber($fair->id, $nextSeq);
            }

            $qrCode = $attempt === 0
                ? JobFairRegistration::generateQrCode($fair->id, $graduate->id)
                : JobFairRegistration::generateQrCode($fair->id, $graduate->id) . '-' . Str::upper(Str::random(4));

            while (JobFairRegistration::where('qr_code', $qrCode)->exists()) {
                $qrCode = JobFairRegistration::generateQrCode($fair->id, $graduate->id) . '-' . Str::upper(Str::random(4));
            }

            try {
                return JobFairRegistration::create([
                    'job_fair_id' => $fair->id,
                    'user_id' => $graduate->id,
                    'registration_number' => $regNumber,
                    'qr_code' => $qrCode,
                    'status' => 'registered',
                    'attended' => false,
                ]);
            } catch (QueryException $e) {
                $concurrent = JobFairRegistration::where('job_fair_id', $fair->id)
                    ->where('user_id', $graduate->id)
                    ->first();

                if ($concurrent) {
                    return $concurrent;
                }

                $nextSeq++;
            }
        }

        return null;
    }

    /**
     * Register an approved, active graduate across all active eligible fairs.
     */
    public static function syncGraduate(User $graduate): int
    {
        if (!static::hasRequiredTables() || !static::isGraduateEligible($graduate)) {
            return 0;
        }

        $fairs = static::eligibleFairs();
        if ($fairs->isEmpty()) {
            return 0;
        }

        $existingFairIds = JobFairRegistration::where('user_id', $graduate->id)
            ->whereIn('job_fair_id', $fairs->pluck('id'))
            ->pluck('job_fair_id')
            ->all();

        $createdCount = 0;

        foreach ($fairs as $fair) {
            if (in_array($fair->id, $existingFairIds, true)) {
                continue;
            }

            $registration = static::ensureGraduateRegisteredForFair($graduate, $fair);
            if ($registration && $registration->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        return $createdCount;
    }

    /**
     * Bulk-register all missing eligible graduates for a single eligible fair.
     */
    public static function syncFair(JobFair $fair): int
    {
        if (!static::hasRequiredTables() || !static::isFairEligible($fair)) {
            return 0;
        }

        $graduateIds = User::query()
            ->where('role', 'graduate')
            ->where('is_approved', true)
            ->where(function ($query) {
                $query->where('is_active', true)->orWhereNull('is_active');
            })
            ->whereNotIn('id', function ($query) use ($fair) {
                $query->select('user_id')
                    ->from('job_fair_registrations')
                    ->where('job_fair_id', $fair->id);
            })
            ->orderBy('id')
            ->pluck('id');

        if ($graduateIds->isEmpty()) {
            return 0;
        }

        $nextSeq = static::computeNextSequenceForFair($fair->id);
        $now = now();
        $rows = [];

        foreach ($graduateIds as $graduateId) {
            $graduateId = (int) $graduateId;
            $rows[] = [
                'job_fair_id' => $fair->id,
                'user_id' => $graduateId,
                'registration_number' => static::formatRegistrationNumber($fair->id, $nextSeq),
                'qr_code' => JobFairRegistration::generateQrCode($fair->id, $graduateId),
                'status' => 'registered',
                'attended' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $nextSeq++;
        }

        $createdCount = 0;

        foreach (array_chunk($rows, 200) as $chunk) {
            try {
                JobFairRegistration::insert($chunk);
                $createdCount += count($chunk);
            } catch (QueryException $e) {
                $createdCount += static::insertChunkIndividually($fair, $chunk);
            }
        }

        return $createdCount;
    }

    /**
     * Sync missing eligible graduates across all eligible fairs (or a specific fair ID).
     */
    public static function syncAllActiveFairs(?int $fairId = null): array
    {
        if (!static::hasRequiredTables()) {
            return [
                'fairs_processed' => 0,
                'registrations_created' => 0,
            ];
        }

        $fairs = $fairId !== null
            ? JobFair::whereKey($fairId)->get()->filter(fn (JobFair $fair) => static::isFairEligible($fair))->values()
            : static::eligibleFairs();

        $createdCount = 0;

        foreach ($fairs as $fair) {
            $createdCount += static::syncFair($fair);
        }

        return [
            'fairs_processed' => $fairs->count(),
            'registrations_created' => $createdCount,
        ];
    }

    private static function computeNextSequenceForFair(int $fairId): int
    {
        $prefix = 'JF' . $fairId . '-';
        $existingNumbers = JobFairRegistration::where('job_fair_id', $fairId)
            ->pluck('registration_number');

        $maxSeq = $existingNumbers->count();

        foreach ($existingNumbers as $number) {
            if (is_string($number) && str_starts_with($number, $prefix)) {
                $suffix = substr($number, strlen($prefix));
                if (ctype_digit($suffix)) {
                    $maxSeq = max($maxSeq, (int) $suffix);
                }
            }
        }

        return $maxSeq + 1;
    }

    private static function formatRegistrationNumber(int $fairId, int $sequence): string
    {
        return 'JF' . $fairId . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private static function insertChunkIndividually(JobFair $fair, array $chunk): int
    {
        $userIds = array_column($chunk, 'user_id');
        $graduates = User::whereIn('id', $userIds)->orderBy('id')->get();
        $created = 0;

        foreach ($graduates as $graduate) {
            $registration = static::ensureGraduateRegisteredForFair($graduate, $fair);
            if ($registration && $registration->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }
}
