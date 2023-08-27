<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage\player;

use Closure;
use phuongaz\azeconomy\AzEconomy;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;
use SplObjectStorage;

final class PlayersPool {

    private static SplObjectStorage $players;

    public static function get(Player $player, ?Closure $closure = null): PlayerCurrencies{
        $playerCurrencies = self::$players[$player];
        if($closure !== null){
            $closure($playerCurrencies);
        }
        return $playerCurrencies;
    }

    public static function load(Player $player): void {
        $storage = AzEconomy::getInstance()->getStorage();
        Await::f2c(function() use ($player, $storage){
            /** @var BaseCurrencies $playerCurrencies */
            $playerCurrencies = yield $storage->awaitSelect($player->getName());
            self::$players[$player] = $playerCurrencies;
        });
    }

    public static function remove(Player $player): void {
        if(isset(self::$players[$player])){
            unset(self::$players[$player]);
        }
    }

    public static function getAll(): SplObjectStorage {
        return self::$players;
    }

    public static function init(): void{
        self::$players = new SplObjectStorage();
    }
}