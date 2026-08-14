<?php

namespace App\Services;

use App\Models\Holiday;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EkaldikHolidayService
{
    private string $baseUrl;
    private ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ekaldik.base_url', ''), '/');
        $this->apiKey = config('services.ekaldik.api_key');
    }

    /**
     * Sync holidays from E-Kaldik API to local database.
     *
     * @return array Stats: added, updated, removed, errors
     */
    public function syncFromEkaldik(): array
    {
        $stats = ['added' => 0, 'updated' => 0, 'removed' => 0, 'errors' => []];

        if (empty($this->baseUrl)) {
            $stats['errors'][] = 'EKALDIK_BASE_URL belum di-set di .env';
            return $stats;
        }

        try {
            $response = Http::timeout(10)
                ->when($this->apiKey, function ($http) {
                    return $http->withHeaders(['X-API-Key' => $this->apiKey]);
                })
                ->get("{$this->baseUrl}/api/holidays");

            if (!$response->successful()) {
                $stats['errors'][] = "API response: {$response->status()}";
                return $stats;
            }

            $data = $response->json();

            if (!($data['success'] ?? false)) {
                $stats['errors'][] = $data['message'] ?? 'Unknown API error';
                return $stats;
            }

            $holidays = $data['data'] ?? [];
            $syncedIds = [];

            foreach ($holidays as $holiday) {
                try {
                    $record = Holiday::updateOrCreate(
                        ['ekaldik_activity_id' => $holiday['id']],
                        [
                            'name' => $holiday['name'],
                            'start_date' => $holiday['start_date'],
                            'end_date' => $holiday['end_date'],
                            'type' => $holiday['type'] ?? null,
                            'description' => $holiday['description'] ?? null,
                            'source' => 'ekaldik',
                            'is_active' => true,
                            'synced_at' => now(),
                        ]
                    );

                    $syncedIds[] = $record->id;

                    if ($record->wasRecentlyCreated) {
                        $stats['added']++;
                    } else {
                        $stats['updated']++;
                    }
                } catch (\Exception $e) {
                    $stats['errors'][] = "Failed to sync '{$holiday['name']}': {$e->getMessage()}";
                }
            }

            // Remove ekaldik holidays that no longer exist in API
            $removed = Holiday::fromEkaldik()
                ->whereNotIn('id', $syncedIds)
                ->update(['is_active' => false]);

            $stats['removed'] = $removed;

            Log::info('Holiday sync completed', $stats);

        } catch (\Exception $e) {
            $stats['errors'][] = "Connection failed: {$e->getMessage()}";
            Log::error('Holiday sync failed', ['error' => $e->getMessage()]);
        }

        return $stats;
    }

    /**
     * Check if a date is a holiday (from local DB).
     */
    public function isHoliday(?string $date = null): bool
    {
        return Holiday::isHoliday($date);
    }

    /**
     * Get holiday info for a date (from local DB).
     */
    public function getHolidayInfo(?string $date = null): ?Holiday
    {
        return Holiday::getForDate($date);
    }
}
