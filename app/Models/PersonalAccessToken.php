<?php

namespace App\Models;

use App\Models\Concerns\AsJson;
use App\Models\Concerns\SerializeDate;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use AsJson, SerializeDate;

    /**
     * @var string[]
     */
    protected $visible = [
        'name', 'abilities',
    ];
}
