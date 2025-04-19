<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

enum TurnMoment: string
{
    case START = 'START';
    case BETWEEN = 'BETWEEN';
    case END = 'END';
}
