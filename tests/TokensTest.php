<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class TokensTest extends TestCase
{
    use GetTestData;

    public function testTokens(): void
    {
        $game = $this->getLog('3');

        $player1Events = $game->player1->timeline->filter(EventType::TOKEN_CREATED);
        $player2Events = $game->player2->timeline->filter(EventType::TOKEN_CREATED);

        self::assertEquals(8, $player1Events->count());
        self::assertEquals('Gate Warden', $player1Events->at(0)?->value);
        self::assertEquals('Titan Outpost', $player1Events->at(1)?->value);
        self::assertEquals('Auto-Autopsy 2.1', $player1Events->at(2)?->value);
        self::assertEquals('Hypothesize', $player1Events->at(3)?->value);
        self::assertEquals('Golis Artificer', $player1Events->at(4)?->value);
        self::assertEquals('Golis Artificer', $player1Events->at(5)?->value);
        self::assertEquals('Titan Outpost', $player1Events->at(6)?->value);
        self::assertEquals('Gate Warden', $player1Events->at(7)?->value);
        self::assertEquals(0, $player2Events->count());
    }
}
