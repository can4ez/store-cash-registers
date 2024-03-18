<?php

namespace Shop;

enum CashRegisterState
{
    case CLOSE;
    case OPEN;
}

class CashRegister
{
    private int $id;
    private array $queue = [];
    private CashRegisterState $state = CashRegisterState::CLOSE;

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
