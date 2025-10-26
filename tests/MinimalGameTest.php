<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class MinimalGameTest extends TestCase
{
    use GetTestData;

    public function testMinimal1(): void
    {
        $game = $this->getLog('minimal_game');
        $timeline = $game->timeline();

        self::assertFalse($game->player1->isWinner);
        self::assertTrue($game->player1->hasConceded);
        self::assertTrue($game->player2->isWinner);
        self::assertFalse($game->player2->hasConceded);
        self::assertFalse($game->winner()?->hasConceded);
        self::assertTrue($game->winner()->isWinner);
        self::assertTrue($game->loser()?->hasConceded);
        self::assertFalse($game->loser()->isWinner);

        self::assertEquals(0, $timeline->totalAmberObtained());
        self::assertEquals(0, $timeline->totalAmberObtainedPositive());
        self::assertEquals(0, $timeline->totalAmberObtainedNegative());
        self::assertEquals(13, $timeline->totalByValue(EventType::CARDS_DRAWN));
        self::assertEquals(13, $timeline->totalCardsDrawn());
        self::assertEquals(0, $timeline->totalCardsPlayed());
        self::assertEquals(0, $timeline->totalByValue(EventType::AMBER_STOLEN));
        self::assertEquals(0, $timeline->filter(EventType::FIGHT)->count());
        self::assertEquals(0, $timeline->filter(EventType::REAP)->count());
    }
}
