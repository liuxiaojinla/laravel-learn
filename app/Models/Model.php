<?php

namespace App\Models;

use App\Models\Concerns\AsJson;
use App\Models\Concerns\HasSearch;
use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @method $this|Builder search(array $data)
 */
class Model extends BaseModel
{
    use AsJson, SerializeDate,
        HasSearch;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 20;


}
