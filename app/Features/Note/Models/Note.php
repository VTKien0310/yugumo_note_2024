<?php

namespace App\Features\Note\Models;

use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes,
        UlidEloquent;

    const ID = 'id';

    const USER_ID = 'user_id';

    const TYPE_ID = 'type_id';

    const TITLE = 'title';

    const DESCRIPTION = 'description';

    protected $table = 'notes';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    const RELATION_USER = 'user';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    const RELATION_TYPE = 'type';

    public function type(): BelongsTo
    {
        return $this->belongsTo(NoteType::class, 'type_id', 'id');
    }
}
