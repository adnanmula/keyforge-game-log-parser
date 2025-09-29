<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Itemsito;

/** @extends Collection<FateResolved|ProphecyActivated|ProphecyFulfilled> */
final class PropheciesTimeline extends Collection
{
    public function __construct(Itemsito ...$item)
    {
        parent::__construct(...$item);
    }
}
