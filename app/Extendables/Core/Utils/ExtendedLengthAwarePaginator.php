<?php

namespace App\Extendables\Core\Utils;

use Illuminate\Pagination\LengthAwarePaginator;

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
}
