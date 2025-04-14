<?php

namespace App\Features\Note\Queries;

enum NoteSortFieldEnum: string
{
    case ID = 'id';
    case CREATED_AT = 'created_at';
    case UPDATED_AT = 'updated_at';
    case TYPE_ID = 'type_id';
    case TITLE = 'title';
}
