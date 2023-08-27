<?php

declare(strict_types=1);

namespace phuongaz\azeconomy\listener\event;

use ColinHDev\libAsyncEvent\AsyncEvent;
use ColinHDev\libAsyncEvent\ConsecutiveEventHandlerExecutionTrait;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class EconomyTransactionEvent extends Event implements AsyncEvent, Cancellable {
    use CancellableTrait, ConsecutiveEventHandlerExecutionTrait;

    public function __construct(
        private string $from,
        private string $to,
        private string $currency,
        private float $amount,
        private string $reason,
        private int $type,
    ) {}

    public function getFrom(): string {
        return $this->from;
    }

    public function getTo(): string {
        return $this->to;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function getReason(): string {
        return $this->currency;
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function getType(): int {
        return $this->type;
    }

}