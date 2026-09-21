<?php

namespace App\Extendables\Core\Utils\FileHandling;

enum ArrayToFileWriteModeEnum: string
{
    case OVERWRITE = 'w';
    case APPEND = 'a';
}
