<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Game\Game;
use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function test1(): void
    {
        $game = $this->getLog('plain_1');
        $timeline = $game->timeline();

        self::assertEquals(8, $game->length);
        self::assertFalse($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertTrue($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);
        self::assertEquals(42, $timeline->totalAmberObtained());
        self::assertEquals(59, $timeline->totalAmberObtainedPositive());
        self::assertEquals(-17, $timeline->totalAmberObtainedNegative());
        self::assertEquals(61, $timeline->totalCardsDrawn());
        self::assertEquals(58, $timeline->totalCardsPlayed());
        self::assertEquals(10, $timeline->totalCardsDiscarded());
        self::assertEquals(5, $timeline->filter(EventType::KEY_FORGED)->count());
        self::assertEquals(0, $timeline->totalAmberStolen());
        self::assertEquals(4, $timeline->filter(EventType::FIGHT)->count());
        self::assertEquals(11, $timeline->filter(EventType::REAP)->count());
        self::assertEquals(0, $timeline->at(0)?->turn()->value());
        self::assertEquals(0, $timeline->at(1)?->turn()->value());
        self::assertEquals(0, $timeline->at(2)?->turn()->value());
        self::assertEquals(1, $timeline->at(3)?->turn()->value());
        self::assertEquals(1, $timeline->at(4)?->turn()->value());
        self::assertEquals(8, $timeline->last()?->turn()->value());
    }

    public function test2(): void
    {
        $game = $this->getLog('plain_2');

        self::assertEquals(3, $game->length);
        self::assertTrue($game->player1->isFirst);
        self::assertTrue($game->player1->isWinner);
        self::assertFalse($game->player2->isFirst);
        self::assertFalse($game->player2->isWinner);

        self::assertEquals(4, $game->timeline()->totalAmberObtained());
        self::assertEquals(15, $game->timeline()->totalCardsDrawn());
        self::assertEquals(4, $game->timeline()->totalCardsPlayed());
        self::assertEquals(0, $game->timeline()->totalCardsDiscarded());
        self::assertEquals(0, $game->timeline()->filter(EventType::KEY_FORGED)->count());
    }

    public function testExtraTurns(): void
    {
        $game = $this->getLog('plain_extra_turns');

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

    public function testTokens(): void
    {
        $game = $this->getLog('plain_3');

        self::assertEquals(8, $game->player1->timeline->filter(EventType::TOKEN_CREATED)->count());
        self::assertEquals(0, $game->player2->timeline->filter(EventType::TOKEN_CREATED)->count());
    }

    public function testProphecies(): void
    {
        $game = $this->getLog('plain_4');

        self::assertEquals(11, $game->player1->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(10, $game->player2->timeline->filter(EventType::PROPHECY_ACTIVATED)->count());
        self::assertEquals(10, $game->player1->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(9, $game->player2->timeline->filter(EventType::PROPHECY_FULFILLED)->count());
        self::assertEquals(10, $game->player1->timeline->filter(EventType::FATE_RESOLVED)->count());
        self::assertEquals(9, $game->player2->timeline->filter(EventType::FATE_RESOLVED)->count());
    }

    private function getLog(string $file): Game
    {
        $log = file_get_contents('tests/data/' . $file . '.txt');

        if (false === $log) {
            self::markTestIncomplete();
        }

        $parser = new GameLogParser();

        return $parser->execute($log);
    }
}
