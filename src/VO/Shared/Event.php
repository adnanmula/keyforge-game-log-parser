<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

enum Event: string
{
    case AMBER_OBTAINED = 'AMBER_OBTAINED';
    case CARDS_DISCARDED = 'CARDS_DISCARDED';
    case CARDS_DRAWN = 'CARDS_DRAWN';
    case CARDS_PLAYED = 'CARDS_PLAYED';
    case HOUSE_CHOSEN = 'HOUSE_CHOSEN';
    case KEY_FORGED = 'KEY_FORGED';
    case FIGHT = 'FIGHT';
    case REAP = 'REAP';
    case AMBER_STOLEN = 'AMBER_STOLEN';
}
