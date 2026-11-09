<?php

function is_empty_array(mixed $var): bool
{
    return $var === [];
}

function is_not_empty_array(mixed $var): bool
{
    return is_array($var) && $var !== [];
}

function is_empty_string(mixed $var): bool
{
    return $var === '';
}

function is_not_empty_string(mixed $var): bool
{
    return is_string($var) && $var !== '';
}

function is_blank_string(mixed $var): bool
{
    return $var === null || (is_string($var) && trim($var) === '');
}

function array_diff_assoc_recursive(array $array1, array $array2): array
{
    $diff = [];

    foreach ($array1 as $key => $value) {
        if (! array_key_exists($key, $array2)) {
            $diff[$key] = $value;

            continue;
        }

        if (is_array($value) && is_array($array2[$key])) {
            $nested = array_diff_assoc_recursive($value, $array2[$key]);
            if ($nested !== []) {
                $diff[$key] = $nested;
            }

            continue;
        }

        if ($value !== $array2[$key]) {
            $diff[$key] = $value;
        }
    }

    return $diff;
}
