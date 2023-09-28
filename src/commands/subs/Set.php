<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\commands\subs;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\azeconomy\commands\Permissions;
use phuongaz\azeconomy\currency\TransactionTypes;
use phuongaz\azeconomy\EcoAPI;
use phuongaz\azeconomy\listener\event\EconomyTransactionEvent;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use phuongaz\azeconomy\trait\LanguageTrait;
use phuongaz\azeconomy\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class Set extends BaseSubCommand {
    use LanguageTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND_SET);
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("currency"));
        $this->registerArgument(2, new FloatArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $target = $args["player"];
        $currency = $args["currency"];
        $amount = $args["amount"];

        if(!Utils::isValidCurrency($currency)) {
            $sender->sendMessage(self::__trans("currency.not.found", [
                "currency" => $currency
            ]));
            return;
        }

        $event = new EconomyTransactionEvent($sender->getName(), $target, $currency, $amount, "Set by " . $sender->getName(), TransactionTypes::SET);
        $event->setCallback(function(EconomyTransactionEvent $event) use ($sender) {
            if($event->isCancelled()) return;

            EcoAPI::getCurrencies($event->getTo(), function(?BaseCurrencies $currencies) use ($sender, $event) {
                if($currencies == null) {
                    $sender->sendMessage(self::__trans("player.not.found", [
                        "player" => $event->getTo()
                    ]));
                    $event->cancel();
                    return;
                }
                EcoAPI::setCurrency($event->getTo(), $event->getCurrency(), $event->getAmount(), function(bool $isSuccess) use ($event, $sender) {
                    if($isSuccess) {
                        $sender->sendMessage(self::__trans("set.from.success", [
                            "player" => $event->getTo(),
                            "currency" => $event->getCurrency(),
                            "amount" => $event->getAmount()
                        ]));
                        Server::getInstance()->getPlayerExact($event->getTo())?->sendMessage(self::__trans("set.to.success", [
                            "currency" => $event->getCurrency(),
                            "amount" => $event->getAmount()
                        ]));
                    }
                });
            });
        });
        $event->call();
    }
}