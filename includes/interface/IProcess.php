<?php

namespace Shop;

interface IProcess
{
    public function process($time): bool;
}
