<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class TidesTest extends TestCase
{
    use GetTestData;

    public function testTides(): void
    {
        $game = $this->getLog('5');

        $player1Timeline = $game->player1->timeline->filter(EventType::TIDE_RAISED);
        $player2Timeline = $game->player2->timeline->filter(EventType::TIDE_RAISED);
        $gameTimeline = $game->timeline()->filter(EventType::TIDE_RAISED);

        self::assertEquals(5, $player1Timeline->count());
        self::assertEquals(5, $player2Timeline->count());
        self::assertEquals(10, $gameTimeline->count());

        self::assertEquals('Pour-tal', $player1Timeline->at(0)?->value);
        self::assertEquals('manual', $player2Timeline->at(0)?->value);

        self::assertEquals('manual', $gameTimeline->at(0)?->value);
        self::assertEquals(1, $gameTimeline->at(0)?->turn()->value());
        self::assertEquals(34, $gameTimeline->at(0)?->turn()->occurredOn());
        self::assertEquals('Pour-tal', $gameTimeline->at(1)?->value);
        self::assertEquals(5, $gameTimeline->at(1)?->turn()->value());
        self::assertEquals(142, $gameTimeline->at(1)?->turn()->occurredOn());
    }
}
