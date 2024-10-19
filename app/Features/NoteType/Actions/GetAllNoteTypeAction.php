<?php

namespace App\Features\NoteType\Actions;

use App\Features\NoteType\Models\NoteType;
use Illuminate\Database\Eloquent\Collection;

class GetAllNoteTypeAction
{
    public function handle(): Collection
    {
        return NoteType::all();
    }
}
