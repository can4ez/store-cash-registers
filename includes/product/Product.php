<?php

namespace Shop;
include __DIR__ . "/PieceProduct.php";
include __DIR__ . "/WeightProduct.php";

abstract class Product
{
    /**
     * Сколько минут продавец тратит на 1 товар?
     */
    const timeProcess = 1;

    protected string $name;
    protected float $price;

    protected float $elapsedTime = 0;

    public function __construct(string $name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * @return float
     */
    public abstract function getPrice(): float;

    public abstract function getTimeToProcess(): float;

    public function process(&$tickStep): bool
    {
        // Сколько осталось для заверщения обработки товара
        $timeLeft = $this->getTimeToProcess() - $this->elapsedTime;

        if (($tickStep - $timeLeft) >= 0) {
            // У нас есть время чтобы полностью обработать этот товар
            $tickStep -= $timeLeft;
            $this->elapsedTime = $this->getTimeToProcess();
            return true;
        }

        // Тратим время на частичную обработку этого товара
        $this->elapsedTime += $tickStep;
        $tickStep = 0;
        return false;
    }

    public function toString(): string
    {
        if ($this->elapsedTime != 0) {
            $timeLeft = $this->getTimeToProcess() - $this->elapsedTime;
            return "{$this->name} - {$this->getPrice()} руб. [" . round($this->elapsedTime / $this->getTimeToProcess() * 100, 2) . "%]";
        }

        return "{$this->name} - {$this->getPrice()} руб.";
    }
}
