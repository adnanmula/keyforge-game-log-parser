<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Game;
use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function test1(): void
    {
        $game = $this->getLog('plain_1');

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
        self::assertEquals(0, $game->timeline()->at(0)?->turn()->value());
        self::assertEquals(0, $game->timeline()->at(1)?->turn()->value());
        self::assertEquals(0, $game->timeline()->at(2)?->turn()->value());
        self::assertEquals(1, $game->timeline()->at(3)?->turn()->value());
        self::assertEquals(1, $game->timeline()->at(4)?->turn()->value());
        self::assertEquals(8, $game->timeline()->last()?->turn()->value());
    }

    public function test2(): void
    {
        $game = $this->getLog('plain_2');

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

    public function testExtraTurns(): void
    {
        $game = $this->getLog('plain_extra_turns');

        self::assertEquals(1, $game->player1->extraTurns->total());
        self::assertEquals('Ancestral Timekeeper', $game->player1->extraTurns->at(0)?->trigger());
        self::assertEquals(1, $game->player2->extraTurns->total());
        self::assertEquals('Tachyon Manifold', $game->player2->extraTurns->at(0)?->trigger());
        self::assertEquals(2, $game->extraTurns()->total());
        self::assertEquals('Ancestral Timekeeper', $game->extraTurns()->at(0)?->trigger());
        self::assertEquals('Tachyon Manifold', $game->extraTurns()->at(1)?->trigger());
    }

    private function getLog(string $file): Game
    {
        $log = file_get_contents('tests/data/' . $file . '.txt');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();

        return $parser->execute($log);
    }
}
