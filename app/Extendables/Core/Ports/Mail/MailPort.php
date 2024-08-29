<?php

namespace App\Extendables\Core\Ports\Mail;

use App\Models\User\User;
use Illuminate\Support\Collection;

interface MailPort
{
    /**
     * Send an email
     *
     * @param string|\App\Models\User\User|\Illuminate\Support\Collection $receivers
     * @param string $mailable
     * @param ...$mailableArgs
     */
    public function send(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void;

    /**
     * Send a queued email
     *
     * @param string|\App\Models\User\User|\Illuminate\Support\Collection $receivers
     * @param string $mailable
     * @param ...$mailableArgs
     */
    public function queueSend(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void;
}