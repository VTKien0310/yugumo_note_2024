<?php

namespace App\Extendables\Core\Ports\File;

use Aws\CloudFront\UrlSigner;
use DateTimeInterface;

class S3WithCloudFrontFileStoragePort extends S3FileStoragePort
{
    public function __construct(
        private readonly UrlSigner $urlSigner,
        string $workDir = '',
        int $defaultTempUrlDuration = 30
    ) {
        parent::__construct($workDir, $defaultTempUrlDuration);
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

        if (is_int($duration)) {
            $duration = intval(now()->addMinutes($duration)->timestamp);
        }

        if (is_null($duration)) {
            $duration = intval(now()->addMinutes($this->defaultTempUrlDuration)->timestamp);
        }

        if ($duration instanceof DateTimeInterface) {
            $duration = intval($duration->getTimestamp());
        }

        return $this->urlSigner->getSignedUrl(
            $this->disk()->url($path),
            $duration,
            $options
        );
    }

    public function makeDownloadUrlForPath(
        string $path,
        string $filename,
        ?string $contentType = null,
        int|DateTimeInterface|null $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        $options = array_merge($options, $this->downloadResponseOptions($filename, $contentType));

        return parent::makeTempUrlForPath($path, $duration, $options, $isWorkDirPath);
    }
}
