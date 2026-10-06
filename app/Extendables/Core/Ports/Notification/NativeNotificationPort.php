<?php

namespace App\Extendables\Core\Ports\Notification;

use App\Models\User\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NativeNotificationPort implements NotificationPort
{
    /**
     * {@inheritDoc}
     */
    public function sendToUser(User|Collection|array $receivers, string $notification, ...$notificationArgs): void
    {
        Notification::send($receivers, new $notification(...$notificationArgs));
    }

    /**
     * {@inheritDoc}
     */
    public function sendToSlackWebhook(string $webhookUrl, string $notification, ...$notificationArgs): void
    {
        Notification::route('slack', $webhookUrl)->notify(new $notification(...$notificationArgs));
    }
}
