<?php

namespace App\Extendables\Core\Utils\Base64Handling;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ConvertRequestBase64ToUploadedFileAction
{
    protected string $jsonBase64NameKey = 'name';

    protected string $jsonBase64ContentKey = 'content';

    protected string $jsonBase64PositionKey = 'position';

    /**
     * @var array<string, UploadedFile|null|array<int, UploadedFile|null|array{file: ?UploadedFile, position: string|int}>>
     */
    protected array $convertedFiles = [];

    /**
     * Handles the conversion of base64-encoded parameters and arrays in the request to uploaded files.
     *
     * @param  string[]  $base64Param
     * @param  string[]  $base64ArrayParam
     * @return array<string, UploadedFile|null|array<int, UploadedFile|null|array{file: ?UploadedFile, position: string|int}>>
     */
    public function handle(Request $request, array $base64Param = [], array $base64ArrayParam = []): array
    {
        foreach ($base64Param as $base64ParamName) {
            $this->convertBase64RequestParamToUploadedFile($request, $base64ParamName);
        }

        foreach ($base64ArrayParam as $base64ArrayParamName) {
            $this->convertBase64ArrayRequestParamToUploadedFile($request, $base64ArrayParamName);
        }

        return $this->convertedFiles;
    }

    protected function convertBase64RequestParamToUploadedFile(Request $request, string $paramName): void
    {
        $paramValue = $request->input($paramName);

        if (empty($paramValue)) {
            return;
        }

        if (! $this->isValidParamValue($paramValue)) {
            $this->saveConvertedFile($paramName, null);

            return;
        }

        if ($this->shouldTreatAsNullParamValue($paramValue)) {
            $this->saveConvertedFile($paramName, null);

            return;
        }

        $base64Converter = new Base64ToUploadedFileConverter;
        $convertedParamValue = $base64Converter->handle(
            $this->getBase64ContentFromArray($paramValue),
            $this->getBase64NameFromArray($paramValue)
        );
        $this->saveConvertedFile($paramName, $convertedParamValue);
    }

    protected function isValidParamValue(mixed $paramValue): bool
    {
        return is_array($paramValue)
            && array_key_exists($this->jsonBase64ContentKey, $paramValue)
            && (is_string($paramValue[$this->jsonBase64ContentKey]) || is_null($paramValue[$this->jsonBase64ContentKey]))
            && array_key_exists($this->jsonBase64NameKey, $paramValue)
            && (is_string($paramValue[$this->jsonBase64NameKey]) || is_null($paramValue[$this->jsonBase64NameKey]));
    }

    protected function shouldTreatAsNullParamValue(mixed $paramValue): bool
    {
        return $this->isValidParamValue($paramValue)
            && (
                empty($paramValue[$this->jsonBase64ContentKey])
                || empty($paramValue[$this->jsonBase64NameKey])
            );
    }

    /**
     * @param  array<string, string>  $arr
     */
    protected function getBase64ContentFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64ContentKey];
    }

    /**
     * @param  array<string, string>  $arr
     */
    protected function getBase64NameFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64NameKey];
    }

    /**
     * @param  UploadedFile|null|array<int, UploadedFile|null|array{file: ?UploadedFile, position: string|int}>  $convertedFile
     */
    protected function saveConvertedFile(string $paramName, array|UploadedFile|null $convertedFile): void
    {
        $this->convertedFiles[$paramName] = $convertedFile;
    }

    protected function convertBase64ArrayRequestParamToUploadedFile(Request $request, string $paramName): void
    {
        $paramValue = $request->input($paramName);

        if (empty($paramValue)) {
            return;
        }

        if (! is_array($paramValue)) {
            $this->saveConvertedFile($paramName, null);

            return;
        }

        $convertedParamValue = $this->convertMultiBase64ToUploadedFile($paramValue);
        $this->saveConvertedFile($paramName, $convertedParamValue);
    }

    /**
     * @param  array<int, array<string, string>>  $base64Arr
     * @return array<int, UploadedFile|null|array{file: ?UploadedFile, position: string|int}>
     */
    protected function convertMultiBase64ToUploadedFile(array $base64Arr): array
    {
        $base64Converter = new Base64ToUploadedFileConverter;
        $uploadedFiles = [];
        foreach ($base64Arr as $index => $base64) {
            if (! $this->isValidParamValue($base64)) {
                continue;
            }

            if ($this->shouldTreatAsNullParamValue($base64)) {
                $uploadedFiles[$index] = $this->registerConvertedFile($base64, null);

                continue;
            }

            $convertedFile = $base64Converter->handle(
                $this->getBase64ContentFromArray($base64),
                $this->getBase64NameFromArray($base64)
            );

            $uploadedFiles[$index] = $this->registerConvertedFile($base64, $convertedFile);
        }

        return $uploadedFiles;
    }

    /**
     * @param  array<string, string>  $base64Data
     * @return array{file: ?UploadedFile, position: string|int}|UploadedFile|null
     */
    protected function registerConvertedFile(array $base64Data, ?UploadedFile $convertedFile): array|null|UploadedFile
    {
        if (isset($base64Data[$this->jsonBase64PositionKey])) {
            return [
                'file' => $convertedFile,
                'position' => $base64Data[$this->jsonBase64PositionKey],
            ];
        }

        return $convertedFile;
    }
}
