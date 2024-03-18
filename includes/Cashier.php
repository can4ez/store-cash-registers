<?php

namespace Shop;

class Cashier
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function processCustomer(Customer $customer): void
    {
        // Логика обработки покупателя
    }
}
