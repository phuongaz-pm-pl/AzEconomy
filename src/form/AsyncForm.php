<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\form;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\CustomFormElement;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\ModalForm;
use Generator;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

class AsyncForm {

    /**
     * @param CustomFormElement[] $elements
     */
    public static function custom(Player $player, string $title, array $elements) : Generator {
        return yield from Await::promise(function (Closure $resolve) use ($elements, $title, $player) {
            $player->sendForm(new CustomForm(
                    $title, $elements,
                    function(Player $player, CustomFormResponse $result) use ($resolve) : void {
                        $resolve($result);
                    },
                    function(Player $player) use ($resolve) : void {
                        $resolve(null);
                    })
            );
        });
    }

    /**
     * @param MenuOption[] $options
     */
    public static function menu(Player $player, string $title, string $text, array $options) : Generator {
        return yield from Await::promise(function (Closure $resolve) use ($text, $player, $title, $options) {
            $player->sendForm(new MenuForm(
                $title, $text, $options,
                function (Player $player, int $selectedOption) use ($resolve) : void {
                    $resolve($selectedOption);
                },
                function (Player $player) use ($resolve) : void {
                    $resolve(null);
                }
            ));
        });
    }

    public static function modal(Player $player, string $title, string $text, string $yesButtonText = "gui.yes", string $noButtonText = "gui.no") : Generator {
        return yield from Await::promise(function(Closure $resolve) use ($noButtonText, $text, $yesButtonText, $title, $player) {
            $player->sendForm(new ModalForm(
                $title, $text,
                function (Player $player, bool $choice) use ($resolve) : void {
                    $resolve($choice);
                },
                $yesButtonText, $noButtonText
            ));
        });
    }
}