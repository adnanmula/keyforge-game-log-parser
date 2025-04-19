<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\TurnMoment;

final readonly class KeyForged implements Item
{
    public function __construct(
        private string $player,
        private Turn $turn,
        private string $value,
        private int $amberCost,
        private int $amberRemaining,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            $array['player'],
            new Turn(
                $array['turn']['value'],
                TurnMoment::from($array['turn']['moment']),
            ),
            $array['value'],
            $array['amber_cost'],
            $array['amber_remaining'],
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

    public function value(): string
    {
        return $this->value;
    }

    public function amberCost(): int
    {
        return $this->amberCost;
    }

    public function amberRemaining(): int
    {
        return $this->amberRemaining;
    }

    public function jsonSerialize(): array
    {
        return [
            'player' => $this->player,
            'turn' => $this->turn->jsonSerialize(),
            'value' => $this->value,
            'amber_cost' => $this->amberCost,
            'amber_remaining' => $this->amberRemaining,
        ];
    }
}
