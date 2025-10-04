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

        self::assertEquals(11, $game->player1->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(10, $game->player2->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(10, $game->player1->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(9, $game->player2->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(10, $game->player1->timeline->filter(EventType::FATE_RESOLVED)->count());
        self::assertEquals(9, $game->player2->timeline->filter(EventType::FATE_RESOLVED)->count());
    }
}
