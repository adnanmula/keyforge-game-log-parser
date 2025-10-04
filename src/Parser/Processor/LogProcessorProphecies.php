<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Game\Game;

final class LogProcessorProphecies implements LogProcessor
{
    public function execute(Game $game, int $index, string $message): Game
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternFate = "/^($player1|$player2)\s+resolves the fate effect of\s+(.+)\s*$/";
        $patternActivate = "/^($player1|$player2)\s+activates their prophecy\s+(.+)\s*$/";
        $patternFulfilled = "/^($player1|$player2)\s+uses\s+(.+)\s+to fulfill its prophecy\s*$/";

        if (preg_match($patternFate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FATE_RESOLVED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternActivate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternFulfilled, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_FULFILLED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        return $game;
    }
}
