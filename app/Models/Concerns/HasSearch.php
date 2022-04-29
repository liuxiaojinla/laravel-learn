<?php

namespace App\Models\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasSearch
{
    /**
     * @var string[]
     */
    protected $likeFields = [];

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
                    if ($field == static::getSearchKeywordsParameterName()) {
                        $this->searchKeywordsAttribute($query, $value, $data);
                    } elseif (in_array($fieldName, $this->likeFields)) {
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
     * @param string $value
     * @param array $data
     * @return void
     */
    protected function searchKeywordsAttribute(Builder $query, $value, $data)
    {
        $query->where(static::getSearchKeywordsFieldName(), 'like', "%{$value}%");
    }

    /**
     * 获取要支持搜索的字段
     * @return string
     */
    public static function getSearchKeywordsFieldName()
    {
        return 'title';
    }

    /**
     * 获取要支持搜索的参数名
     * @return string
     */
    public static function getSearchKeywordsParameterName()
    {
        return 'keywords';
    }
}