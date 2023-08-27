<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands\subs;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\commands\Permissions;
use phuongaz\azeconomy\currency\TransactionTypes;
use phuongaz\azeconomy\listener\event\EconomyTransactionEvent;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use phuongaz\azeconomy\trait\LanguageTrait;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class Give extends BaseSubCommand {
    use LanguageTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND_GIVE);
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("currency"));
        $this->registerArgument(2, new FloatArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $target = $args["player"];
        $currency = $args["currency"];
        $amount = $args["amount"];

        $event = new EconomyTransactionEvent($sender->getName(), $target, $currency, $amount, "Give by " . $sender->getName(), TransactionTypes::GIVE);
        $event->setCallback(function(EconomyTransactionEvent $event) use ($sender) {
            if($event->isCancelled()) return;

            $storage = AzEconomy::getInstance()->getStorage();
            $storage->awaitSelect($event->getTo(), function(BaseCurrencies $currencies) use ($event) {
                $currencies->addCurrency($event->getCurrency(), $event->getAmount());
            });
            $sender->sendMessage(self::__trans("give.from.success", [
                "amount" => $event->getAmount(),
                "currency" => $event->getCurrency(),
                "player" => $event->getTo()
            ]));
            if(Server::getInstance()->getPlayerExact($event->getTo()) !== null) {
                Server::getInstance()->getPlayerExact($event->getTo())->sendMessage(self::__trans("give.success", [
                    "amount" => $event->getAmount(),
                    "currency" => $event->getCurrency()
                ]));
            }
        });

        $event->call();
    }
}