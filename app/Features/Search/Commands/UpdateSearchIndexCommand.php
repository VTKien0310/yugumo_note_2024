<?php

namespace App\Features\Search\Commands;

use App\Features\Search\Models\SearchIndex;

class UpdateSearchIndexCommand
{
    public function handle(SearchIndex $searchIndex, array $data): SearchIndex
    {
        $searchIndex->update($data);

        return $searchIndex->refresh();
    }
}
