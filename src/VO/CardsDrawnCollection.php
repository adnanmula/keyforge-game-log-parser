<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<CardsDrawn> */
final class CardsDrawnCollection extends Collection
{
    public function __construct(CardsDrawn ...$items)
    {
        parent::__construct(...$items);
    }

    public function total(): int
    {
        return array_reduce(
            $this->items(),
            static fn (int $c, $s): int => $c + $s->amount,
            0,
        );
    }
}
