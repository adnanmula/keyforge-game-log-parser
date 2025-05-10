<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<ExtraTurn> */
final class ExtraTurnCollection extends Collection
{
    public function __construct(ExtraTurn ...$item)
    {
        parent::__construct(...$item);
    }

    public function total(): int
    {
        return $this->count();
    }
}
