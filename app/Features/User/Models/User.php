<?php

namespace App\Features\User\Models;

use App\Extendables\Core\Models\Traits\UlidEloquent;
use App\Features\Note\Models\Note;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable,
        SoftDeletes,
        UlidEloquent;

    const ID = 'id';

    const NAME = 'name';

    const EMAIL = 'email';

    const PASSWORD = 'password';

    const REMEMBER_TOKEN = 'remember_token';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    const RELATION_NOTES = 'notes';

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'user_id', 'id');
    }
}
