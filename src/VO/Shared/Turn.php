<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

use JsonSerializable;

final readonly class Turn implements JsonSerializable
{
    public function __construct(
        private int $value,
        private TurnMoment $moment,
        private int $occurredOn,
    ) {}

    public function value(): int
    {
        return $this->value;
    }

    public function moment(): TurnMoment
    {
        return $this->moment;
    }

    public function occurredOn(): int
    {
        return $this->occurredOn;
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'moment' => $this->moment->name,
            'occurredOn' => $this->occurredOn,
        ];
    }
}
