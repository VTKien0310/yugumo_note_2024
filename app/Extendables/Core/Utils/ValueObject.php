<?php

namespace App\Extendables\Core\Utils;

use BackedEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;
use UnitEnum;

abstract class ValueObject implements Arrayable, Responsable, JsonSerializable
{
    /**
     * Get all stored value as a collection
     */
    public function toCollection(): Collection
    {
        return collect($this->toArray());
    }

    /**
     * Get all stored value as an array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Get all stored value as a snake case key array.
     * Will also affect stored properties if used recursively.
     */
    public function toSnakeCaseKeyArray(bool $recursively = true): array
    {
        return $this->turnKeysIntoSnakeCase($this->toArray(), $recursively);
    }

    private function turnKeysIntoSnakeCase(array $originalArray, bool $recursively = true): array
    {
        $snakeCaseKeyArray = [];

        foreach ($originalArray as $key => $value) {
            $key = is_int($key) ? $key : Str::snake($key);

            $snakeCaseKeyArray[$key] = $recursively
                ? match (true) {
                    is_array($value) => $this->turnKeysIntoSnakeCase($value, $recursively),
                    $value instanceof ValueObject => $value->toSnakeCaseKeyArray($recursively),
                    $value instanceof Arrayable => $this->turnKeysIntoSnakeCase($value->toArray(), $recursively),
                    is_object($value) => $this->turnKeysIntoSnakeCase(get_object_vars($value), $recursively),
                    default => $value
                }
                : $value;
        }

        return $snakeCaseKeyArray;
    }

    /**
     * Get all stored value as a response formatted array
     */
    public function toResponseData(): array
    {
        $result = [];

        foreach ($this->toSnakeCaseKeyArray(false) as $prop => $value) {
            $value = is_bool($value) ? (int) $value : $value;
            $result[$prop] = $value;
        }

        return $result;
    }

    /**
     * Get all stored value as a response formatted array. Also format nested array and object values
     */
    public function toResponseDataRecursive(): array
    {
        $result = [];

        foreach ($this->toSnakeCaseKeyArray(false) as $prop => $value) {
            $result[$prop] = $this->propValueToResponseData($value);
        }

        return $result;
    }

    private function propValueToResponseData(mixed $propValue): mixed
    {
        if (is_array($propValue)) {
            return $this->arrayPropValueToResponseData($propValue);
        }

        if ($propValue instanceof BackedEnum) {
            return $propValue->value;
        }

        if ($propValue instanceof UnitEnum) {
            return $propValue->name;
        }

        if ($propValue instanceof ValueObject) {
            return $propValue->toResponseDataRecursive();
        }

        if ($propValue instanceof Arrayable) {
            return $this->arrayPropValueToResponseData($propValue->toArray());
        }

        if (is_object($propValue)) {
            return $this->arrayPropValueToResponseData(get_object_vars($propValue));
        }

        if (is_bool($propValue)) {
            return (int) $propValue;
        }

        return $propValue;
    }

    private function arrayPropValueToResponseData(array $propValue): array
    {
        $result = [];

        foreach ($propValue as $key => $value) {
            $result[is_int($key) ? $key : Str::snake($key)] = $this->propValueToResponseData($value);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request): JsonResponse|Response
    {
        return response()->json($this->toResponseDataRecursive());
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this->toResponseDataRecursive();
    }
}
