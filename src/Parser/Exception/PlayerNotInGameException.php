<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Exception;

use InvalidArgumentException;

final class PlayerNotInGameException extends InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Player "%s" is not part of the game', $name));
    }
}
