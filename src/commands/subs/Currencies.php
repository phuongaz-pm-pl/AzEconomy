<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\commands\Permissions;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use phuongaz\azeconomy\trait\LanguageTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Currencies extends BaseSubCommand {
    use LanguageTrait;

    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND_CURRENCIES);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player) return;

        $storage = AzEconomy::getInstance()->getStorage();

        $storage->awaitSelect($sender->getName(), function (BaseCurrencies $currencies) use ($sender): void {
            foreach($currencies->getCurrencies() as $currency => $amount) {
                $sender->sendMessage(self::__trans("currencies.show", [
                    "currency" => $currency,
                    "amount" => $amount
                ]));
            }
        });
    }
}