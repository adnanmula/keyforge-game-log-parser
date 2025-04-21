<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<AmberObtained> */
final class AmberObtainedCollection extends Collection
{
    public function __construct(AmberObtained ...$steps)
    {
        parent::__construct(...$steps);
    }

    public function total(): int
    {
        return array_reduce(
            $this->items(),
            static fn (int $c, AmberObtained $s): int => $c + $s->delta(),
            0,
        );
    }
}
