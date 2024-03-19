<?php

include __DIR__ . "/includes/Shop.php";

use Shop\Shop;

$shop = new Shop('5ka');
$customerIndex = 0;

for ($time = 8; ; $time += 1 / 60) {

    $shop->process($time);

    if ($time >= 21 && $shop->) break;

    for ($k = 0; $k < random_int(0, 10); $k++) {
        $newCustomer = new \Shop\Customer('Customer_' . $customerIndex++);
        $shop->addCustomer($newCustomer);
    }
}

//echo "<pre>";
//var_dump($shop);
//echo "</pre>";
