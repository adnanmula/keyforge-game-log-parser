<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

abstract readonly class Event
{
    protected function __construct(
        protected string $player,
        protected Turn $turn,
        protected Source $source,
        protected int|string|array $value,
    ) {}

    abstract public function type(): EventType;

    public static function fromArray(array $array): static
    {
        return new static(
            $array['player'],
            new Turn(
                $array['turn']['value'],
                TurnMoment::from($array['turn']['moment']),
                $array['turn']['occurredOn'],
            ),
            Source::from($array['source'] ?? Source::UNKNOWN->value),
            $array['value'],
        );
    }

    public function player(): string
    {
        return $this->player;
    }

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function value(): int|string|array
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'player' => $this->player,
            'type' => $this->type()->name,
            'source' => $this->source->value,
            'turn' => $this->turn->jsonSerialize(),
            'value' => $this->value,
        ];
    }
}
