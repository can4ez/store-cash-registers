<?php

namespace Shop;

class Cashier
{
    private string $name;
    private CashRegister $register;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function processCustomer(): bool
    {
        // Логика обработки покупателя
        return true;
    }

    /**
     * @param CashRegister $register
     */
    public function setRegister(CashRegister $register): void
    {
        $this->register = $register;
    }

    private function __destruct()
    {
        $this->register = null;
    }
}
