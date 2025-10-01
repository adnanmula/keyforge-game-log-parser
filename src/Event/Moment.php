<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

enum Moment: string
{
    case START = 'START';
    case BETWEEN = 'BETWEEN';
    case END = 'END';
}
