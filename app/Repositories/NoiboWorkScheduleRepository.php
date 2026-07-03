<?php

declare(strict_types=1);

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class NoiboWorkScheduleRepository
{
    private string $apiUrl;

    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('services.noibo.api_url'), '/');
        $this->apiToken = (string) config('services.noibo.api_token');
    }

    /**
     * Fetch work schedules from the noibo main system API.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEventsInRange(string $start, string $end): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->apiUrl}/api/work-schedules", [
                    'start' => date('Y-m-d', strtotime($start)),
                    'end' => date('Y-m-d', strtotime($end)),
                    'token' => $this->apiToken,
                ]);

            if (! $response->successful()) {
                Log::warning('Noibo API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $data = $response->json('data', []);

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            Log::warning('Noibo API connection error', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Format a noibo work schedule API item into a FullCalendar event.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function toCalendarEvent(array $item): array
    {
        $startAt = $this->buildDateTime($item['start_date'], $item['start_time'] ?? null);
        $endAt = $this->buildEndDateTime($item, $startAt);

        $creatorName = $item['creator_name'] ?? 'N/A';
        $participants = $item['participants'] ?? [];

        $participantsStr = '';
        if (! empty($participants)) {
            $names = array_column($participants, 'name');
            $participantsStr = ' (với '.implode(', ', $names).')';
        }

        $title = "{$creatorName}: {$item['title']}{$participantsStr}";

        return [
            'id' => 'noibo_'.$item['id'],
            'title' => $title,
            'raw_title' => $item['title'],
            'start' => $startAt->toIso8601String(),
            'end' => $endAt?->toIso8601String(),
            'description' => $item['description'] ?? null,
            'location' => null,
            'classNames' => ['bg-warning-subtle', 'text-warning', 'border-warning', 'p-1', 'fw-semibold'],
            'label_color' => 'noibo',
            'creator_name' => $creatorName,
            'participants' => $participants,
            'can_edit' => false,
            'can_delete' => false,
            'source' => 'noibo',
        ];
    }

    /**
     * Format a noibo work schedule API item for the day schedules modal.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function toDayScheduleItem(array $item): array
    {
        $startAt = $this->buildDateTime($item['start_date'], $item['start_time'] ?? null);
        $endAt = $this->buildEndDateTime($item, $startAt);

        return [
            'id' => 'noibo_'.$item['id'],
            'title' => $item['title'],
            'start_formatted' => $startAt->format('H:i d/m/Y'),
            'end_formatted' => $endAt?->format('H:i d/m/Y'),
            'description' => $item['description'] ?? null,
            'location' => null,
            'label_color' => 'noibo',
            'creator_name' => $item['creator_name'] ?? 'N/A',
            'participants' => $item['participants'] ?? [],
            'can_edit' => false,
            'can_delete' => false,
            'source' => 'noibo',
        ];
    }

    private function buildDateTime(string $date, ?string $time): Carbon
    {
        if ($time !== null && $time !== '') {
            return Carbon::parse("{$date} {$time}");
        }

        return Carbon::parse($date)->startOfDay();
    }

    private function buildEndDateTime(array $item, Carbon $startAt): ?Carbon
    {
        $endDate = $item['end_date'] ?? $item['start_date'];
        $endTime = $item['end_time'] ?? null;

        if ($endTime !== null && $endTime !== '') {
            return Carbon::parse("{$endDate} {$endTime}");
        }

        // Multi-day event: end at end of day
        if ($endDate !== $item['start_date']) {
            return Carbon::parse($endDate)->endOfDay();
        }

        // Single-day with start_time: +1 hour
        if (($item['start_time'] ?? null) !== null && $item['start_time'] !== '') {
            return $startAt->copy()->addHour();
        }

        // All-day event
        return Carbon::parse($endDate)->endOfDay();
    }
}
