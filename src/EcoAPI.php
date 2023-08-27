<?php

declare(strict_types=1);

namespace phuongaz\azeconomy;

use Closure;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use pocketmine\utils\Utils;

final class EcoAPI {

    public function getCurrency(string $username, string $currency, Closure $closure) :void {
        Utils::validateCallableSignature(function(float $amount) {}, $closure);
        $storage = AzEconomy::getInstance()->getStorage();
        $storage->awaitSelect($username, function(float $amount) use ($currency, $closure) :void {
            $closure($amount);
        });
    }

    public function getCurrencies(string $username, Closure $closure) :void {
        Utils::validateCallableSignature(function(BaseCurrencies $currencies) {}, $closure);
        $storage = AzEconomy::getInstance()->getStorage();
        $storage->awaitSelect($username, function(BaseCurrencies $currencies) use ($closure) :void {
            $closure($currencies);
        });
    }

    public function addCurrency(string $username, string $currency, float $amount, ?Closure $closure = null) :void {
        $storage = AzEconomy::getInstance()->getStorage();
        $storage->awaitSelect($username, function(BaseCurrencies $currencies) use ($currency, $amount, $closure) :void {
            $currencies->addCurrency($currency, $amount, $closure);
        });
    }

    public function removeCurrency(string $username, string $currency, float $amount, ?Closure $closure = null) :void {
        $storage = AzEconomy::getInstance()->getStorage();
        $storage->awaitSelect($username, function(BaseCurrencies $currencies) use ($currency, $amount, $closure) :void {
            $currencies->removeCurrency($currency, $amount, $closure);
        });
    }

    public function setCurrency(string $username, string $currency, float $amount, ?Closure $closure = null) :void {
        $storage = AzEconomy::getInstance()->getStorage();
        $storage->awaitSelect($username, function(BaseCurrencies $currencies) use ($currency, $amount, $closure) :void {
            $currencies->setCurrency($currency, $amount, $closure);
        });
    }

}