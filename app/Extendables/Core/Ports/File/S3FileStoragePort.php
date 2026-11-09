<?php

namespace App\Extendables\Core\Ports\File;

use DateTimeInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class S3FileStoragePort implements FileStoragePort
{
    private readonly string $diskName;

    public function __construct(
        protected string $workDir = '',
        protected int $defaultTempUrlDuration = 30
    ) {
        $this->diskName = 's3';
    }

    public function setWorkDir(string $workDir): void
    {
        $this->workDir = $workDir;
    }

    /**
     * Get a new disk instance
     */
    protected function disk(): Filesystem|FilesystemAdapter
    {
        return Storage::disk($this->diskName);
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
        ?string $extension = null,
        bool $isRelativeToWorkDir = true
    ): bool|string {
        $fileStoragePath = $isRelativeToWorkDir ? $this->workDir : '';

        if ($extension) {
            $name = "$name.$extension";
        }

        return $this->disk()->putFileAs($fileStoragePath, $file, $name);
    }

    public function putFile(File|UploadedFile $file, bool $isRelativeToWorkDir = true): bool|string
    {
        $fileStoragePath = $isRelativeToWorkDir ? $this->workDir : '';

        return $this->disk()->putFile($fileStoragePath, $file);
    }

    public function delete(string $path, bool $isWorkDirPath = false): bool
    {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->delete($path);
    }

    /**
     * {@inheritDoc}
     */
    public function makeTempUrlForPath(
        string $path,
        int|DateTimeInterface|null $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->temporaryUrl(
            $path,
            is_int($duration)
                ? now()->addMinutes($duration)
                : ($duration ?? now()->addMinutes($this->defaultTempUrlDuration)),
            $options
        );
    }

    public function makeUrlForPath(string $path, bool $isWorkDirPath = false): string
    {
        if ($isWorkDirPath) {
            $path = "$this->workDir/$path";
        }

        return $this->disk()->url($path);
    }

    public function get(string $path, bool $isWorkDirPath = true): string
    {
        $path = $isWorkDirPath ? "{$this->getWorkDir()}/$path" : $path;

        return $this->disk()->get($path) ?? '';
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function makeDownloadUrlForPath(
        string $path,
        string $filename,
        ?string $contentType = null,
        int|DateTimeInterface|null $duration = null,
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

    /**
     * @return string[]
     */
    protected function downloadResponseOptions(string $filename, ?string $contentType = null): array
    {
        $responseOptions = [
            // Encoding the filename with rawurlencode ensures the filename is always valid with ISO-8859-1.
            'ResponseContentDisposition' => 'filename='.rawurlencode($filename),
        ];

        if ($contentType) {
            $responseOptions['ResponseContentType'] = $contentType;
        }

        return $responseOptions;
    }

    /**
     * {@inheritDoc}
     */
    public function saveFileToLocal(string $path, string $localName, ?string $localDirectory = null, bool $isWorkDirPath = false): string
    {
        $localDirectory = $localDirectory ?? sys_get_temp_dir();
        $localPath = $localDirectory.'/'.$localName;

        $fileContent = $this->get($path, $isWorkDirPath);
        file_put_contents($localPath, $fileContent);

        return $localPath;
    }

    /**
     * {@inheritDoc}
     */
    public function streamFileToLocal(string $path, string $localName, ?string $localDirectory = null, bool $isWorkDirPath = false): string
    {
        $localDirectory = $localDirectory ?? sys_get_temp_dir();
        $localPath = $localDirectory.'/'.$localName;

        $resolvedPath = $isWorkDirPath ? "{$this->getWorkDir()}/$path" : $path;

        $remoteStream = $this->disk()->readStream($resolvedPath);

        if ($remoteStream === null) {
            throw new \RuntimeException("Unable to open remote stream for file: {$resolvedPath}");
        }

        $localStream = fopen($localPath, 'w');

        if ($localStream === false) {
            fclose($remoteStream);
            throw new \RuntimeException("Unable to open local file for writing: {$localPath}");
        }

        try {
            stream_copy_to_stream($remoteStream, $localStream);
        } finally {
            fclose($remoteStream);
            fclose($localStream);
        }

        return $localPath;
    }
}
