<?php

namespace App\Extendables\Core\Http\Enums;

enum HttpRequestParamEnum: string
{
    case COUNT = 'count';

    case SUM = 'sum';

    case FILTER = 'filter';

    case INCLUDE = 'include';

    case ONLY = 'only';

    case SORT = 'sort';

    case SORT_PRIORITY = 'sort_priority';

    case PAGINATE = 'page';
}
