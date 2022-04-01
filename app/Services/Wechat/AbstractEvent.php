<?php

namespace App\Services\Wechat;

use Illuminate\Queue\SerializesModels;

class AbstractEvent implements \JsonSerializable, \ArrayAccess
{
    use SerializesModels;

    /**
     * 回调事件的消息
     * @var array
     */
    public $message;

    /**
     * 创建一个事件实例
     *
     * @param array $message
     * @return void
     */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    /**
     * 获取消息属性
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMessageAttribute($key, $default = null)
    {
        return $this->message[$key] ?? $default;
    }

    /**
     * 消息属性是否存在
     * @param string $key
     * @return bool
     */
    public function hasMessageAttribute($key)
    {
        return isset($this->message[$key]);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        $this->hasMessageAttribute($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->getMessageAttribute($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->message;
    }

    /**
     * @param array $transformKeys
     * @return array
     */
    public function toArray($transformKeys = [])
    {
        $result = [];

        foreach ($this->message as $key => $value) {
            $newKey = $transformKeys[$key] ?? $key;
            $result[$newKey] = $value;
        }

        return $result;
    }

    /**
     * @param static $event
     * @return static
     */
    public static function ofEvent(self $event)
    {
        return new static($event->toArray());
    }
}
