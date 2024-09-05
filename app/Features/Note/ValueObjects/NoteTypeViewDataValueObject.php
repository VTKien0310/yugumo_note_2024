<?php

namespace App\Features\Note\ValueObjects;

use App\Extendables\Core\Utils\ValueObject;
use App\Features\Note\Models\NoteType;
use Illuminate\Support\Facades\Vite;

class NoteTypeViewDataValueObject extends ValueObject
{
    public readonly string $name;

    public readonly string $description;

    public readonly string $illustrationPath;

    public readonly string $illustrationAlt;

    public function __construct(NoteType $noteType)
    {
        $this->name = $noteType->name;
        $this->description = $noteType->description;
        $this->illustrationPath = Vite::asset($noteType->illustration_path);
        $this->illustrationAlt = $noteType->name.' illustration';
    }
}
