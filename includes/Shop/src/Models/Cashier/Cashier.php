<?php
namespace Shop\Models\Cashier;

use Shop\Interfaces\IProcessData;
use Shop\Models\CasheRegister\CashRegister;
use Shop\Models\Customer\Customer;
use Shop\Models\Product\Product;
use Shop\Utils\Utils;

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

    public function processProduct($time, &$timeLeft): bool
    {
        if ($this->currentProduct->process($timeLeft) === true) {
            Utils::debug("&nbsp;&nbsp;&nbsp;&nbsp;" . Utils::formatHours($time) . " | "
                . $this->name . " > "
                . $this->currentCustomer->getName()
                . " (" . $this->currentCustomer->getProductsCount() - 1 . ") |"
                . " Кассир пробил товар: " . $this->currentProduct->toString());
            return true;
        }

        Utils::debug("&nbsp;&nbsp;&nbsp;&nbsp;" . Utils::formatHours($time) . " | "
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

        while ($timeLeft > 0 && $this->currentCustomer->getProductsCount() > 0) {
            if ($this->currentProduct === null) {
                $this->currentProduct = $this->currentCustomer->getFirstProduct();
            }

            if ($this->processProduct($time, $timeLeft)) {
                $this->currentCustomer->removeProduct($this->currentProduct);
                $this->currentProduct = null;
            }
        }

        Utils::debug("CashierProcess: " . $this->currentCustomer->getName());

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
