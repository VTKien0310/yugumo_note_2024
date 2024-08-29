<?php

namespace App\Extendables\Core\Utils\Base64Handling;

use Illuminate\Http\UploadedFile;

class Base64ToUploadedFileConverter
{
    /**
     * @param  string  $base64
     * @param  string  $fileName
     * @return UploadedFile
     */
    function handle(string $base64, string $fileName): UploadedFile
    {
        // trim the data header from the encoded string
        if (str_contains($base64, ';base64')) {
            [, $base64] = explode(';', $base64);
            [, $base64] = explode(',', $base64);
        }

        // save the decoded binary to a temp file
        $binary = base64_decode($base64);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tempFilePath, $binary);

        // create a new UploadedFile instance with 'test: true' for locally created file
        return new UploadedFile($tempFilePath, $fileName, test: true);
    }
}
