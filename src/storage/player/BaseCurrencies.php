<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage\player;

use Closure;
use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\currency\Currencies;
use phuongaz\azeconomy\currency\TransactionTypes;
use phuongaz\azeconomy\listener\event\EconomyTransactionEvent;
use pocketmine\utils\Utils;
use SOFe\AwaitGenerator\Await;

abstract class BaseCurrencies {
    public function __construct(
        private string $username,
        private array $currencies = []
    ){}

    public function getUsername(): string{
        return $this->username;
    }

    public function addCurrency(string $name, float $amount, ?Closure $closure = null): void{
        $currency = $this->currencies[$name] ?? 0;
        if($closure !== null){
            Utils::validateCallableSignature(function(BaseCurrencies $currencies): void{}, $closure);
            $currency = $closure($this);
        }
        $this->currencies[$name] = $currency + $amount;
    }

    public function removeCurrency(string $name, float $amount, ?Closure $closure = null): void{
        $currency = $this->currencies[$name] ?? 0;
        if($closure !== null){
            Utils::validateCallableSignature(function(BaseCurrencies $currencies): void{}, $closure);
            $currency = $closure($this);
        }
        $this->currencies[$name] = $currency - $amount;
    }

    public function getCurrency(string $name, ?Closure $closure = null): float{
        $currency = $this->currencies[$name] ?? 0;
        if($closure !== null){
            Utils::validateCallableSignature(function(BaseCurrencies $currencies): void{}, $closure);
            $currency = $closure($this);
        }
        return $currency;
    }

    public function setCurrency(string $name, float $amount, ?Closure $closure = null): void{
        if($closure !== null){
            Utils::validateCallableSignature(function(BaseCurrencies $currencies): void{}, $closure);
            $closure($this);
        }
        $this->currencies[$name] = $amount;
    }

    public function getCurrencies(?Closure $closure = null): array{

        $currencies = $this->currencies;
        if($closure !== null){
            Utils::validateCallableSignature(function(array $currencies): void{}, $closure);
            $currencies = $closure($currencies);
        }
        return $currencies;
    }

    public function getSortedCurrencies(): array{
        $currencies = $this->currencies;
        arsort($currencies);
        return $currencies;
    }

    public function transferTo(BaseCurrencies $target, string $name, float $amount, string $reason = "", ?Closure $closure = null): void{
        $event = new EconomyTransactionEvent($this->username, $target->getUsername(), $name, $amount, $reason, TransactionTypes::PAY);
        $event->setCallback(function(EconomyTransactionEvent $event) use ($closure, $target, $name, $amount): void{
            if(!$event->isCancelled()){
                $this->removeCurrency($name, $amount);
                $target->addCurrency($name, $amount);
                if($closure !== null){
                    Utils::validateCallableSignature(function(BaseCurrencies $target, string $name, float $amount): void{}, $closure);
                    $closure($target, $name, $amount);
                }
            }
        });
        $event->call();
    }

    public function save(): void{
        Await::f2c(function () {
            $storage = AzEconomy::getInstance()->getStorage();
            yield $storage->saveCurrencies($this);
        });
    }

    public function toString(): string {
        $string = "";
        foreach ($this->currencies as $name => $amount) {
            $string .= "$name:$amount;";
        }
        return $string;
    }

    public function fromString(string $string): void {
        $currencies = explode(";", $string);

        foreach ($currencies as $currency) {
            if (!empty($currency) && count($currencyParts = explode(":", $currency)) === 2) {
                list($name, $amount) = $currencyParts;
                $this->addCurrency($name, (float) $amount);
            }
        }
    }


    /**
     * @param string $username
     * @param array $currencies
     *
     * @return BaseCurrencies
     */
    public static function new(string $username, array $currencies = []): BaseCurrencies{
        $playerCurrencies = new static($username);
        if(count($currencies) === 0){
            $currencies = Currencies::getAll();
        }
        foreach($currencies as $currency){
            $playerCurrencies->addCurrency($currency->getName(), $currency->getDefault());
        }
        return $playerCurrencies;
    }
}