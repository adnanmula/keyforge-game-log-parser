<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;
use AdnanMula\KeyforgeGameLogParser\TurnMoment;

final readonly class KeyForged extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        string $value,
        private int $amberCost,
        private int $amberRemaining,
    ) {
        parent::__construct($player, $turn, Source::UNKNOWN, $value);
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
            $array['value'],
            $array['amber_cost'],
            $array['amber_remaining'],
        );
    }

    public function type(): EventType
    {
        return EventType::KEY_FORGED;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function amberCost(): int
    {
        return $this->amberCost;
    }

    public function amberRemaining(): int
    {
        return $this->amberRemaining;
    }

    public function jsonSerialize(): array
    {
        return [
            ...$this->jsonSerialize(),
            'amber_cost' => $this->amberCost,
            'amber_remaining' => $this->amberRemaining,
        ];
    }
}
