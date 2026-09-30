<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

use AdnanMula\KeyforgeGameLogParser\Parser\Exception\InvalidLogType;
use AdnanMula\KeyforgeGameLogParser\Parser\Exception\MalformedLog;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;
use PHPUnit\Framework\TestCase;

final class ExtractPlayerAndDeckTest extends TestCase
{
    public function testExtract1(): void
    {
        $messages = [
            '  Kf player   brings   The Mighty-Deck 2000   to The Crucible  ',
            'Other_player brings Deck of Many Things to The Crucible',
            ' ',
            'Kf player has connected to the game server',
            'Other_player has connected to the game server',
        ];

        $parser = new GameLogParser();
        $game = $parser->execute($messages, ParseType::ARRAY);

        self::assertEquals('Kf player', $game->player1->name);
        self::assertEquals('The Mighty-Deck 2000', $game->player1->deck);
        self::assertEquals('Other_player', $game->player2->name);
        self::assertEquals('Deck of Many Things', $game->player2->deck);
    }

    public function testExtract2(): void
    {
        $html = '<div class="message"> Alice brings Deck A to The Crucible </div>'
              . '<div class="message chat-bubble">chat</div>'
              . '<div class="message">Bob brings Deck B to The Crucible</div>';

        $parser = new GameLogParser();
        $game = $parser->execute($html, ParseType::HTML);

        self::assertEquals('Alice', $game->player1->name);
        self::assertEquals('Deck A', $game->player1->deck);
        self::assertEquals('Bob', $game->player2->name);
        self::assertEquals('Deck B', $game->player2->deck);
    }

    public function testExtract3(): void
    {
        self::expectException(InvalidLogType::class);

        $parser = new GameLogParser();
        $game = $parser->execute('', ParseType::HTML);

        self::assertEquals('Alice', $game->player1->name);
        self::assertEquals('Deck A', $game->player1->deck);
        self::assertEquals('Bob', $game->player2->name);
        self::assertEquals('Deck B', $game->player2->deck);
    }

    public function testExtractIncomplete(): void
    {
        self::expectException(MalformedLog::class);

        $messages = [
            '  Kf player   brings   The Mighty-Deck 2000   to The Crucible  ',
        ];

        $parser = new GameLogParser();
        $parser->execute($messages, ParseType::ARRAY);
    }

    public function testExtractIncomplete2(): void
    {
        $messages = [
            '  Kf player   brings   The Mighty-Deck 2000   to The Crucible  ',
            'Kf player has connected to the game server',
            'Other_player has connected to the game server',
        ];

        $parser = new GameLogParser();
        $game = $parser->execute($messages, ParseType::ARRAY);

        self::assertEquals('Kf player', $game->player1->name);
        self::assertEquals('Other_player', $game->player2->name);
        self::assertCount(0, $game->timeline());

        self::assertEquals([
            'player1' => [
                'name' => 'Kf player',
                'escaped_name' => 'Kf player',
                'deck' => 'The Mighty-Deck 2000',
                'is_first' => false,
                'is_winner' => false,
                'has_conceded' => false,
                'timeline' => [],
            ],
            'player2' => [
                'name' => 'Other_player',
                'escaped_name' => 'Other_player',
                'deck' => 'Unknown',
                'is_first' => false,
                'is_winner' => false,
                'has_conceded' => false,
                'timeline' => [],
            ],
            'winner' => null,
            'raw_log' => [
                'Kf player brings The Mighty-Deck 2000 to The Crucible',
                'Kf player has connected to the game server',
                'Other_player has connected to the game server',
            ],
        ], $game->jsonSerialize());
    }

    public function testWrongParseType(): void
    {
        self::expectException(InvalidLogType::class);

        $messages = [];

        $parser = new GameLogParser();
        $parser->execute($messages, ParseType::ARRAY);
    }

    public function testWrongParseType2(): void
    {
        self::expectException(InvalidLogType::class);

        $messages = '';

        $parser = new GameLogParser();
        $parser->execute($messages, ParseType::PLAIN);
    }

    public function testWrongParseType3(): void
    {
        self::expectException(InvalidLogType::class);

        $messages = 'asd asda asd';

        $parser = new GameLogParser();
        $parser->execute($messages, ParseType::ARRAY);
    }
}
