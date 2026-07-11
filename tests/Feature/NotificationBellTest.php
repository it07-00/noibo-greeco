<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Notifications\NotificationBell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

final class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    public function test_poll_dispatches_new_notification_to_browser_only_once(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 0)
            ->assertSet('lastNotificationId', null);

        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'test',
            'data' => [
                'title' => 'Lịch công tác mới',
                'message' => 'Bạn vừa được phân công.',
                'url' => '/duty-schedules',
            ],
        ]);

        $component->call('loadUnreadCount')
            ->assertSet('unreadCount', 1)
            ->assertSet('lastNotificationId', $notification->id)
            ->assertDispatched('browser-notification', fn (string $event, array $payload): bool =>
                $payload[0]['title'] === 'Lịch công tác mới'
                && $payload[0]['url'] === '/duty-schedules');

        $component->call('loadUnreadCount')
            ->assertNotDispatched('browser-notification');
    }

    public function test_notification_dropdown_offers_browser_permission_action(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertSee('Bật thông báo trình duyệt');
    }
}
