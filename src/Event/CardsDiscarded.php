<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;

final readonly class CardsDiscarded extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        Source $source,
        int $value,
    ) {
        parent::__construct($player, $turn, $source, $value);
    }

    public function type(): EventType
    {
        return EventType::CARDS_DISCARDED;
    }

    public function value(): int
    {
        return $this->value;
    }
}
