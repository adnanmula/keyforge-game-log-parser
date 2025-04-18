<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\HasTurn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Source;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;

final readonly class CardsDiscarded implements Item, HasTurn
{
    public function __construct(
        public Turn $turn,
        public Source $source,
        public int $amount,
    ) {}

    public function turn(): Turn
    {
        return $this->turn;
    }

    public function jsonSerialize(): array
    {
        return [
            'turn' => $this->turn,
            'amount' => $this->amount,
            'source' => $this->source->name,
        ];
    }
}
