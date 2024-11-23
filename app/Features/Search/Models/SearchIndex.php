<?php

namespace App\Features\Search\Models;

use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SearchIndex extends Model
{
    use SoftDeletes,
        UlidEloquent;

    const string ID = 'id';

    const string NOTE_ID = 'note_id';

    const string CONTENT = 'content';

    const string SEARCHABLE_ID = 'searchable_id';

    const string SEARCHABLE_TYPE = 'searchable_type';

    protected $table = 'search_indexes';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', Note::ID);
    }

    public function searchable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__);
    }
}
