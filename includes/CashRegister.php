<?php

namespace Shop;

class CashRegister
{
    private int $id;
    private array $queue = [];

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function addToQueue(Customer $customer): void
    {
        $this->queue[] = $customer;
    }

    public function removeFromQueue(): Customer
    {
        return array_shift($this->queue);
    }
}