<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class UnforgeTest extends TestCase
{
    use GetTestData;

    public function testUnforges(): void
    {
        $game = $this->getLog('unforges');
        $timeline = $game->timeline();
        $timelinePlayer1 = $game->player1->timeline;
        $timelinePlayer2 = $game->player2->timeline;

        self::assertEquals(3, $game->player1->score);
        self::assertEquals(1, $game->player2->score);
        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(1, $game->loser()?->score);
        self::assertEquals(4, $timeline->filter(EventType::KEY_UNFORGED)->count());
        self::assertEquals(2, $timelinePlayer1->filter(EventType::KEY_UNFORGED)->count());
        self::assertEquals(2, $timelinePlayer2->filter(EventType::KEY_UNFORGED)->count());

        self::assertEquals('blue', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['key']);
        self::assertEquals($game->player2->name, $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['target']);
        self::assertEquals('red', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(1)?->payload()['key']);
        self::assertEquals($game->player2->name, $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['target']);
        self::assertEquals('red', $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['key']);
        self::assertEquals($game->player1->name, $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['target']);
        self::assertEquals('blue', $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(1)?->payload()['key']);
        self::assertEquals($game->player1->name, $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['target']);
    }
}
