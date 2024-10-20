<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Commands\UpdateChecklistNoteContentByIdCommand;

readonly class UpdateChecklistNoteContentByIdAction
{
    public function __construct(private UpdateChecklistNoteContentByIdCommand $updateChecklistNoteContentByIdCommand) {}

    public function handle(string $id, array $data): bool
    {
        return (bool) $this->updateChecklistNoteContentByIdCommand->handle($id, $data);
    }
}
