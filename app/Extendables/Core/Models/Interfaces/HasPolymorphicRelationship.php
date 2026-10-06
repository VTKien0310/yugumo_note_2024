<?php

namespace App\Extendables\Core\Models\Interfaces;

interface HasPolymorphicRelationship
{
    /**
     * Get the morph type value of the model used in polymorphic relationships.
     */
    public static function morphType(): string;
}
