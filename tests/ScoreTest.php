<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class ScoreTest extends TestCase
{
    use GetTestData;

    public function testScore1(): void
    {
        $game = $this->getLog('1');
        $timeline = $game->timeline();

        self::assertEquals(3, $game->player1->score);
        self::assertEquals(2, $game->player2->score);
        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(2, $game->loser()?->score);

        self::assertFalse($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player1->hasConceded);

        self::assertTrue($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertFalse($game->player2->hasConceded);

        self::assertEquals(5, $timeline->filter(EventType::KEY_FORGED)->count());
    }

    public function testScore2(): void
    {
        $game = $this->getLog('2');

        self::assertTrue($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player1->hasConceded);
        self::assertEquals(3, $game->player1->score);

        self::assertFalse($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertTrue($game->player2->hasConceded);
        self::assertEquals(0, $game->player2->score);

        self::assertEquals(3, $game->winner()?->score);
        self::assertEquals(0, $game->loser()?->score);

        self::assertEquals(0, $game->timeline()->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(0, $game->player1->timeline->filter(EventType::PLAYER_CONCEDED)->count());
        self::assertEquals(1, $game->player2->timeline->filter(EventType::PLAYER_CONCEDED)->count());
    }

    public function testMinimal1(): void
    {
        $game = $this->getLog('minimal_game');
        $timeline = $game->timeline();
        $timelinePLayer1 = $game->player1->timeline;
        $timelinePLayer2 = $game->player2->timeline;

        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player1->hasConceded);
        self::assertTrue($game->player1->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertTrue($game->player2->hasConceded);
        self::assertFalse($game->player2->isFirst);
        self::assertFalse($game->winner()?->hasConceded);
        self::assertTrue($game->winner()->isWinner);
        self::assertTrue($game->winner()->isFirst);
        self::assertTrue($game->loser()?->hasConceded);
        self::assertFalse($game->loser()->isWinner);
        self::assertFalse($game->loser()->isFirst);

        self::assertEquals(0, $timeline->totalAmberObtained());
        self::assertEquals(0, $timeline->totalAmberObtainedPositive());
        self::assertEquals(0, $timeline->totalAmberObtainedNegative());
        self::assertEquals(0, $timelinePLayer1->totalAmberObtained());
        self::assertEquals(0, $timelinePLayer2->totalAmberObtained());
        self::assertEquals(0, $timelinePLayer1->totalAmberObtainedPositive());
        self::assertEquals(0, $timelinePLayer2->totalAmberObtainedPositive());
        self::assertEquals(0, $timelinePLayer1->totalAmberObtainedNegative());
        self::assertEquals(0, $timelinePLayer2->totalAmberObtainedNegative());

        self::assertEquals(13, $timeline->totalByValue(EventType::CARDS_DRAWN));
        self::assertEquals(13, $timeline->totalCardsDrawn());

        self::assertEquals(7, $timelinePLayer1->totalCardsDrawn());
        self::assertEquals(6, $timelinePLayer2->totalCardsDrawn());

        self::assertEquals(0, $timeline->totalCardsPlayed());
        self::assertEquals(0, $timeline->totalByValue(EventType::AMBER_STOLEN));

        self::assertCount(
            0,
            $timeline->filter(...array_filter(
                EventType::cases(),
                static fn (EventType $e): bool => $e !== EventType::CARDS_DRAWN && $e !== EventType::PLAYER_CONCEDED,
            )),
        );

        self::assertCount(1, $timeline->filter(EventType::PLAYER_CONCEDED));
        self::assertEquals('nan26', $timeline->filter(EventType::PLAYER_CONCEDED)->first()?->player());
    }
}
