<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Tests;

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

    public function testExtractIncomplete(): void
    {
        self::expectException(\Throwable::class);

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
    }
}
