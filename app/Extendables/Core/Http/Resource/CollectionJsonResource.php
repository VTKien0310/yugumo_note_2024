<?php

namespace App\Extendables\Core\Http\Resource;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class CollectionJsonResource extends ResourceCollection
{
    public $preserveAllQueryParameters = true;

    public function mapBy(callable $callback): static
    {
        $this->collection = $this->collection->map($callback);

        return $this;
    }
}
