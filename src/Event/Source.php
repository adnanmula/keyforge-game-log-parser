<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

enum Source: string
{
    case PLAYER = 'PLAYER';
    case OPPONENT = 'OPPONENT';
    case UNKNOWN = 'UNKNOWN';
}
