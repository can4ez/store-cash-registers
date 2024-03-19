<?php

namespace Shop;

interface IProcessData
{
    public function process($time, $data): bool;
}
