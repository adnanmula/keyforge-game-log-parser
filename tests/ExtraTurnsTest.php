<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class ExtraTurnsTest extends TestCase
{
    use GetTestData;

    public function testExtraTurns(): void
    {
        $game = $this->getLog('6');

        $extraTurn1 = $game->player1->timeline->filter(EventType::EXTRA_TURN)->at(0);
        $extraTurn2 = $game->player2->timeline->filter(EventType::EXTRA_TURN)->at(0);
        $extraTurn3 = $game->timeline()->filter(EventType::EXTRA_TURN)->at(0);
        $extraTurn4 = $game->timeline()->filter(EventType::EXTRA_TURN)->at(1);

        self::assertEquals(1, $game->player1->timeline->totalExtraTurns());
        self::assertEquals('Ancestral Timekeeper', $extraTurn1?->payload()['trigger'] ?? null);
        self::assertEquals(1, $game->player2->timeline->totalExtraTurns());
        self::assertEquals('Tachyon Manifold', $extraTurn2?->payload()['trigger'] ?? null);
        self::assertEquals(2, $game->timeline()->totalExtraTurns());
        self::assertEquals('Ancestral Timekeeper', $extraTurn3?->payload()['trigger'] ?? null);
        self::assertEquals('Tachyon Manifold', $extraTurn4?->payload()['trigger'] ?? null);
    }
}
