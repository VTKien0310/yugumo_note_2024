<?php

namespace App\Features\Note\Models\Casts;

use App\Features\Note\ValueObjects\ChecklistItemValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ChecklistItemCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<int, ChecklistItemValueObject>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        $value = json_decode($value, true);

        return array_map(
            fn (array $item): ChecklistItemValueObject => new ChecklistItemValueObject(...$item),
            $value
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|false
    {
        $value = array_map(
            fn (ChecklistItemValueObject $item): array => $item->toArray(),
            $value
        );

        return json_encode($value);
    }
}
