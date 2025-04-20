<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Event;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\TurnMoment;

final readonly class AmberObtained implements Item
{
    public function __construct(
        private string $player,
        private Turn $turn,
        private int $keys,
        private int $value,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            $array['player'],
            new Turn(
                $array['turn']['value'],
                TurnMoment::from($array['turn']['moment']),
                $array['turn']['occurredOn'],
            ),
            $array['keys'],
            $array['value'],
        );
    }

    public function type(): Event
    {
        return Event::AMBER_OBTAINED;
    }

    public function player(): string
    {
        return $this->player;
    }

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function keys(): int
    {
        return $this->keys;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'player' => $this->player,
            'type' => $this->type()->name,
            'turn' => $this->turn->jsonSerialize(),
            'keys' => $this->keys,
            'value' => $this->value,
        ];
    }
}
