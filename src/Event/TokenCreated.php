<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;

final readonly class TokenCreated extends Event
{
    public function __construct(string $player, Turn $turn, string $value)
    {
        parent::__construct($player, $turn, Source::UNKNOWN, $value);
    }

    public function type(): EventType
    {
        return EventType::TOKEN_CREATED;
    }
}
