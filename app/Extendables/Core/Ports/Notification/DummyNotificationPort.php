<?php

namespace App\Extendables\Core\Ports\Notification;

use App\Features\User\Models\User;
use Illuminate\Support\Collection;

class DummyNotificationPort implements NotificationPort
{
    /**
     * {@inheritDoc}
     */
    public function sendToUser(User|array|Collection $receivers, string $notification, ...$notificationArgs): void {}

    /**
     * {@inheritDoc}
     */
    public function sendToSlackWebhook(string $webhookUrl, string $notification, ...$notificationArgs): void {}

    /**
     * {@inheritDoc}
     */
    public function sendToEmail(array|string $email, string $notification, ...$notificationArgs): void {}
}
