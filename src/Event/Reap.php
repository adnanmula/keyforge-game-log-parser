<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;
use AdnanMula\KeyforgeGameLogParser\TurnMoment;

final readonly class Reap extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        Source $source,
        private string $trigger,
        string $value,
    ) {
        parent::__construct($player, $turn, $source, $value);
    }

    public static function fromArray(array $array): static
    {
        return new self(
            $array['player'],
            new Turn(
                $array['turn']['value'],
                TurnMoment::from($array['turn']['moment']),
                $array['turn']['occurredOn'],
            ),
            Source::from($array['source']),
            $array['trigger'],
            $array['value'],
        );
    }

    public function type(): EventType
    {
        return EventType::REAP;
    }

    public function trigger(): string
    {
        return $this->trigger;
    }

    public function jsonSerialize(): array
    {
        return [
            ...$this->jsonSerialize(),
            'trigger' => $this->trigger,
        ];
    }
}
