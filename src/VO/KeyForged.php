<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\HasTurn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;

final readonly class KeyForged implements Item, HasTurn
{
    public function __construct(
        public Turn $turn,
        public string $key,
        public int $amberCost,
        public int $amberRemaining,
    ) {}

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function jsonSerialize(): array
    {
        return [
            'turn' => $this->turn->jsonSerialize(),
            'key' => $this->key,
            'amber_cost' => $this->amberCost,
            'amber_remaining' => $this->amberRemaining,
        ];
    }
}
