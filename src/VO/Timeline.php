<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Item;

/** @extends Collection<Item> */
final class Timeline extends Collection
{
    public function __construct(Item ...$item)
    {
        parent::__construct(...$item);
    }
}
