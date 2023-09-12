<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\listener;

use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\EcoAPI;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use phuongaz\azeconomy\storage\player\PlayerCurrencies;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use SOFe\AwaitGenerator\Await;

class EventHandler implements Listener{

    public function onLogin(PlayerLoginEvent $event) :void {
        $player = $event->getPlayer();
        EcoAPI::getCurrencies($player->getName(), function(?BaseCurrencies $currencies) use ($player) {
            if(is_null($currencies)) {
                $storage = AzEconomy::getInstance()->getStorage();
                Await::f2c(fn() => yield $storage->registerCurrencies(BaseCurrencies::new($player->getName(), PlayerCurrencies::class)));
            }
        });
    }
}