<?php

namespace Shop\Interfaces;

interface IProcessData
{
    public function process($time, $tickStep, $data): bool;
}
