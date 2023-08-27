<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage;

use Closure;
use Generator;
use phuongaz\azeconomy\storage\player\BaseCurrencies;
use phuongaz\azeconomy\storage\player\OfflineCurrencies;
use phuongaz\azeconomy\storage\player\PlayerCurrencies;
use phuongaz\azeconomy\utils\Utils;
use pocketmine\Server;
use poggit\libasynql\DataConnector;
use SOFe\AwaitGenerator\Await;

class SqliteStorage extends BaseStorage {

    public array $cache;

    public function __construct(DataConnector $connector) {
        parent::__construct($connector);
        $this->init();
    }

    public function awaitSelect(string $username, ?Closure $closure = null): Generator {
        $connector = $this->getConnector();

        $rows = yield from $connector->asyncSelect(self::SELECT, ["username" => $username]);

        $currencyData = OfflineCurrencies::new($username);

        if (isset($rows[0])) {
            $player = Server::getInstance()->getPlayerExact($username);
            if ($player !== null) {
                $currencyData = new PlayerCurrencies($player);
            }

            $currencyData->fromString($rows[0]["currencies"]);

            if ($closure !== null) {
                $closure($currencyData);
            }
        } else {
            yield $this->addCurrencies($currencyData);
        }
        $this->cache[$username] = $currencyData;
        return $currencyData;
    }



    public function addCurrencies(BaseCurrencies $currencies, ?Closure $closure = null): Generator {
        $connector = $this->getConnector();

        yield $connector->asyncInsert(self::INSERT, [
            "username" => $currencies->getUsername(),
            "currencies" => $currencies->toString()
        ]);

        if ($closure !== null) {
            $closure();
        }
    }

    public function saveCurrencies(BaseCurrencies $currencies, ?Closure $closure = null): Generator {
        $connector = $this->getConnector();

        yield $connector->asyncChange(self::UPDATE, [
            "username" => $currencies->getUsername(),
            "currencies" => $currencies->toString()
        ]);

        if ($closure !== null) {
            $closure();
        }
    }

    public function topCurrencies(string $currency, ?Closure $closure = null): void {
        Await::f2c(function() use ($currency, $closure) {
            $connector = $this->getConnector();

            $rows = yield from $connector->asyncSelect(self::SELECT_ALL);

            $currencies = [];

            foreach ($rows as $row) {
                $username = $row['username'];
                $currencyAmount = $this->getCurrencyAmount($row['currencies'], $currency);
                $currencies[$username] = $currencyAmount;
            }

            arsort($currencies);

            $topCurrencies = array_slice($currencies, 0, 10, true);

            if ($closure !== null) {
                $closure($topCurrencies);
            }
        });
    }

    private function getCurrencyAmount(string $currencyData, string $currencyName): float {
        $currencyPairs = explode(';', $currencyData);

        foreach ($currencyPairs as $currencyPair) {
            list($name, $amount) = explode(':', $currencyPair);
            if ($name === $currencyName) {
                return (float) $amount;
            }
        }

        return 0.0; // Default amount if currency not found
    }








}