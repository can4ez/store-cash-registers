<?php

include __DIR__ . "/includes/Shop.php";

use Shop\AkimaSpline;
use Shop\Customer;
use Shop\PieceProduct;
use Shop\Shop;
use Shop\Utils;
use Shop\WeightProduct;

set_time_limit(0);
define('SHOW_DEBUG', false);

$shop = new Shop('5ka');

$shop->addProduct(new WeightProduct('Помидоры', 250));
$shop->addProduct(new PieceProduct('Чипсы лейс с медом', 150));
$shop->addProduct(new PieceProduct('Молоко 2% 1л', 53));

$workStartTime = 60 * 8;
$maxWorkTime = $workStartTime + 60 * 15; // Магазин будет открыт с 8 до 23 часов
$tickStep = 1; // Количество минут за 1 итерацию
$maxCustomers = 10; // Максимальное количество покупателей в пик

$customers = 1; // Количество новых посетителей
$customerIndex = 0;


// График распредения посетителей
$x = [8, 10, 12, 14, 16, 18, 20, 21, 23];
$y = [0, 2, 3, 15, 2, 2, 1, 2, 0];
$curve = new AkimaSpline($x, $y);

for ($time = $workStartTime; ; $time += $tickStep) {
    $hour = round($time / 60);
    $maxCustomers = $curve->interpolate($hour);
    if ($shop->getCustomersCount() >= $maxCustomers) {
        // Сейчас и так "максимум" посетителей для этого часа
        $newCustomers = 0;
    } else {
        // Случайным образом определим, сколько посетителей добавить
        // "< 0" == "0"
        $newCustomers = $maxCustomers - $shop->getCustomersCount();
        $newCustomers = rand(-$maxCustomers, $maxCustomers);
    }

    Utils::debug("--- start loop ---");

    for ($k = 0; $k < $newCustomers; $k++) {
        $newCustomer = new Customer('Покупатель #' . ++$customerIndex);
        $shop->addCustomer($newCustomer);
    }

    $shop->process($time, $tickStep);

    if ($time % 60 === 0) {
        $shop->showStatus($time);
    }

    Utils::debug("--- end loop ---");

    if ($time >= $maxWorkTime) break;
}

echo "<hr>";
echo "Магазин закрылся, время: " . Utils::formatHours($time) . " ч. <br>";
echo "Всего было посетителей: " . $customerIndex . " <br>";
echo "Осталось не обслуженных: " . $shop->getCustomersCount() . " <br>";

