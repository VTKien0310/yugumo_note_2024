<?php

namespace App\Features\Note\Models;

use App\Extendables\Core\Models\Interfaces\HasPolymorphicRelationship;
use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Features\Note\Relationships\BelongsToNote;
use App\Features\Search\Models\SearchIndex;
use App\Features\Search\Relationships\HasSearchIndex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class XmlNoteContent extends Model implements BelongsToNote, HasPolymorphicRelationship, HasSearchIndex
{
    use SoftDeletes,
        UlidEloquent;

    const string ID = 'id';

    const string NOTE_ID = 'note_id';

    const string CONTENT = 'content';

    protected $table = 'xml_note_contents';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $touches = [
        'note',
    ];

    public static function morphType(): string
    {
        return 'xml_note_content';
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', Note::ID);
    }

    public function searchIndex(): MorphOne
    {
        return $this->morphOne(SearchIndex::class, SearchIndex::RELATION_SEARCHABLE);
    }
}
