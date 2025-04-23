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
}
