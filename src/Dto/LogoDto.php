<?php

namespace Da\QrCode\Dto;

class LogoDto
{
    /**
     * @var \GdImage
     */
    private $image;
    /**
     * @var int
     */
    private $width;
    /**
     * @var int
     */
    private $height;
    /**
     * @var int
     */
    private $targetWidth;
    /**
     * @var int
     */
    private $targetHeight;

    private function __construct($image, $width, $height, $targetWidth, $targetHeight)
    {
        $this->image = $image;
        $this->width = $width;
        $this->height = $height;
        $this->targetWidth = $targetWidth;
        $this->targetHeight = $targetHeight;
    }

    /**
     * @param \GdImage $image
     * @param int $width
     * @param int $height
     * @param int $targetWidth
     * @param int $targetHeight
     * @return LogoDto
     */
    public static function create($image, $width, $height, $targetWidth, $targetHeight): LogoDto
    {
        return new self($image, $width, $height, $targetWidth, $targetHeight);
    }

    /**
     * @return \GdImage
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * @return int
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function targetWidth()
    {
        return $this->targetWidth;
    }

    /**
     * @return int
     */
    public function targetHeight()
    {
        return $this->targetHeight;
    }
}
