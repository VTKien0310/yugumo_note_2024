<?php

namespace App\Extendables\Core\Utils\FileHandling;

class ArrayToTextFileConverter
{
    public function handle(
        array $content,
        array $lineBeforeWriteHooksPipe = [],
        array $fileBeforeWriteHooksPipe = [],
        string $fileExtension = 'txt',
        string $fileName = '',
        ArrayToFileWriteModeEnum $writeMode = ArrayToFileWriteModeEnum::APPEND
    ): string {
        $fileExtension = $fileExtension ?: 'txt';
        $fileName = $fileName ?: uniqid('laravel');
        $filePath = sys_get_temp_dir().'/'.$fileName.'.'.$fileExtension;

        $fOpenMode = $writeMode->value;

        $content = $this->runFileHooksPipe($content, $fileBeforeWriteHooksPipe);

        $file = fopen($filePath, $fOpenMode);
        foreach ($content as $newLine) {
            $newLine = $this->runLineHooksPipe($newLine, $lineBeforeWriteHooksPipe);
            fwrite($file, "$newLine\n");
        }
        fclose($file);

        return $filePath;
    }

    private function runLineHooksPipe(mixed $line, array $lineHooksPipe): string
    {
        foreach ($lineHooksPipe as $hook) {
            $line = $hook($line);
        }

        return $line;
    }

    private function runFileHooksPipe(array $content, array $fileHooksPipe): array
    {
        foreach ($fileHooksPipe as $hook) {
            $content = $hook($content);
        }

        return $content;
    }
}
