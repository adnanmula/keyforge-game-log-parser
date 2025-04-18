<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<CardsDiscarded> */
final class CardsDiscardedCollection extends Collection
{
    public function __construct(CardsDiscarded ...$items)
    {
        parent::__construct(...$items);
    }

    public function total(): int
    {
        return array_reduce(
            $this->items(),
            static fn (int $c, CardsDiscarded $s): int => $c + $s->amount,
            0,
        );
    }
}
