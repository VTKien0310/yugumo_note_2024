<?php

namespace App\Extendables\Core\Ports\Mail;

use App\Features\User\Models\User;
use Illuminate\Support\Collection;

interface MailPort
{
    /**
     * Send an email
     *
     * @param  mixed  ...$mailableArgs
     */
    public function send(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void;

    /**
     * Send a queued email
     *
     * @param  mixed  ...$mailableArgs
     */
    public function queueSend(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void;
}
