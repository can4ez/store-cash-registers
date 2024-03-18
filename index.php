<?php

include __DIR__ . "/includes/Shop.php";

use Shop\Shop;

$shop = new Shop('test');

echo "<pre>";
var_dump($shop);
echo "</pre>";
