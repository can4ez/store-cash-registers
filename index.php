<?php

include __DIR__ . "/includes/Shop.php";

use Shop\Shop;

$shop = new Shop('5ka');

$shop->addProduct(new \Shop\WeightProduct('Помидоры', 250));
$shop->addProduct(new \Shop\PieceProduct('Чипсы лейс с медом', 150));
$shop->addProduct(new \Shop\PieceProduct('Молоко 2% 1л', 53));

$maxWorkTime = 60 * 8 + 60 * 15; // Магазин будет открыт с 8 до 23 часов
$tickStep = 1; // Количество минут за 1 итерацию
$maxCustomers = 10; // Максимальное количество покупателей в пик

$customers = 1; // Количество новых посетителей
$customerIndex = 0;

for ($time = 60 * 8; ; $time += $tickStep) {

    if ($time >= $maxWorkTime) break;

    if ($time % 60 === 0) {
        echo "Магазин работает, время: " . \Shop\Utils::formatHours($time) . " ч. <br>";
    }

    echo "<br>--- start loop ---<br>";

    for ($k = 0; $k < $customers; $k++) {
        $newCustomer = new \Shop\Customer('Покупатель #' . ++$customerIndex);
        $shop->addCustomer($newCustomer);
    }

    $shop->process($time, $tickStep);

    $shop->showStatus($time);

    echo "--- end loop ---<br>";

    $hour = round($time / 60);
    if ($hour > 8 && $hour < 12) {
        $customers = rand(0, 3); // Постепенный нарастающий поток покупателей до пика
    } else if ($hour >= 12 && $hour <= 19) {
        $customers = rand(3, 5); // Постепенный нарастающий поток покупателей до пика
    } else if ($hour > 20) {
        $customers = rand(0, 2); // Спад числа покупателей до 0 к концу дня
    }
    // Ограничиваем количество покупателей в пик
    $customers = max(min($customers, $maxCustomers), 0);
}

echo "<hr>";
echo "Магазин закрылся, время: " . \Shop\Utils::formatHours($time) . " ч. <br>";
echo "Всего было посетителей: " . $customerIndex;

