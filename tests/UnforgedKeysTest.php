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

        self::assertEquals('NaN', $game->player1->name);
        self::assertEquals('nan26', $game->player2->name);

        self::assertEquals(3, $game->player1->score);
        self::assertEquals(2, $game->player2->score);

        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(2, $game->loser()?->score);

        self::assertEquals(3, $timeline->filter(EventType::KEY_UNFORGED)->count());
        self::assertEquals(2, $timelinePlayer1->filter(EventType::KEY_UNFORGED)->count());
        self::assertEquals(1, $timelinePlayer2->filter(EventType::KEY_UNFORGED)->count());

        self::assertEquals(8, $timeline->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(4, $timelinePlayer1->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(4, $timelinePlayer2->filter(EventType::KEY_FORGED)->count());

        self::assertEquals('red', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['key']);
        self::assertEquals('red', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->value());
        self::assertEquals($game->player2->name, $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['target']);
        self::assertEquals('Break-key', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['card']);

        self::assertEquals('blue', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(1)?->payload()['key']);
        self::assertEquals('blue', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(1)?->value());
        self::assertEquals($game->player2->name, $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(1)?->payload()['target']);
        self::assertEquals('Turnkey', $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(1)?->payload()['card']);

        self::assertEquals('red', $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['key']);
        self::assertEquals('red', $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->value());
        self::assertEquals($game->player1->name, $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['target']);
        self::assertEquals('Art Project', $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0)?->payload()['card']);

        self::assertEquals($timeline->filter(EventType::KEY_UNFORGED)->at(0), $timelinePlayer2->filter(EventType::KEY_UNFORGED)->at(0));
        self::assertEquals($timeline->filter(EventType::KEY_UNFORGED)->at(1), $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(0));
        self::assertEquals($timeline->filter(EventType::KEY_UNFORGED)->at(2), $timelinePlayer1->filter(EventType::KEY_UNFORGED)->at(1));
    }
}
