<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\MenuOption;
use phuongaz\azeconomy\commands\Permissions;
use phuongaz\azeconomy\form\AsyncForm;
use phuongaz\azeconomy\storage\player\PlayersPool;
use phuongaz\azeconomy\trait\LanguageTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class Currencies extends BaseSubCommand {
    use LanguageTrait;

    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND_CURRENCIES);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player) return;

        $currencies = PlayersPool::get($sender)->getCurrencies();
        foreach($currencies as $currency => $amount) {
            $sender->sendMessage(self::__trans("currencies.show", [
                "currency" => $currency,
                "amount" => $amount
            ]));
        }
    }
}