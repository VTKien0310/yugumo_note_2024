<?php

namespace App\Features\Note\Actions;

use Illuminate\Support\Str;

class GetRawTextFromQuillDeltaAction
{
    public function handle(array $delta): string
    {
        $ops = $delta['ops'] ?? [];

        $inserts = [];

        foreach ($ops as $op) {
            if (! isset($op['insert']) || ! is_string($op['insert'])) {
                continue;
            }

            $inserts[] = $op['insert'];
        }

        if ($inserts === []) {
            return '';
        }

        return Str::of(implode('', $inserts))
            ->replace(["\r\n", "\n", "\r"], ' ')
            ->squish()
            ->toString();
    }
}
