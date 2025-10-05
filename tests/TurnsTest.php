<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use PHPUnit\Framework\TestCase;

final class TurnsTest extends TestCase
{
    use GetTestData;

    public function testTurn1(): void
    {
        $game = $this->getLog('1');
        $timeline = $game->timeline();

        self::assertEquals(8, $game->length);

        self::assertEquals(0, $timeline->at(0)?->turn()->value());
        self::assertEquals(0, $timeline->at(1)?->turn()->value());
        self::assertEquals(0, $timeline->at(2)?->turn()->value());
        self::assertEquals(1, $timeline->at(3)?->turn()->value());
        self::assertEquals(1, $timeline->at(4)?->turn()->value());
        self::assertEquals(8, $timeline->last()?->turn()->value());
    }

    public function testTurn2(): void
    {
        $game = $this->getLog('2');
        $timeline = $game->timeline();

        self::assertEquals(3, $game->length);
        self::assertEquals(0, $timeline->at(0)?->turn()->value());
        self::assertEquals(0, $timeline->at(1)?->turn()->value());
        self::assertEquals(0, $timeline->at(2)?->turn()->value());
        self::assertEquals(1, $timeline->at(3)?->turn()->value());
        self::assertEquals(1, $timeline->at(4)?->turn()->value());
        self::assertEquals(3, $timeline->last()?->turn()->value());
    }
}
