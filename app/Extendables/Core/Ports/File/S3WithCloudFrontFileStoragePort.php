<?php

namespace App\Extendables\Core\Ports\File;

use Aws\CloudFront\UrlSigner;

class S3WithCloudFrontFileStoragePort extends S3FileStoragePort
{
    /**
     * @param  UrlSigner  $urlSigner
     * @param  string  $workDir
     * @param  int  $defaultTempUrlDuration
     */
    public function __construct(
        private readonly UrlSigner $urlSigner,
        string $workDir = '',
        int $defaultTempUrlDuration = 30
    ) {
        parent::__construct($workDir, $defaultTempUrlDuration);
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

        return $this->urlSigner->getSignedUrl(
            $this->disk()->url($path),
            $duration ?: now()->addMinutes($this->defaultTempUrlDuration)->timestamp,
            $options
        );
    }

    public function makeDownloadUrlForPath(
        string $path,
        string $filename,
        string $contentType = null,
        int $duration = null,
        array $options = [],
        bool $isWorkDirPath = false
    ): string {
        $options = array_merge($options, $this->downloadResponseOptions($filename, $contentType));

        return parent::makeTempUrlForPath($path, $duration, $options, $isWorkDirPath);
    }
}
