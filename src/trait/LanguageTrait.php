<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\trait;

use Closure;
use phuongaz\azeconomy\AzEconomy;
use pocketmine\utils\Utils;

trait LanguageTrait {

    public static function __trans(string $key, array $params = []): string{
        return AzEconomy::getInstance()->getLanguage()->translateString($key, $params);
    }
}