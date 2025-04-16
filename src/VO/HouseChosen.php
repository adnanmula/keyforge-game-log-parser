<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\HasTurn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;

final readonly class HouseChosen implements Item, HasTurn
{
    public function __construct(
        public Turn $turn,
        public string $house,
    ) {}

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function jsonSerialize(): array
    {
        return [
            'turn' => $this->turn->jsonSerialize(),
            'house' => $this->house,
        ];
    }
}
