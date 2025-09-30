<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

enum TurnMoment: string
{
    case START = 'START';
    case BETWEEN = 'BETWEEN';
    case END = 'END';
}
