<?php

namespace App\Features\Note\Actions;

use App\Features\Note\Models\NoteType;
use Illuminate\Database\Eloquent\Collection;

class GetAllNoteTypeAction
{
    public function handle(): Collection
    {
        return NoteType::all();
    }
}
