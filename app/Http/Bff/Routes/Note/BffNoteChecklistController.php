<?php

namespace App\Http\Bff\Routes\Note;

use App\Extendables\Core\Http\Controllers\ApiController;
use App\Extendables\Core\Http\Response\Responder;
use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\Note\Actions\CreateEmptyChecklistNoteContentAction;
use App\Features\Note\Actions\DeleteChecklistNoteContentAction;
use App\Features\Note\Actions\UpdateChecklistNoteContentAction;
use App\Features\Note\Authorizers\ManageNoteAuthorizer;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BffNoteChecklistController extends ApiController
{
    public function __construct(
        private readonly Responder $responder
    ) {}

    /**
     * POST /bff/notes/:note/checklist-items
     */
    public function store(
        Note $note,
        Request $request,
        ManageNoteAuthorizer $manageNoteAuthorizer,
        CreateEmptyChecklistNoteContentAction $createEmptyChecklistNoteContentAction
    ): JsonResponse {
        $manageNoteAuthorizer->handle($note, $request->user());

        $this->abortUnlessChecklistNote($note);

        $checklistItem = $createEmptyChecklistNoteContentAction->handle($note);

        return $this->responder->responseRawContent([
            'id' => $checklistItem->id,
            'content' => $checklistItem->content,
            'is_completed' => (bool) $checklistItem->is_completed->value,
        ]);
    }

    /**
     * PUT /bff/notes/:note/checklist-items/:checklistItem
     */
    public function update(
        Note $note,
        ChecklistNoteContent $checklistItem,
        Request $request,
        ManageNoteAuthorizer $manageNoteAuthorizer,
        UpdateChecklistNoteContentAction $updateChecklistNoteContentAction
    ): JsonResponse {
        $manageNoteAuthorizer->handle($note, $request->user());

        $this->abortUnlessChecklistNote($note);
        $this->abortUnlessItemBelongsToNote($note, $checklistItem);

        $data = $request->validate([
            // `required` treats an emptied item ("") as missing - presence is
            // what matters here, emptiness is a valid state for a checklist row.
            'content' => 'present|string|max:255',
            'is_completed' => 'required|boolean',
        ]);

        $updateChecklistNoteContentAction->handle($checklistItem, [
            ChecklistNoteContent::CONTENT => $data['content'],
            ChecklistNoteContent::IS_COMPLETED => BoolIntValueEnum::from((int) $data['is_completed']),
        ]);

        // Saving an item touches the parent note, so the note's updated_at
        // reflects the item edit (same semantic as the title sync endpoint).
        return $this->responder->responseRawContent([
            'saved_at' => $note->refresh()->updated_at->toIso8601String(),
        ]);
    }

    /**
     * DELETE /bff/notes/:note/checklist-items/:checklistItem
     */
    public function destroy(
        Note $note,
        ChecklistNoteContent $checklistItem,
        Request $request,
        ManageNoteAuthorizer $manageNoteAuthorizer,
        DeleteChecklistNoteContentAction $deleteChecklistNoteContentAction
    ): JsonResponse {
        $manageNoteAuthorizer->handle($note, $request->user());

        $this->abortUnlessChecklistNote($note);
        $this->abortUnlessItemBelongsToNote($note, $checklistItem);

        $deleteChecklistNoteContentAction->handle($checklistItem);

        return $this->responder->responseNoContent();
    }

    private function abortUnlessChecklistNote(Note $note): void
    {
        abort_if($note->type_id !== NoteTypeEnum::CHECKLIST->value, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function abortUnlessItemBelongsToNote(Note $note, ChecklistNoteContent $checklistItem): void
    {
        abort_if($checklistItem->note_id !== $note->id, Response::HTTP_NOT_FOUND);
    }
}
