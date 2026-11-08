<?php

namespace Tests\Fakes;

use App\Extendables\Core\Ports\File\DummyFileStoragePort;
use RuntimeException;

/**
 * A Dummy file storage that keeps what is written, so a test can read an object back or tamper with it.
 */
class InMemoryFileStoragePort extends DummyFileStoragePort
{
    /** @var array<array-key, string> */
    public array $written = [];

    public function putBinaryContentAs(string $file, string $name, ?string $extension = null, bool $isWorkDirPath = true): string|bool
    {
        $path = parent::putBinaryContentAs($file, $name, $extension, $isWorkDirPath);
        $this->written[$path] = $file;

        return $path;
    }

    public function get(string $path, bool $isWorkDirPath = true): string
    {
        return $this->written[$path] ?? throw new RuntimeException("No object at {$path}.");
    }
}
