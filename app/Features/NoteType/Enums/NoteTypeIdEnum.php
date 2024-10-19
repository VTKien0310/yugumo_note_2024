<?php

namespace App\Features\NoteType\Enums;

enum NoteTypeIdEnum: int
{
    case SIMPLE = 1;
    case ADVANCED = 2;
    case CHECKLIST = 3;
}
