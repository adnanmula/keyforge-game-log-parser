<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use PHPUnit\Framework\TestCase;

final class FirstPlayerTest extends TestCase
{
    use GetTestData;

    public function testRandomPlayerIsFirst(): void
    {
        $game = $this->getLog('minimal_game');

        self::assertTrue($game->player1->isFirst);
        self::assertFalse($game->player2->isFirst);
        self::assertTrue($game->winner()?->isFirst);
        self::assertFalse($game->loser()?->isFirst);
    }

    public function testPlayerChoosesToGoFirst(): void
    {
        $game = $this->getLog('player_chooses_to_go_first');

        self::assertFalse($game->player1->isFirst);
        self::assertTrue($game->player2->isFirst);
        self::assertFalse($game->winner()?->isFirst);
        self::assertTrue($game->loser()?->isFirst);
    }

    public function testPlayerChoosesToGoSecond(): void
    {
        $game = $this->getLog('player_chooses_to_go_second');

        self::assertFalse($game->player1->isFirst);
        self::assertTrue($game->player2->isFirst);
        self::assertTrue($game->winner()?->isFirst);
        self::assertFalse($game->loser()?->isFirst);
    }
}
