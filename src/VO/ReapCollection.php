<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<Reap> */
final class ReapCollection extends Collection
{
    public function __construct(Reap ...$item)
    {
        parent::__construct(...$item);
    }
}
