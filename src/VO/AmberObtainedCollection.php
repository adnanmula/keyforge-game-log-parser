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
}
