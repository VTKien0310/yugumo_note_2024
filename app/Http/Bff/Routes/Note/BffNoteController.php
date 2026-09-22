<?php

namespace App\Http\Bff\Routes\Note;

use App\Extendables\Core\Http\Controllers\ApiController;
use App\Extendables\Core\Http\Response\Responder;
use App\Features\Note\Actions\UpdateNoteAction;
use App\Features\Note\Authorizers\ManageNoteAuthorizer;
use App\Features\Note\Models\Note;
use App\Features\NoteType\Enums\NoteTypeEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BffNoteController extends ApiController
{
    public function __construct(
        private readonly Responder $responder
    ) {}

    /**
     * PUT /bff/notes/:id
     */
    public function update(
        Note $note,
        Request $request,
        ManageNoteAuthorizer $manageNoteAuthorizer,
        UpdateNoteAction $updateNoteAction
    ): JsonResponse {
        $manageNoteAuthorizer->handle($note, $request->user());

        abort_if($note->type_id !== NoteTypeEnum::ADVANCED->value, Response::HTTP_UNPROCESSABLE_ENTITY);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|array',
            'content.ops' => 'required|array',
        ]);

        $note = $updateNoteAction->handle($note, [
            Note::TITLE => $data['title'],
            'rich_text_content' => $data['content'],
        ]);

        return $this->responder->responseRawContent([
            'saved_at' => $note->refresh()->updated_at->toIso8601String(),
        ]);
    }
}
