<?php

namespace App\Extendables\Core\Utils;

use App\Extendables\Core\Http\Constants\HttpRequestParamConstants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ExtendedLengthAwarePaginator extends LengthAwarePaginator
{
    public function toArray(): array
    {
        return [
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
            'last_page' => $this->lastPage(),
            'path' => $this->path(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'first_page_url' => $this->url(1),
            'last_page_url' => $this->url($this->lastPage()),
        ];
    }

    /**
     * Build the URL for a given page following the JSON API convention (`page[number]` / `page[size]`).
     *
     * Laravel's implementation emits a scalar `?page=N` and drops the request's query string (and any `page[...]`
     * value passed to `appends()`, since it collides with the page name), so the generated links would lose the
     * page size, filters, sorts, etc. Here the current request's query string is preserved and only the `page`
     * entry is replaced.
     *
     * @param  int  $page
     */
    public function url($page): string
    {
        // Read the resolver directly: resolveQueryString() is documented as returning a string, while the framework's
        // resolver (registered by the framework's pagination state) actually returns the request's query array.
        $requestQuery = (static::$queryStringResolver)();
        $parameters = array_merge(is_array($requestQuery) ? $requestQuery : [], $this->query);
        $parameters[HttpRequestParamConstants::PAGINATE] = [
            HttpRequestParamConstants::PAGINATE_NUMBER => max((int) $page, 1),
            HttpRequestParamConstants::PAGINATE_SIZE => $this->perPage(),
        ];
        $path = $this->path() ?? '';

        return $path
            .(str_contains($path, '?') ? '&' : '?')
            .Arr::query($parameters)
            .$this->buildFragment();
    }
}
