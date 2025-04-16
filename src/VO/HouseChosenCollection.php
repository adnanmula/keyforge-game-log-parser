<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<HouseChosen> */
final class HouseChosenCollection extends Collection
{
    public function __construct(HouseChosen ...$item)
    {
        parent::__construct(...$item);
    }
}
