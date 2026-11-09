<?php

namespace App\Extendables\Core\Http\Resource;

class RawContentResource extends InjectableJsonResource
{
    public function handle()
    {
        return $this->resource;
    }
}
