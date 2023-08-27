<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\listener;

use phuongaz\azeconomy\AzEconomy;
use phuongaz\azeconomy\storage\player\PlayersPool;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use SOFe\AwaitGenerator\Await;

class EventHandler implements Listener{

    public function onLogin(PlayerLoginEvent $event) :void {
        PlayersPool::load($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event) :void {
        $player = $event->getPlayer();
        Await::f2c(function () use ($player) {
            $storage = AzEconomy::getInstance()->getStorage();
            $currency = PlayersPool::get($player);
            yield $storage->saveCurrencies($currency);
            PlayersPool::remove($player);
        });
    }
}