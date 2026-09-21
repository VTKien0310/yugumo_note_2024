<?php

namespace App\Features\Note\Relationships;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface BelongsToNote
{
    const string RELATION_NOTE = 'note';

    public function note(): BelongsTo;
}
