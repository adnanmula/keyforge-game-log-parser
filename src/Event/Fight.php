<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use AdnanMula\KeyforgeGameLogParser\Event;
use AdnanMula\KeyforgeGameLogParser\EventType;
use AdnanMula\KeyforgeGameLogParser\Source;
use AdnanMula\KeyforgeGameLogParser\Turn;
use AdnanMula\KeyforgeGameLogParser\TurnMoment;

final readonly class Fight extends Event
{
    public function __construct(
        string $player,
        Turn $turn,
        Source $source,
        private string $trigger,
        private string $target,
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
            $array['target'],
            $array['value'],
        );
    }

    public function type(): EventType
    {
        return EventType::FIGHT;
    }

    public function trigger(): string
    {
        return $this->trigger;
    }

    public function target(): string
    {
        return $this->target;
    }

    public function jsonSerialize(): array
    {
        return [
            ...$this->jsonSerialize(),
            'trigger' => $this->trigger,
            'target' => $this->target,
        ];
    }
}
