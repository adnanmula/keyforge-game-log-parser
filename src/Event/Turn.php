<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

use JsonSerializable;

final readonly class Turn implements JsonSerializable
{
    public function __construct(
        private int $value,
        private Moment $moment,
        private int $occurredOn,
    ) {}

    public function value(): int
    {
        return $this->value;
    }

    public function moment(): Moment
    {
        return $this->moment;
    }

    public function occurredOn(): int
    {
        return $this->occurredOn;
    }

    public function compareTo(self $other): int
    {
        $cmp = $this->occurredOn <=> $other->occurredOn;

        if ($cmp !== 0) {
            return $cmp;
        }

        $cmp = $other->value <=> $this->value;

        if ($cmp !== 0) {
            return $cmp;
        }

        if ($this->moment === $other->moment) {
            return 0;
        }

        return $this->moment === Moment::START ? -1 : 1;
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'moment' => $this->moment->value,
            'occurredOn' => $this->occurredOn,
        ];
    }
}
