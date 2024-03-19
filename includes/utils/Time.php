<?php

namespace Shop;

class Time
{
    static public function format($time, $format = '%02d:%02d'): string
    {
        if ($time < 1) {
            return '00:00';
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
}
