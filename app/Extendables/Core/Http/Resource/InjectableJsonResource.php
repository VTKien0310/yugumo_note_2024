<?php

namespace App\Extendables\Core\Http\Resource;

use App\Extendables\Core\Http\Request\States\QueryString\OnlyQueryStringState;
use App\Extendables\Core\Http\Request\States\QueryString\RelationQueryStringState;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class InjectableJsonResource extends JsonResource
{
    protected string $resourceName = '';

    protected array $only = [];

    protected array $relations = [];

    protected bool $appIsInDebugMode = false;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->prepareResourceNameProp();

        $this->only = app(OnlyQueryStringState::class)->getOnlyOfResource($this->resourceName);
        $this->relations = app(RelationQueryStringState::class)->getRelations();

        $this->appIsInDebugMode = config('app.debug');
    }

    private function prepareResourceNameProp(): void
    {
        if (empty($this->resourceName)) {
            $jsonResourceClassName = str_replace('Resource', '', class_basename($this));
            $this->resourceName = Str::snake($jsonResourceClassName);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($request)
    {
        return App::call([$this, 'handle']);
    }

    /**
     * Indicate a potentially missing value by the effect of the only query string
     *
     * @throws Exception
     */
    protected function potentiallyMissing(string $onlyQueryStringKey, callable $valueResolver): mixed
    {
        if (! empty($this->only) && ! in_array($onlyQueryStringKey, $this->only)) {
            return new MissingValue();
        }

        try {
            return $valueResolver();
        } catch (Exception $exception) {
            if ($this->appIsInDebugMode) {
                throw $exception;
            }

            return new MissingValue();
        }
    }

    private function relationIsRequested(string $relation): bool
    {
        return in_array($relation, $this->relations);
    }

    /**
     * Indicate a value that can be loaded by the effect of the include query string
     *
     * @throws Exception
     */
    protected function whenRelationRequested(string $relation, callable $valueResolver): mixed
    {
        try {
            return $this->when($this->relationIsRequested($relation), $valueResolver);
        } catch (Exception $exception) {
            if ($this->appIsInDebugMode) {
                throw $exception;
            }

            return new MissingValue();
        }
    }

    /**
     * Indicate values that can be merged by the effect of the include query string
     *
     * @throws Exception
     */
    protected function mergeWhenRelationRequested(string $relation, callable $valueResolver): mixed
    {
        try {
            return $this->mergeWhen($this->relationIsRequested($relation), $valueResolver);
        } catch (Exception $exception) {
            if ($this->appIsInDebugMode) {
                throw $exception;
            }

            return new MissingValue();
        }
    }
}
