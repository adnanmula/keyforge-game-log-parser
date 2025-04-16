<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<CardsPlayed> */
final class CardsPlayedCollection extends Collection
{
    public function __construct(CardsPlayed ...$item)
    {
        parent::__construct(...$item);
    }

    public function total(): int
    {
        return array_reduce(
            $this->items(),
            static fn (int $c, CardsPlayed $s): int => $c + count($s->cards),
            0,
        );
    }
}
