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
}
