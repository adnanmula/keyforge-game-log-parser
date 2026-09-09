<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Exception;

use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;
use InvalidArgumentException;

final class InvalidLogType extends InvalidArgumentException
{
    public function __construct(ParseType $type)
    {
        $message = match ($type) {
            ParseType::PLAIN => 'Log must be a string when using plain type',
            ParseType::HTML => 'Log must be a string when using html type',
            ParseType::ARRAY => 'Log must be an array when using array type',
        };

        parent::__construct($message);
    }
}
