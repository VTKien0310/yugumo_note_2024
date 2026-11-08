<?php

use App\Extendables\Core\Utils\GetRawTextFromWYSIWYGContentAction;

describe(GetRawTextFromWYSIWYGContentAction::class, function () {
    it('returns empty string when input is empty', function () {
        $action = new GetRawTextFromWYSIWYGContentAction;
        expect($action->handle(''))->toBe('');
    });

    it('strips html tags, converts linebreaks to spaces, collapses spaces, and decodes entities', function () {
        $action = new GetRawTextFromWYSIWYGContentAction;
        $html = '<p>Hello <strong>World</strong>!</p><br/><span>Tom &amp; Jerry</span>';

        expect($action->handle($html))->toBe('Hello World! Tom & Jerry');
    });
});
