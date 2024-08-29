<?php

namespace App\Extendables\Core\Ports\Notification;

use App\Models\User\User;
use Illuminate\Support\Collection;

interface NotificationPort
{
    /**
     * Send notification to user(s)
     */
    public function sendToUser(User|Collection|array $receivers, string $notification, ...$notificationArgs): void;

    /**
     * Send notification to slack webhook URL
     */
    public function sendToSlackWebhook(string $webhookUrl, string $notification, ...$notificationArgs): void;
}
