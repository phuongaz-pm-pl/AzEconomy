<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\utils;

use phuongaz\azeconomy\AzEconomy;

class Utils {

    public static function isValidCurrency(string $name): bool {
        return isset(AzEconomy::getInstance()->getConfig()->get("currencies")[$name]);
    }
}