<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    use GetTestData;

    public function testResult1(): void
    {
        $game = $this->getLog('1');
        $timeline = $game->timeline();

        self::assertFalse($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player1->hasConceded);

        self::assertTrue($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertFalse($game->player2->hasConceded);

        self::assertEquals(5, $timeline->filter(EventType::KEY_FORGED)->count());
    }

    public function testResult2(): void
    {
        $game = $this->getLog('2');

        self::assertTrue($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player1->hasConceded);

        self::assertFalse($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertTrue($game->player2->hasConceded);

        self::assertEquals(0, $game->timeline()->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(0, $game->player1->timeline->filter(EventType::PLAYER_CONCEDED)->count());
        self::assertEquals(1, $game->player2->timeline->filter(EventType::PLAYER_CONCEDED)->count());
    }
}
