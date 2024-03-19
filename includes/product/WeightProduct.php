<?php

namespace Shop;

class WeightProduct extends Product
{
    public function getPrice(): float
    {
        return $this->price;
    }

    public function getTimeToProcess(): float
    {
        return self::timeProcess * 1.2;
    }
}
