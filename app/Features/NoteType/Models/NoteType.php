<?php

namespace App\Features\NoteType\Models;

use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoteType extends Model
{
    use SoftDeletes;

    const string ID = 'id';

    const string NAME = 'name';

    const string DESCRIPTION = 'description';

    const string ILLUSTRATION_PATH = 'illustration_path';

    protected $table = 'note_types';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    const string RELATION_NOTES = 'notes';

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'type_id', 'id');
    }
}
