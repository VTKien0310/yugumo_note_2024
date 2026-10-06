<?php

namespace App\Features\Search\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasSearchIndex
{
    const string RELATION_SEARCH_INDEX = 'searchIndex';

    public function searchIndex(): MorphOne;
}
