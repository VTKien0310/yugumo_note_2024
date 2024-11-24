<?php

namespace App\Features\Search\Commands;

use App\Features\Search\Models\SearchIndex;
use App\Features\Search\Relationships\HasSearchIndex;

class CreateSearchIndexCommand
{
    public function handle(HasSearchIndex $model, array $data): SearchIndex
    {
        return $model->searchIndex()->create($data);
    }
}
