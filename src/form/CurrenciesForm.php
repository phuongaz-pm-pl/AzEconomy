<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\form;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\MenuOption;
use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use phuongaz\azeconomy\trait\LanguageTrait;
use pocketmine\player\Player;
use pocketmine\Server;
use SOFe\AwaitGenerator\Await;

class CurrenciesForm {
    use LanguageTrait;

    private BaseCurrencies $currencies;

    public function __construct(
        private Player $player
    ) {
        Await::f2c(function() use ($player) {
            $storage = AzEconomy::getInstance()->getStorage();
            $this->currencies = yield from $storage->awaitSelect($player->getName());
        });
    }

    public function getPlayer() :Player {
        return $this->player;
    }

    public function send() :\Generator {
        $storage = AzEconomy::getInstance()->getStorage();
        $options = [];
        /** @var BaseCurrencies $currencies*/
        $baseCurrencies = yield from $storage->awaitSelect($this->getPlayer()->getName());
        $currencies = $baseCurrencies->getSortedCurrencies();
        foreach($currencies as $name => $currency) {
            $options[] = new MenuOption($name . ": " . $currency);
        }
        $selected = yield AsyncForm::menu($this->getPlayer(), self::__trans("currencies.form.title"), self::__trans("currencies.form.text"), $options);
        if($selected === null) return;

        $currency = array_keys($currencies)[$selected];

        yield $this->currencyForm($currency);
    }

    public function currencyForm(string $currency) :\Generator {
        $options = [
            new MenuOption(self::__trans("currency.form.button.top")),
            new MenuOption(self::__trans("currency.form.button.pay")),
        ];
        $text = self::__trans("currency.form.text", ["currency" => $currency, "amount" => $this->currencies->getCurrency($currency)]);
        $selected = yield AsyncForm::menu($this->getPlayer(), $currency, $text, $options);
        if($selected === null) {
            return yield $this->send();
        }
        match ($selected) {
            0 =>  $this->topForm($currency),
            1 => yield $this->payForm($currency),
        };
    }

    public function topForm(string $currency) :void {
        $storage = AzEconomy::getInstance()->getStorage();
        $storage->topCurrencies($currency, function (array $tops) use ($currency) {
            $index = 1;
            $elements = [];
            foreach($tops as $username => $amount) {
                $elements[] = new Label("bxh_$index", self::__trans("currency.form.top.label", ["rank" => $index, "player" => $username, "amount" => $amount]));
                $index++;
            }
            Await::f2c(function() use ($elements, $currency) {
                $response = yield AsyncForm::custom($this->getPlayer(), self::__trans("currency.form.top.title", ["currency" => $currency]), $elements);
                if(is_null($response)) {
                    yield $this->currencyForm($currency);
                }
            });
        });
    }

    public function payForm(string $currency, string $text = "") :\Generator {
        $storage = AzEconomy::getInstance()->getStorage();
        $labelContent = self::__trans("currency.form.pay.label", ["currency" => $currency, "amount" => $this->currencies->getCurrency($currency)]);
        if($text !== "") {
            $labelContent .= "\n$text";
        }
        /** @var CustomFormResponse $response */
        $response = yield AsyncForm::custom($this->getPlayer(), self::__trans("currency.form.pay.title", ["currency" => $currency]), [
            new Label("text", $labelContent),
            new Input("username", self::__trans("currency.form.pay.username")),
            new Input("amount", self::__trans("currency.form.pay.amount"))
        ]);
        if(is_null($response)) {
            return yield $this->currencyForm($currency);
        }
        $response = $response->getAll();
        $username = $response["username"];
        $amount = $response["amount"];

        if (!is_numeric($amount) || floatval($amount) != $amount) {
            return yield $this->payForm($currency, self::__trans("currency.form.pay.error.amount"));
        }

        if(Server::getInstance()->getPlayerExact($username) === null) {
            return yield $this->payForm($currency, self::__trans("currency.form.pay.error.player", ["player" => $username]));
        }
        if($username === $this->getPlayer()->getName()) {
            return yield $this->payForm($currency, self::__trans("currency.form.pay.error.self"));
        }
        $targetCurrencies = yield from $storage->awaitSelect($username);
        $this->currencies->transferTo($targetCurrencies, $currency, $amount, function(bool $success) use ($username, $currency, $amount, $targetCurrencies) {
            if($success) {
                yield $this->payForm($currency, self::__trans("currency.form.pay.success", ["player" => $username, "amount" => $amount, "currency" => $currency]));
                $targetCurrencies->getPlayer()->sendMessage(self::__trans("currency.to.pay.success", ["player" => $this->getPlayer()->getName(), "amount" => $amount, "currency" => $currency]));
            } else {
                yield $this->payForm($currency, self::__trans("currency.form.pay.error.money", ["currency" => $currency, "amount" => $amount]));
            }
        });
    }
}