<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use PHPUnit\Framework\TestCase;

final class PropheciesTest extends TestCase
{
    use GetTestData;

    public function testProphecies1(): void
    {
        $game = $this->getLog('prophecies_1');

        $propheciesActivatedPlayer1 = $game->player1->timeline->filter(EventType::PROPHECY_ACTIVATED);
        $propheciesFulfilledPlayer1 = $game->player1->timeline->filter(EventType::PROPHECY_FULFILLED);
        $fatesPlayer1 = $game->player1->timeline->filter(EventType::FATE_RESOLVED);
        $propheciesActivatedPlayer2 = $game->player2->timeline->filter(EventType::PROPHECY_ACTIVATED);
        $propheciesFulfilledPlayer2 = $game->player2->timeline->filter(EventType::PROPHECY_FULFILLED);
        $fatesPlayer2 = $game->player2->timeline->filter(EventType::FATE_RESOLVED);

        self::assertCount(10, $propheciesActivatedPlayer1);
        self::assertEquals('Look How Far You’ve Come', $propheciesActivatedPlayer1->first()?->value());
        self::assertEquals('Overreach', $propheciesActivatedPlayer1->at(4)?->value());
        self::assertEquals('Look How Far You’ve Come', $propheciesActivatedPlayer1->last()?->value());
        self::assertCount(10, $propheciesFulfilledPlayer1);
        self::assertEquals('Look How Far You’ve Come', $propheciesFulfilledPlayer1->first()?->value());
        self::assertEquals('Overreach', $propheciesFulfilledPlayer1->at(4)?->value());
        self::assertEquals('Look How Far You’ve Come', $propheciesFulfilledPlayer1->last()?->value());

        self::assertCount(8, $propheciesActivatedPlayer2);
        self::assertEquals('Heads, I Win', $propheciesActivatedPlayer2->first()?->value());
        self::assertEquals('Outlook Not So Good', $propheciesActivatedPlayer2->at(1)?->value());
        self::assertEquals('The Early Bird', $propheciesActivatedPlayer2->at(2)?->value());
        self::assertEquals('Trust Your Feelings', $propheciesActivatedPlayer2->at(4)?->value());
        self::assertEquals('Trust Your Feelings', $propheciesActivatedPlayer2->last()?->value());
        self::assertCount(8, $propheciesFulfilledPlayer2);
        self::assertEquals('Outlook Not So Good', $propheciesFulfilledPlayer2->first()?->value());
        self::assertEquals('The Early Bird', $propheciesFulfilledPlayer2->at(1)?->value());
        self::assertEquals('Heads, I Win', $propheciesFulfilledPlayer2->at(2)?->value());
        self::assertEquals('Trust Your Feelings', $propheciesFulfilledPlayer2->at(4)?->value());
        self::assertEquals('Trust Your Feelings', $propheciesFulfilledPlayer2->last()?->value());

        self::assertCount(10, $fatesPlayer1);
        self::assertEquals('Hock', $fatesPlayer1->first()?->value());
        self::assertFalse($fatesPlayer1->first()?->payload()['has_fate']);
        self::assertEquals('DAL-33-T3R', $fatesPlayer1->at(4)?->value());
        self::assertTrue($fatesPlayer1->at(4)?->payload()['has_fate']);
        self::assertEquals('Event Horizon', $fatesPlayer1->last()?->value());
        self::assertFalse($fatesPlayer1->last()?->payload()['has_fate']);

        self::assertCount(8, $fatesPlayer2);
        self::assertEquals('Hock', $fatesPlayer2->first()?->value());
        self::assertFalse($fatesPlayer2->first()?->payload()['has_fate']);
        self::assertEquals('Chasm Vespid', $fatesPlayer2->at(4)?->value());
        self::assertTrue($fatesPlayer2->at(4)?->payload()['has_fate']);
        self::assertEquals('Bondsman Belvan', $fatesPlayer2->last()?->value());
        self::assertTrue($fatesPlayer2->last()?->payload()['has_fate']);
    }

    public function testProphecies2(): void
    {
        $game = $this->getLog('prophecies_2');

        $propheciesPlayer1 = $game->player1->timeline->filter(EventType::PROPHECY_ACTIVATED);
        $fatesPlayer1 = $game->player1->timeline->filter(EventType::FATE_RESOLVED);

        self::assertCount(0, $game->player2->timeline->filter(EventType::PROPHECY_ACTIVATED, EventType::FATE_RESOLVED));
        self::assertCount(2, $propheciesPlayer1);
        self::assertCount(2, $fatesPlayer1);

        self::assertEquals('Outlook Not So Good', $propheciesPlayer1->at(0)?->value());
        self::assertEquals('Outlook Not So Good', $propheciesPlayer1->at(1)?->value());

        self::assertEquals('Citizen Shrix', $fatesPlayer1->at(0)?->value());
        self::assertFalse($fatesPlayer1->at(0)?->payload()['has_fate']);
        self::assertEquals('Predatory Lending', $fatesPlayer1->at(1)?->value());
        self::assertTrue($fatesPlayer1->at(1)?->payload()['has_fate']);
    }

    public function testAskAgainLater(): void
    {
        $game = $this->getLog('prophecies_3');

        $summary = $game->timeline()->propheciesSummary();

        self::assertArrayHasKey('prophecies', $summary);
        self::assertArrayHasKey('fates', $summary);

        self::assertCount(1, $summary['prophecies']);
        self::assertCount(3, $summary['fates']);

        self::assertEquals(3, $summary['prophecies']['Ask Again Later']['activated']);
        self::assertEquals(3, $summary['prophecies']['Ask Again Later']['fulfilled']);
        self::assertEquals(100, $summary['prophecies']['Ask Again Later']['percent']);
    }

    public function testHeadsOrTails(): void
    {
        $game = $this->getLog('prophecies_4');
        $summaryPlayer1 = $game->player1->timeline->propheciesSummary();
        $summaryPlayer2 = $game->player2->timeline->propheciesSummary();

        self::assertArrayHasKey('prophecies', $summaryPlayer1);
        self::assertArrayHasKey('prophecies', $summaryPlayer2);

        self::assertCount(2, $summaryPlayer1['prophecies']);
        self::assertCount(2, $summaryPlayer2['prophecies']);

        self::assertEquals(7, $summaryPlayer1['prophecies']['Heads, I Win']['activated']);
        self::assertEquals(6, $summaryPlayer1['prophecies']['Heads, I Win']['fulfilled']);
        self::assertEquals(1, $summaryPlayer1['prophecies']['Trust Your Feelings']['activated']);
        self::assertEquals(0, $summaryPlayer1['prophecies']['Trust Your Feelings']['fulfilled']);

        self::assertEquals(4, $summaryPlayer2['prophecies']['Tails, You Lose']['activated']);
        self::assertEquals(1, $summaryPlayer2['prophecies']['Tails, You Lose']['fulfilled']);
        self::assertEquals(3, $summaryPlayer2['prophecies']['Stars Aligned']['activated']);
        self::assertEquals(2, $summaryPlayer2['prophecies']['Stars Aligned']['fulfilled']);
    }
}
