<?php

namespace App\Http\Controllers\Traits;

trait EscapesLikeWildcards
{
    protected function escapeLike(string $value): string
    {
        return escapeLike($value);
    }
}
