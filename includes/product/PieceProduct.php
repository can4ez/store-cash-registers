<?php

namespace Shop;

class PieceProduct extends Product
{
    public function getPrice(): float
    {
        return $this->price;
    }
}
