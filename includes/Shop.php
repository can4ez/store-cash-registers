<?php

namespace Shop;

include __DIR__ . '/utils/Utils.php';
include __DIR__ . '/utils/AkimaSpline.php';

include __DIR__ . '/interface/IProcess.php';
include __DIR__ . '/interface/IProcessData.php';

include __DIR__ . '/cashier/Cashier.php';
include __DIR__ . '/casheRegister/CashRegister.php';
include __DIR__ . '/product/Product.php';
include __DIR__ . '/customer/Customer.php';


class Shop implements IProcess
{
    public const maxRegistersCount = 40;
    public const maxCustomersOnRegister = 5;

    private static ?Shop $instance = null;

    /**
     * @var string Название магазина
     */
    private string $name;

    /**
     * @var CashRegister[] Список доступных касс
     */
    private array $registers = [];

    /**
     * @var Customer[] Список посетителей в магазине
     */
    private array $customers = [];

    /**
     * @var Products[] Список продуктов в магазине
     */
    private array $products = [];

    /**
     * @param string $name - Название магазина
     */
    public function __construct(string $name)
    {
        if (self::$instance !== null) return self::$instance;

        self::$instance = $this;

        $this->name = $name;

        for ($i = 1; $i <= self::maxRegistersCount; $i++) {
            // Упроситм, что 1 касса = 1 продавец,
            // на деле же продавец может открыть и другую кассу
            $cashier = new Cashier("Кассир #${i}");
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

    public function getOpenedRegister()
    {
        return array_filter($this->registers, function ($register, $key) {
            return $register->getState() === CashRegisterState::OPEN;
        }, ARRAY_FILTER_USE_BOTH);;
    }

    public function tryOpenRegister(&$register): bool
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

        if ($result === null || $result->getQueueCount() > self::maxCustomersOnRegister) {
            if ($this->tryOpenRegister($register) !== false) {
                return $register;
            }
        }

        return $result;
    }

    public function addCustomer($customer): bool|int
    {
        if (in_array($customer, $this->customers)) return false;

        $this->customers[] = $customer;

        \Shop\Utils::debug($customer->getName() . " пришел в магазин");

        return count($this->customers) - 1;
    }

    public function removeCustomer($customer): bool
    {
        $this->customers = array_filter($this->customers, function ($item, $key) use ($customer) {
            return $item !== $customer;
        }, ARRAY_FILTER_USE_BOTH);

        return true;
    }

    public function addProduct($product): bool
    {
        if (in_array($product, $this->products)) return false;

        $this->products[] = $product;

        return true;
    }

    public function getRandomProduct(): false|Product
    {
        if (empty($this->products)) return false;

        return $this->products[array_rand($this->products)];
    }

    public function getCustomersCount(): int
    {
        return count($this->customers);
    }

    public static function getInstance(): ?Shop
    {
        return self::$instance;
    }

    public function process($time, $tickStep): bool
    {
        foreach ($this->customers as $customer) {
            $customer->process($time, $tickStep);
        }

        foreach ($this->registers as $register) {
            $register->process($time, $tickStep);
        }

        return true;
    }

    public function showStatus($time)
    {
        $openRegisters = $this->getOpenedRegister();

        echo "- status - <br>";
        echo "Время: " . Utils::formatHours($time) . "<br>";
        echo "Количество посетителей: " . count($this->customers) . "<br>";
        echo "Количество открытых касс: " . count($openRegisters) . "<br>";

        foreach ($openRegisters as $register) {
            echo "&nbsp; Касса #" . $register->getId() . " открыта, размер очереди: " . $register->getQueueCount() . " ";
            echo "(Время с посл. кл-а " . ($time - $register->getLastQueuePulled()) . " мин.)<br>";
        }

        echo "- status - <br>";
    }
}
