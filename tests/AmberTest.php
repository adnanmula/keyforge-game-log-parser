<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class AmberTest extends TestCase
{
    use GetTestData;

    public function testAmber1(): void
    {
        $game = $this->getLog('1');
        $timeline = $game->timeline();

        self::assertEquals(42, $timeline->totalAmberObtained());
        self::assertEquals(59, $timeline->totalAmberObtainedPositive());
        self::assertEquals(-17, $timeline->totalAmberObtainedNegative());
        self::assertEquals(61, $timeline->totalByValue(EventType::CARDS_DRAWN));
        self::assertEquals(58, $timeline->totalCardsPlayed());
        self::assertEquals(0, $timeline->totalByValue(EventType::AMBER_STOLEN));
        self::assertEquals(4, $timeline->filter(EventType::FIGHT)->count());
        self::assertEquals(11, $timeline->filter(EventType::REAP)->count());
    }

    public function testAmber2(): void
    {
        $game = $this->getLog('2');

        self::assertEquals(4, $game->timeline()->totalByPayloadValue('delta', EventType::AMBER_OBTAINED));
        self::assertEquals(15, $game->timeline()->totalByValue(EventType::CARDS_DRAWN));
        self::assertEquals(4, $game->timeline()->totalCardsPlayed());
        self::assertEquals(0, $game->timeline()->totalByValue(EventType::CARDS_DISCARDED));
    }
}
