<?php

namespace Shop;
include __DIR__ . '/Cashier.php';
include __DIR__ . '/CashRegister.php';
include __DIR__ . '/product/Product.php';
include __DIR__ . '/customer/Customer.php';
include __DIR__ . '/interface/IProcess.php';


class Shop implements IProcess
{
    public const registersCount = 3;
    public const maxCustomersOnRegister = 5;

    private static ?Shop $instance = null;

    private string $name;

    /**
     * @var CashRegister[]
     */
    private array $registers = [];

    /**
     * @var Customer[]
     */
    private array $customers = [];

    /**
     * @param string $name - Название магазина
     */
    public function __construct(string $name)
    {
        if (self::$instance !== null) return self::$instance;

        $this->name = $name;

        for ($i = 0; $i < self::registersCount; $i++) {
            // Упроситм, что 1 касса = 1 продавец,
            // на деле же продавец может открыть и другую кассу
            $cashier = new Cashier(`Cashier_${i}`);
            $register = new CashRegister($i, $cashier);
            $this->registers[] = $register;
            $cashier->setRegister($register);
        }
    }

    public function findClosedRegister()
    {
        foreach ($this->registers as $register) {
            if ($register->getState() === CashRegisterState::CLOSE) return $register;
        }

        return false;
    }

    public function tryOpenRegister(&$register): false|CashRegister
    {
        $register = $this->findClosedRegister();
        if ($register === false) {
            return false;
        }

        return $register->open();
    }

    /*
     * Поиск свободной кассы
     */
    public function getMostEmptyRegister()
    {
        $result = null;
        foreach ($this->registers as $item) {
            if ($item->getState() === CashRegisterState::CLOSE) continue;

            if (!$result || $item->getQueueCount() < $result->getQueueCount()) {
                $result = $item;
            }
        }

        if ($result->getQueueCount() > self::maxCustomersOnRegister) {
            if ($this->tryOpenRegister($register) !== false) {
                return $register;
            }
        }

        return $result;
    }

    public function addCustomer($customer)
    {
        if (in_array($customer, $this->customers)) return false;

        $this->customers[] = $customer;

        return count($this->customers) - 1;
    }

    public function removeCustomer($customer)
    {
        $this->customers = array_filter($this->customers, function ($key, $item) use ($customer) {
            return $item !== $customer;
        }, ARRAY_FILTER_USE_BOTH);

        return true;
    }

    public static function getInstance(): ?Shop
    {
        return self::$instance;
    }

    public function process($time)
    {
        foreach ($this->registers as $register) {
            $register->process($time);
        }
    }
}
