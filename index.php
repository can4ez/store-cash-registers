<?php

include __DIR__ . "/includes/Shop.php";

use Shop\Shop;

$shop = new Shop('5ka');

$shop->addProduct(new \Shop\WeightProduct('Помидоры', 250));
$shop->addProduct(new \Shop\PieceProduct('Чипсы лейс с медом', 150));
$shop->addProduct(new \Shop\PieceProduct('Молоко 2% 1л', 53));

$customerIndex = 1;
$lastShow = 8;

$tickStep = 1; // Количество минут за 1 итерацию

for ($time = 0; ; $time += $tickStep) {

    if ($time % 60 === 0) {
        echo "Магазин работает: " . \Shop\Time::format($time) . " ч. <br>";
        $lastShow = $time;
    }

    echo "<br>--- start loop ---<br>";

    for ($k = 0; $k < random_int(0, 2); $k++) {
        $newCustomer = new \Shop\Customer('Покупатель #' . $customerIndex++);
        $shop->addCustomer($newCustomer);
    }

    $shop->process($time, $tickStep);

    // А мы обслуживаем "последних покупателей" или нет?
    if ($time >= 8 * 60) break;
    // if ($time >= 21 && $shop->getCustomersCount() == 0) break;

    $shop->showStatus();

    echo "--- end loop ---<br>";
}

echo PHP_EOL . "<hr> End day... <br> Total customers: " . $customerIndex;

//echo "<pre>";
//var_dump($shop);
//echo "</pre>";
