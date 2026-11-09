<?php

namespace App\Extendables\Core\Http\RouteDocumentation;

abstract readonly class RouteDoc
{
    public function __construct(
        public string $endpoint
    ) {}
}
