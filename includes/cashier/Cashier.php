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
        // Логика обработки покупателя
//        if(($data instanceof Customer) === false) {
//            // throw new \Exception();
//            return false;
//        }

        echo "[CASHIER:" . $this->name . "] Start process customer " . $data->getName() . " (products: " . $data->getProductsCount() . ")<br>";

        while ($product = $data->shiftProduct()) {
            echo "[CASHIER:" . $this->name . "] Process product: " . $product->toString() . " <br>";
        }

        echo "[CASHIER:" . $this->name . "] Finish process customer " . $data->getName() . " (products: " . $data->getProductsCount() . ")<br>";

        return true;
    }
}
