<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\ParseType;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function test1(): void
    {
        $log = file_get_contents('tests/data/plain_1.txt');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log, ParseType::PLAIN);

        self::assertEquals(8, $game->length);
        self::assertFalse($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertTrue($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertEquals(42, $game->amberObtained()->total());
        self::assertEquals(59, $game->amberObtained()->totalPositive());
        self::assertEquals(-17, $game->amberObtained()->totalNegative());
        self::assertEquals(61, $game->cardsDrawn()->total());
        self::assertEquals(58, $game->cardsPlayed()->total());
        self::assertEquals(10, $game->cardsDiscarded()->total());
        self::assertEquals(5, $game->keysForged()->count());
        self::assertEquals(0, $game->amberStolen()->total());
        self::assertEquals(4, $game->fights()->count());
        self::assertEquals(11, $game->reaps()->count());
    }

    public function test2(): void
    {
        $log = file_get_contents('tests/data/plain_2.txt');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log, ParseType::PLAIN);

        self::assertEquals(3, $game->length);
        self::assertTrue($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);

        self::assertEquals(4, $game->amberObtained()->total());
        self::assertEquals(15, $game->cardsDrawn()->total());
        self::assertEquals(4, $game->cardsPlayed()->total());
        self::assertEquals(0, $game->cardsDiscarded()->total());
        self::assertEquals(0, $game->keysForged()->count());
    }
}
