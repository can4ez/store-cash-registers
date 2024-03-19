<?php

namespace Shop;

class Cashier implements IProcessData
{
    private string $name;
    private CashRegister $register;

    private ?Product $currentProduct = null;
    private ?Customer $currentCustomer = null;

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

    public function processProduct($time, &$timeLeft, $product = null): bool
    {
        if ($this->currentProduct === null) {
            $this->currentProduct = $product;
        }

        if ($this->currentProduct->process($timeLeft) === true) {
            \Shop\Utils::debug("&nbsp;&nbsp;&nbsp;&nbsp;" . Utils::formatHours($time) . " | "
                . $this->name . " > "
                . $this->currentCustomer->getName()
                . " (" . $this->currentCustomer->getProductsCount() - 1 . ") |"
                . " Кассир пробил товар: " . $this->currentProduct->toString());
            $this->currentProduct = null;
            return true;
        }

        \Shop\Utils::debug("&nbsp;&nbsp;&nbsp;&nbsp;" . Utils::formatHours($time) . " | "
            . $this->name . " > "
            . $this->currentCustomer->getName()
            . " (" . $this->currentCustomer->getProductsCount() . ") |"
            . " Кассир пробивает товар: " . $this->currentProduct->toString());

        return false;
    }

    /**
     * @param int $time
     * @param $tickStep
     * @param Customer $customer
     * @return bool
     */
    public function process($time, $tickStep, $customer): bool
    {
        $timeLeft = $tickStep;

        if ($this->currentCustomer === null) {
            $this->currentCustomer = $customer;
        }

        $product = $this->currentProduct;

        while ($timeLeft > 0 && $this->currentCustomer->getProductsCount() > 0) {
            if ($product === null) {
                $product = $this->currentCustomer->getFirstProduct();
            }

            if ($this->processProduct($time, $timeLeft, $product)) {
                $this->currentCustomer->shiftProduct();
            }
        }

        if ($finish = ($this->currentCustomer->getProductsCount() === 0)) {
            if ($finish = $this->currentCustomer->pay($time, $timeLeft)) {
                $this->currentCustomer = null;
            }
        }

        return $finish;
    }

    /**
     * @return Customer|null
     */
    public function getCurrentCustomer(): ?Customer
    {
        return $this->currentCustomer;
    }

    /**
     * @return Product|null
     */
    public function getCurrentProduct(): ?Product
    {
        return $this->currentProduct;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
