<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DutyScheduleApiController extends Controller
{
    /**
     * Return duty schedules in a date range as JSON for cross-system consumption.
     *
     * GET /api/duty-schedules?start=2026-07-01&end=2026-07-31&token=xxx
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'token' => ['required', 'string'],
        ]);

        if ($request->input('token') !== config('services.noibo.api_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $events = DutySchedule::query()
            ->with(['creator:id,name', 'users:id,name'])
            ->where('is_private', false)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->whereNotNull('end_at')
                        ->where('start_at', '<=', $end)
                        ->where('end_at', '>=', $start);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->whereNull('end_at')
                        ->whereBetween('start_at', [$start, $end]);
                });
            })
            ->orderBy('start_at')
            ->get();

        $data = $events->map(function (DutySchedule $event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_date' => $event->start_at->format('Y-m-d'),
                'start_time' => $event->start_at->format('H:i:s'),
                'end_date' => $event->end_at ? $event->end_at->format('Y-m-d') : $event->start_at->format('Y-m-d'),
                'end_time' => $event->end_at ? $event->end_at->format('H:i:s') : null,
                'color' => $event->label_color,
                'creator_name' => $event->creator?->name ?? 'N/A',
                'participants' => $event->users->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ])->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data->toArray(),
        ]);
    }
}
