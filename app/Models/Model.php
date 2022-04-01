<?php

namespace App\Models;

use App\Models\Concerns\AsJson;
use App\Models\Concerns\SerializeDate;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

/**
 * @method $this|Builder search(array $data)
 */
class Model extends BaseModel
{
    use AsJson, SerializeDate;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * @var string[]
     */
    protected array $likeFields = [];

    /**
     * @return array
     */
    public static function getSearchFields(): array
    {
        return [];
    }

    /**
     * 搜索作用域
     * @param Builder $query
     * @param array $data
     */
    public function scopeSearch(Builder $query, array $data)
    {
        foreach (static::getSearchFields() as $key => $field) {
            if ($field instanceof Closure) {
                $field($this, $data[$key] ?? null, $data);
            } else {
                $value = $data[$field] ?? null;

                // 检测搜索器
                $fieldName = is_numeric($key) ? $field : $key;
                $method = 'search' . Str::studly($fieldName) . 'Attribute';
                if (method_exists($this, $method)) {
                    $this->$method($query, $value, $data);
                } elseif ($value !== null) {
                    if (in_array($fieldName, $this->likeFields)) {
                        $query->where($key, 'like', "%{$value}%");
                    } else {
                        $query->where($fieldName, $value);
                    }
                }
            }
        }
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    public function searchKeywordsAttribute(Builder $query, mixed $value)
    {
        $query->where('title', 'like', "%{$value}%");
    }
}
