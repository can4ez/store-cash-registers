<?php

namespace Shop;

interface IProcessData
{
    public function process($time, $tickStep, $data): bool;
}
