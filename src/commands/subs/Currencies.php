<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use phuongaz\azeconomy\commands\Permissions;
use phuongaz\azeconomy\EcoAPI;
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

        EcoAPI::getCurrencies($sender->getName(), function(?BaseCurrencies $currencies) use ($sender){
            foreach($currencies->getSortedCurrencies() as $currencyName => $amount) {
                $sender->sendMessage(self::__trans("currencies.show", [
                    "currency" => $currencyName,
                    "amount" => $amount
                ]));
            }
        });
    }
}