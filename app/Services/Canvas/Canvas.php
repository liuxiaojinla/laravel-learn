<?php

namespace App\Services\Canvas;

class Canvas
{
    /**
     * @var \GdImage|resource
     */
    protected $gd;

    /**
     * @var Color
     */
    protected $backgroundColor;

    /**
     * @var bool
     */
    protected $isEnableAntialias;

    /**
     * @param int $width
     * @param int $height
     * @param Color|null $backgroundColor
     */
    public function __construct($width, $height, Color $backgroundColor = null)
    {
        $this->gd = imagecreatetruecolor($width, $height);

        $this->setEnableAntialias();

        $this->alphaBlending();

        if ($backgroundColor) {
            $this->setBackgroundColor($backgroundColor);
        }
    }

    /**
     * @inerhitDoc
     */
    public function __destruct()
    {
        if ($this->gd) {
            imagedestroy($this->gd);
            unset($this->gd);
        }
    }

    /**
     * 设置背景
     * @param Color $backgroundColor
     */
    public function setBackgroundColor(Color $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;

        $backgroundColor = imagecolorallocatealpha(
            $this->gd,
            $backgroundColor->getRed(),
            $backgroundColor->getGreen(),
            $backgroundColor->getBlue(),
            $backgroundColor->getAlpha()
        );
        imagefill($this->gd, 0, 0, $backgroundColor);
    }

    /**
     * 启用抗锯齿
     * @param int $enable
     * @return void
     */
    public function setEnableAntialias($enable = true)
    {
        $this->isEnableAntialias = $enable;
        imageantialias($this->gd, $enable);
    }

    /**
     * 画图形
     * @param Draw $draw
     */
    public function draw(Draw $draw)
    {
        $draw->onDraw($this);
    }

    /**
     * @return \GdImage|resource
     */
    public function getGd()
    {
        return $this->gd;
    }

    /**
     * 关闭 alpha 渲染并设置 alpha 标志，必须将 alphablending 清位
     * @return void
     */
    protected function saveAlpha()
    {
        imagealphablending($this->gd, false);
        imagesavealpha($this->gd, true);
    }

    /**
     * 打开 alpha 渲染并设置 alpha 标志，必须将 savealpha 清位
     * @return void
     */
    protected function alphaBlending()
    {
        imagesavealpha($this->gd, false);
        imagealphablending($this->gd, true);
    }

    /**
     * @param string $filepath
     * @param string $type
     * @param ...$args
     * @return bool
     */
    public function save($filepath, $type = 'png', ...$args)
    {
        $this->saveAlpha();

        $method = "image{$type}";
        $flag = $method($this->gd, $filepath, ...$args);

        // $this->alphaBlending();

        return $flag;
    }

    /**
     * @return string
     */
    public function toData()
    {
        $this->saveAlpha();

        ob_start();
        imagepng($this->gd);
        $data = ob_get_contents();
        ob_end_clean();

        $this->alphaBlending();

        return $data;
    }
}
