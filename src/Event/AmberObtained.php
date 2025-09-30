<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;
use AdnanMula\KeyforgeGameLogParser\TurnMoment;

final readonly class AmberObtained extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        int $value,
        private int $keys,
        private int $delta,
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
            $array['keys'],
            $array['delta'],
        );
    }

    public function type(): EventType
    {
        return EventType::AMBER_OBTAINED;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function keys(): int
    {
        return $this->keys;
    }

    public function delta(): int
    {
        return $this->delta;
    }

    public function jsonSerialize(): array
    {
        return [
            ...$this->jsonSerialize(),
            'keys' => $this->keys,
            'delta' => $this->delta,
        ];
    }
}
