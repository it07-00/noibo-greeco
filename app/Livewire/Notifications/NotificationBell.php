<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public ?string $lastNotificationId = null;

    public function mount(): void
    {
        $this->refreshUnreadState(false);
    }

    public function loadUnreadCount(): void
    {
        $this->refreshUnreadState(true);
    }

    public function markAsRead(string $id): void
    {
        $notification = Auth::user()?->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $this->refreshUnreadState(false);
        }

        // Redirect to the notification URL
        $url = $notification?->data['url'] ?? null;
        if ($url) {
            $this->redirect($url);
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();
        $this->unreadCount = 0;
    }

    public function deleteNotification(string $id): void
    {
        Auth::user()?->notifications()->where('id', $id)->delete();
        $this->refreshUnreadState(false);
    }

    public function render(): View
    {
        $notifications = Auth::user()
            ?->notifications()
            ->latest()
            ->take(20)
            ->get() ?? collect();

        return view('livewire.notifications.notification-bell', [
            'notifications' => $notifications,
        ]);
    }

    private function refreshUnreadState(bool $announceNew): void
    {
        $user = Auth::user();
        $this->unreadCount = $user?->unreadNotifications()->count() ?? 0;
        $latest = $user?->unreadNotifications()->latest()->first();

        if ($announceNew && $latest && $latest->id !== $this->lastNotificationId) {
            $this->dispatch('browser-notification', [
                'title' => $latest->data['title'] ?? 'Thông báo mới',
                'message' => $latest->data['message'] ?? '',
                'url' => $latest->data['url'] ?? null,
            ]);
        }

        $this->lastNotificationId = $latest?->id;
    }
}
