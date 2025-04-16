<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\HasTurn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;

final readonly class AmberObtained implements Item, HasTurn
{
    public function __construct(
        public Turn $turn,
        public int $keys,
        public int $amberAmount,
    ) {}

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function jsonSerialize(): array
    {
        return [
            'turn' => $this->turn->jsonSerialize(),
            'keys' => $this->keys,
            'amber_amount' => $this->amberAmount,
        ];
    }
}
