<?php

namespace Shop\Utils\Akima;

class AkimaInterpolation
{
    private $x;
    private $y;
    private $m;

    public function __construct($x, $y)
    {
        $this->setPoints($x, $y);
    }

    public function setPoints($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->m = $this->calculateSlopes($x, $y);
    }

    public function interpolate($xValue)
    {
        $n = count($this->x);
        $left = 0;
        $right = $n - 1;

        // Находим интервал, в который попадает xValue
        while ($left < $right) {
            if ($right - $left == 1) {
                break;
            }
            $mid = (int)(($left + $right) / 2);
            if ($this->x[$mid] <= $xValue) {
                $left = $mid;
            } else {
                $right = $mid;
            }
        }

        $h = $this->x[$right] - $this->x[$left];
        $t = ($xValue - $this->x[$left]) / $h;

        $a = $this->y[$left];
        $b = $this->m[$left];
        $c = (3 * ($this->y[$right] - $this->y[$left]) / $h) - (2 * $this->m[$left]) - $this->m[$right];
        $d = (2 * ($this->y[$left] - $this->y[$right]) / ($h * $h)) + ($this->m[$left] + $this->m[$right]);

        return $a + $b * $t + $c * $t * $t + $d * $t * $t * $t;
    }

    private function calculateSlopes($x, $y)
    {
        $n = count($x);
        $m = array_fill(0, $n, 0);
        $delta = [];

        for ($i = 0; $i < $n - 1; $i++) {
            $delta[] = ($y[$i + 1] - $y[$i]) / ($x[$i + 1] - $x[$i]);
        }

        for ($i = 1; $i < $n - 1; $i++) {
            $m[$i] = (($delta[$i - 1] * ($x[$i + 1] - $x[$i]) + $delta[$i] * ($x[$i] - $x[$i - 1])) / ($x[$i + 1] - $x[$i - 1]));
        }

        $m[0] = $this->interpolateFirstThreeSlopes(0, $delta, $m);
        $m[$n - 1] = $this->interpolateLastThreeSlopes($n - 1, $delta, $m);

        return $m;
    }

    private function interpolateFirstThreeSlopes($index, $delta, $m)
    {
        $m0 = (2 * $delta[0]) - $m[1];
        return ((3 * $delta[0]) - (2 * $m[1]) + $m0) / 2;
    }

    private function interpolateLastThreeSlopes($index, $delta, $m)
    {
        $n = count($this->x);

        $mn = (2 * $delta[$n - 2]) - $m[$n - 3];
        return ((3 * $delta[$n - 2]) - (2 * $m[$n - 3]) + $mn) / 2;
    }
}
