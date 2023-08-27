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

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->initStorage();
        Currencies::init();
        PlayersPool::init();
        $this->registerEventsAndCommands();
        $this->initLanguage();
    }

    private function initStorage(): void {
        $databaseConfig = $this->getConfig()->get("database");
        $connector = libasynql::create($this, $databaseConfig, [
            "sqlite" => "sqlite.sql",
        ]);

        $this->storage = match ($databaseConfig["type"]) {
            "sqlite" => new SqliteStorage($connector),
            default => throw new Error("Invalid database type"),
        };
    }

    private function registerEventsAndCommands(): void {
        $eventHandler = new EventHandler();
        $this->getServer()->getPluginManager()->registerEvents($eventHandler, $this);

        $commandMap = $this->getServer()->getCommandMap();
        $command = new BaseEconomyCommand($this, "azeconomy", "azeconomy.command", ["azeco"]);
        $commandMap->register("azeconomy", $command);
    }

    private function initLanguage(): void {
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

    public function onDisable(): void {
        $this->getStorage()->getConnector()->waitAll();
    }

}