<?php

namespace App\Extendables\Core\Utils\FileHandling;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

class TextFileToArrayConverter
{
    public function handle(
        UploadedFile|File|string $file,
        int $lineLimit,
        array $lineAfterReadHooksPipe = [],
        array $fileAfterReadHooksPipe = [],
        array $lineOnReadHooksPipe = [],
        array $fileOnReadHooksPipe = []
    ): ?TextFileToArrayResultValueObject {
        if (! is_string($file)) {
            $file = $file->path();
        }

        $lineLimitWithEofLine = $lineLimit;

        $readLines = [];
        $readLinesCount = 0;

        $fileStream = fopen($file, 'r');
        while (! feof($fileStream)) {
            $newLine = fgets($fileStream);

            $lineOnReadHooksPipeResult = $this->runLineHooksPipe($newLine, $lineOnReadHooksPipe);
            if ($lineOnReadHooksPipeResult === FileHandlingControlEnum::SKIP_LINE) {
                continue;
            }
            if ($lineOnReadHooksPipeResult === FileHandlingControlEnum::STOP) {
                fclose($fileStream);

                return null;
            }

            $readLines[] = $this->runLineHooksPipe($newLine, $lineAfterReadHooksPipe);

            $readLinesCount++;
            if ($readLinesCount > $lineLimitWithEofLine) {
                fclose($fileStream);

                $readLines = $this->freeMemory();

                return new TextFileToArrayResultValueObject(
                    isSuccess: false,
                    result: $readLines,
                    readLinesCount: $readLinesCount,
                    error: TextFileToArrayErrorEnum::LINE_LIMIT_EXCEED
                );
            }
        }
        fclose($fileStream);

        $fileOnReadHooksPipeResult = $this->runFileHooksPipe($readLines, $fileOnReadHooksPipe);
        if ($fileOnReadHooksPipeResult === FileHandlingControlEnum::STOP) {
            return null;
        }
        if ($fileOnReadHooksPipeResult === FileHandlingControlEnum::RETURN_EMPTY_RESULT) {
            return new TextFileToArrayResultValueObject(
                isSuccess: true,
                result: [],
                readLinesCount: 0
            );
        }

        $readLines = $this->runFileHooksPipe($readLines, $fileAfterReadHooksPipe);

        return new TextFileToArrayResultValueObject(
            isSuccess: true,
            result: $readLines,
            readLinesCount: $readLinesCount
        );
    }

    private function runLineHooksPipe(string $line, array $lineHooksPipe): mixed
    {
        $pipeInput = $line;
        foreach ($lineHooksPipe as $hook) {
            $pipeInput = $hook($pipeInput);
        }

        return $pipeInput;
    }

    private function runFileHooksPipe(array $file, array $fileHooksPipe): mixed
    {
        $pipeInput = $file;
        foreach ($fileHooksPipe as $hook) {
            $pipeInput = $hook($pipeInput);
        }

        return $pipeInput;
    }

    /**
     * Read more here: https://www.geeksforgeeks.org/which-one-is-better-unset-or-var-null-to-free-memory-in-php/
     *
     * @return null
     */
    private function freeMemory()
    {
        return null;
    }
}
