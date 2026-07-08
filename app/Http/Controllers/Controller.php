<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\EscapesLikeWildcards;

abstract class Controller
{
    use EscapesLikeWildcards;
}
