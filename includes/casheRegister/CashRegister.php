<?php

namespace Shop;

enum CashRegisterState
{
    case CLOSE;
    case OPEN;
}

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
        $this->lastQueuePulled = $time;

        return count($this->queue) - 1;
    }

    public function getFirstFromQueue(): false|Customer
    {
        if (count($this->queue) === 0) return false;
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
        return true;
    }

    public function close(): bool
    {
        $this->state = CashRegisterState::CLOSE;
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

    public function process($time): bool
    {
        $customer = $this->getFirstFromQueue();
        if ($customer !== false) {
            if ($this->cashier->process($time, $customer) === false) {
                return false;
            }
            $this->removeFromQueue($customer);
        }

        if (($time - $this->lastQueuePulled) >= self::maxEmptyMinutes) {
            $this->close();
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
}
