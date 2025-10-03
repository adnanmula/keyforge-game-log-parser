<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use PHPUnit\Framework\TestCase;

final class ChainsTest extends TestCase
{
    use GetTestData;

    public function testChains1(): void
    {
        $game = $this->getLog('3');

        self::assertEquals(1, $game->player1->timeline->filter(EventType::CHAINS_REDUCED)->count());
        self::assertEquals(0, $game->player1->timeline->filter(EventType::CHAINS_REDUCED)->at(0)?->payload()['currentChains']);
        self::assertEquals(0, $game->player2->timeline->filter(EventType::CHAINS_REDUCED)->count());
        self::assertEquals(1, $game->timeline()->filter(EventType::CHAINS_REDUCED)->count());
    }

    public function testChains2(): void
    {
        $game = $this->getLog('5');

        $player1Events = $game->player1->timeline->filter(EventType::CHAINS_REDUCED);
        $player2Events = $game->player2->timeline->filter(EventType::CHAINS_REDUCED);

        self::assertEquals(6, $player1Events->count());
        self::assertEquals(3, $player2Events->count());
        self::assertEquals(9, $game->timeline()->filter(EventType::CHAINS_REDUCED)->count());

        self::assertEquals(2, $player1Events->at(0)?->payload()['currentChains']);
        self::assertEquals(1, $player1Events->at(1)?->payload()['currentChains']);
        self::assertEquals(0, $player1Events->at(2)?->payload()['currentChains']);
        self::assertEquals(2, $player1Events->at(3)?->payload()['currentChains']);
        self::assertEquals(1, $player1Events->at(4)?->payload()['currentChains']);
        self::assertEquals(0, $player1Events->at(5)?->payload()['currentChains']);

        self::assertEquals(2, $player2Events->at(0)?->payload()['currentChains']);
        self::assertEquals(1, $player2Events->at(1)?->payload()['currentChains']);
        self::assertEquals(0, $player2Events->at(2)?->payload()['currentChains']);
    }

    public function testChains3(): void
    {
        $game = $this->getLog('7');

        $player1Events = $game->player1->timeline->filter(EventType::CHAINS_ADDED);
        $player2Events = $game->player2->timeline->filter(EventType::CHAINS_ADDED);

        self::assertEquals(30, $player1Events->count());

        self::assertEquals('Coward’s End', $player1Events->at(0)?->payload()['trigger']);
        self::assertEquals(3, $player1Events->at(0)?->value());
        self::assertEquals(Source::PLAYER, $player1Events->at(0)?->source());

        self::assertEquals('Binding Irons', $player1Events->at(2)?->payload()['trigger']);
        self::assertEquals(3, $player1Events->at(2)?->value());
        self::assertEquals(Source::OPPONENT, $player1Events->at(2)?->source());

        self::assertEquals('Mimicry', $player1Events->at(4)?->payload()['trigger']);
        self::assertEquals(3, $player1Events->at(4)?->value());
        self::assertEquals(Source::OPPONENT, $player1Events->at(4)?->source());

        self::assertEquals('Angwish', $player1Events->at(5)?->payload()['trigger']);
        self::assertEquals(1, $player1Events->at(5)?->value());
        self::assertEquals(Source::PLAYER, $player1Events->at(5)?->source());

        self::assertEquals(3, $player2Events->count());

        self::assertEquals('Save the Pack', $player2Events->at(0)?->payload()['trigger']);
        self::assertEquals(1, $player2Events->at(0)?->value());
        self::assertEquals(Source::PLAYER, $player2Events->at(0)?->source());

        self::assertEquals('Binding Irons', $player2Events->at(1)?->payload()['trigger']);
        self::assertEquals(3, $player2Events->at(1)?->value());
        self::assertEquals(Source::OPPONENT, $player2Events->at(1)?->source());

        self::assertEquals('Collector Worm', $player2Events->at(2)?->payload()['trigger']);
        self::assertEquals(1, $player2Events->at(2)?->value());
        self::assertEquals(Source::PLAYER, $player2Events->at(2)?->source());
    }
}
