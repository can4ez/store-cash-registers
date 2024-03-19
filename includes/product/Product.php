<?php

namespace Shop;
include __DIR__ . "/PieceProduct.php";
include __DIR__ . "/WeightProduct.php";

abstract class Product
{
    protected string $name;
    protected float $price;

    public function __construct(string $name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * @return float
     */
    public abstract function getPrice(): float;

    public function toString()
    {
        return "{$this->name} - {$this->getPrice()} руб.";
    }
}
