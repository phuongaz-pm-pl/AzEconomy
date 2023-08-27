<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\storage;

use poggit\libasynql\DataConnector;

abstract class BaseStorage {

    const INIT = "az_economy.init";
    const SELECT = "az_economy.select";
    const SELECT_ALL = "az_economy.selects";
    const INSERT = "az_economy.insert";
    const UPDATE = "az_economy.update";

    public function __construct(
        private DataConnector $connector
    ) {
        $this->init();
    }

    public function getConnector(): DataConnector{
        return $this->connector;
    }

    public function init(): void{
        $this->connector->executeGeneric(self::INIT);
    }

}