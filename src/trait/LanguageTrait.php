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

    /**
     * Translate an array of keys using a provided closure.
     *
     * @param array   $keys     Array of translation keys.
     * @param Closure $closure  Closure for processing each translation.
     * @param array   $params   Optional array of parameter arrays for each key.
     * @return void
     */
    public static function __trans_func(array $keys, Closure $closure, array $params = []): void {
        Utils::validateCallableSignature(function (string $key, string $translation): void {}, $closure);
        foreach ($keys as $index => $key) {
            $translation = self::__trans($key, $params[$index] ?? []);
            $closure($key, $translation);
        }
    }

}