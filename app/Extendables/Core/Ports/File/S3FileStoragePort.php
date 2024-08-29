<?php

namespace App\Extendables\Core\Ports\File;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class S3FileStoragePort implements FileStoragePort
{
    /**
     * @var string
     */
    private readonly string $diskName;

    /**
     * @param  string  $workDir
     * @param  int  $defaultTempUrlDuration
     */
    public function __construct(
        protected string $workDir = '',
        protected int $defaultTempUrlDuration = 30
    ) {
        $this->diskName = 's3';
    }

    /**
     * @inheritDoc
     */
    public function setWorkDir(string $workDir): void
    {
        $this->workDir = $workDir;
    }

    /**
     * Get a new disk instance
     *
     * @return Filesystem|FilesystemAdapter
     */
    protected function disk(): Filesystem|FilesystemAdapter
    {
        return Storage::disk($this->diskName);
    }

    /**
     * @inheritDoc
     */
    public function getDiskName(): string
    {
        return $this->diskName;
    }

    /**
     * @inheritDoc
     */
    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    /**
     * @inheritDoc
     */
    public function putFileAs(
        File|UploadedFile $file,
        string $name,
        ?string $extension = null,
        bool $isRelativeToWorkDir = true
    ): bool|string {
        $fileStoragePath = $isRelativeToWorkDir ? $this->workDir : '';

        if ($extension) {
            $name = "$name.$extension";
        }

        return $this->disk()->putFileAs($fileStoragePath, $file, $name);
    }

    /**
     * @inheritDoc
     */
    public function putFile(File|UploadedFile $file, bool $isRelativeToWorkDir = true): bool|string
    {
        $fileStoragePath = $isRelativeToWorkDir ? $this->workDir : '';
        return $this->disk()->putFile($fileStoragePath, $file);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, bool $isWorkDirPath = false): bool
    {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->delete($path);
    }

    /**
     * @inheritDoc
     */
    public function makeTempUrlForPath(
        string $path,
        ?int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->temporaryUrl(
            $path,
            $duration ?: now()->addMinutes($this->defaultTempUrlDuration),
            $options
        );
    }

    /**
     * @inheritDoc
     */
    public function makeUrlForPath(string $path, bool $isWorkDirPath = false): string
    {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->url($path);
    }

    /**
     * @inheritDoc
     */
    public function get(string $path, bool $isWorkDirPath = true): string
    {
        $path = $isWorkDirPath ? "{$this->getWorkDir()}/$path" : $path;
        return $this->disk()->get($path);
    }

    /**
     * @inheritDoc
     */
    public function putBinaryContentAs(
        string $file,
        string $name,
        ?string $extension = null,
        bool $isWorkDirPath = true
    ): string|bool {
        $path = $isWorkDirPath ? "{$this->getWorkDir()}/$name" : $name;

        if ($extension) {
            $path = "$path.$extension";
        }

        if ($this->disk()->put($path, $file)) {
            return $path;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function download(
        string $path,
        ?string $name = null,
        array $headers = [],
        bool $isWorkDirPath = false
    ): StreamedResponse {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->download($path, $name, $headers);
    }

    /**
     * @inheritDoc
     */
    public function getFullPath(string $path, bool $isWorkDirPath = true): string
    {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->path($path);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $filePath, bool $isWorkDirPath = true, ?string $fileExtension = null): bool
    {
        if ($fileExtension) {
            $filePath = "$filePath.$fileExtension";
        }

        if ($isWorkDirPath) {
            $filePath = $this->workDir.'/'.$filePath;
        }

        return $this->disk()->exists($filePath);
    }

    /**
     * @inheritDoc
     */
    public function makeDownloadUrlForPath(
        string $path,
        string $filename,
        string $contentType = null,
        int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        $options = array_merge($options, $this->downloadResponseOptions($filename, $contentType));

        return $this->makeTempUrlForPath(
            path: $path,
            duration: $duration,
            options: $options,
            isWorkDirPath: $isWorkDirPath
        );
    }

    protected function downloadResponseOptions(string $filename, string $contentType = null): array
    {
        $responseOptions = [
            'ResponseContentDisposition' => "filename=$filename",
        ];

        if ($contentType) {
            $responseOptions['ResponseContentType'] = $contentType;
        }

        return $responseOptions;
    }
}
