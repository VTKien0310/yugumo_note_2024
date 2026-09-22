<?php

namespace App\Extendables\Core\Http\Exception;

use Closure;
use Exception;
use Throwable;

abstract class HasSideEffectsException extends Exception implements ExtendableException
{
    /**
     * @param  array<int, callable|Closure>  $sideEffects
     */
    public function __construct(
        protected array $sideEffects,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function appendSideEffect(callable|Closure $sideEffect): void
    {
        $this->sideEffects[] = $sideEffect;
    }

    /**
     * @param  array<int, callable|Closure>  $sideEffects
     */
    public function appendSideEffects(array $sideEffects): void
    {
        foreach ($sideEffects as $sideEffect) {
            $this->sideEffects[] = $sideEffect;
        }
    }

    public function prependSideEffect(callable|Closure $sideEffect): void
    {
        array_unshift($this->sideEffects, $sideEffect);
    }

    /**
     * @param  array<int, callable|Closure>  $sideEffects
     */
    public function prependSideEffects(array $sideEffects): void
    {
        foreach (array_reverse($sideEffects) as $sideEffect) {
            array_unshift($this->sideEffects, $sideEffect);
        }
    }

    /**
     * @return array<int, callable|Closure>
     */
    public function getSideEffects(): array
    {
        return $this->sideEffects;
    }

    public function clearSideEffects(): void
    {
        $this->sideEffects = [];
    }

    public function hasSideEffects(): bool
    {
        return count($this->sideEffects) > 0;
    }
}
