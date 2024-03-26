<?php

namespace Shop\Models\CasheRegister;

use Shop\Interfaces\IProcess;
use Shop\Models\Cashier\Cashier;
use Shop\Models\Customer\Customer;
use Shop\Shop;
use Shop\Utils\Utils;

class CashRegister implements IProcess
{
    const maxEmptyMinutes = 10;

    private int $id;
    private array $queue = [];
    private CashRegisterState $state = CashRegisterState::CLOSE;
    private Cashier $cashier;
    private int $lastQueuePulled = 0;

    public function __construct(int $id, Cashier $cashier)
    {
        $this->id = $id;
        $this->cashier = $cashier;
    }

    public function addToQueue(Customer $customer, int $time): int|bool
    {
        if ($this->state !== CashRegisterState::OPEN) return false;
        if (in_array($customer, $this->queue)) return false;

        $this->queue[] = $customer;

        return count($this->queue) - 1;
    }

    public function getFirstFromQueue(): ?Customer
    {
        if (count($this->queue) === 0) return null;
        return $this->queue[array_key_first($this->queue)];
    }

    public function removeFromQueue(Customer $customer): bool
    {
        $this->queue = array_filter($this->queue, function ($item, $key) use ($customer) {
            return $item !== $customer;
        }, ARRAY_FILTER_USE_BOTH);

        return true;
    }

    public function open(): bool
    {
        $this->state = CashRegisterState::OPEN;
        Utils::debug($this->cashier->getName() . " открыл свою кассу");
        return true;
    }

    public function close(): bool
    {
        $this->state = CashRegisterState::CLOSE;
        Utils::debug($this->cashier->getName() . " закрыл свою кассу");
        return true;
    }

    public function getQueueCount(): int
    {
        return count($this->queue);
    }

    /**
     * @return CashRegisterState
     */
    public function getState(): CashRegisterState
    {
        return $this->state;
    }

    public function process($time, $tickStep): bool
    {
        if ($this->getState() !== CashRegisterState::OPEN) return false;

        if ($this->getQueueCount() === 0) {
            if (($time - $this->lastQueuePulled) >= self::maxEmptyMinutes) {
                $this->close();
            }
            return true;
        }

        $this->lastQueuePulled = $time;

        $customer = $this->cashier->getCurrentCustomer();
        if ($customer == null) {
            $customer = $this->getFirstFromQueue();
        }

        if ($this->cashier->process($time, $tickStep, $customer)) {
            $this->removeFromQueue($customer);
            Shop::getInstance()->removeCustomer($customer);
        }

        return true;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLastQueuePulled(): int
    {
        return $this->lastQueuePulled;
    }
}
