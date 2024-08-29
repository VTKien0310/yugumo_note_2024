<?php

namespace App\Extendables\Core\Http\Resource;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class RawContentResource extends InjectableJsonResource
{
    function handle()
    {
        return $this->resource;
    }
}
