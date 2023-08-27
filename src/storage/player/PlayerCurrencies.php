<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage\player;

use pocketmine\player\Player;

class PlayerCurrencies extends BaseCurrencies {

    public function __construct(
        private Player $player,
        private array $currencies = []
    ){
        parent::__construct($player->getName(), $currencies);
    }

    public function getPlayer(): Player{
        return $this->player;
    }
}