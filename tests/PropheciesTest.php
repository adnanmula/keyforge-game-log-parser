<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class PropheciesTest extends TestCase
{
    use GetTestData;

    public function testProphecies(): void
    {
        $game = $this->getLog('4');

        self::assertEquals(12, $game->player1->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(10, $game->player2->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(10, $game->player1->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(9, $game->player2->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(10, $game->player1->timeline->filter(EventType::FATE_RESOLVED)->count());
        self::assertEquals(9, $game->player2->timeline->filter(EventType::FATE_RESOLVED)->count());
    }

    public function testProphecies2(): void
    {
        $game = $this->getLog('12');

        self::assertEquals(10, $game->player1->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(7, $game->player2->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(6, $game->player1->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(3, $game->player2->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(8, $game->player1->timeline->filter(EventType::FATE_RESOLVED)->count());
        self::assertEquals(3, $game->player2->timeline->filter(EventType::FATE_RESOLVED)->count());
    }

    public function testPropheciesSummary(): void
    {
        $game = $this->getLog('4');

        $summary = $game->timeline()->propheciesSummary();

        self::assertArrayHasKey('prophecies', $summary);
        self::assertArrayHasKey('fates', $summary);

        self::assertCount(7, $summary['prophecies']);
        self::assertCount(12, $summary['fates']);

        self::assertEquals(5, $summary['prophecies']['Heads, I Win']['activated']);
        self::assertEquals(3, $summary['prophecies']['Heads, I Win']['fulfilled']);
        self::assertEquals(15.79, $summary['prophecies']['Heads, I Win']['percent']);
    }
}
