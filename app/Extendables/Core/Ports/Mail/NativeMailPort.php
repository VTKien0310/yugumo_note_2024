<?php

namespace App\Extendables\Core\Ports\Mail;

use App\Models\User\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NativeMailPort implements MailPort
{
    /**
     * @inheritDoc
     */
    public function send(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void
    {
        Mail::to($receivers)->send(new $mailable(...$mailableArgs));
    }

    /**
     * @inheritDoc
     */
    public function queueSend(string|User|Collection $receivers, string $mailable, ...$mailableArgs): void
    {
        Mail::to($receivers)->queue(new $mailable(...$mailableArgs));
    }
}