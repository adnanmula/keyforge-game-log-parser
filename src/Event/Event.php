<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use JsonSerializable;
use Stringable;

readonly class Event implements JsonSerializable, Stringable
{
    public function __construct(
        private(set) EventType $type,
        private(set) string $player,
        private(set) Turn $turn,
        private(set) Source $source,
        private(set) int|string|array $value,
        private(set) array $payload = [],
    ) {}

    public function type(): EventType
    {
        return $this->type;
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

    public function payload(): array
    {
        return $this->payload;
    }

    public function jsonSerialize(): array
    {
        return [
            'player' => $this->player,
            'type' => $this->type()->name,
            'source' => $this->source->value,
            'turn' => $this->turn->jsonSerialize(),
            'value' => $this->value,
            'payload' => $this->payload,
        ];
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s %s | %s | %s',
            $this->turn->value(),
            $this->turn->moment()->value,
            $this->turn->occurredOn(),
            $this->player,
            $this->type->value,
        );
    }
}
