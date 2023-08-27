<?php

declare(strict_types=1);

namespace phuongaz\azeconomy;

use Error;
use phuongaz\azeconomy\commands\BaseEconomyCommand;
use phuongaz\azeconomy\currency\Currencies;
use phuongaz\azeconomy\listener\EventHandler;
use phuongaz\azeconomy\storage\BaseStorage;
use phuongaz\azeconomy\storage\player\PlayersPool;
use phuongaz\azeconomy\storage\SqliteStorage;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\libasynql;

class AzEconomy extends PluginBase {
    use SingletonTrait;

    private Language $language;
    private BaseStorage $storage;

    public function onLoad(): void{
        self::setInstance($this);
    }

    public function onEnable(): void{
        $this->saveResource("languages/eng.ini");
        $this->saveDefaultConfig();

        $connector = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
        ]);

        $this->storage = match ($this->getConfig()->get("database")["type"]) {
            "sqlite" => new SqliteStorage($connector),
            default => throw new Error("Invalid database type"),
        };

        Currencies::init();
        PlayersPool::init();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->getServer()->getCommandMap()->register("azeconomy", new BaseEconomyCommand($this, "azeconomy", "azeconomy.command", ["azeco"]));
        $defaultLanguage = "eng";
        $languageCode = $this->getConfig()->get("language", $defaultLanguage);
        $languagesFolder = $this->getFile() . "resources/languages/";

        if (!file_exists($languagesFolder . $languageCode . ".ini")) {
            $this->getLogger()->warning("Language $languageCode not found, using default language");
            $languageCode = $defaultLanguage;
        }

        $this->language = new Language($languageCode, $languagesFolder);

    }

    public function getLanguage(): Language{
        return $this->language;
    }


    public function getStorage() :BaseStorage {
        return $this->storage;
    }

}