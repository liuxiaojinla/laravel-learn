<?php

namespace App\Support;

use ArrayIterator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class Formatter implements \IteratorAggregate, \ArrayAccess, \Serializable, \JsonSerializable
{
    /**
     * @var string
     */
    const defaultDateFormat = 'Y-m-d H:i:s';

    /**
     * @var array
     */
    protected $type = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * 获取所有属性
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * 设置属性
     * @param string $key
     * @param callable|null $default
     * @return mixed|null
     */
    public function getAttribute($key, $default = null)
    {
        if ($this->hasAttribute($key)) {
            $value = $this->attributes[$key];

            if ($value instanceof Carbon) {
                $value = $value->copy();
            }

            $customMethod = 'get' . Str::studly($key) . 'Attribute';
            if (method_exists($this, $customMethod)) {
                return $this->$customMethod($this->attributes[$key]);
            }

            return $value;
        }

        if (is_callable($default)) {
            $default = $default();
        }

        return $default;
    }

    /**
     * 设置一组属性
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            $this->setAttribute($key, $attribute);
        }

        return $this;
    }

    /**
     * 设置属性
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (!$this->isAllowFill($key)) {
            return $this;
        }

        $newKey = Str::camel($key);

        $customMethod = 'set' . Str::studly($key) . 'Attribute';
        if (method_exists($this, $customMethod)) {
            $this->attributes[$newKey] = $this->$customMethod($value);

            return $this;
        }

        $type = $this->resolveAttributeType($newKey);

        $this->attributes[$newKey] = $this->writeTransform($type, $value);

        return $this;
    }

    /**
     * 是否允许填充
     * @param string $key
     * @return bool
     */
    public function isAllowFill($key)
    {
        $key = Str::camel($key);

        return isset($this->type[$key]);
    }

    /**
     * 解析属性类型
     * @param string $key
     * @return mixed|string
     */
    protected function resolveAttributeType($key)
    {
        return $this->type[$key];
    }

    /**
     * 设置属性类型
     * @param array $types
     * @return $this
     */
    public function setAttributeTypes(array $types)
    {
        $this->type = array_merge($this->type, $types);

        return $this;
    }

    /**
     * 写入转换
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function writeTransform($type, $value)
    {
        switch ($type) {
            case 'int':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'string':
                return strval($value);
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
                return (array) $value;
            case 'object':
                return (object) $value;
            case 'json':
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            case 'datetime':
                return $this->writeDatetimeTransform($value);
        }

        throw new \RuntimeException('不支持的类型');
    }

    /**
     * 日期格式转换
     * @param mixed $value
     * @return \Carbon\Carbon|false|Carbon
     */
    protected function writeDatetimeTransform($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        return Carbon::createFromFormat(static::defaultDateFormat, $value);
    }

    /**
     * 属性是否存在
     * @param string $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * 移除属性
     * @param string $key
     * @return $this
     */
    public function removeAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            $value = $this->attributes[$key];
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * 移除指定的元素
     * @param array $keys
     * @return $this
     */
    public function removeAttributes($keys)
    {
        foreach ($keys as $key) {
            $this->removeAttribute($key);
        }

        return $this;
    }

    /**
     * 移除包含元素
     * @param array $keys
     * @param array $with
     * @return $this
     */
    public function removeWith(array $keys, $with = [])
    {
        $self = $this->with($with);
        $self->removeAttributes($keys);

        return $this;
    }

    /**
     * 获取所有属性
     * @return array
     */
    public function toArray()
    {
        return $this->getAttributes();
    }

    /**
     * @param string $name
     * @return callable|mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasAttribute($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return callable|mixed|void|null
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        if ($prefix == 'get') {
            $key = substr($name, 3);

            return $this->getAttribute($key, ...$arguments);
        } elseif ($prefix == 'set') {
            $key = substr($name, 3);
            $this->setAttribute($key, ...$arguments);

            return;
        }

        throw new \BadMethodCallException(static::class . "->{$name}() not exists.");
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->removeAttribute($offset);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($data)
    {
        $this->attributes = unserialize($data);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return json_encode($this->attributes, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * 使用当前实例产生一个新的实例
     * @param array $attributes
     * @return $this
     */
    public function with($attributes = [])
    {
        $self = new static();
        $self->attributes = $this->attributes;
        $self->setAttributes($attributes);

        return $self;
    }

    /**
     * 拷贝当前实例
     * @return $this
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * 验证数据合法性
     */
    public function validate()
    {
        $validate = Validator::make(
            $this->attributes,
            $this->getValidateRules(),
            $this->getValidateMessages(),
            $this->getValidateFields()
        );

        $validate->validate();
    }

    /**
     * 获取验证规则
     * @return array
     */
    protected function getValidateRules()
    {
        return [];
    }

    /**
     * 获取验证消息
     * @return array
     */
    protected function getValidateMessages()
    {
        return [];
    }

    /**
     * 获取验证字段映射
     * @return array
     */
    protected function getValidateFields()
    {
        return [];
    }

    /**
     * 根据数组创建新的实例
     * @param array $attributes
     * @param callable $initialize
     * @return static
     */
    public static function fromArray($attributes, $initialize = null)
    {
        $formatter = new static();

        if ($initialize) {
            app()->call($initialize, ['formatter' => $formatter]);
        }

        $formatter->setAttributes($attributes);

        return $formatter;
    }

    /**
     * 根据stdClass创建新的实例
     * @param \stdClass $attributes
     * @param callable $initialize
     * @return static
     */
    public static function fromObject($attributes, $initialize = null)
    {
        return static::fromArray((array) $attributes, $initialize);
    }

    /**
     * 使用自身实例创建新的实例
     * @param self $attributes
     * @param callable $initialize
     * @return static
     */
    public static function fromSelf(self $attributes, $initialize = null)
    {
        return static::fromArray($attributes->getAttributes(), $initialize);
    }
}
