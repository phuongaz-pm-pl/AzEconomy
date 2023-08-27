<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\currency;

class Currency {

    public function __construct(
        private string $name,
        private string $symbol,
        private float $default,
    ){}

    public function getName(): string{
        return $this->name;
    }

    public function getSymbol(): string{
        return $this->symbol;
    }

    public function getDefault(): float{
        return $this->default;
    }

}