<?php

namespace App\Extendables\Core\Ports\RemoteLog;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Stringable;

class SlackLogPort implements RemoteLogPort
{
    protected function driver(): LoggerInterface
    {
        return Log::channel('slack');
    }

    public function error(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->error(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function debug(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->debug(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function info(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->info(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function critical(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->critical(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function alert(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->alert(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function emergency(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->emergency(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function notice(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->notice(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function warning(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->driver()->warning(
            $message,
            array_merge(
                $this->getExtraContextData($environmentInfoKey),
                $context
            )
        );
    }

    public function log(
        LogLevelEnum $logLevel,
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
        $this->{$logLevel->value}(
            $message,
            $context,
            $environmentInfoKey
        );
    }

    private function getExtraContextData(string $environmentInfoKey = 'environment'): array
    {
        return [
            $environmentInfoKey => app()->environment(),
        ];
    }
}
