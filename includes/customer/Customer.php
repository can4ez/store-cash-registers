<?php

namespace Shop;

/**
 * Покупатель
 */
class Customer
{
    private string $name;
    private array $products = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function findMostEmptyRegister()
    {
        return Shop::getInstance()->getMostEmptyRegister();
    }

    public function process($time)
    {

    }
}
