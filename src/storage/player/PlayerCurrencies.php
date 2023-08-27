<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage\player;

use pocketmine\player\Player;
use pocketmine\Server;

class PlayerCurrencies extends BaseCurrencies {

    public function getPlayer(): Player{
        return Server::getInstance()->getPlayerExact($this->getUsername());
    }

}