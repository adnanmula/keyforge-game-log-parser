<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Event;

enum EventType: string
{
    case AMBER_OBTAINED = 'AMBER_OBTAINED';
    case CARDS_DISCARDED = 'CARDS_DISCARDED';
    case CARDS_DRAWN = 'CARDS_DRAWN';
    case CARDS_PLAYED = 'CARDS_PLAYED';
    case HOUSE_CHOSEN = 'HOUSE_CHOSEN';
    case KEY_FORGED = 'KEY_FORGED';
    case FIGHT = 'FIGHT';
    case REAP = 'REAP';
    case CARD_USED = 'CARD_USED';
    case AMBER_STOLEN = 'AMBER_STOLEN';
    case EXTRA_TURN = 'EXTRA_TURN';
    case TOKEN_CREATED = 'TOKEN_CREATED';
    case PROPHECY_ACTIVATED = 'PROPHECY_ACTIVATED';
    case PROPHECY_FULFILLED = 'PROPHECY_FULFILLED';
    case FATE_RESOLVED = 'FATE_RESOLVED';
}
