<?php

namespace App\Features\Note\Models;

use App\Extendables\Core\Models\Interfaces\HasPolymorphicRelationship;
use App\Extendables\Core\Models\Traits\StaticColumnQualifier;
use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Extendables\Core\Utils\BoolIntValueEnum;
use App\Features\NoteType\Models\NoteType;
use App\Features\Search\Models\SearchIndex;
use App\Features\Search\Relationships\HasSearchIndex;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model implements HasPolymorphicRelationship, HasSearchIndex
{
    use SoftDeletes,
        StaticColumnQualifier,
        UlidEloquent;

    const string ID = 'id';

    const string USER_ID = 'user_id';

    const string TYPE_ID = 'type_id';

    const string TITLE = 'title';

    const string BOOKMARKED = 'bookmarked';

    const string LAST_VIEWED_AT = 'last_viewed_at';

    const string VIEWS = 'views';

    const string DELETED_AT = 'deleted_at';

    protected $table = 'notes';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'last_viewed_at' => 'datetime',
            'bookmarked' => BoolIntValueEnum::class,
        ];
    }

    public static function morphType(): string
    {
        return 'note';
    }

    const string RELATION_USER = 'user';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    const string RELATION_TYPE = 'type';

    public function type(): BelongsTo
    {
        return $this->belongsTo(NoteType::class, 'type_id', 'id');
    }

    const string RELATION_TEXT_CONTENT = 'textContent';

    public function textContent(): HasOne
    {
        return $this->hasOne(TextNoteContent::class, TextNoteContent::NOTE_ID, 'id');
    }

    const string RELATION_CHECKLIST_CONTENT = 'checklistContent';

    public function checklistContent(): HasMany
    {
        return $this->hasMany(ChecklistNoteContent::class, ChecklistNoteContent::NOTE_ID, 'id');
    }

    const string RELATION_XML_CONTENT = 'xmlContent';

    public function xmlContent(): HasOne
    {
        return $this->hasOne(XmlNoteContent::class, XmlNoteContent::NOTE_ID, 'id');
    }

    const string RELATION_SEARCH_INDEX = 'searchIndex';

    public function searchIndex(): MorphOne
    {
        return $this->morphOne(SearchIndex::class, SearchIndex::RELATION_SEARCHABLE);
    }

    const string RELATION_FULL_TEXT_SEARCHABLE_CONTENTS = 'fullTextSearchableContents';

    public function fullTextSearchableContents(): HasMany
    {
        return $this->hasMany(SearchIndex::class, SearchIndex::NOTE_ID, 'id');
    }

    public static function maxBookmarkedNotesPerUser(): int
    {
        return 20;
    }
}
