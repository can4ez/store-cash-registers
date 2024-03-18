<?php

namespace Shop;
include __DIR__ . "/Cashier.php";
include __DIR__ . "/CashRegister.php";
include __DIR__ . "/product/Product.php";
include __DIR__ . "/customer/Customer.php";


class Shop
{
    const registersCount = 3;

    private string $name;
    private array $registers = [];

    /**
     * @param string $name - Название магазина
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        for ($i = 0; $i < self::registersCount; $i++) {
            $this->registers[] = new CashRegister($i);
        }
    }
}
