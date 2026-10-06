<?php

namespace App\Extendables\Core\Ports\Mail;

use App\Models\User\User;
use Illuminate\Support\Collection;

interface MailPort
{
    /**
     * Send an email
     */
    public function send(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void;

    /**
     * Send a queued email
     */
    public function queueSend(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void;
}
