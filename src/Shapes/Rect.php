<?php

declare(strict_types=1);

namespace BladeUI\Icons\Shapes;

use NumPHP\Core\NumArray;

class Rect extends Shape
{
    /** @var float $x */
    protected float $x;

    /** @var float $y */
    protected float $y;

    /** @var float $width */
    protected float $width;

    /** @var float $height */
    protected float $height;

    /** @var float $rx */
    protected float $rx;

    /** @var float $ry */
    protected float $ry;

    /**
     * __construct
     *
     * @param  string $name
     * @param  string $contents
     * @param  array $attributes
     * @param  mixed $context
     * @return void
     */
    public function __construct(string $name, string $contents, array $attributes = [], $context = null)
    {
        parent::__construct($name, $contents, $attributes, $context);
        $att = $this->attributes();
        $this->x = (float)$att['x'];
        $this->y = (float)$att['y'];
        $this->rx = (float)$att['rx'] ?? 0;
        $this->ry = (float)$att['ry'] ?? 0;
        $this->width = (float)$att['width'];
        $this->height = (float)$att['height'];
        $this->removeAtt('x');
        $this->removeAtt('y');
        $this->removeAtt('rx');
        $this->removeAtt('ry');
        $this->removeAtt('width');
        $this->removeAtt('height');
    }

    /**
     * getCenter
     *
     * @return NumArray
     */
    public function getCenter(): NumArray
    {
        return new NumArray([
            'x' => $this->x + $this->width / 2,
            'y' => $this->y + $this->height / 2,
        ]);
    }

    /**
     * getArea
     *
     * @return float
     */
    public function getArea(): float
    {
        return $this->x * $this->y;
    }
}
