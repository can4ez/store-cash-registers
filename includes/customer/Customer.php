<?php

namespace Shop;

/**
 * Покупатель
 */
class Customer implements IProcess
{
    private string $name;
    private array $products = [];
    private ?CashRegister $cashRegister = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    /**
     * @return Product
     */
    public function shiftProduct()
    {
        return array_shift($this->products);
    }

    public function findMostEmptyRegister()
    {
        return Shop::getInstance()->getMostEmptyRegister();
    }

    public function process($time): bool
    {
        if ($this->cashRegister === null) {
            $cashRegister = $this->findMostEmptyRegister();
            $cashRegister->addToQueue($this, $time);
            $this->cashRegister = $cashRegister;

            echo "[CUSTOMER:" . $this->name . "] Move to cash register: " . $cashRegister->getId() . " (" . $cashRegister->getQueueCount() . ")<br>";
        }

        return true;
    }
}
