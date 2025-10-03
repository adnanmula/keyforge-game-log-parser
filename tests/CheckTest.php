<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class CheckTest extends TestCase
{
    use GetTestData;

    public function testAmber1(): void
    {
        $game = $this->getLog('1');

        self::assertEquals(6, $game->timeline()->filter(EventType::CHECK_DECLARED)->count());
        self::assertEquals(3, $game->player1->timeline->filter(EventType::CHECK_DECLARED)->count());
        self::assertEquals(3, $game->player2->timeline->filter(EventType::CHECK_DECLARED)->count());
    }

    public function testAmber2(): void
    {
        $game = $this->getLog('2');

        self::assertEquals(0, $game->timeline()->filter(EventType::CHECK_DECLARED)->count());
        self::assertEquals(0, $game->player1->timeline->filter(EventType::CHECK_DECLARED)->count());
        self::assertEquals(0, $game->player2->timeline->filter(EventType::CHECK_DECLARED)->count());
    }
}
