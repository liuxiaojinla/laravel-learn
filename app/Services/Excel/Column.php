<?php

namespace App\Services\Excel;

class Column
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $width = -1;

    /**
     * @var bool
     */
    protected $autoSize = false;

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * @var callable
     */
    protected $valueResolver;

    /**
     * @param string $key
     * @param string $title
     * @param string $type
     */
    public function __construct($key, $title = null, $type = 'string')
    {
        $this->key = $key;
        $this->title = $title === null ? $key : $title;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoSize()
    {
        return $this->autoSize;
    }

    /**
     * @param bool $autoSize
     * @return $this
     */
    public function setAutoSize($autoSize)
    {
        $this->autoSize = $autoSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     * @return $this
     */
    public function setStyles(array $styles)
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasValueResolver()
    {
        return $this->valueResolver != null;
    }

    /**
     * @return callable
     */
    public function getValueResolver()
    {
        return $this->valueResolver;
    }

    /**
     * @param callable $valueResolver
     */
    public function setValueResolver(callable $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    /**
     * @param string $key
     * @param string $title
     * @param string $type
     * @return static
     */
    public static function create($key, $title = null, $type = 'string')
    {
        return new static($key, $title, $type);
    }
}
