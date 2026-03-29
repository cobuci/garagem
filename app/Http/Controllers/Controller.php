<?php

namespace App\Http\Controllers;

use App\Http\Responses\Api\Concerns\HasApiResponses;

abstract class Controller
{
    use HasApiResponses;
}
