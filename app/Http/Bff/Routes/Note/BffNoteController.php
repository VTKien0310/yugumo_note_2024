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

        abort_if(
            ! in_array($note->type_id, [NoteTypeEnum::ADVANCED->value, NoteTypeEnum::CHECKLIST->value]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $rules = [
            'title' => 'required|string|max:255',
        ];

        // Only advanced notes carry rich text content; checklist notes sync
        // just the title here and edit their items via the checklist endpoints.
        if ($note->type_id === NoteTypeEnum::ADVANCED->value) {
            $rules = array_merge($rules, [
                'content' => 'required|array',
                'content.ops' => 'required|array',
                // `required` considers whitespace-only strings (e.g. "\n" line-break ops) empty,
                // so presence is enforced with `present` instead.
                'content.ops.*.insert' => 'present|string',
                'content.ops.*.attributes' => 'sometimes|array',
            ]);
        }

        $data = $request->validate($rules);

        $updateData = [
            Note::TITLE => $data['title'],
        ];

        if (isset($data['content'])) {
            $updateData['rich_text_content'] = $data['content'];
        }

        $note = $updateNoteAction->handle($note, $updateData);

        return $this->responder->responseRawContent([
            'saved_at' => $note->refresh()->updated_at->toIso8601String(),
        ]);
    }
}
