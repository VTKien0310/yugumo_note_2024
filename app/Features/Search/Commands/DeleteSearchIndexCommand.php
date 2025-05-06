<?php

namespace App\Features\Search\Commands;

use App\Features\Search\Models\SearchIndex;

class DeleteSearchIndexCommand
{
    public function handle(SearchIndex $searchIndex): bool
    {
        return (bool) $searchIndex->delete();
    }
}
