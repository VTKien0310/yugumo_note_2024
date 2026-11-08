<?php

use App\Extendables\Core\Models\Interfaces\HasPolymorphicRelationship;
use App\Extendables\Core\Models\Traits\StaticColumnQualifier;
use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Features\Note\Models\ChecklistNoteContent;
use App\Features\Note\Models\Note;
use App\Features\Note\Models\RichTextNoteContent;
use App\Features\Note\Models\TextNoteContent;
use App\Features\NoteType\Models\NoteType;
use App\Features\Search\Models\SearchIndex;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

$modelContracts = [
    User::class => [
        'table' => 'users',
        'key' => 'id',
        'key_type' => 'string',
        'incrementing' => false,
        'timestamps' => true,
        'fillable' => ['name', 'email', 'password'],
        'hidden' => ['password', 'remember_token'],
        'casts' => ['password' => 'hashed'],
        'traits' => [Notifiable::class, SoftDeletes::class, StaticColumnQualifier::class, UlidEloquent::class],
    ],
    Note::class => [
        'table' => 'notes',
        'key' => 'id',
        'key_type' => 'string',
        'incrementing' => false,
        'timestamps' => true,
        'guarded' => ['id', 'created_at', 'updated_at'],
        'hidden' => [],
        'casts' => ['last_viewed_at' => 'datetime', 'bookmarked' => 'App\Extendables\Core\Utils\BoolIntValueEnum'],
        'traits' => [SoftDeletes::class, StaticColumnQualifier::class, UlidEloquent::class],
    ],
    NoteType::class => [
        'table' => 'note_types',
        'key' => 'id',
        'key_type' => 'int',
        'incrementing' => true,
        'timestamps' => true,
        'guarded' => ['id', 'created_at', 'updated_at'],
        'hidden' => [],
        'casts' => [],
        'traits' => [SoftDeletes::class],
    ],
    ChecklistNoteContent::class => [
        'table' => 'checklist_note_contents',
        'key' => 'id',
        'key_type' => 'string',
        'incrementing' => false,
        'timestamps' => true,
        'guarded' => ['id', 'created_at', 'updated_at'],
        'hidden' => [],
        'casts' => ['is_completed' => 'App\Extendables\Core\Utils\BoolIntValueEnum'],
        'traits' => [SoftDeletes::class, UlidEloquent::class],
    ],
    RichTextNoteContent::class => [
        'table' => 'rich_text_note_contents',
        'key' => 'id',
        'key_type' => 'string',
        'incrementing' => false,
        'timestamps' => true,
        'guarded' => ['id', 'created_at', 'updated_at'],
        'hidden' => [],
        'casts' => ['content' => 'array'],
        'traits' => [SoftDeletes::class, UlidEloquent::class],
    ],
    TextNoteContent::class => [
        'table' => 'text_note_contents',
        'key' => 'id',
        'key_type' => 'string',
        'incrementing' => false,
        'timestamps' => true,
        'guarded' => ['id', 'created_at', 'updated_at'],
        'hidden' => [],
        'casts' => [],
        'traits' => [SoftDeletes::class, UlidEloquent::class],
    ],
    SearchIndex::class => [
        'table' => 'search_indexes',
        'key' => 'id',
        'key_type' => 'string',
        'incrementing' => false,
        'timestamps' => true,
        'guarded' => ['id', 'created_at', 'updated_at'],
        'hidden' => [],
        'casts' => [],
        'traits' => [SoftDeletes::class, StaticColumnQualifier::class, UlidEloquent::class],
    ],
];

