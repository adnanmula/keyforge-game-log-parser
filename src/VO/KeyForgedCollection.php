<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Collection;

/** @extends Collection<KeyForged> */
final class KeyForgedCollection extends Collection
{
    public function __construct(KeyForged ...$steps)
    {
        parent::__construct(...$steps);
    }
}
