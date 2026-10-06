<?php

namespace App\Extendables\Core\Ports\RemoteLog;

use Stringable;

interface RemoteLogPort
{
    public function error(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function debug(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function info(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function critical(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function alert(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function emergency(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function notice(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function warning(
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;

    public function log(
        LogLevelEnum $logLevel,
        string|Stringable $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void;
}
