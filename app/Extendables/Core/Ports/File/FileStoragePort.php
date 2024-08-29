<?php

namespace App\Extendables\Core\Ports\File;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface FileStoragePort
{
    /**
     * @param  string  $workDir
     */
    public function setWorkDir(string $workDir): void;

    /**
     * @return string
     */
    public function getDiskName(): string;

    /**
     * @return string
     */
    public function getWorkDir(): string;

    /**
     * Save an uploaded file as a given name.
     * If an extension is provided, append the extension to the file name
     *
     * @param  File|UploadedFile  $file
     * @param  string  $name
     * @param  string|null  $extension
     * @param  bool  $isRelativeToWorkDir
     * @return bool|string
     */
    public function putFileAs(
        File|UploadedFile $file,
        string $name,
        ?string $extension = null,
        bool $isRelativeToWorkDir = true
    ): bool|string;

    /**
     * @param  File|UploadedFile  $file
     * @param  bool  $isRelativeToWorkDir
     * @return bool|string
     */
    public function putFile(File|UploadedFile $file, bool $isRelativeToWorkDir = true): bool|string;

    /**
     * @param  string  $path
     * @param  bool  $isWorkDirPath
     * @return bool
     */
    public function delete(string $path, bool $isWorkDirPath = false): bool;

    /**
     * @param  string  $path
     * @param  int|null  $duration
     * @param  array  $options
     * @param  bool  $isWorkDirPath
     * @return string
     */
    public function makeTempUrlForPath(
        string $path,
        ?int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string;

    /**
     * @param  string  $path
     * @param  bool  $isWorkDirPath
     * @return string
     */
    public function makeUrlForPath(
        string $path,
        bool $isWorkDirPath = false
    ): string;

    /**
     * @param  string  $path
     * @param  bool  $isWorkDirPath
     * @return string
     */
    public function get(string $path, bool $isWorkDirPath = true): string;

    /**
     * Save a binary content as a given name.
     * If an extension is provided, append the extension to the file name
     *
     * @param  string  $file
     * @param  string  $name
     * @param  string|null  $extension
     * @param  bool  $isWorkDirPath
     * @return string|bool
     */
    public function putBinaryContentAs(
        string $file,
        string $name,
        ?string $extension = null,
        bool $isWorkDirPath = true
    ): string|bool;

    /**
     * @param  string  $path
     * @param  string|null  $name
     * @param  array  $headers
     * @param  bool  $isWorkDirPath
     * @return StreamedResponse
     */
    public function download(
        string $path,
        ?string $name = null,
        array $headers = [],
        bool $isWorkDirPath = false
    ): StreamedResponse;

    /**
     * Get the absolute path for a file from the root directory
     *
     * @param  string  $path
     * @param  bool  $isWorkDirPath
     * @return string
     */
    public function getFullPath(string $path, bool $isWorkDirPath = true): string;

    /**
     * Check whether a file exists in the storage at a given path.
     * If the path is rooted, check relatively with the root directory.
     * Else, treat the path as an absolute path.
     * If an extension is provided, append the extension to the file path
     *
     * @param  string  $filePath
     * @param  bool  $isWorkDirPath
     * @param  string|null  $fileExtension
     * @return bool
     */
    public function exists(string $filePath, bool $isWorkDirPath = true, ?string $fileExtension = null): bool;

    /**
     * Make a temporary download url for an asset
     */
    public function makeDownloadUrlForPath(
        string $path,
        string $filename,
        string $contentType = null,
        int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string;
}
