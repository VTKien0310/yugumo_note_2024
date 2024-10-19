<?php

namespace App\Features\Note\Models;

use App\Extendables\Core\Models\Traits\UlidEloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TextNoteContent extends Model
{
    use SoftDeletes,
        UlidEloquent;

    const string ID = 'id';

    const string NOTE_ID = 'note_id';

    const string CONTENT = 'content';

    protected $table = 'text_note_contents';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    const string RELATION_NOTE = 'note';

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', Note::ID);
    }
}
