<?php

namespace App\Extendables\Core\Http\Resource;

class RawContentCollectionResource extends CollectionJsonResource
{
    public $collects = RawContentResource::class;
}
