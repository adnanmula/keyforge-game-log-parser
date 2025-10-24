<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorCardsDrawn implements LogProcessor
{
    public function execute(Game $game, int $index, string $message, ?array $messages = null): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+draws\s+(\d+)\s+card/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::CARDS_DRAWN,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    (int) $matches[2],
                ),
            );
        }

        return $game;
    }
}
