<?php

namespace Shop\Utils\Akima;

// Класс реализующий сплайн Акимы
class AkimaSpline
{
    private $x;
    private $y;
    private $akima;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->akima = new AkimaInterpolation($x, $y);
    }

    public function setPoints($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->akima->setPoints($x, $y);
    }

    public function interpolate($xValue)
    {
        return $this->akima->interpolate($xValue);
    }
}
