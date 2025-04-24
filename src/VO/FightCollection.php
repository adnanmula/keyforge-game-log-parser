<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<Fight> */
final class FightCollection extends Collection
{
    public function __construct(Fight ...$item)
    {
        parent::__construct(...$item);
    }

    public function amountToArrayByTurn(): array
    {
        $result = [];

        foreach ($this->items() as $item) {
            $turn = $item->turn()->value();
            $result[$turn] = ($result[$turn] ?? 0) + 1;
        }

        return $result;
    }
}
