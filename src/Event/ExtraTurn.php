<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;
use AdnanMula\KeyforgeGameLogParser\TurnMoment;

final readonly class ExtraTurn extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        private string $trigger,
    ) {
        parent::__construct($player, $turn, Source::UNKNOWN, 1);
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
            $array['trigger'],
        );
    }

    public function type(): EventType
    {
        return EventType::EXTRA_TURN;
    }

    public function trigger(): string
    {
        return $this->trigger;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            ...$this->jsonSerialize(),
            'trigger' => $this->trigger,
        ];
    }
}
