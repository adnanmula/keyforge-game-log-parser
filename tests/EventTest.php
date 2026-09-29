<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Player;
use AdnanMula\KeyforgeGameLogParser\Parser\Exception\PlayerNotInGameException;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    use GetTestData;

    public function testEventPayload(): void
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

        $player1 = new Player('Name', 'The deck', true, true, false, 3);

        self::assertEquals(
            [
                'name' => 'Name',
                'escaped_name' => 'Name',
                'deck' => 'The deck',
                'is_first' => true,
                'is_winner' => true,
                'has_conceded' => false,
                'timeline' => [],
            ],
            $player1->jsonSerialize(),
        );
    }

    public function testGameIntegrity1(): void
    {
        self::expectException(PlayerNotInGameException::class);

        $game = $this->getLog('minimal_game');
        $game->player('no-one');
    }

    public function testGameIntegrity2(): void
    {
        self::expectException(PlayerNotInGameException::class);

        $game = $this->getLog('minimal_game');
        $game->opponentOf('no-one');
    }

    public function testGameIntegrity3(): void
    {
        self::expectException(PlayerNotInGameException::class);

        $game = $this->getLog('minimal_game');
        $game->opponentOf(new Player('no-one', ''));
    }

    public function testGameIntegrity4(): void
    {
        $messages = [
            '  Kf player   brings   The Mighty-Deck 2000   to The Crucible  ',
            'Other_player brings Deck of Many Things to The Crucible',
            ' ',
            'Kf player has connected to the game server',
            'Other_player has connected to the game server',
            'Kf player mutes spectators',
        ];

        $parser = new GameLogParser();
        $game = $parser->execute($messages, ParseType::ARRAY);

        self::assertNull($game->winner());
        self::assertNull($game->loser());
        self::assertNull($game->first());
    }
}
