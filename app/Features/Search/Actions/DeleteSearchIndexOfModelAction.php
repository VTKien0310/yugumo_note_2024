<?php

namespace App\Features\Search\Actions;

use App\Features\Search\Commands\DeleteSearchIndexCommand;
use App\Features\Search\Relationships\HasSearchIndex;

readonly class DeleteSearchIndexOfModelAction
{
    public function __construct(private DeleteSearchIndexCommand $deleteSearchIndexCommand) {}

    public function handle(HasSearchIndex $model): bool
    {
        return $this->deleteSearchIndexCommand->handle($model->searchIndex()->first());
    }
}
