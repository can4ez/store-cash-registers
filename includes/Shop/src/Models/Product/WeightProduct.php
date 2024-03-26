<?php

namespace Shop\Models\Product;

class WeightProduct extends Product
{
    public function getPrice(): float
    {
        return $this->price;
    }

    public function getTimeToProcess(): float
    {
        return self::timeProcess;
    }

    public function clone(): Product
    {
        return new self($this->name, $this->price);
    }
}
