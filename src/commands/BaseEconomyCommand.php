<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands;

use CortexPE\Commando\BaseCommand;
use phuongaz\azeconomy\commands\subs\Currencies;
use phuongaz\azeconomy\commands\subs\Give;
use phuongaz\azeconomy\commands\subs\Pay;
use phuongaz\azeconomy\commands\subs\Set;
use phuongaz\azeconomy\commands\subs\Take;
use phuongaz\azeconomy\commands\subs\Top;
use pocketmine\command\CommandSender;

class BaseEconomyCommand extends BaseCommand {

    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND);
        $this->registerSubCommand(new Give("give", "Give money to player"));
        $this->registerSubCommand(new Take("take", "Take money from player"));
        $this->registerSubCommand(new Set("set", "Set money for player"));
        $this->registerSubCommand(new Pay("pay", "Pay money to player"));
        $this->registerSubCommand(new Top("top", "Top money of player"));
        $this->registerSubCommand(new Currencies("currencies", "Show currencies of player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $this->sendUsage();
    }

    public function getPermission(): string {
        return Permissions::ECONOMY_COMMAND;
    }
}