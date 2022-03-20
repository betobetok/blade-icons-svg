<?php

declare(strict_types=1);

namespace ASK\Svg\Commands;

use Error;

/**
 * A comand "a" in a d attribute of a svg path
 * 
 * A rx ry x-axis-rotation large-arc-flag sweep-flag x y
 * a rx ry x-axis-rotation large-arc-flag sweep-flag dx dy
 */
class A extends Command
{

    public function initialization()
    {
        /** a command a must have parameters in multiples of 7 */
        if (count($this->attributes) % 7 > 0) {
            throw new Error('Incorrect configuration of attributes');
        }

        $count = 0;
        foreach ($this->attributes as $k => $condition) {
            switch ($k % 7) {
                case 0:
                    $coordinates[$count]['rx'] = $condition;
                    break;
                case 1:
                    $coordinates[$count]['ry'] = $condition;
                    break;
                case 2:
                    $coordinates[$count]['xRotation'] = $condition;
                    break;
                case 3:
                    $coordinates[$count]['large'] = $condition;
                    break;
                case 4:
                    $coordinates[$count]['sweep'] = $condition;
                    break;
                case 5:
                    $coordinates[$count]['x'] = $condition;
                    break;
                case 6:
                    $coordinates[$count]['y'] = $condition;
                    $count++;
                    break;
            }
        }
        $this->coordinates = $coordinates;
        $this->count = $count;
        $absolutePoint = $this->getEndPoint();
        $this->resetNext();
        $relativePoint = $this->getEndPoint(false);
        $this->resetNext();
        $this->setEndPoint($relativePoint, $absolutePoint);
        unset($this->attributes);
    }

    /**
     * getCenter get the centero of the n arc 
     *
     * @param  int $n the arc number of which we want the center 
     * @return void
     */
    public function getCenter(int $n = null)
    {
        if ($n >= $this->count) {
            throw new Error("Point doesn't exist, max position: " . $this->count, 1);
        }

        if ($n === null) {
            $n = $this->nextPoint;
            if ($this->nextPoint >= $this->count) {
                $this->nextPoint = 0;
            } else {
                $this->nextPoint++;
            }
        }

        /** first point aPoint, if ist the first parameters group in the a command, the aPoint must be taken from the previus command */
        if ($n < 1) {
            $aPoint = $this->prev->getEndPoint();
        } else {
            $aPoint = $this->getPoint($n - 1);
        }

        /** last point bPoint*/
        $bPoint = $this->getPoint($n);

        $angle = $this->coordinates[$n]['xRotation'] * 180 / pi();

        if ($angle > 0) {
            $aPoint = [
                'x' => $aPoint['x'] * cos($angle) + $aPoint['y'] * sin($angle),
                'y' => $aPoint['y'] * cos($angle) - $aPoint['x'] * sin($angle),
            ];

            $bPoint = [
                'x' => $bPoint['x'] * cos($angle) + $bPoint['y'] * sin($angle),
                'y' => $bPoint['y'] * cos($angle) - $bPoint['x'] * sin($angle),
            ];
        }

        $arcRatio = (pow($this->coordinates[$n]['rx'], 2) / pow($this->coordinates[$n]['ry'], 2));
        $dx = ($aPoint['x'] - $bPoint['x']);
        $dy = ($aPoint['y'] - $bPoint['y']);

        $h = (($arcRatio * $dy) + pow($aPoint['x'], 2) - pow($bPoint['x'], 2)) / (2 * $dx);
        $k = $bPoint['y'] + pow($this->coordinates[$n]['rx'], 2) + ($arcRatio * pow(($bPoint['x'] - $h), 2));

        return [
            'x' => $h,
            'y' => $k
        ];
    }
}
