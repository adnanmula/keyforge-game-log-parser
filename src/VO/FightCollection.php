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

    public function amountToArrayByTurn(?int $turns = null): array
    {
        $result = [];

        foreach ($this->items() as $item) {
            $turn = $item->turn()->value();
            $result[$turn] = ($result[$turn] ?? 0) + 1;
        }

        if (null !== $turns) {
            $result = $this->fillMissingTurns($result, $turns);
        }

        return $result;
    }

    private function fillMissingTurns(array $data, int $turns): array
    {
        $filled = [];

        for ($i = 1; $i <= $turns; $i++) {
            $filled[$i] = $data[$i] ?? 0;
        }

        return $filled;
    }
}
