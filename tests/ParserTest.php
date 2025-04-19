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
        self::assertEquals(125, $game->amberObtained()->total());
        self::assertEquals(61, $game->totalCardsDrawn());
        self::assertEquals(58, $game->totalCardsPlayed());
        self::assertEquals(10, $game->totalCardsDiscarded());
        self::assertEquals(5, $game->totalKeysForged());
    }
}
