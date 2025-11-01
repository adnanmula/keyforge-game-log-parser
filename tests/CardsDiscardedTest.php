<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Game\Timeline;
use PHPUnit\Framework\TestCase;

final class CardsDiscardedTest extends TestCase
{
    use GetTestData;

    public function testDiscard1(): void
    {
        $game = $this->getLog('8');

        self::assertEquals(3, $game->timeline()->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(4, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(4, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(3, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(4, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(4, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(0, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(0, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(0, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));
    }

    public function testDiscard2(): void
    {
        $game = $this->getLog('9');

        self::assertEquals(15, $game->timeline()->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(33, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(33, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(15, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(33, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(33, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(0, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(0, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(0, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));
    }

    public function testDiscard3(): void
    {
        $game = $this->getLog('10');

        self::assertEquals(2, $game->timeline()->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(7, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(7, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(1, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(6, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(6, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(1, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(1, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(1, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));
    }

    public function testDiscard4(): void
    {
        $game = $this->getLog('1');

        self::assertEquals(7, $game->timeline()->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(11, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(11, $game->timeline()->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(4, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(8, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(8, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));

        self::assertEquals(3, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->count());
        self::assertEquals(3, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalCardsDiscarded());
        self::assertEquals(3, $game->player2->timeline->filter(EventType::CARDS_DISCARDED)->totalByValue(EventType::CARDS_DISCARDED));
    }

    public function testDiscardAtrocity(): void
    {
        $game = $this->getLog('11');

        /** @var Timeline $timeline */
        $timeline = $game->winner()?->timeline->filter(EventType::CARDS_DISCARDED);

        self::assertEquals(1, $timeline->at(0)?->value());
        self::assertEquals(1, $timeline->at(1)?->value());
        self::assertEquals(1, $timeline->at(2)?->value());
        self::assertEquals(1, $timeline->at(3)?->value());

        self::assertEquals(['Novu Archaeologist'], $timeline->at(0)?->payload()['cards']);
        self::assertEquals(['Phase Shift'], $timeline->at(1)?->payload()['cards']);
        self::assertEquals(['Dextre'], $timeline->at(2)?->payload()['cards']);
        self::assertEquals(['Umbra'], $timeline->at(3)?->payload()['cards']);

        self::assertStringContainsString('Atrocity', $timeline->at(0)?->payload()['msg'] ?? null);
        self::assertStringContainsString('Atrocity', $timeline->at(1)?->payload()['msg'] ?? null);
        self::assertStringNotContainsString('Atrocity', $timeline->at(2)?->payload()['msg'] ?? null);
        self::assertStringContainsString('Atrocity', $timeline->at(3)?->payload()['msg'] ?? null);
    }
}
