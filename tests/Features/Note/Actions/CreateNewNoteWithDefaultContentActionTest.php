<?php

use App\Features\Note\Actions\CreateNewNoteWithDefaultContentAction;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\RichTextNoteContent;
use App\Features\Note\Models\TextNoteContent;
use App\Features\NoteType\Enums\NoteTypeEnum;
use App\Features\NoteType\Models\NoteType;
use Database\Seeders\NoteTypeSeeder;
use Tests\Factories\UserFactory;

describe(CreateNewNoteWithDefaultContentAction::class, function () {
    beforeEach(function () {
        $this->seed(NoteTypeSeeder::class);
    });

    it('creates a simple note with default empty text content and title search index', function () {
        $user = UserFactory::new()->create();
        $noteType = NoteType::query()->findOrFail(NoteTypeEnum::SIMPLE->value);

        $note = app(CreateNewNoteWithDefaultContentAction::class)->handle($user, $noteType);

        expect($note->title)->toBe('Untitled')
            ->and($note->user_id)->toBe($user->id)
            ->and($note->type_id)->toBe(NoteTypeEnum::SIMPLE->value)
            ->and($note->textContent)->toBeInstanceOf(TextNoteContent::class)
            ->and($note->textContent->content)->toBe('')
            ->and($note->searchIndex)->not->toBeNull();
    });

    it('creates a markdown note with default empty text content', function () {
        $user = UserFactory::new()->create();
        $noteType = NoteType::query()->findOrFail(NoteTypeEnum::MARKDOWN->value);

        $note = app(CreateNewNoteWithDefaultContentAction::class)->handle($user, $noteType);

        expect($note->type_id)->toBe(NoteTypeEnum::MARKDOWN->value)
            ->and($note->textContent)->toBeInstanceOf(TextNoteContent::class)
            ->and($note->textContent->content)->toBe('');
    });

    it('creates a checklist note with default checklist items', function () {
        $user = UserFactory::new()->create();
        $noteType = NoteType::query()->findOrFail(NoteTypeEnum::CHECKLIST->value);

        $note = app(CreateNewNoteWithDefaultContentAction::class)->handle($user, $noteType);

        expect($note->type_id)->toBe(NoteTypeEnum::CHECKLIST->value)
            ->and($note->checklistContent)->not->toBeEmpty()
            ->and($note->checklistContent->first())->toBeInstanceOf(ChecklistNoteContent::class);
    });

    it('creates an advanced note with default rich text content', function () {
        $user = UserFactory::new()->create();
        $noteType = NoteType::query()->findOrFail(NoteTypeEnum::ADVANCED->value);

        $note = app(CreateNewNoteWithDefaultContentAction::class)->handle($user, $noteType);

        expect($note->type_id)->toBe(NoteTypeEnum::ADVANCED->value)
            ->and($note->richTextContent)->toBeInstanceOf(RichTextNoteContent::class)
            ->and($note->richTextContent->content)->toBeArray();
    });
});
