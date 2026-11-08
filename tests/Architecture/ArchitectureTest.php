<?php

use App\Extendables\Core\Cache\Concerns\CacheKeyGeneration;
use App\Extendables\Core\Utils\Base64Handling\Base64ToUploadedFileConverter;
use App\Extendables\Core\Utils\FileHandling\ArrayToTextFileConverter;

arch()->expect('App')->not->toUse(['die', 'dd', 'dump']);

arch()->preset()->php();

arch()->preset()->security()->ignoring([
    CacheKeyGeneration::class,
    ArrayToTextFileConverter::class,
    Base64ToUploadedFileConverter::class,
]);
