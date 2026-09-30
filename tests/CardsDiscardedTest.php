<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use PHPUnit\Framework\TestCase;

final class CardsDiscardedTest extends TestCase
{
    use GetTestData;

    public function testDiscard1(): void
    {
        $game = $this->getLog('discards');

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

    public function testDiscardAtrocity(): void
    {
        $game = $this->getLog('prophecies_1');

        $player1Events = $game->player1->timeline->filter(EventType::CARDS_DISCARDED);
        $player2Events = $game->player2->timeline->filter(EventType::CARDS_DISCARDED);

        self::assertCount(11, $player1Events);
        self::assertCount(3, $player2Events);

        self::assertEquals(['Carrion Wyrm', 'They Tell No Tales'], $player1Events->at(0)?->payload()['cards']);
        self::assertEquals(['Cover Fire'], $player1Events->at(1)?->payload()['cards']);
        self::assertEquals(['We Can ALL Win'], $player1Events->at(2)?->payload()['cards']);
        self::assertEquals(['Miasma'], $player1Events->at(3)?->payload()['cards']);
        self::assertEquals(['Embellish Imp'], $player1Events->at(4)?->payload()['cards']);
        self::assertEquals(['Predatory Lending'], $player1Events->at(5)?->payload()['cards']);
        self::assertEquals(['Dark Minion'], $player1Events->at(6)?->payload()['cards']);
        self::assertEquals('Atrocity', $player1Events->at(6)?->payload()['trigger']);
        self::assertEquals(Source::OPPONENT, $player1Events->at(6)?->source());
        self::assertEquals(['Citizen Shrix', 'Bondsman Belvan', 'Agamignus'], $player1Events->at(7)?->payload()['cards']);
        self::assertEquals(['Navigator Ali', 'Mickey the Carver', 'Skorpeon'], $player1Events->at(8)?->payload()['cards']);
        self::assertEquals(['Disabled Security', 'Predatory Lending', 'Event Horizon'], $player1Events->at(9)?->payload()['cards']);
        self::assertEquals(['Predatory Lending', 'Vial of Mutation', 'They Tell No Tales'], $player1Events->at(10)?->payload()['cards']);

        self::assertStringContainsString('random card', $player2Events->at(0)?->payload()['msg']);
        self::assertEquals(['Fallen Sovereign', 'Navigator Ali'], $player2Events->at(1)?->payload()['cards']);
        self::assertEquals(['Urchin'], $player2Events->at(2)?->payload()['cards']);
    }

    public function testDiscardInHereSomewhere(): void
    {
        $game = $this->getLog('1');

        self::assertEquals(5, $game->player1->timeline->filter(EventType::CARDS_DISCARDED)->at(0)?->value());
    }
}
