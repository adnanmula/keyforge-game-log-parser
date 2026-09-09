<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    use GetTestData;

    public function testExtraTurns(): void
    {
        $event = new Event(
            EventType::TIDE_RAISED,
            'player1',
            new Turn(2, Moment::BETWEEN, 10),
            Source::PLAYER,
            'manual',
        );

        self::assertEquals([
            'player' => 'player1',
            'type' => EventType::TIDE_RAISED->value,
            'source' => Source::PLAYER->value,
            'turn' => [
                'value' => 2,
                'moment' => 'BETWEEN',
                'occurredOn' => 10,
            ],
            'value' => 'manual',
            'payload' => [],
        ], $event->jsonSerialize());

        self::assertEquals(
            '2 BETWEEN 10 | player1 | TIDE_RAISED',
            (string) $event,
        );
    }
}
