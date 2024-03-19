<?php

include __DIR__ . "/includes/Shop.php";

use Shop\Shop;

$shop = new Shop('5ka');

$shop->addProduct(new \Shop\PieceProduct('Чипсы лейс с медом', 150));
$shop->addProduct(new \Shop\PieceProduct('Молоко 2% 1л', 150));

$customerIndex = 0;
$lastShow = 8;

echo "[=== START ===] <br>";

for ($time = 8; ; $time += 1 / 60) {

    if (($time - $lastShow) >= 1) {
        echo "[=== " . round($time) . " ===]<br>";
        $lastShow = $time;
    }

    $shop->process($time);

    // А мы обслуживаем "последних покупателей" или нет?
    if ($time >= 21) break;
    // if ($time >= 21 && $shop->getCustomersCount() == 0) break;

    // if($time < 21 ) {
    for ($k = 0; $k < random_int(0, 2); $k++) {
        $newCustomer = new \Shop\Customer('Customer_' . $customerIndex++);
        $shop->addCustomer($newCustomer);
    }
    // }
}

echo PHP_EOL . "<hr> End day... <br> Total customers: " . $customerIndex - 1;

//echo "<pre>";
//var_dump($shop);
//echo "</pre>";
