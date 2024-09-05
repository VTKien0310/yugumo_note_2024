<?php

namespace App\Features\Note\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoteType extends Model
{
    use SoftDeletes;

    const ID = 'id';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    protected $table = 'note_types';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    const RELATION_NOTES = 'notes';

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'type_id', 'id');
    }
}
