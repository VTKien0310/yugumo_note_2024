<?php

namespace App\Extendables\Core\Utils;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ExtendedLengthAwarePaginator extends LengthAwarePaginator
{
    public function toArray(): array
    {
        $requestQueryString = (array) request()->query();

        return [
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
            'last_page' => $this->lastPage(),
            'path' => $this->path(),
            'next_page_url' => $this->buildNextPageUrl($requestQueryString),
            'previous_page_url' => $this->buildPreviousPageUrl($requestQueryString),
            'first_page_url' => $this->buildFirstPageUrl(),
            'last_page_url' => $this->buildLastPageUrl(),
        ];
    }

    private function buildNextPageUrl(array $requestQueryString): string
    {
        $nextPageNumber = $this->currentPage() + 1;
        $noNextPageAvailable = null;

        $requestQueryString['page']['number'] = $nextPageNumber <= $this->lastPage() ? $nextPageNumber : $noNextPageAvailable;
        $requestQueryString['page']['size'] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }

    private function buildPreviousPageUrl(array $requestQueryString): string
    {
        $previousPageNumber = $this->currentPage() - 1;
        $noPreviousPageAvailable = null;

        $requestQueryString['page']['number'] = $previousPageNumber >= 1 ? $previousPageNumber : $noPreviousPageAvailable;
        $requestQueryString['page']['size'] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }

    private function buildFirstPageUrl(): string
    {
        $requestQueryString['page']['number'] = 1;
        $requestQueryString['page']['size'] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }

    private function buildLastPageUrl(): string
    {
        $requestQueryString['page']['number'] = $this->lastPage();
        $requestQueryString['page']['size'] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }
}
