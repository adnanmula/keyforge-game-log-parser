<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class UnforgedKeysTest extends TestCase
{
    use GetTestData;

    public function testUnforgedKeys(): void
    {
        $game = $this->getLog('keys_unforged_and_amber_stolen');

        $timeline = $game->timeline();
        $timelinePlayer1 = $game->player1->timeline;
        $timelinePlayer2 = $game->player2->timeline;

        $allEvents = $timeline->filter(EventType::KEY_UNFORGED);
        $player1Events = $timelinePlayer1->filter(EventType::KEY_UNFORGED);
        $player2Events = $timelinePlayer2->filter(EventType::KEY_UNFORGED);

        self::assertEquals('NaN', $game->player1->name);
        self::assertEquals('nan26', $game->player2->name);

        self::assertEquals(3, $game->player1->score);
        self::assertEquals(2, $game->player2->score);

        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(2, $game->loser()?->score);

        self::assertEquals(3, $allEvents->count());
        self::assertEquals(2, $player1Events->count());
        self::assertEquals(1, $player2Events->count());

        self::assertEquals(8, $timeline->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(4, $timelinePlayer1->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(4, $timelinePlayer2->filter(EventType::KEY_FORGED)->count());

        self::assertEquals('red', $player1Events->at(0)?->payload()['key']);
        self::assertEquals('red', $player1Events->at(0)?->value());
        self::assertEquals($game->player2->name, $player1Events->at(0)?->payload()['target']);
        self::assertEquals('Break-key', $player1Events->at(0)?->payload()['card']);

        self::assertEquals('blue', $player1Events->at(1)?->payload()['key']);
        self::assertEquals('blue', $player1Events->at(1)?->value());
        self::assertEquals($game->player2->name, $player1Events->at(1)?->payload()['target']);
        self::assertEquals('Turnkey', $player1Events->at(1)?->payload()['card']);

        self::assertEquals('red', $player2Events->at(0)?->payload()['key']);
        self::assertEquals('red', $player2Events->at(0)?->value());
        self::assertEquals($game->player1->name, $player2Events->at(0)?->payload()['target']);
        self::assertEquals('Art Project', $player2Events->at(0)?->payload()['card']);

        self::assertEquals($allEvents->at(0), $player2Events->at(0));
        self::assertEquals($allEvents->at(1), $player1Events->at(0));
        self::assertEquals($allEvents->at(2), $player1Events->at(1));
    }
}
