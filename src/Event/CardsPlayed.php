<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;

final readonly class CardsPlayed extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        array $value,
    ) {
        parent::__construct($player, $turn, Source::UNKNOWN, $value);
    }

    public function type(): EventType
    {
        return EventType::CARDS_PLAYED;
    }

    public function value(): array
    {
        return $this->value;
    }
}
