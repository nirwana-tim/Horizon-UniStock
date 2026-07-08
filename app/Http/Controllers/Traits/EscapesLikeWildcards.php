<?php

namespace App\Http\Controllers\Traits;

trait EscapesLikeWildcards
{
    protected function escapeLike(string $value): string
    {
        return str_replace(['%', '_'], ['\%', '\_'], $value);
    }
}
