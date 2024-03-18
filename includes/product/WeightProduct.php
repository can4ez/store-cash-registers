<?php

namespace Shop;

class WeightProduct extends Product
{
    public function getPrice(): float
    {
        return $this->price;
    }
}
