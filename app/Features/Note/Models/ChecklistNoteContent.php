<?php

namespace App\Features\Note\Models;

use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Extendables\Core\Utils\BoolIntValueEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistNoteContent extends Model
{
    use SoftDeletes,
        UlidEloquent;

    const string ID = 'id';

    const string NOTE_ID = 'note_id';

    const string CONTENT = 'content';

    const string IS_COMPLETED = 'is_completed';

    protected $table = 'checklist_note_contents';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => BoolIntValueEnum::class,
        ];
    }

    const string RELATION_NOTE = 'note';

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', Note::ID);
    }
}
