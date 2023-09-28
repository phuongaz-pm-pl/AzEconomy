<?php

declare(strict_types=1);

namespace phuongaz\azeconomy;

use Closure;
use phuongaz\azeconomy\currency\Currencies;
use phuongaz\azeconomy\currency\Currency;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use pocketmine\utils\Utils;
use SOFe\AwaitGenerator\Await;

final class EcoAPI {

    public static function getCurrency(string $username, string $currency, Closure $closure) :void {
        Utils::validateCallableSignature(function(float $amount) {}, $closure);
        Await::f2c(function() use ($closure, $currency, $username) {
            $storage = AzEconomy::getInstance()->getStorage();
            yield $storage->awaitSelect($username, function(?BaseCurrencies $currencies) use ($currency, $closure) :void {
                if(!is_null($currencies)) {
                    $closure($currencies->getCurrency($currency));
                }
            });
        });
    }

    public static function getCurrencies(string $username, Closure $closure) :void {
        Utils::validateCallableSignature(function(?BaseCurrencies $currencies) {}, $closure);
        Await::f2c(function() use ($closure, $username) {
            $storage = AzEconomy::getInstance()->getStorage();
            yield $storage->awaitSelect($username, function(?BaseCurrencies $currencies) use ($closure) :void {
                $closure($currencies);
            });
        });
    }

    public static function addCurrency(string $username, string $currency, float $amount, ?Closure $closure = null) :void {
        Await::f2c(function() use ($closure, $amount, $currency, $username) {
            $storage = AzEconomy::getInstance()->getStorage();
            yield $storage->awaitSelect($username, function(?BaseCurrencies $currencies) use ($currency, $amount, $closure) :void {
                $currencies?->addCurrency($currency, $amount, $closure);
                $currencies?->save();
            });
        });
    }

    public static function removeCurrency(string $username, string $currency, float $amount, ?Closure $closure = null) :void {
        Await::f2c(function() use ($closure, $amount, $currency, $username) {
            $storage = AzEconomy::getInstance()->getStorage();
            yield $storage->awaitSelect($username, function(?BaseCurrencies $currencies) use ($currency, $amount, $closure) :void {
                $currencies?->removeCurrency($currency, $amount, $closure);
                $currencies?->save();
            });
        });
    }

    public static function setCurrency(string $username, string $currency, float $amount, ?Closure $closure = null) :void {
        Await::f2c(function() use ($closure, $amount, $currency, $username) {
            $storage = AzEconomy::getInstance()->getStorage();
            yield $storage->awaitSelect($username, function(?BaseCurrencies $currencies) use ($currency, $amount, $closure) :void {
                $currencies?->setCurrency($currency, $amount, $closure);
                $currencies?->save();
            });
        });
    }

    /**
     * @return Currency[]
     */
    public static function getAvailableCurrencies() :array {
        return (array)Currencies::getAll();
    }

}