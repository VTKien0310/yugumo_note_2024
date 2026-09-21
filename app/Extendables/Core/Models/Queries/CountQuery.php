<?php

namespace App\Extendables\Core\Models\Queries;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CountQuery
{
    public function handle(
        Builder|EloquentBuilder $builder,
        array $allowedCounts = ['id'],
        string $countColumn = 'id'
    ): int {
        if (! in_array($countColumn, $allowedCounts)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        return $builder->count($countColumn);
    }
}
