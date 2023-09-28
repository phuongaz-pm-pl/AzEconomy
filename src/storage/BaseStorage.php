<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage;

use Closure;
use Generator;
use poggit\libasynql\DataConnector;
use SOFe\AwaitGenerator\Await;

abstract class BaseStorage {

    const INIT = "az_economy.init";
    const SELECT = "az_economy.select";
    const SELECT_ALL = "az_economy.selects";
    const INSERT = "az_economy.insert";
    const UPDATE = "az_economy.update";

    public function __construct(
        private DataConnector $connector
    ) {
        Await::g2c($this->connector->asyncGeneric(self::INIT));
    }

    public function getConnector(): DataConnector{
        return $this->connector;
    }

}