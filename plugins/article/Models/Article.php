<?php

namespace Plugins\article\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    public static function getSearchFields()
    {
        return ['keywords'];
    }

    public static function getMakeRepositoryConfig()
    {
        return [
            'model' => static::class,
        ];
    }

}