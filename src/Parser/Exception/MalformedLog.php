<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Exception;

use InvalidArgumentException;

final class MalformedLog extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Malformed or incomplete log');
    }
}
