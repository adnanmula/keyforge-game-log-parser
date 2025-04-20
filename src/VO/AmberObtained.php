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
        private int $value,
        private int $keys,
        private int $delta,
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
            $array['value'],
            $array['keys'],
            $array['delta'],
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
            'player' => $this->player,
            'type' => $this->type()->name,
            'turn' => $this->turn->jsonSerialize(),
            'value' => $this->value,
            'keys' => $this->keys,
            'delta' => $this->delta,
        ];
    }
}
