<?php

namespace App\Extendables\Core\Http\Constants;

final class HttpRequestParamConstants
{
    public const string COUNT = 'count';

    public const string SUM = 'sum';

    public const string FILTER = 'filter';

    public const string INCLUDE = 'include';

    public const string ONLY = 'only';

    public const string SORT = 'sort';

    public const string SORT_PRIORITY = 'sort_priority';

    public const string PAGINATE = 'page';

    public const string PAGINATE_NUMBER = 'number';

    public const string PAGINATE_SIZE = 'size';

    public const string PAGINATE_CURSOR = 'cursor';

    public const string TIMEZONE = 'timezone';

    private function __construct() {}
}
