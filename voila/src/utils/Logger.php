<?php
namespace Voila\Utils;

class Logger
{
    public function info($message)
    {
        echo "[INFO] " . date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;
    }

    public function error($message)
    {
        echo "[ERROR] " . date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;
    }
}
