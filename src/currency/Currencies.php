<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\currency;

use phuongaz\azeconomy\AzEconomy;
use SplObjectStorage;

final class Currencies {

    private static SplObjectStorage $currencies;

    public static function add(Currency $currency, string $name): void{
        self::$currencies[$currency] = $name;
    }

    public static function remove(Currency $currency): void{
        if(self::has($currency)){
            unset(self::$currencies[$currency]);
        }
    }

    public static function has(Currency $currency): bool{
        return isset(self::$currencies[$currency]);
    }

    public static function getAll(): SplObjectStorage {
        return self::$currencies;
    }

    public static function init(): void{
        self::$currencies = new SplObjectStorage();
        $currenciesRaw = AzEconomy::getInstance()->getConfig()->get("currencies");
        foreach($currenciesRaw as $name => $data){
            $currency = new Currency($name, $data["symbol"], $data["default"]);
            self::add($currency, $name);
        }
    }
}