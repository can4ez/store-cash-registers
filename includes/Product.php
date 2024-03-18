<?php

namespace Shop;

class Product
{
    private string $name;
    private float $price;

    public function __construct(string $name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }
}
