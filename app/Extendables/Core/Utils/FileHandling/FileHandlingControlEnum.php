<?php

namespace App\Extendables\Core\Utils\FileHandling;

enum FileHandlingControlEnum
{
    case STOP;
    case SKIP_LINE;
    case RETURN_EMPTY_RESULT;
}
