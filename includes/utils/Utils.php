<?php

namespace Shop;

class Utils
{
    static public function formatHours($time, $format = '%02d:%02d'): string
    {
        if ($time < 1) {
            return '00:00';
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    static public function debug($str)
    {
        if (!defined('SHOW_DEBUG') || SHOW_DEBUG !== true) return;

        echo $str . "<br>";
    }
}
