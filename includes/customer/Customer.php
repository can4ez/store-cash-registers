<?php

namespace Shop;

/**
 * Покупатель
 */
class Customer implements IProcess
{
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
//        echo "[CUSTOMER:" . $this->name . "] Take product: " . $product->toString() . "<br>";
        $this->products[] = $product;
    }


    public function getFirstProduct(): false|Product
    {
        if (count($this->products) === 0) return false;
        return $this->products[array_key_first($this->products)];
    }

    public function shiftProduct()
    {
        return array_shift($this->products);
    }

    public function getProductsCount()
    {
        return count($this->products);
    }

    public function findMostEmptyRegister()
    {
        return Shop::getInstance()->getMostEmptyRegister();
    }

    public function process($time, $tickStep): bool
    {
        if ($this->cashRegister === null) {

            // Наполняем продуктовую корзину
            $productsCount = random_int(1, 3);
            while ($productsCount > 0) {
                $product = Shop::getInstance()->getRandomProduct();
                if ($product === false) {
                    throw new \Exception("В магазине нет товаров!");
                }
                $this->addProduct($product);
                $productsCount--;
            }

            echo "&nbsp;" . $this->getName() . " взял " . $this->getProductsCount() . " товара(ов) <br>";

            // Шагаем к первой самой свободной кассе
            $cashRegister = $this->findMostEmptyRegister();
            $cashRegister->addToQueue($this, $time);
            $this->cashRegister = $cashRegister;

//            echo "[CUSTOMER:" . $this->name . "] Move to cash register: " . $cashRegister->getId() . " (" . $cashRegister->getQueueCount() . ")<br>";

        }

        return true;
    }
}
