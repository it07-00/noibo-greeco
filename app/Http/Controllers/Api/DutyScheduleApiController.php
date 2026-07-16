<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySchedule;
use App\Models\User;
use App\Notifications\DutyScheduleAssigned;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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
                'id'           => $event->id,
                'title'        => $event->title,
                'description'  => $event->description,
                'start_date'   => $event->start_at->format('Y-m-d'),
                'start_time'   => $event->start_at->format('H:i:s'),
                'end_date'     => $event->end_at ? $event->end_at->format('Y-m-d') : $event->start_at->format('Y-m-d'),
                'end_time'     => $event->end_at ? $event->end_at->format('H:i:s') : null,
                'color'        => $event->label_color,
                'creator_name' => $event->creator?->name ?? 'N/A',
                'participants' => collect($event->combined_participants)->map(fn ($p) => [
                    'id'   => $p['id'],
                    'name' => $p['name'],
                ])->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data->toArray(),
        ]);
    }

    /**
     * Return all active users as JSON for cross-system participant selection.
     *
     * GET /api/users?token=xxx
     */
    public function users(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        if ($request->input('token') !== config('services.noibo.api_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $users = User::query()
            ->with('department')
            ->whereNull('locked_at')
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'department' => $u->department?->name ?? 'Nhân viên',
            ]);

        return response()->json([
            'success' => true,
            'data'    => $users->toArray(),
        ]);
    }

    /**
     * Receive a cross-system notification request from Bảo Châu.
     * Bảo Châu calls this when it adds Greeco users to a work schedule.
     *
     * POST /api/notify
     * Body (JSON): { token, user_ids[], event_title, creator_name, action, event_date }
     */
    public function notify(Request $request): JsonResponse
    {
        $request->validate([
            'token'        => ['required', 'string'],
            'user_ids'     => ['required', 'array'],
            'user_ids.*'   => ['integer'],
            'event_title'  => ['required', 'string'],
            'creator_name' => ['required', 'string'],
            'action'       => ['nullable', 'string', 'in:added,updated,deleted'],
        ]);

        if ($request->input('token') !== config('services.noibo.api_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $users = User::whereIn('id', $request->input('user_ids', []))
            ->whereNull('locked_at')
            ->get();

        if ($users->isEmpty()) {
            return response()->json(['success' => true, 'notified' => 0]);
        }

        // Build a stub DutySchedule for the notification (not persisted, used for message only)
        $stub = new DutySchedule();
        $stub->title = $request->input('event_title');

        $creatorName = $request->input('creator_name');

        Notification::send($users, new DutyScheduleAssigned($stub, $creatorName));

        return response()->json(['success' => true, 'notified' => $users->count()]);
    }
}
