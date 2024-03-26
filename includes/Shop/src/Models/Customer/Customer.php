<?php

namespace Shop\Models\Customer;

use Exception;
use Shop\Interfaces\IProcess;
use Shop\Models\CasheRegister\CashRegister;
use Shop\Models\Product\Product;
use Shop\Shop;
use Shop\Utils\Utils;

/**
 * Покупатель
 */
class Customer implements IProcess
{
    /**
     * Сколько минут покупатель оплачивает твоары
     */
    const timeToPay = 1;

    protected float $elapsedTime = 0;

    private string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    private array $products = [];
    private ?CashRegister $cashRegister = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product->clone();
    }


    public function getFirstProduct(): false|Product
    {
        if (count($this->products) === 0) return false;
        return $this->products[array_key_first($this->products)];
    }

    public function removeProduct($product): bool
    {
        Utils::debug($this->getName() . " Remove product: " . $product->getId());
        $this->products = array_filter($this->products, function ($item) use ($product) {
            Utils::debug($this->getName() . " Check product: " . $item->getId());
            return $item !== $product;
        });
        return true;
    }

    public function getProductsCount()
    {
        return count($this->products);
    }

    public function findMostEmptyRegister()
    {
        return Shop::getInstance()->getMostEmptyRegister();
    }

    public function getTimeToPay(): float
    {
        return self::timeToPay;
    }

    public function pay($time, &$tickStep): bool
    {
        // Сколько осталось для заверщения обработки товара
        $timeLeft = $this->getTimeToPay() - $this->elapsedTime;

        if (($tickStep - $timeLeft) >= 0) {
            // У нас есть время чтобы полностью обработать этот товар
            $tickStep -= $timeLeft;
            $this->elapsedTime = $this->getTimeToPay();

            Utils::debug("&nbsp;&nbsp;&nbsp;&nbsp;"
                . Utils::formatHours($time)
                . " | "
                . $this->name
                . " Оплатил покупки ["
                . round($this->elapsedTime / $this->getTimeToPay() * 100, 2)
                . "%] ");

            return true;
        }

        Utils::debug("&nbsp;&nbsp;&nbsp;&nbsp;"
            . Utils::formatHours($time)
            . " | "
            . $this->name
            . " Оплачивает покупки ["
            . round($this->elapsedTime / $this->getTimeToPay() * 100, 2)
            . "%] ");

        // Тратим время на частичную обработку этого товара
        $this->elapsedTime += $tickStep;
        $tickStep = 0;
        return false;
    }

    public function process($time, $tickStep): bool
    {
        // TODO: Можно добавить состояния посетителя...
        //  1. Выбирает товар
        //  2. Ищет кассу
        //  3. Стоит в очереди
        //  4. Пробивает товары
        //  5. Оплачивает товары
        if ($this->cashRegister === null) {
            // Наполняем продуктовую корзину
            $productsCount = random_int(1, 3);
            while ($productsCount > 0) {
                $product = Shop::getInstance()->getRandomProduct();
                if ($product === false) {
                    throw new Exception("В магазине нет товаров!");
                }
                $this->addProduct($product);
                $productsCount--;
            }

            Utils::debug("&nbsp;" . $this->getName() . " взял " . $this->getProductsCount() . " товара(ов)");

            foreach ($this->products as $product) {
                Utils::debug("&nbsp;&nbsp;" . $this->getName() . " " . $product->toString());
            }

            // Шагаем к первой самой свободной кассе
            $cashRegister = $this->findMostEmptyRegister();
            $cashRegister->addToQueue($this, $time);
            $this->cashRegister = $cashRegister;
        }

        return true;
    }
}
