<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

use JsonSerializable;

final readonly class Turn implements JsonSerializable
{
    public function __construct(
        private int $turn,
        private TurnMoment $moment,
    ) {}

    public function turn(): int
    {
        return $this->turn;
    }

    public function moment(): TurnMoment
    {
        return $this->moment;
    }

    public function jsonSerialize(): array
    {
        return [
            'turn' => $this->turn,
            'moment' => $this->moment->name,
        ];
    }
}