$relationshipContracts = [
    User::class => [
        ['notes', HasMany::class, Note::class, 'user_id', 'id', 'local'],
    ],
    Note::class => [
        ['user', BelongsTo::class, User::class, 'user_id', 'id', 'owner'],
        ['type', BelongsTo::class, NoteType::class, 'type_id', 'id', 'owner'],
        ['textContent', HasOne::class, TextNoteContent::class, 'note_id', 'id', 'local'],
        ['richTextContent', HasOne::class, RichTextNoteContent::class, 'note_id', 'id', 'local'],
        ['checklistContent', HasMany::class, ChecklistNoteContent::class, 'note_id', 'id', 'local'],
        ['fullTextSearchableContents', HasMany::class, SearchIndex::class, 'note_id', 'id', 'local'],
    ],
    NoteType::class => [
        ['notes', HasMany::class, Note::class, 'type_id', 'id', 'local'],
    ],
    ChecklistNoteContent::class => [
        ['note', BelongsTo::class, Note::class, 'note_id', 'id', 'owner'],
    ],
    RichTextNoteContent::class => [
        ['note', BelongsTo::class, Note::class, 'note_id', 'id', 'owner'],
    ],
    TextNoteContent::class => [
        ['note', BelongsTo::class, Note::class, 'note_id', 'id', 'owner'],
    ],
    SearchIndex::class => [
        ['note', BelongsTo::class, Note::class, 'note_id', 'id', 'owner'],
    ],
];

foreach ($modelContracts as $modelClass => $contract) {
    describe($modelClass.' contract', function () use ($modelClass, $contract, $relationshipContracts) {
        it('configures table structure and model behaviors as expected', function () use ($modelClass, $contract) {
            $model = new $modelClass;

            expect($model->getTable())->toBe($contract['table'])
                ->and($model->getKeyName())->toBe($contract['key'])
                ->and($model->getKeyType())->toBe($contract['key_type'])
                ->and($model->getIncrementing())->toBe($contract['incrementing'])
                ->and($model->usesTimestamps())->toBe($contract['timestamps']);

            if (isset($contract['fillable'])) {
                expect($model->getFillable())->toEqualCanonicalizing($contract['fillable']);
            }

            if (isset($contract['guarded'])) {
                expect($model->getGuarded())->toEqualCanonicalizing($contract['guarded']);
            }

            if (isset($contract['hidden'])) {
                expect($model->getHidden())->toEqualCanonicalizing($contract['hidden']);
            }

            if (isset($contract['casts'])) {
                foreach ($contract['casts'] as $field => $cast) {
                    expect($model->hasCast($field))->toBeTrue()
                        ->and($model->getCasts()[$field])->toBe($cast);
                }
            }

            $modelTraits = class_uses_recursive($modelClass);
            foreach ($contract['traits'] as $trait) {
                expect(isset($modelTraits[$trait]))->toBeTrue();
            }
        });

        if (isset($relationshipContracts[$modelClass])) {
            it('declares the expected relationships', function () use ($modelClass, $relationshipContracts) {
                $model = new $modelClass;

                foreach ($relationshipContracts[$modelClass] as [$method, $relationClass, $relatedClass, $foreignKey, $otherKey, $keyKind]) {
                    $relation = $model->{$method}();

                    expect($relation)->toBeInstanceOf($relationClass)
                        ->and($relation->getRelated())->toBeInstanceOf($relatedClass)
                        ->and($relation->getForeignKeyName())->toBe($foreignKey);

                    $actualOtherKey = $keyKind === 'owner'
                        ? $relation->getOwnerKeyName()
                        : $relation->getLocalKeyName();

                    expect($actualOtherKey)->toBe($otherKey);
                }
            });
        }
    });
}

describe(HasPolymorphicRelationship::class, function () {
    it('registers the expected morph aliases', function () {
        expect(Note::morphType())->toBe('note')
            ->and(TextNoteContent::morphType())->toBe('text_note_content')
            ->and(RichTextNoteContent::morphType())->toBe('rich_text_note_content')
            ->and(ChecklistNoteContent::morphType())->toBe('checklist_note_content')
            ->and(Relation::getMorphedModel(Note::morphType()))->toBe(Note::class)
            ->and(Relation::getMorphedModel(TextNoteContent::morphType()))->toBe(TextNoteContent::class)
            ->and(Relation::getMorphedModel(RichTextNoteContent::morphType()))->toBe(RichTextNoteContent::class)
            ->and(Relation::getMorphedModel(ChecklistNoteContent::morphType()))->toBe(ChecklistNoteContent::class)
            ->and(Relation::requiresMorphMap())->toBeTrue();
    });
});
