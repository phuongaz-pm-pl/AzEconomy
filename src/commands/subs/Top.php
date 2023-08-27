<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands\subs;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\commands\Permissions;
use phuongaz\azeconomy\trait\LanguageTrait;
use phuongaz\azeconomy\utils\Utils;
use pocketmine\command\CommandSender;

class Top extends BaseSubCommand {
    use LanguageTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND_TOP);
        $this->registerArgument(0, new RawStringArgument("currency"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $storage = AzEconomy::getInstance()->getStorage();

        $currency = $args["currency"];

        if(!Utils::isValidCurrency($currency)) {
            $sender->sendMessage(self::__trans("currency.not.found", [
                "currency" => $currency
            ]));
            return;
        }

        $storage->topCurrencies($currency, function(array $currencies) use ($sender, $currency): void {
            $index = 1;
            $sender->sendMessage(self::__trans("currency.top.title", [
                "currency" => $currency
            ]));
            foreach($currencies as $username => $amount) {
                $sender->sendMessage(self::__trans("currency.top.line", [
                    "rank" => $index,
                    "player" => $username,
                    "amount" => $amount
                ]));
                $index++;
            }
        });
    }
}