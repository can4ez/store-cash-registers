<?php

include __DIR__ . "/includes/Shop/autoload.php";

set_time_limit(0);
define('SHOW_DEBUG', false);

$shop = new \Shop\Shop('5ka');

$shop->addProduct(new \Shop\Models\Product\WeightProduct('Помидоры', 250));
$shop->addProduct(new \Shop\Models\Product\PieceProduct('Чипсы лейс с медом', 150));
$shop->addProduct(new \Shop\Models\Product\PieceProduct('Молоко 2% 1л', 53));

$workStartTime = 60 * 8;
$maxWorkTime = $workStartTime + 60 * 15; // Магазин будет открыт с 8 до 23 часов
$tickStep = 1; // Количество минут за 1 итерацию
$maxCustomers = 10; // Максимальное количество покупателей в пик

$customers = 1; // Количество новых посетителей
$customerIndex = 0;


// График распредения посетителей
$x = [8, 10, 12, 14, 16, 18, 20, 21, 23];
$y = [0, 2, 3, 15, 2, 2, 1, 2, 0];
$curve = new \Shop\Utils\Akima\AkimaSpline($x, $y);

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

    \Shop\Utils\Utils::debug("--- start loop ---");

    for ($k = 0; $k < $newCustomers; $k++) {
        $newCustomer = new \Shop\Models\Customer\Customer('Покупатель #' . ++$customerIndex);
        $shop->addCustomer($newCustomer);
    }

    $shop->process($time, $tickStep);

    if ($time % 60 === 0) {
        $shop->showStatus($time);
    }

    \Shop\Utils\Utils::debug("--- end loop ---");

    if ($time >= $maxWorkTime) break;
}

echo "<hr>";
echo "Магазин закрылся, время: " . \Shop\Utils\Utils::formatHours($time) . " ч. <br>";
echo "Всего было посетителей: " . $customerIndex . " <br>";
echo "Осталось не обслуженных: " . $shop->getCustomersCount() . " <br>";

