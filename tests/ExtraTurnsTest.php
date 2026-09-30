<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class ExtraTurnsTest extends TestCase
{
    use GetTestData;

    public function testExtraTurns1(): void
    {
        $game = $this->getLog('extra_turns');

        $allEvents = $game->timeline()->filter(EventType::EXTRA_TURN);
        $player1Events = $game->player1->timeline->filter(EventType::EXTRA_TURN);
        $player2Events = $game->player2->timeline->filter(EventType::EXTRA_TURN);

        self::assertCount(2, $allEvents);
        self::assertCount(1, $player1Events);
        self::assertCount(1, $player2Events);

        self::assertEquals(2, $game->timeline()->totalExtraTurns());
        self::assertEquals('Tachyon Manifold', $allEvents->at(0)?->payload()['trigger']);
        self::assertEquals('Ancestral Timekeeper', $allEvents->at(1)?->payload()['trigger']);
        self::assertEquals(1, $game->player1->timeline->totalExtraTurns());
        self::assertEquals('Tachyon Manifold', $player1Events->at(0)?->payload()['trigger']);
        self::assertEquals(1, $game->player2->timeline->totalExtraTurns());
        self::assertEquals('Ancestral Timekeeper', $player2Events->at(0)?->payload()['trigger']);
    }

    public function testExtraTurns2(): void
    {
        $game = $this->getLog('extra_turns_2');

        $allEvents = $game->timeline()->filter(EventType::EXTRA_TURN);
        $player1Events = $game->player1->timeline->filter(EventType::EXTRA_TURN);
        $player2Events = $game->player2->timeline->filter(EventType::EXTRA_TURN);

        self::assertCount(2, $allEvents);
        self::assertCount(1, $player1Events);
        self::assertCount(1, $player2Events);

        self::assertEquals(2, $game->timeline()->totalExtraTurns());
        self::assertEquals('Ancestral Timekeeper', $allEvents->at(0)?->payload()['trigger']);
        self::assertEquals('Tachyon Manifold', $allEvents->at(1)?->payload()['trigger']);
        self::assertEquals(1, $game->player1->timeline->totalExtraTurns());
        self::assertEquals('Ancestral Timekeeper', $player1Events->at(0)?->payload()['trigger']);
        self::assertEquals(1, $game->player2->timeline->totalExtraTurns());
        self::assertEquals('Tachyon Manifold', $player2Events->at(0)?->payload()['trigger']);
    }
}
