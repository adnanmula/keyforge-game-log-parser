<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class CardUsedTest extends TestCase
{
    use GetTestData;

    public function testCardUsed1(): void
    {
        $game = $this->getLog('1');
        $events = $game->timeline()->filter(EventType::CARD_USED);

        self::assertEquals(68, $events->count());
        self::assertEquals(34, $game->player1->timeline->filter(EventType::CARD_USED)->count());
        self::assertEquals(34, $game->player2->timeline->filter(EventType::CARD_USED)->count());

        self::assertEquals('Eclectic Inquiry', $events->at(0)?->value());
        self::assertEquals('Archive the top two cards of their deck', $events->at(0)?->payload()['effect']);
        self::assertEquals('NaN', $events->at(0)?->player());

        self::assertEquals('Eldest Batchminder', $events->at(10)?->value());
        self::assertEquals('Place 2 +1 power counters on Terrance Surefoot, Yandylinx, and Naja', $events->at(10)?->payload()['effect']);
        self::assertEquals('nan26', $events->at(10)?->player());

        self::assertEquals('Mindfire', $events->at(12)?->value());
        self::assertEquals("Discard a card at random from nan26's hand", $events->at(12)?->payload()['effect']);
        self::assertEquals('NaN', $events->at(12)?->player());
    }

    public function testCardUsed2(): void
    {
        $game = $this->getLog('2');
        $events = $game->timeline()->filter(EventType::CARD_USED);
        $eventsPlayer1 = $game->player1->timeline->filter(EventType::CARD_USED);
        $eventsPlayer2 = $game->player2->timeline->filter(EventType::CARD_USED);

        self::assertEquals(2, $events->count());
        self::assertEquals(1, $game->player1->timeline->filter(EventType::CARD_USED)->count());
        self::assertEquals(1, $game->player2->timeline->filter(EventType::CARD_USED)->count());

        self::assertEquals('Flaxia', $events->at(0)?->value());
        self::assertEquals('Gain 2 Æmber', $events->at(0)?->payload()['effect']);
        self::assertEquals('NaN', $events->at(0)?->player());

        self::assertEquals('Gleeful Mayhem', $events->at(1)?->value());
        self::assertEquals('Deal 5 damage to Flaxia', $events->at(1)?->payload()['effect']);
        self::assertEquals('nan26', $events->at(1)?->player());

        self::assertEquals('Flaxia', $eventsPlayer1->at(0)?->value());
        self::assertEquals('Gain 2 Æmber', $eventsPlayer1->at(0)?->payload()['effect']);
        self::assertEquals('NaN', $eventsPlayer1->at(0)?->player());

        self::assertEquals('Gleeful Mayhem', $eventsPlayer2->at(0)?->value());
        self::assertEquals('Deal 5 damage to Flaxia', $eventsPlayer2->at(0)?->payload()['effect']);
        self::assertEquals('nan26', $eventsPlayer2->at(0)?->player());
    }
}
