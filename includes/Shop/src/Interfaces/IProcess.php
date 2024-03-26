<?php

namespace Shop\Interfaces;

interface IProcess
{
    public function process($time, $tickStep): bool;
}
