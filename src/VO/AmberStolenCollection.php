<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<AmberStolen> */
final class AmberStolenCollection extends Collection
{
    public function __construct(AmberStolen ...$item)
    {
        parent::__construct(...$item);
    }

    public function total(): int
    {
        return array_reduce(
            $this->items(),
            static fn (int $c, AmberStolen $s): int => $c + $s->value(),
            0,
        );
    }

    public function amountToArrayByTurn(?int $turns = null): array
    {
        $result = [];

        foreach ($this->items() as $item) {
            $turn = $item->turn()->value();
            $result[$turn] = ($result[$turn] ?? 0) + $item->value();
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
