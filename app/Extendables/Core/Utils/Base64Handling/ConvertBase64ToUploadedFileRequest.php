<?php

namespace App\Extendables\Core\Utils\Base64Handling;

use Illuminate\Http\UploadedFile;

trait ConvertBase64ToUploadedFileRequest
{
    protected function jsonBase64NameKey(): string
    {
        return 'name';
    }

    protected function jsonBase64ContentKey(): string
    {
        return 'content';
    }

    protected function jsonBase64PositionKey(): string
    {
        return 'position';
    }

    protected function base64Param(): array
    {
        return [];
    }

    protected function base64ArrayParam(): array
    {
        return [];
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

            if (isset($base64['position'])) {
                $uploadedFiles[$index]['file'] = $convertedFile;
                $uploadedFiles[$index]['position'] = $this->getBase64PositionFromArray($base64);
            } else {
                $uploadedFiles[] = $convertedFile;
            }
        }

        return $uploadedFiles;
    }

    protected function convertBase64RequestParamToUploadedFile(string $paramName): void
    {
        $paramValue = $this->input($paramName);

        if (empty($paramValue)) {
            return;
        }

        if ($this->isValidParamValue($paramValue)) {
            $this->unsetRequestParam($paramName);
            $base64Converter = new Base64ToUploadedFileConverter;
            $convertedParamValue = $base64Converter->handle(
                $this->getBase64ContentFromArray($paramValue),
                $this->getBase64NameFromArray($paramValue)
            );
            $this->saveConvertedFileToRequest($paramName, $convertedParamValue);
        } else {
            $this->setNullRequestParam($paramName);
        }
    }

    protected function isValidParamValue(mixed $paramValue): bool
    {
        return is_array($paramValue)
            && ! empty($paramValue[$this->jsonBase64ContentKey()])
            && ! empty($paramValue[$this->jsonBase64NameKey()]);
    }

    protected function convertBase64ArrayRequestParamToUploadedFile(string $paramName): void
    {
        $paramValue = $this->input($paramName);

        if (empty($paramValue)) {
            return;
        }

        if (is_array($paramValue)) {
            $convertedParamValue = $this->convertMultiBase64ToUploadedFile($paramValue);
            $this->unsetRequestParam($paramName);
            $this->saveConvertedFileToRequest($paramName, $convertedParamValue);
        } else {
            $this->setNullRequestParam($paramName);
        }
    }

    protected function saveConvertedFileToRequest(string $paramName, array|UploadedFile $convertedFile): void
    {
        $this->convertedFiles[$paramName] = $convertedFile;
    }

    protected function unsetRequestParam(string $paramName): void
    {
        $this->request->remove($paramName);
        $this->json->remove($paramName);
    }

    protected function setNullRequestParam(string $paramName): void
    {
        $this->merge([
            $paramName => null,
        ]);
    }

    protected function getBase64ContentFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64ContentKey()];
    }

    protected function getBase64NameFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64NameKey()];
    }

    protected function getBase64PositionFromArray(array $arr): string
    {
        return $arr[$this->jsonBase64PositionKey()];
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareForValidation()
    {
        foreach ($this->base64Param() as $base64ParamName) {
            $this->convertBase64RequestParamToUploadedFile($base64ParamName);
        }

        foreach ($this->base64ArrayParam() as $base64ArrayParamName) {
            $this->convertBase64ArrayRequestParamToUploadedFile($base64ArrayParamName);
        }
    }
}
