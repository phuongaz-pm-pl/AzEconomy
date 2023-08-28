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
use phuongaz\azeconomy\storage\player\PlayersPool;
use phuongaz\azeconomy\trait\LanguageTrait;
use phuongaz\azeconomy\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Pay extends BaseSubCommand {
    use LanguageTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->setPermission(Permissions::ECONOMY_COMMAND_PAY);
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("currency"));
        $this->registerArgument(2, new FloatArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player) return;

        $from = $sender->getName();
        $to = $args["player"];
        $currency = $args["currency"];
        $amount = $args["amount"];

        if(!Utils::isValidCurrency($currency)) {
            $sender->sendMessage(self::__trans("currency.not.found", [
                "currency" => $currency
            ]));
            return;
        }

        if($from === $to) {
            $sender->sendMessage(self::__trans("pay.self"));
            return;
        }

        $event = new EconomyTransactionEvent($from, $to, $currency, $amount, "Pay by " . $from, TransactionTypes::PAY);
        $event->setCallback(function(EconomyTransactionEvent $event) use ($sender) {
            if($event->isCancelled()) return;

            $storage = AzEconomy::getInstance()->getStorage();

            $formCurrencies = PlayersPool::get($sender);
            if($formCurrencies->getCurrency($event->getCurrency()) < $event->getAmount()) {
                $sender->sendMessage(self::__trans("not.enough.currency", [
                    "currency" => $event->getCurrency()
                ]));
                return;
            }
            $storage->awaitSelect($event->getTo(), function(?BaseCurrencies $currencies) use ($event, $formCurrencies, $sender) {
                if($currencies == null) {
                    $sender->sendMessage(self::__trans("player.not.found", [
                        "player" => $event->getTo()
                    ]));
                    $event->cancel();
                    return;
                }
                $currencies->addCurrency($event->getCurrency(), $event->getAmount(), function(BaseCurrencies $currencies) {
                    $currencies->save();
                });
                $formCurrencies->removeCurrency($event->getCurrency(), $event->getAmount());
            });
            $sender->sendMessage(self::__trans("pay.success", [
                "amount" => $event->getAmount(),
                "currency" => $event->getCurrency(),
                "player" => $event->getTo()
            ]));
        });
        $event->call();
    }
}