<?php

namespace App\Features\Note\Queries;

enum NoteFilterParamEnum: string
{
    case TYPE_ID = 'type_id';
    case KEYWORD = 'keyword';
    case BOOKMARKED = 'bookmarked';
}
