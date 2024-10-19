<?php

namespace App\Features\Note\Models;

use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Features\NoteType\Models\NoteType;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes,
        UlidEloquent;

    const string ID = 'id';

    const string USER_ID = 'user_id';

    const string TYPE_ID = 'type_id';

    const string TITLE = 'title';

    protected $table = 'notes';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

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
}
