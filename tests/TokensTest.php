<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class TokensTest extends TestCase
{
    use GetTestData;

    public function testTokensGenerated(): void
    {
        $game = $this->getLog('token_creatures');

        $eventsPlayer1 = $game->player1->timeline->filter(EventType::TOKEN_CREATED);
        $eventsPlayer2 = $game->player2->timeline->filter(EventType::TOKEN_CREATED);

        self::assertCount(11, $eventsPlayer1);
        self::assertCount(6, $eventsPlayer2);

        self::assertEquals('Slipshot', $eventsPlayer1->at(0)?->value());
        self::assertEquals('Unbinding', $eventsPlayer1->at(1)?->value());
        self::assertEquals('Whimsical Conjuror to resolve Song of Spring\'s Æmber bonus icon', $eventsPlayer1->at(2)?->value());
        self::assertEquals('Whimsical Conjuror to resolve The Evil Eye\'s Æmber bonus icon', $eventsPlayer1->at(3)?->value());
        self::assertEquals('Whimsical Conjuror to resolve Levy of Souls\'s Æmber bonus icon', $eventsPlayer1->at(4)?->value());
        self::assertEquals('Whimsical Conjuror to resolve Unbinding\'s Æmber bonus icon', $eventsPlayer1->at(5)?->value());
        self::assertEquals('Unbinding', $eventsPlayer1->at(6)?->value());
        self::assertEquals('Whimsical Conjuror to resolve Bonesaw\'s Æmber bonus icon', $eventsPlayer1->at(7)?->value());
        self::assertEquals('Whimsical Conjuror to resolve Hidden Stash\'s Æmber bonus icon', $eventsPlayer1->at(8)?->value());
        self::assertEquals('Slipshot', $eventsPlayer1->at(9)?->value());
        self::assertEquals('Whimsical Conjuror to resolve Life for a Life\'s Æmber bonus icon', $eventsPlayer1->at(10)?->value());

        self::assertEquals('Ironyx Vatminder', $eventsPlayer2->at(0)?->value());
        self::assertEquals('Ironyx Vatminder', $eventsPlayer2->at(1)?->value());
        self::assertEquals('Ironyx Banner', $eventsPlayer2->at(2)?->value());
        self::assertEquals('The Grand Gord', $eventsPlayer2->at(3)?->value());
        self::assertEquals('Ironyx Vatminder', $eventsPlayer2->at(4)?->value());
        self::assertEquals('Ironyx Vatminder', $eventsPlayer2->at(5)?->value());
    }

    public function testTokensGenerated2(): void
    {
        $game = $this->getLog('token_creatures_2');

        $player1Events = $game->player1->timeline->filter(EventType::TOKEN_CREATED);
        $player2Events = $game->player2->timeline->filter(EventType::TOKEN_CREATED);

        self::assertEquals(10, $player1Events->count());
        self::assertEquals('Gate Warden', $player1Events->at(0)?->value());
        self::assertEquals('Titan Outpost', $player1Events->at(1)?->value());
        self::assertEquals('Auto-Autopsy 2.1', $player1Events->at(2)?->value());
        self::assertEquals('Hypothesize', $player1Events->at(3)?->value());
        self::assertEquals('Golis Artificer', $player1Events->at(4)?->value());
        self::assertEquals('Golis Artificer', $player1Events->at(5)?->value());
        self::assertEquals('Titan Outpost', $player1Events->at(6)?->value());
        self::assertEquals('Gate Warden', $player1Events->at(7)?->value());
        self::assertEquals('Big Sal', $player1Events->at(8)?->value());
        self::assertEquals('Big Sal', $player1Events->at(9)?->value());
        self::assertEquals(0, $player2Events->count());
    }
}
