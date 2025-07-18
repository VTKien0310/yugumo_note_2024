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
     * @var UploadedFile[]|null[]|array[]
     */
    protected array $convertedFiles = [];

    /**
     * @return UploadedFile[]
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
            && ! empty($paramValue[$this->jsonBase64ContentKey])
            && ! empty($paramValue[$this->jsonBase64NameKey]);
    }

    protected function getBase64ContentFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64ContentKey];
    }

    protected function getBase64NameFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64NameKey];
    }

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

    protected function convertMultiBase64ToUploadedFile(array $base64Arr): array
    {
        $base64Converter = new Base64ToUploadedFileConverter;
        $uploadedFiles = [];
        foreach ($base64Arr as $index => $base64) {
            if (! $this->isValidParamValue($base64)) {
                continue;
            }

            $convertedFile = $base64Converter->handle(
                $this->getBase64ContentFromArray($base64),
                $this->getBase64NameFromArray($base64)
            );

            if (isset($base64[$this->jsonBase64PositionKey])) {
                $uploadedFiles[$index]['file'] = $convertedFile;
                $uploadedFiles[$index]['position'] = $this->getBase64PositionFromArray($base64);
            } else {
                $uploadedFiles[] = $convertedFile;
            }
        }

        return $uploadedFiles;
    }

    protected function getBase64PositionFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64PositionKey];
    }
}
