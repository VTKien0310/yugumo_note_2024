<?php

namespace App\Extendables\Core\Utils;

use Illuminate\Support\Str;

class GetRawTextFromWYSIWYGContentAction
{
    public function handle(string $wysiwygContent): string
    {
        if (empty($wysiwygContent)) {
            return '';
        }

        $wysiwygContent = Str::of($wysiwygContent)
            ->replace(['<br>', '<br/>', '<br />'], ' ')     // keep line breaks
            ->stripTags()                                           // remove other markup
            ->squish();                                             // collapse repeated whitespace

        return html_entity_decode($wysiwygContent);                 // &amp; → &
    }
}
