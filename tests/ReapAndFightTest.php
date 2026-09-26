<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class ReapAndFightTest extends TestCase
{
    use GetTestData;

    public function testReapAndFight1(): void
    {
        $game = $this->getLog('play_creatures_fight_and_reap');

        $player1Timeline = $game->player1->timeline;
        $player2Timeline = $game->player2->timeline;
        $fullTimeline = $game->timeline();

        self::assertEquals(10, $fullTimeline->filter(EventType::FIGHT)->count());
        self::assertEquals(4, $player1Timeline->filter(EventType::FIGHT)->count());
        self::assertEquals(6, $player2Timeline->filter(EventType::FIGHT)->count());

        self::assertEquals(11, $fullTimeline->filter(EventType::REAP)->count());
        self::assertEquals(6, $player1Timeline->filter(EventType::REAP)->count());
        self::assertEquals(5, $player2Timeline->filter(EventType::REAP)->count());

        self::assertEquals(25, $fullTimeline->filter(EventType::CARDS_PLAYED)->count());
        self::assertEquals(25, $fullTimeline->filter(EventType::CARDS_PLAYED)->totalCardsPlayed());
        self::assertEquals(14, $player1Timeline->filter(EventType::CARDS_PLAYED)->totalCardsPlayed());
        self::assertEquals(11, $player2Timeline->filter(EventType::CARDS_PLAYED)->totalCardsPlayed());
    }
}
