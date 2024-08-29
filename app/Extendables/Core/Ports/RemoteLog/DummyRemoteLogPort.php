<?php

namespace App\Extendables\Core\Ports\RemoteLog;

use Stringable;

class DummyRemoteLogPort implements RemoteLogPort
{
    public function error(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function debug(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function info(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function critical(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function alert(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function emergency(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function notice(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function warning(
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }

    public function log(
        LogLevelEnum $logLevel,
        Stringable|string $message,
        array $context = [],
        string $environmentInfoKey = 'environment'
    ): void {
    }
}
