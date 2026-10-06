<?php

namespace App\Extendables\Core\Utils;

use App\Extendables\Core\Http\Enums\HttpRequestParamEnum;
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
            'first_page_url' => $this->buildFirstPageUrl($requestQueryString),
            'last_page_url' => $this->buildLastPageUrl($requestQueryString),
        ];
    }

    private function buildNextPageUrl(array $requestQueryString): string
    {
        $nextPageNumber = $this->currentPage() + 1;
        $noNextPageAvailable = null;

        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_NUMBER->value] = $nextPageNumber <= $this->lastPage() ? $nextPageNumber : $noNextPageAvailable;
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_SIZE->value] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }

    private function buildPreviousPageUrl(array $requestQueryString): string
    {
        $previousPageNumber = $this->currentPage() - 1;
        $noPreviousPageAvailable = null;

        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_NUMBER->value] = $previousPageNumber >= 1 ? $previousPageNumber : $noPreviousPageAvailable;
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_SIZE->value] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }

    private function buildFirstPageUrl(array $requestQueryString): string
    {
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_NUMBER->value] = 1;
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_SIZE->value] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }

    private function buildLastPageUrl(array $requestQueryString): string
    {
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_NUMBER->value] = $this->lastPage();
        $requestQueryString[HttpRequestParamEnum::PAGINATE->value][HttpRequestParamEnum::PAGE_SIZE->value] = $this->perPage();

        return $this->path().'?'.Arr::query($requestQueryString);
    }
}
