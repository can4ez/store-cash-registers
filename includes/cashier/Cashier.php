<?php

namespace Shop;

class Cashier implements IProcessData
{
    private string $name;
    private CashRegister $register;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param CashRegister $register
     */
    public function setRegister(CashRegister $register): void
    {
        $this->register = $register;
    }

    /**
     * @param int $time
     * @param Customer $data
     * @return bool
     */
    public function process($time, $data): bool
    {
//        if(($data instanceof Customer) === false) {
//            // throw new \Exception();
//            return false;
//        }

        while ($product = $data->shiftProduct()) {
            echo "Product price: " . $product->getPrice() . " <br>";
        }

        // Логика обработки покупателя
        return true;
    }
}
