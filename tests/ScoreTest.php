<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use PHPUnit\Framework\TestCase;

final class ScoreTest extends TestCase
{
    use GetTestData;

    public function testScore1(): void
    {
        $game = $this->getLog('1');

        self::assertEquals(3, $game->player1->score);
        self::assertEquals(2, $game->player2->score);
        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(2, $game->loser()?->score);
    }

    public function testScore2(): void
    {
        $game = $this->getLog('2');

        self::assertFalse($game->player1->hasConceded);
        self::assertTrue($game->player2->hasConceded);
        self::assertEquals(3, $game->player1->score);
        self::assertEquals(0, $game->player2->score);
        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(0, $game->loser()?->score);
    }

    public function testScore3(): void
    {
        $game = $this->getLog('3');

        self::assertEquals(3, $game->player1->score);
        self::assertEquals(2, $game->player2->score);
        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(2, $game->loser()?->score);
    }
}
