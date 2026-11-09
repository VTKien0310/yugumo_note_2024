<?php

namespace App\Extendables\Core\Utils\Base64Handling;

use Illuminate\Http\UploadedFile;

class Base64ToUploadedFileConverter
{
    public function handle(string $base64, string $fileName): UploadedFile
    {
        // trim the data header from the encoded string
        if (str_contains($base64, ';base64,')) {
            [, $base64] = explode(';base64,', $base64);
        }

        // create a temporary file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'php');
        $tempFile = fopen($tempFilePath, 'wb');

        if (! $tempFile) {
            throw new \RuntimeException('Could not create temporary file');
        }

        try {
            // decode base64 in chunks
            $base64Filter = stream_filter_append($tempFile, 'convert.base64-decode', STREAM_FILTER_WRITE);

            if (! $base64Filter) {
                throw new \RuntimeException('Could not create base64 decode filter');
            }

            // write the base64 string in chunks
            $chunkSize = 65536;
            $offset = 0;
            $length = strlen($base64);

            while ($offset < $length) {
                $chunk = substr($base64, $offset, $chunkSize);
                if (fwrite($tempFile, $chunk) === false) {
                    throw new \RuntimeException('Failed to write chunk to temporary file');
                }
                $offset += $chunkSize;
            }

            stream_filter_remove($base64Filter);
        } finally {
            fclose($tempFile);
        }

        // create a new UploadedFile instance with 'test: true' to indicate a locally created file instead of an HTTP uploaded one
        return new UploadedFile($tempFilePath, $fileName, null, null, true);
    }
}
