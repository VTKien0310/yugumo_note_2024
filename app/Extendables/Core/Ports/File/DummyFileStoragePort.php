<?php

namespace App\Extendables\Core\Ports\File;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DummyFileStoragePort implements FileStoragePort
{
    private readonly string $diskName;

    private string $workDir;

    public function __construct()
    {
        $this->diskName = 's3';
        $this->workDir = 'testing';
    }

    public function setWorkDir(string $workDir): void
    {
        $this->workDir = $workDir;
    }

    public function getDiskName(): string
    {
        return $this->diskName;
    }

    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    /**
     * {@inheritDoc}
     */
    public function putFileAs(
        File|UploadedFile $file,
        string $name,
        string $extension = null,
        bool $isRelativeToWorkDir = true
    ): bool|string {
        $fileStoragePath = $isRelativeToWorkDir ? $this->workDir : '';

        if ($extension) {
            $name = "$name.$extension";
        }

        return $fileStoragePath.'/'.$name;
    }

    public function putFile(File|UploadedFile $file, bool $isRelativeToWorkDir = true): bool|string
    {
        $fileStoragePath = $isRelativeToWorkDir ? $this->workDir : '';

        return $fileStoragePath.'/'.$file->getFilename();
    }

    public function delete(string $path, bool $isWorkDirPath = false): bool
    {
        return true;
    }

    public function makeTempUrlForPath(
        string $path,
        int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        return $path;
    }

    public function makeUrlForPath(string $path, bool $isWorkDirPath = false): string
    {
        return $path;
    }

    public function get(string $path, bool $isWorkDirPath = true): string
    {
        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function putBinaryContentAs(
        string $file,
        string $name,
        string $extension = null,
        bool $isWorkDirPath = true
    ): string|bool {
        $path = $isWorkDirPath ? "{$this->getWorkDir()}/$name" : $name;

        if ($extension) {
            $path = "$path.$extension";
        }

        return $path;
    }

    public function download(
        string $path,
        string $name = null,
        array $headers = [],
        bool $isWorkDirPath = false
    ): StreamedResponse {
        return new StreamedResponse();
    }

    /**
     * {@inheritDoc}
     */
    public function getFullPath(string $path, bool $isWorkDirPath = true): string
    {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $filePath, bool $isWorkDirPath = true, string $fileExtension = null): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function makeDownloadUrlForPath(
        string $path,
        string $filename,
        string $contentType = null,
        int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        return $path;
    }
}
