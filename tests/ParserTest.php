<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\ParseType;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function test1(): void
    {
        $log = file_get_contents('tests/data/game1.html');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log, ParseType::HTML);

        self::assertEquals(12, $game->length);
        self::assertFalse($game->player1->isFirst);
        self::assertFalse($game->player1->isWinner);
        self::assertTrue($game->player2->isFirst);
        self::assertTrue($game->player2->isWinner);
        self::assertEquals(35, $game->player1->cardsDrawn->total());
        self::assertEquals(38, $game->player2->cardsDrawn->total());
    }

    public function test2(): void
    {
        $log = file_get_contents('tests/data/game2.html');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log, ParseType::HTML);

        self::assertEquals(9, $game->length);
        self::assertTrue($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertEquals(27, $game->player1->cardsDrawn->total());
        self::assertEquals(48, $game->player2->cardsDrawn->total());
    }

    public function test3(): void
    {
        $log = file_get_contents('tests/data/game3.html');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log, ParseType::HTML);

        self::assertEquals(10, $game->length);
    }

    public function test4Plain(): void
    {
        $log = file_get_contents('tests/data/game4.txt');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log);

        self::assertEquals(1, $game->length);
    }

    public function test4Html(): void
    {
        $log = file_get_contents('tests/data/game4.html');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();
        $game = $parser->execute($log, ParseType::HTML);

        self::assertEquals(1, $game->length);
    }
}
